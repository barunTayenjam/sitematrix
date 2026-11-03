// 10K Agency — Warm Editorial Light interactions.
// Scroll reveals (JS-gated), header clock, scroll-spy, mobile nav.
// Zero dependencies.

(() => {
	const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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

	// --- Mobile nav + mega menu
	const navToggle = document.querySelector('.nav-toggle');
	const navMenu = document.querySelector('.nav-menu');
	if (navToggle && navMenu) {
		navToggle.addEventListener('click', () => {
			const open = navMenu.classList.toggle('open');
			navToggle.setAttribute('aria-expanded', String(open));
		});
		navMenu.querySelectorAll(':scope > li').forEach((li) => {
			const parentLink = li.querySelector(':scope > a');
			if (!parentLink) return;
			if (li.classList.contains('has-children')) {
				parentLink.addEventListener('click', (ev) => {
					ev.preventDefault();
					const open = li.classList.toggle('open');
					parentLink.setAttribute('aria-expanded', String(open));
				});
				li.querySelectorAll('.mega-menu a').forEach((sub) => {
					sub.addEventListener('click', () => {
						navMenu.classList.remove('open');
						navToggle.setAttribute('aria-expanded', 'false');
					});
				});
			} else {
				parentLink.addEventListener('click', () => {
					navMenu.classList.remove('open');
					navToggle.setAttribute('aria-expanded', 'false');
				});
			}
		});
		// Escape closes the mobile menu
		document.addEventListener('keydown', (ev) => {
			if (ev.key === 'Escape' && navMenu.classList.contains('open')) {
				navMenu.classList.remove('open');
				navToggle.setAttribute('aria-expanded', 'false');
			}
		});
	}

	// --- Scroll-spy: highlight nav link for the section in view
	const spySections = ['s1', 's2', 's3', 's4', 's5a', 's5']
		.map((id) => document.getElementById(id))
		.filter(Boolean);
	const navSpies = Array.from(document.querySelectorAll('.nav-menu a'));
	const spyThreshold = 0.38;
	function onScrollSpy() {
		const line = document.documentElement.clientHeight * spyThreshold;
		let current = null;
		for (const sec of spySections) {
			if (sec.getBoundingClientRect().top <= line) current = sec.id;
		}
		navSpies.forEach((a) => {
			const href = a.getAttribute('href') || '';
			const id = href.indexOf('#') !== -1 ? href.split('#')[1] : null;
			if (id && current === id) {
				a.setAttribute('aria-current', 'page');
				a.classList.add('active');
			} else {
				a.removeAttribute('aria-current');
				a.classList.remove('active');
			}
		});
	}
	document.addEventListener('scroll', onScrollSpy, { passive: true });
	window.addEventListener('scroll', onScrollSpy, { passive: true });
	onScrollSpy();
})();
