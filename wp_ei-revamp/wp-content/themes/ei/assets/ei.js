// 10K Agency — Technical Luxury interactions.
// Scroll reveals, oscilloscope canvas, mobile nav, native contact form.
// Zero external dependencies.

(() => {
	const reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	// --- IntersectionObserver reveals
	if ( 'IntersectionObserver' in window ) {
		const ro = new IntersectionObserver(
			( entries ) => {
				entries.forEach( ( e ) => {
					if ( e.isIntersecting ) {
						e.target.classList.add( 'in' );
						ro.unobserve( e.target );
					}
				} );
			},
			{ rootMargin: '0px 0px 100px 0px', threshold: 0.1 }
		);
		document.querySelectorAll( '.reveal' ).forEach( ( r ) => ro.observe( r ) );
	} else {
		document.querySelectorAll( '.reveal' ).forEach( ( r ) => r.classList.add( 'in' ) );
	}

	// Reduced motion: reveal everything immediately
	if ( reduced ) {
		document.querySelectorAll( '.reveal' ).forEach( ( r ) => r.classList.add( 'in' ) );
	}

	// --- Mobile nav toggle
	const navToggle = document.querySelector( '.nav-toggle' );
	const navMenu = document.querySelector( '.nav-menu' );
	if ( navToggle && navMenu ) {
		navToggle.addEventListener( 'click', () => {
			const open = navMenu.classList.toggle( 'open' );
			navToggle.setAttribute( 'aria-expanded', String( open ) );
		} );
		navMenu.querySelectorAll( 'a' ).forEach( ( link ) => {
			link.addEventListener( 'click', () => {
				navMenu.classList.remove( 'open' );
				navToggle.setAttribute( 'aria-expanded', 'false' );
			} );
		} );
	}

	// --- Oscilloscope trace canvas (sheet s3)
	const canvas = document.getElementById( 'scopeCanvas' );
	if ( canvas && canvas.getContext ) {
		const ctx = canvas.getContext( '2d' );
		const dpr = window.devicePixelRatio || 1;
		let w = 0, h = 0, noise = [];

		function size() {
			const box = canvas.parentElement.getBoundingClientRect();
			w = Math.max( 320, Math.round( box.width ) );
			h = Math.round( w * 0.22 );
			canvas.width = w * dpr;
			canvas.height = h * dpr;
			canvas.style.width = w + 'px';
			canvas.style.height = h + 'px';
			ctx.setTransform( dpr, 0, 0, dpr, 0, 0 );
			noise = Array.from( { length: w + 1 }, () => ( Math.random() * 2 - 1 ) * 1.5 );
		}

		function trace( phase ) {
			ctx.clearRect( 0, 0, w, h );
			// Grid
			ctx.strokeStyle = 'rgba(197, 203, 208, 0.025)';
			ctx.lineWidth = 1;
			for ( let gx = 0; gx <= w; gx += 64 ) {
				ctx.beginPath();
				ctx.moveTo( gx, 0 );
				ctx.lineTo( gx, h );
				ctx.stroke();
			}
			for ( let gy = 0; gy <= h; gy += 40 ) {
				ctx.beginPath();
				ctx.moveTo( 0, gy );
				ctx.lineTo( w, gy );
				ctx.stroke();
			}
			// Wave
			ctx.strokeStyle = 'rgba(229, 72, 77, 0.85)';
			ctx.lineWidth = 1.5;
			ctx.beginPath();
			const cy = h / 2;
			const amp = h * 0.25;
			for ( let x = 0; x <= w; x++ ) {
				const base = Math.sin( ( x + phase * 30 ) * 0.02 ) * amp;
				const mod = 0.6 + 0.4 * Math.sin( x * 0.0025 );
				const y = cy + base * mod + noise[ x ] * 2;
				if ( x === 0 ) ctx.moveTo( x, y );
				else ctx.lineTo( x, y );
			}
			ctx.stroke();
		}

		size();
		window.addEventListener( 'resize', size );
		if ( ! reduced ) {
			let phase = 0;
			( function animate() {
				phase += 0.045;
				trace( phase );
				requestAnimationFrame( animate );
			} )();
		} else {
			trace( 0 );
		}
	}

	// --- Native contact form submit (no page navigation)
	const form = document.getElementById( 'ei-contact-form' );
	if ( form ) {
		const statusSuccess = form.querySelector( '.form-status.success' );
		const statusError = form.querySelector( '.form-status.error' );
		const btn = form.querySelector( 'button[type="submit"]' );
		const endpoint = window.eiContact?.endpoint || '/wp-json/ei/v1/contact';

		const validate = () => {
			let ok = true;
			form.querySelectorAll( '[required]' ).forEach( ( field ) => {
				const isEmail = field.type === 'email';
				const val = field.value.trim();
				const valid = isEmail
					? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( val )
					: val !== '';
				if ( ! valid ) {
					ok = false;
					field.setAttribute( 'aria-invalid', 'true' );
				} else {
					field.removeAttribute( 'aria-invalid' );
				}
			} );
			return ok;
		};

		const showStatus = ( type, text ) => {
			if ( type === 'success' ) {
				if ( statusSuccess ) {
					statusSuccess.textContent = text;
					statusSuccess.style.display = 'block';
				}
				if ( statusError ) statusError.style.display = 'none';
			} else {
				if ( statusError ) {
					statusError.textContent = text;
					statusError.style.display = 'block';
				}
				if ( statusSuccess ) statusSuccess.style.display = 'none';
			}
		};

		form.addEventListener( 'submit', async ( ev ) => {
			ev.preventDefault();
			if ( ! validate() ) {
				showStatus( 'error', 'Please fill in the required fields.' );
				const first = form.querySelector( '[aria-invalid="true"]' );
				if ( first ) first.focus();
				return;
			}

			btn.disabled = true;
			btn.textContent = 'Sending…';
			if ( statusSuccess ) statusSuccess.style.display = 'none';
			if ( statusError ) statusError.style.display = 'none';

			const data = {
				name: form.elements.name?.value.trim() || '',
				email: form.elements.email?.value.trim() || '',
				phone: form.elements.phone?.value.trim() || '',
				subject: form.elements.subject?.value.trim() || '',
				message: form.elements.message?.value.trim() || '',
				website: '', // honeypot
			};

			const nonce = window.eiContact?.nonce || '';
			const headers = { 'Content-Type': 'application/json' };
			if ( nonce ) headers[ 'X-EI-Nonce' ] = nonce;
			data._ei_nonce = nonce;

			try {
				const res = await fetch( endpoint, {
					method: 'POST',
					headers,
					body: JSON.stringify( data ),
				} );
				const payload = await res.json().catch( () => ( {} ) );

				if ( res.ok && payload.success ) {
					btn.textContent = 'Sent';
					btn.disabled = false;
					showStatus( 'success', payload.message || 'Message sent. We will reply shortly.' );
					form.reset();
				} else {
					btn.textContent = 'Send Message';
					btn.disabled = false;
					showStatus( 'error', payload.message || 'Could not send — please try again.' );
				}
			} catch ( err ) {
				btn.textContent = 'Send Message';
				btn.disabled = false;
				showStatus( 'error', 'Network error — please email us directly.' );
			}
		} );
	}
} )();