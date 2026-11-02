// EI / scope.js
// Reveals, counters, family selector, mega menu, hero artwork, mobile nav. No pointer tracking.

(() => {
	const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// --- IO reveals ---
	const ro = new IntersectionObserver(
		entries => { entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('in'); }); },
		{ rootMargin: '0px 0px 200px 0px', threshold: 0 }
	);
	document.querySelectorAll('.reveal').forEach(r => ro.observe(r));
	// hero title spans/em also animate via .in
	document.querySelectorAll('.hero-title span, .hero-title em').forEach(r => ro.observe(r));

	// --- counters roll ---
	const fmt = (el, v) => { el.textContent = v + (el.dataset.suffix || ''); };
	const ro2 = new IntersectionObserver(
		entries => {
			entries.forEach(e => {
				if (!e.isIntersecting) return;
				ro2.unobserve(e.target);
				const t = e.target;
				const end = parseInt(t.dataset.count, 10);
				if (reduced) { fmt(t, end); return; }
				let i = 0, steps = 28;
				const tick = () => {
					i++;
					if (i < steps) { fmt(t, Math.round(end * (i / steps))); requestAnimationFrame(tick); }
					else fmt(t, end);
				};
				tick();
			});
		},
		{ rootMargin: '0px 0px 200px 0px', threshold: 0 }
	);
	document.querySelectorAll('.proof-cell .count').forEach(c => ro2.observe(c));

	// --- mobile nav ---
	const navToggle = document.querySelector('.nav-toggle');
	const navList = document.querySelector('.nav-list');
	if (navToggle && navList) {
		navToggle.addEventListener('click', () => {
			const open = navList.classList.toggle('open');
			navToggle.setAttribute('aria-expanded', String(open));
			navToggle.querySelector('.nav-x').textContent = open ? '×' : '+';
		});
	}

	// --- mega menu ---
	const mega = document.getElementById('mega');
	const megaTrigger = document.querySelector('.mega-trigger');
	if (mega && megaTrigger) {
		let openState = false;
		const setOpen = (state) => {
			openState = state;
			megaTrigger.setAttribute('aria-expanded', String(state));
			megaTrigger.querySelector('.nav-x').textContent = state ? '×' : '+';
			if (state) { mega.hidden = false; requestAnimationFrame(() => mega.classList.add('open')); }
			else { mega.classList.remove('open'); setTimeout(() => { if (!mega.classList.contains('open')) mega.hidden = true; }, 280); }
		};
		megaTrigger.addEventListener('click', () => setOpen(!openState));
		mega.querySelector('.mega-backdrop').addEventListener('click', () => setOpen(false));
		document.addEventListener('keydown', e => { if (e.key === 'Escape') setOpen(false); });
		mega.querySelectorAll('a').forEach(link => {
			link.addEventListener('click', () => setOpen(false));
		});
	}

	// --- product silhouettes per family (inline SVG, zero third-party deps) ---
	const silhouettes = [
		`<svg viewBox="0 0 200 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M20 100 H180 L150 40 L50 40 Z" fill="#E7E5E4"/><path d="M60 80 H140 L130 35 L70 35 Z" fill="#D6D3D1"/><rect x="40" y="95" width="120" height="20" fill="#EFEEEC"/><circle cx="100" cy="70" r="8" fill="#AE1010"/></svg>`,
		`<svg viewBox="0 0 200 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M60 30 H140 L150 80 H50 Z" fill="#E7E5E4"/><circle cx="100" cy="62" r="20" fill="#D6D3D1"/><rect x="90" y="82" width="20" height="16" fill="#D6D3D1"/><rect x="30" y="100" width="140" height="16" fill="#EFEEEC"/></svg>`,
		`<svg viewBox="0 0 200 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M40 40 H160 L170 90 H30 Z" fill="#E7E5E4"/><path d="M80 55 H120 Q120 90 80 90 Z" fill="#D6D3D1"/><rect x="20" y="100" width="160" height="16" fill="#EFEEEC"/></svg>`,
		`<svg viewBox="0 0 200 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M60 40 H140 L150 90 H50 Z" fill="#E7E5E4"/><circle cx="100" cy="75" r="25" fill="#D6D3D1"/><rect x="30" y="105" width="140" height="15" fill="#EFEEEC"/></svg>`,
		`<svg viewBox="0 0 200 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M50 30 H150 L160 80 H40 Z" fill="#E7E5E4"/><path d="M70 65 H130 Q130 100 70 100 Z" fill="#D6D3D1"/><rect x="20" y="105" width="160" height="15" fill="#EFEEEC"/></svg>`,
		`<svg viewBox="0 0 200 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M70 30 H130 L140 80 H60 Z" fill="#E7E5E4"/><path d="M80 65 H120 L125 100 H75 Z" fill="#D6D3D1"/><rect x="30" y="105" width="140" height="15" fill="#EFEEEC"/></svg>`,
		`<svg viewBox="0 0 200 140" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M60 40 H140 L150 90 H50 Z" fill="#E7E5E4"/><path d="M80 60 H120 Q120 95 80 95 Z" fill="#D6D3D1"/><rect x="30" y="100" width="140" height="15" fill="#EFEEEC"/></svg>`
	];

	// --- family selector & flagship readout ----
	const bands = document.querySelectorAll('.band');
	const bandReadout = document.getElementById('bandReadout');
	const specimenHost = document.getElementById('specimenHost');

	// Real EI product catalog data: [label, reference standard, EI flagship]
	const specimens = [
		{
			title: 'SPECTROPHOTOMETERS', model: 'EI-UV-2000', badge: 'NEW',
			name: 'UV-Visible Spectrophotometer',
			legend: 'SPECTROPHOTOMETERS · SINGLE & DOUBLE BEAM · 190–1100 NM',
			rows: [
				['MEASURAND', 'Absorbance 190–1100 nm', 'Absorbance 190–1100 nm'],
				['OPTICS', 'Single beam, Czerny-Turner', 'Double beam, Czerny-Turner'],
				['BANDPASS', '2 nm fixed', '0.5 / 1 / 2 nm selectable'],
				['DETECTOR', 'Photodiode', 'Photodiode, sample & reference'],
				['OUTPUT', 'Analog absorbance', 'USB + parallel printer port'],
				['CALIBRATION', 'Factory NIST-traceable', 'ISO 17025 certificate included'],
			]
		},
		{
			title: 'ELECTROCHEMISTRY', model: 'EI-pH-800', badge: 'BEST SELLER',
			name: 'Benchtop pH Meter',
			legend: 'ELECTROCHEMISTRY · pH · CONDUCTIVITY · TDS · 0–14 PH',
			rows: [
				['RANGE', '0–14 pH', '0–14 pH · ±1999 mV'],
				['RESOLUTION', '0.01 pH', '0.01 pH'],
				['ACCURACY', '±0.05 pH', '±0.01 pH'],
				['ATC', 'Manual', 'Automatic, 0–100 °C probe'],
				['ELECTRODE', 'Glass, separate ref', 'Combined, gel-filled, stand incl.'],
			]
		},
		{
			title: 'PHARMACEUTICAL', model: 'EI-DT-500', badge: 'USP <711>',
			name: 'Dissolution Test Apparatus',
			legend: 'PHARMACEUTICAL · DISSOLUTION · FRIABILITY · MOISTURE',
			rows: [
				['TEST', 'Dissolution USP <711>', 'Dissolution USP <711> + friability'],
				['VESSELS', '6-station', '6-station, water bath 28 °C'],
				['SPEED', '25–200 rpm', '25–250 rpm, ±1 rpm'],
				['SAMPLING', 'Manual withdrawal', 'Auto-sampler, 8-channel'],
				['COMPLIANCE', '21 CFR Part 11 ready', '21 CFR Part 11 + audit trail'],
			]
		},
		{
			title: 'BLOOD TESTING', model: 'EI-CC-300', badge: 'CLINICAL',
			name: 'Digital Colony Counter',
			legend: 'BLOOD TESTING · COLONY COUNTER · HAEMOGLOBIN',
			rows: [
				['ASSAY', 'Colony count (manual)', 'Colony count + haemoglobin'],
				['GRID', 'Quebec, glass', 'LED-backlit, etched Quebec'],
				['MAGNIFICATION', '10× loupe', '10× loupe + digital capture'],
				['COUNTS', 'Manual tally', 'Auto-zone, 2000 colonies'],
				['REPORT', 'Paper log', 'CSV / LIMS export'],
			]
		},
		{
			title: 'FLAME PHOTOMETERS', model: 'EI-FP-700', badge: '4-ELEMENT',
			name: 'Digital Flame Photometer',
			legend: 'FLAME PHOTOMETERS · SODIUM · POTASSIUM · CALCIUM',
			rows: [
				['ELEMENTS', 'Na, K', 'Na, K, Ca, Li (4 filters)'],
				['READOUT', 'Galvanometer', 'Digital, direct ppm'],
				['SENSITIVITY', '0.5 ppm Na', '0.1 ppm Na · 0.5 ppm K'],
				['FLAME', 'LPG, manual air', 'LPG, compressor, safety cut-off'],
				['CALIBRATION', 'Single-point', 'Multi-point auto-dilution'],
			]
		},
		{
			title: 'MELTING POINT', model: 'EI-MP-96', badge: '450 °C',
			name: 'Digital Melting Point Apparatus',
			legend: 'MELTING POINT · DIGITAL · CAPILLARY · TO 450 °C',
			rows: [
				['RANGE', 'Ambient–400 °C', 'Ambient–450 °C'],
				['ACCURACY', '±2 °C', '±1 °C'],
				['SAMPLES', '1 capillary', '3 capillaries, simultaneous'],
				['VIEWING', 'External, manual', 'Magnified window + video out'],
				['RECORD', 'Observer notes', 'Auto-capture, PDF report'],
			]
		},
		{
			title: 'MOISTURE TITRATORS', model: 'EI-MT-400', badge: '10 PPM',
			name: 'Karl Fischer Moisture Titrator',
			legend: 'MOISTURE TITRATORS · KARL FISCHER · VOLUMETRIC',
			rows: [
				['METHOD', 'Karl Fischer volumetric', 'Karl Fischer volumetric'],
				['RANGE', '0.01–100% water', '10 ppm–100% water'],
				['ENDPOINT', 'Manual colour', 'Automatic, dual platinum pin'],
				['THROUGHPUT', '~3 min / sample', '~60 s / sample'],
				['TRACEABILITY', 'Reagent lot', 'Reagent lot + ISO 17025 cert'],
			]
		}
	];

	// Real product photography for the family being viewed (from inline WP map, falls back to silhouettes).
	function familyImage(title) {
		const map = window.eiFamilyImages || {};
		const t = (title || '').toLowerCase();
		const key = Object.keys(map).find(k => t.includes(k) || k.includes(t));
		return key ? map[key] : null;
	}

	bands.forEach((band, i) => {
		band.addEventListener('click', () => {
			bands.forEach(b => b.setAttribute('aria-selected', 'false'));
			band.setAttribute('aria-selected', 'true');
			if (bandReadout) bandReadout.textContent = specimens[i].legend;
			if (specimenHost) {
				const s = specimens[i];
				const rows = s.rows.map(r => `
					<div class="cmp-row">
						<span class="cmp-label">${r[0]}</span>
						<span class="cmp-ref">${r[1]}</span>
						<span class="cmp-samp">${r[2]}</span>
					</div>`).join('');
				const famImg = familyImage(s.title);
				const imgHtml = famImg
					? `<img class="spec-img" src="${famImg}" alt="${s.name}" loading="lazy" decoding="async"/>`
					: `<div class="spec-img">${silhouettes[i]}</div>`;
				specimenHost.innerHTML = `
					<div class="specimen">
						<div class="cmp-head">
							<span></span>
							<span class="cmp-col">REFERENCE</span>
							<span class="cmp-col cmp-col-ei">EI INSTRUMENT</span>
						</div>
						<div class="spec-main">
							${imgHtml}
							<div class="spec-id">
								<div class="spec-model">
									<span class="model-no">${s.model}</span>
									<span class="model-badge">${s.badge}</span>
								</div>
								<div class="cmp-name">${s.name}</div>
							</div>
						</div>
						<div class="cmp-body">${rows}</div>
					</div>
				`;
			}
		});
	});
	if (bands.length) bands[0].click();
})();

