// 10K Agency — UI interactions. Zero external dependencies.
// Reveals, CSS marquee pause, mobile nav, native contact form.

(() => {
	const reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	// --- Sheet reveals: IO with generous bottom margin so content reveals before scroll ---
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
			{ rootMargin: '0px 0px 600px 0px', threshold: 0 }
		);
		document.querySelectorAll( '.reveal' ).forEach( ( r ) => ro.observe( r ) );
	} else {
		document.querySelectorAll( '.reveal' ).forEach( ( r ) => r.classList.add( 'in' ) );
	}

	// If reduced-motion, reveal everything immediately
	if ( reduced ) {
		document.querySelectorAll( '.reveal' ).forEach( ( r ) => r.classList.add( 'in' ) );
	}

	// --- Marquee: pause CSS animation on hover ---
	const marquees = document.querySelectorAll( '.marquee' );
	marquees.forEach( ( m ) => {
		const lanes = m.querySelectorAll( '.marquee-lane' );
		m.addEventListener( 'mouseenter', () => {
			lanes.forEach( ( l ) => { l.style.animationPlayState = 'paused'; } );
		} );
		m.addEventListener( 'mouseleave', () => {
			lanes.forEach( ( l ) => { l.style.animationPlayState = 'running'; } );
		} );
	} );

	// --- Mobile nav toggle ---
	const navToggle = document.querySelector( '.nav-toggle' );
	const navList = document.querySelector( '.nav-menu' );
	if ( navToggle && navList ) {
		navToggle.addEventListener( 'click', () => {
			const open = navList.classList.toggle( 'open' );
			navToggle.setAttribute( 'aria-expanded', String( open ) );
		} );
	}

	// --- Native contact form submit (no CF7 / page navigation) ---
	const form = document.getElementById( 'ei-contact-form' );
	if ( form ) {
		const status = form.querySelector( '.ei-form-status' );
		const btn = form.querySelector( 'button[type="submit"]' );
		const endpoint = form.action || ( window.eiContact && window.eiContact.endpoint );

		const validate = () => {
			let ok = true;
			form.querySelectorAll( '[required]' ).forEach( ( field ) => {
				const isEmail = field.type === 'email';
				const val = field.value.trim();
				const valid = isEmail ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( val ) : val !== '';
				if ( ! valid ) {
					ok = false;
					field.setAttribute( 'aria-invalid', 'true' );
				} else {
					field.removeAttribute( 'aria-invalid' );
				}
			} );
			return ok;
		};

		const show = ( kind, text ) => {
			if ( ! status ) return;
			status.className = 'ei-form-status ' + kind;
			status.textContent = text;
		};

		form.addEventListener( 'submit', async ( ev ) => {
			ev.preventDefault();
			if ( ! validate() ) {
				show( 'error', 'Please fill in the required fields.' );
				const first = form.querySelector( '[aria-invalid="true"]' );
				if ( first ) first.focus();
				return;
			}

			if ( btn ) {
				btn.disabled = true;
				btn.textContent = 'Sending…';
			}
			show( '', '' );

			const data = {
				name: form.elements.name ? form.elements.name.value.trim() : '',
				email: form.elements.email ? form.elements.email.value.trim() : '',
				phone: form.elements.phone ? form.elements.phone.value.trim() : '',
				subject: form.elements.subject ? form.elements.subject.value.trim() : '',
				message: form.elements.message ? form.elements.message.value.trim() : '',
				website: '', // honeypot
			};

			const nonce = ( window.eiContact && window.eiContact.nonce ) || '';
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
					if ( btn ) {
						btn.textContent = 'Sent';
						btn.disabled = false;
					}
					show( 'success', payload.message || 'Message sent. We will reply shortly.' );
					form.reset();
				} else {
					if ( btn ) {
						btn.textContent = 'Send Message';
						btn.disabled = false;
					}
					show( 'error', payload.message || 'Could not send — please try again.' );
				}
			} catch ( err ) {
				if ( btn ) {
					btn.textContent = 'Send Message';
					btn.disabled = false;
				}
				show( 'error', 'Network error — please email us directly.' );
			}
		} );
	}
} )();
