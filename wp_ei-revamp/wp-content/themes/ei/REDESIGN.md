# Electronics India Redesign Plan — Light "Laboratory Datasheet"

## Open decisions (locked)

1. `/products/` root archive — existing `/products/` WP page already serves; no new `archive-product.php` template needed.
2. Mobile nav at ≤767px — wrapped list with guaranteed ≥44px tap targets; drawer/toggle decision not taken.
3. Keep `ei-instruments.mp4` anywhere — recommended: skip hero video entirely; no factory block added.

## Design system tokens (light laboratory datasheet)

```
--color-background: #FAFAF9   warm paper ground
--color-foreground: #1C1917   ink
--color-secondary: #44403C    stone-600 secondary text
--color-surface: #FFFFFF      cards
--color-surface-2: #F5F5F4    alternate section tint
--color-line: #E7E5E4         hairline border
--color-accent: #AE1010       EI red — CTA/links/active, ~7.3:1 on white
--color-accent-hover: #C21515
--color-on-accent: #FFFFFF
--color-muted-ink: #44403C    body text (≥4.5:1 vs ground)
--color-focus-ring: #AE1010
--color-destructive: #DC2626

--type-heading: 'IBM Plex Sans', system-ui, sans-serif   600/700
--type-body: 'IBM Plex Sans', system-ui, sans-serif      400, line-height 1.55
--type-mono: 'IBM Plex Mono', ui-monospace, monospace    model numbers, spec labels/values, eyebrows
--radius: 0px (hairline Swiss aesthetic; cards 0-4px only)
--space: 4px scale; rows separated by hairlines, not shadows
--reveal: 350-450ms, staggered, reduced-motion safe
```

theme.json palette aligned to slugs above; back-compat aliases (`--color-primary`, `--color-card`, etc.) map to new values so legacy inline usage does not break mid-transition.

## Fonts (self-hosted, woff2)

Replaced Gambarino/GeneralSans with IBM Plex Sans (400/600) + IBM Plex Mono (400/500) woff2 (Latin-1 subset from jsDelivr). Added `@font-face` blocks in `style.css`. Updated `theme.json` fontFamilies heading/body. Ceiling: font-swap via `font-display: swap`; never add Google Fonts @import (zero third-party requests at runtime).

## Per-template plan (settled)

### `front-page.php` — 5 sections

1. **Hero** — light typographic hero: `H1` claim ("Precision Instruments"), ISO certified eyebrow, established/location + instrument-count fact line, one sentence, `btn--primary` "Browse the catalogue" + `ei-textlink` "Get a quote", framed product photo card beside/right at ≥1024px, stacked below on mobile. No video background (dark room removed).
2. **Proof strip** — static row: ISO badge, "Est. 1983 · Parwanoo", "90+ instruments", "7 families". Static logos/stat cards only; no motion.
3. **Instrument families** — numbered term rows with real product-line counts (existing loop kept), `--d` stagger, full-height tap targets (≥44px).
4. **Featured instruments** — 4 cards via `ei_featured_products()`, white cards, hairline border, mono model number, subtle hover lift (150-300ms).
5. **Closing CTA band** — stone-tint band (`surface-2`), sentence + primary button + phone link.

### `header.php`

- Light header: white bg, hairline bottom border (`color-line`), topbar stone text + phone/mail.
- Navigation: mega menu unchanged structurally; add focus-visible ring on `ei-mega` anchors; `aria-expanded` kept.
- CTA: "Get a Quote" outline button (red text on white, hairline border), plus phone/mail in topbar.

### `footer.php`

- Dark stone footer (`#1C1917`) per decision; logo, categories, quick links, contact, bottom strip; keep red accent minimal (social/link hover only if added).

### `single-product.php`

- Two-column sticky gallery + info panel (kept), now light: white media area, mono model badge, `H1`, CTA buttons (Download brochure outline, Request a quote primary), specs table hairlined with mono row labels, zebra rows optional.