/* --- contact form: native REST submit, no page navigation --- */
(function () {
	const form = document.getElementById('ei-contact-form');
	if (!form) return;

	const status = form.querySelector('.ei-form-status');
	const btn = form.querySelector('button[type="submit"]');
	const endpoint = form.action || (window.eiContact && window.eiContact.endpoint);

	if (!endpoint) {
		status.textContent = 'Form misconfigured — contact us directly.';
		return;
	}

	const validate = () => {
		let ok = true;
		form.querySelectorAll('[required]').forEach((field) => {
			const email = field.type === 'email';
			const value = field.value.trim();
			const isEmail = email ? /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) : value !== '';
			if (!isEmail) {
				ok = false;
				field.setAttribute('aria-invalid', 'true');
			} else {
				field.removeAttribute('aria-invalid');
			}
		});
		return ok;
	};

	const show = (kind, text) => {
		status.className = 'ei-form-status ' + kind;
		status.textContent = text;
	};

	form.addEventListener('submit', async (ev) => {
		ev.preventDefault();
		if (!validate()) {
			show('error', 'Please fill in the required fields.');
			const first = form.querySelector('[aria-invalid="true"]');
			if (first) first.focus();
			return;
		}

		btn.disabled = true;
		btn.textContent = 'Sending…';
		show('', '');

		const data = {
			name: form.elements.name.value.trim(),
			email: form.elements.email.value.trim(),
			phone: form.elements.phone.value.trim(),
			subject: form.elements.subject.value.trim(),
			message: form.elements.message.value.trim(),
			website: '', // honeypot — never filled by humans
		};

		const nonce = (window.eiContact && window.eiContact.nonce) || '';
		const headers = { 'Content-Type': 'application/json' };
		if (nonce) headers['X-EI-Nonce'] = nonce;
		data._ei_nonce = nonce;

		try {
			const res = await fetch(endpoint, {
				method: 'POST',
				headers,
				body: JSON.stringify(data),
			});
			const payload = await res.json().catch(() => ({}));

			if (res.ok && payload.success) {
				btn.textContent = 'Sent';
				btn.disabled = false;
				show('success', payload.message || 'Message sent. We will reply shortly.');
				form.reset();
			} else {
				btn.textContent = 'Send Message';
				btn.disabled = false;
				show('error', payload.message || 'Could not send — please try again.');
			}
		} catch (err) {
			btn.textContent = 'Send Message';
			btn.disabled = false;
			show('error', 'Network error — please email us directly.');
		}
	});
})();

