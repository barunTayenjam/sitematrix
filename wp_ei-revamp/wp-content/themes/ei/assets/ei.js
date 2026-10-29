/*
 * ei.js — Electronics India theme behaviour.
 * Zero dependencies. IntersectionObserver + click toggles only.
 */
( function () {
	'use strict';

	var media = window.matchMedia( '(prefers-reduced-motion: reduce)' );
	var isReduced = media.matches;

	/* Pause hero video if reduced motion preferred */
	function setHeroVideo( paused ) {
		var v = document.querySelector( '.ei-hero-bg' );
		if ( ! v ) return;
		if ( paused ) { v.pause(); v.removeAttribute( 'autoplay' ); }
		else { v.play().catch( function () {} ); }
	}
	setHeroVideo( isReduced );
	media.addEventListener( 'change', function ( e ) { setHeroVideo( e.matches ); } );

	/* ---------- Scroll reveal ---------- */
	var observer = new IntersectionObserver(
		function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting ) {
					entry.target.classList.add( 'is-visible' );
					observer.unobserve( entry.target );
				}
			} );
		},
		{ rootMargin: '0px 0px -120px 0px', threshold: 0.1 }
	);

	document.querySelectorAll( '.reveal' ).forEach( function ( el ) {
		observer.observe( el );
	} );

	/* Also trigger reveals on elements already in viewport on load */
	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.reveal' ).forEach( function ( el ) {
			el.classList.add( 'is-visible' );
		} );
	} );

	/* ---------- Mega menu click toggle (touch devices) ---------- */
	document.querySelectorAll( '.ei-nav .menu-item-has-children > a' ).forEach( function ( link ) {
		var panel   = link.closest( '.menu-item-has-children' ).querySelector( '.ei-mega-panel' );
		var isMega  = !! panel;

		if ( isMega ) {
			link.setAttribute( 'aria-expanded', 'false' );
			link.setAttribute( 'aria-haspopup', 'true' );
			link.id = 'ei-mega-' + Math.random().toString( 36 ).slice( 2 );
			panel.setAttribute( 'aria-labelledby', link.id );

			link.addEventListener( 'click', function ( e ) {
				if ( window.innerWidth <= 767 ) {
					e.preventDefault();
					var expanded = 'true' === link.getAttribute( 'aria-expanded' );
					link.setAttribute( 'aria-expanded', String( ! expanded ) );
					panel.style.display = expanded ? 'none' : 'block';
				}
			} );
		}
	} );

	/* ---------- Contact form ---------- */
	var form = document.getElementById( 'ei-contact-form' );
	if ( form ) {
		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();

			var data = {};
			Array.prototype.forEach.call( form.querySelectorAll( 'input, textarea, select' ), function ( field ) {
				if ( field.name ) { data[ field.name ] = field.value.trim(); }
			} );

			var status = form.querySelector( '.ei-form-status' );
			var btn    = form.querySelector( 'button[type="submit"]' );
			var orig   = btn.textContent;

			status.textContent = '';
			status.setAttribute( 'role', 'status' );
			status.setAttribute( 'aria-live', 'polite' );

			fetch( form.action, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( data )
			} ).then( function ( res ) {
				return res.json();
			} ).then( function ( json ) {
				btn.textContent = orig;
				btn.disabled = false;
				status.setAttribute( 'aria-live', 'assertive' );
				status.textContent = json.message || ( json.success ? 'Message sent. We will get back to you soon.' : 'Failed to send. Please email us directly.' );
				if ( json.success ) { form.reset(); }
			} ).catch( function () {
				btn.textContent = orig;
				btn.disabled = false;
				status.setAttribute( 'aria-live', 'assertive' );
				status.textContent = 'Something went wrong. Please email info@electronicsindia.co.in directly.';
			} );

			btn.textContent = 'Sending…';
			btn.disabled = true;
		} );
	}
} )();