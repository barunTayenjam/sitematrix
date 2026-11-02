// 10K Agency — Warm Editorial Light interactions.
// Scroll reveals (JS-gated), header clock, mobile nav.
// Zero dependencies.

(() => {
	const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// Enable motion-aware reveals (CSS hides .reveal only when .js present).
	document.documentElement.classList.add('js');

	// --- Scroll reveals
	if ('IntersectionObserver' in window && !reduced) {
		const ro = new IntersectionObserver(
			(entries) => {
				entries.forEach((e) => {
					if (e.isIntersecting) {
						e.target.classList.add('in');
						ro.unobserve(e.target);
					}
				});
			},
			{ rootMargin: '0px 0px 100px 0px', threshold: 0.1 }
		);
		document.querySelectorAll('.reveal').forEach((r) => ro.observe(r));
	} else {
		document.querySelectorAll('.reveal').forEach((r) => r.classList.add('in'));
	}

	// --- Header clock (local time, no request)
	const clockEl = document.getElementById('headerClockTime');
	if (clockEl) {
		const fmt = new Intl.DateTimeFormat('en-GB', {
			hour: '2-digit',
			minute: '2-digit',
			second: '2-digit',
			hour12: false,
		});
		const tick = () => { clockEl.textContent = fmt.format(new Date()); };
		tick();
		setInterval(tick, 1000);
	}

	// --- Mobile nav
	const navToggle = document.querySelector('.nav-toggle');
	const navMenu = document.querySelector('.nav-menu');
	if (navToggle && navMenu) {
		navToggle.addEventListener('click', () => {
			const open = navMenu.classList.toggle('open');
			navToggle.setAttribute('aria-expanded', String(open));
		});
		navMenu.querySelectorAll('a').forEach((link) => {
			link.addEventListener('click', () => {
				navMenu.classList.remove('open');
				navToggle.setAttribute('aria-expanded', 'false');
			});
		});
	}
})();