/* --- live trace canvas (sheet 04) --- */
(function () {
	const canvas = document.getElementById('scopeCanvas');
	if (!canvas || !canvas.getContext) return;
	const ctx = canvas.getContext('2d');
	const dpr = window.devicePixelRatio || 1;
	let w, h, noise = [];

	function size() {
		const box = canvas.parentElement.getBoundingClientRect();
		w = Math.max(280, Math.round(box.width - 2));
		h = Math.round(w * 0.25);
		canvas.width = w * dpr; canvas.height = h * dpr;
		canvas.style.width = w + 'px'; canvas.style.height = h + 'px';
		ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
		noise = Array.from({ length: w + 1 }, () => Math.random() * 2 - 1);
	}

	function trace(phase) {
		ctx.clearRect(0, 0, w, h);
		ctx.strokeStyle = 'rgba(28, 25, 23, 0.07)'; ctx.lineWidth = 1;
		for (let gx = 0; gx <= w; gx += 60) { ctx.beginPath(); ctx.moveTo(gx, 0); ctx.lineTo(gx, h); ctx.stroke(); }
		for (let gy = 0; gy <= h; gy += 40) { ctx.beginPath(); ctx.moveTo(0, gy); ctx.lineTo(w, gy); ctx.stroke(); }
		ctx.strokeStyle = 'rgba(174, 16, 16, 0.9)'; ctx.lineWidth = 2;
		ctx.beginPath();
		const cy = h / 2, amp = h * 0.22;
		for (let x = 0; x <= w; x++) {
			const y = cy + Math.sin((x + phase * 28) * 0.022) * amp * (0.55 + 0.45 * Math.sin(x * 0.003)) + noise[x] * 2.5;
			if (x === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
		}
		ctx.stroke();
	}

	size();
	const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (reduced) { trace(0); return; }
	let phase = 0;
	(function animate() { phase += 0.055; trace(phase); requestAnimationFrame(animate); })();
})();
