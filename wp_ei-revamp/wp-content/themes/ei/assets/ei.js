// EI / scope.js — 10K Agency.
// Reveals, dual-lane marquee, type motion, mega menu, product cards. Zero deps.

(() => {
	const reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	// --- Sheet reveals (JS adds .in; CSS matches .reveal.in) ---
	const ro = new IntersectionObserver(
		entries => {
			entries.forEach( ( e ) => {
				if ( e.isIntersecting ) {
					e.target.classList.add( 'in' );
					ro.unobserve( e.target );
				}
			} );
		},
		{ rootMargin: '0px 0px 200px 0px', threshold: 0.1 }
	);
	document.querySelectorAll( '.reveal' ).forEach( ( r ) => ro.observe( r ) );

	// --- Type reveal on hero words (staggered char scramble resolve) ---
	const heroWords = document.querySelectorAll( '.hero h1 .word' );
	if ( heroWords.length && ! reduced ) {
		heroWords.forEach( ( word, wi ) => {
			const text = word.textContent;
			const chars = text.split( '' );
			word.innerHTML = '';
			chars.forEach( ( ch, ci ) => {
				const span = document.createElement( 'span' );
				span.className = 'char';
				span.textContent = ch;
				span.style.opacity = '0';
				span.style.transform = 'translateY(20px)';
				span.style.transition = `opacity 400ms cubic-bezier(0.22,1,0.36,1) ${40 + wi * 120 + ci * 30}ms, transform 400ms cubic-bezier(0.22,1,0.36,1) ${40 + wi * 120 + ci * 30}ms`;
				word.appendChild( span );
			} );
			// resolve after a beat
			setTimeout( () => {
				word.querySelectorAll( '.char' ).forEach( ( c ) => {
					c.style.opacity = '1';
					c.style.transform = 'translateY(0)';
				} );
			}, 40 + wi * 120 );
		} );
	}

	// --- Dual-lane marquee (pause on hover) ---
	const lanes = document.querySelectorAll( '.marquee-lane' );
	lanes.forEach( ( lane ) => {
		const base = lane.cloneNode( true );
		lane.appendChild( base );
		let pct = 0;
		const duration = lane.classList.contains( 'slow' ) ? 32 : 20;
		let raf;
		const animate = () => {
			pct = ( pct + 0.15 ) % 100;
			lane.style.transform = `translateX( -${pct}% )`;
			raf = requestAnimationFrame( animate );
		};
		animate();
		lane.addEventListener( 'mouseenter', () => cancelAnimationFrame( raf ) );
		lane.addEventListener( 'mouseleave', animate );
	} );

	// --- Mobile nav toggle ---
	const navToggle = document.querySelector( '.nav-toggle' );
	const navList = document.querySelector( '.nav-list' );
	if ( navToggle && navList ) {
		navToggle.addEventListener( 'click', () => {
			const open = navList.classList.toggle( 'open' );
			navToggle.setAttribute( 'aria-expanded', String( open ) );
			navToggle.querySelector( '.nav-x' ).textContent = open ? '×' : '+';
		} );
	}

	// --- Mega menu ---
	const mega = document.getElementById( 'mega' );
	const megaTrigger = document.querySelector( '.mega-trigger' );
	if ( mega && megaTrigger ) {
		let openState = false;
		const setOpen = ( state ) => {
			openState = state;
			megaTrigger.setAttribute( 'aria-expanded', String( state ) );
			megaTrigger.querySelector( '.nav-x' ).textContent = state ? '×' : '+';
			if ( state ) {
				mega.hidden = false;
				requestAnimationFrame( () => mega.classList.add( 'open' ) );
			} else {
				mega.classList.remove( 'open' );
				setTimeout( () => { if ( ! mega.classList.contains( 'open' ) ) mega.hidden = true; }, 280 );
			}
		};
		megaTrigger.addEventListener( 'click', () => setOpen( ! openState ) );
		mega.querySelector( '.mega-backdrop' ).addEventListener( 'click', () => setOpen( false ) );
		document.addEventListener( 'keydown', ( e ) => { if ( e.key === 'Escape' ) setOpen( false ); } );
		mega.querySelectorAll( 'a' ).forEach( ( link ) => {
			link.addEventListener( 'click', () => setOpen( false ) );
		} );
	}

	// --- Product card entrance stagger ---
	const cards = document.querySelectorAll( '.product-card' );
	cards.forEach( ( card, i ) => {
		card.style.opacity = '0';
		card.style.transform = 'translateY(16px)';
		card.style.transition = `opacity 400ms cubic-bezier(0.22,1,0.36,1) ${i * 60}ms, transform 400ms cubic-bezier(0.22,1,0.36,1) ${i * 60}ms`;
		requestAnimationFrame( () => {
			card.style.opacity = '1';
			card.style.transform = 'translateY(0)';
		} );
	} );

	// --- Contact form: native REST submit, no page navigation ---
	(function () {
		const form = document.getElementById( 'ei-contact-form' );
		if ( ! form ) return;

		const status = form.querySelector( '.ei-form-status' );
		const btn = form.querySelector( 'button[type="submit"]' );
		const endpoint = form.action || ( window.eiContact && window.eiContact.endpoint );

		if ( ! endpoint ) {
			if ( status ) status.textContent = 'Form misconfigured — contact us directly.';
			return;
		}

		const validate = () => {
			let ok = true;
			form.querySelectorAll( '[required]' ).forEach( ( field ) => {
				const email = field.type === 'email';
				const value = field.value.trim();
				const isEmail = email ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( value ) : value !== '';
				if ( ! isEmail ) {
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

			btn.disabled = true;
			btn.textContent = 'Sending…';
			show( '', '' );

			const data = {
				name: form.elements.name.value.trim(),
				email: form.elements.email.value.trim(),
				phone: form.elements.phone.value.trim(),
				subject: form.elements.subject.value.trim(),
				message: form.elements.message.value.trim(),
				website: '', // honeypot — never filled by humans
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
					btn.textContent = 'Sent';
					btn.disabled = false;
					show( 'success', payload.message || 'Message sent. We will reply shortly.' );
					form.reset();
				} else {
					btn.textContent = 'Send Message';
					btn.disabled = false;
					show( 'error', payload.message || 'Could not send — please try again.' );
				}
			} catch ( err ) {
				btn.textContent = 'Send Message';
				btn.disabled = false;
				show( 'error', 'Network error — please email us directly.' );
			}
		} );
	} )();
} )();