### `taxonomy-products.php`

- Term hero (hairline-bottomed), children grid ("Product lines" — `ei-cat-card` white cards), product grid (`ei-grid`), pagination.
- No inline `style=` on cards; moved to CSS classes (`ei-term-desc` for description paragraph).

### Missing surface

- `/products/` archive root — satisfied by existing `/products/` WP page; no `archive-product.php` template required.

### Other templates

- `index.php`, `search.php`, `page.php`, `404.php` — shared `ei-page-head` (hairline bottom) + `ei-section` light containers; `page.php` contact form light fields with visible labels and `.ei-form-status` live region; 404 keeps two primary actions.

### Motion

Standard stagger per design system: 350-450ms, `back.out(1.4)`-family easing, grid-aware stagger for `.ei-grid` items, `prefers-reduced-motion` → instant final state. No scroll-scrub/parallax. Delete `video` autoplay from hero.

## Fixes & hardening

1. **`ei.js` reveal bug**: remove `DOMContentLoaded` block that marks every `.reveal` visible immediately; IO observer only, `rootMargin: '-120px'`, threshold 0.1; `prefers-reduced-motion` skips animation and renders final state immediately (already partially handled via CSS, keep both).
2. **Mobile menu**: replace wrapped-list fallback at ≤767px with a plain nested list, guaranteed every link ≥44px tap target — decision taken: wrapped list.
3. **Contact REST**: add nonce (`_ei_nonce` param + `X-EI-Nonce` header) or switch route to `permission_callback` requiring logged-in? — keep public but add honeypot field (`website`) + rate-limit-ish nonce; validate email with `is_email()`, return 400 on invalid; honeypot filled → fake success 200 without sending mail.
4. **Remove stale preload** of `bg-title7.jpg` in `functions.php`; replaced with dynamic hero image preload (newest product featured, eager + fetchpriority high + width/height attrs).
5. **Inline styles**: remove `style=` on term description, 404 section, and other inline presentational attributes → CSS classes (`ei-term-desc`, `.ei-404`).
6. **Focus-visible**: add `.ei-focus-visible`/`:focus-visible` ring styles (3-4px, accent) for keyboard nav (a11y priority 1).
7. **Images**: keep `loading="lazy"` on below-fold; add explicit `width`/`height` attrs to prevent CLS; hero image `eager` only.
8. **Contrast pass**: verify all text ≥4.5:1 vs ground (stone-600 `#44403C` on `#FAFAF9` ≈ 5:1 OK; verify), red `#AE1010` on white ≈7.3:1 OK.
9. **Accessibility**: skip link exists (keep), ARIA labels on nav kept, no emoji icons (already SVG-only), cursor-pointer on clickables, hover transitions 150-300ms.

## Rollout & QA (repo bar)

Run sequentially on `wp_ei-revamp` (port 8209):

1. Token swap + font files → `style.css` `:root` + `theme.json` alignment. Done.
2. Header/footer/shell re-skin. Done.
3. Front-page rebuild (sections 1-5). Done.
4. Catalog surfaces + product page. Done.
5. Legacy pages + contact form hardening. Done.
6. JS/a11y fixes. Done.
7. QA: responsive checkpoints 375/768/1024/1440, axe a11y pass, LCP (preload LCP image), no horizontal scroll, JSON output unchanged where used (`--json` routes unaffected by visual changes), `./matrix check ei-revamp` phpcs clean.

## Baseline captures

- `home-desktop.png`, `home-mobile.png`, `home-tablet.png` — updated after front-page rebuild.
- `product-page.png`, `404.png` — updated after template changes.
- `contact-form.png` — unchanged data flow.

## Ponytail ceiling

Swap to light lab datasheet; keep all existing WordPress data flows, ACF fields, REST contact route, and helper signatures unchanged. Add features (wrapped nav, archive root via existing page, contact nonce/honeypot) only if confirmed above; do not add shop/cart, filters, or CMS sections beyond what exists.