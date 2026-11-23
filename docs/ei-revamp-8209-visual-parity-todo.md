# EI visual parity — dev `:8209` checklist

**Source of truth:** `wp_ei-revamp` · http://192.168.31.207:8209/  
**Theme contract:** `wp-content/themes/ei/DESIGN.md`  
**Typography audit:** `electronics/tests/typography-dev-live-audit.mjs` → `typography-dev-live-audit.json`  
**Downstream targets after 8209 sign-off:** local `:8207` (`wp_ei`), live https://electronicsindia.co.in/

Goal: finish typography, color, and template parity **on 8209 first**, then export/cutover so local and live match dev—not patch legacy apicona.

---

## Sign-off URLs (8209)

| Page | Path | Priority |
|------|------|----------|
| Home | `/` | P0 |
| About | `/about-us/` | P1 |
| Contact | `/contact/` | P1 |
| Product single | `/product/…/` (pick 3 representatives) | P1 |
| Category / listing | product taxonomy archive | P2 |
| 404 | any bad path | P3 |

Viewports for QA: **375**, **768**, **1024**, **1440**, **1920**.

---

## P0 — Global foundation (8209 theme)

- [x] **Body stack matches DESIGN.md** — Inter 16px / 1.75, `--ink` `#111215`, background `--paper` `#F9F8F6` (aligned in `style.css` 2026-11-03).
  - **Done when:** computed `body` on `/` at 1440px = Inter ~16px, color rgb(17,18,21), line-height ~28px.
- [x] **Self-hosted fonts only** — Anton, Archivo, Inter, IBM Plex Mono, Playfair italic 400; no runtime `fonts.googleapis.com` / `gstatic` on home (verified 2026-11-03).
  - **Done when:** view-source on home has zero Google Fonts `<link>`; Network tab shows no font hosts except same-origin `/wp-content/themes/ei/assets/fonts/`.
- [x] **CSS surface area** — home loads theme CSS + CF7 only (2 stylesheets). No apicona/VC/UberMenu.
  - **Done when:** ≤ 5 stylesheet links on home (excluding block library if unavoidable).
- [x] **Color tokens** — `:root` matches DESIGN.md §3 (`--paper` `#F9F8F6`, `--ink-soft` `#4A4A46`, `--ink-mute` `#8A887F`, `--rule` `#E2DFD8`, `--vermilion-ink` `#B72727`).
  - **Done when:** no body text at `#7C7C7C`-style legacy gray on marketing pages.
- [x] **Hero headline pattern** — “Precision,” Anton display-xl + “measured.” Playfair italic 400, vermilion-ink. Scale clamps match DESIGN.md §4.
  - **Done when:** hero matches DESIGN.md hero pattern at 375 and 1440.

---

## P1 — Chrome & key pages (8209)

### Header / nav

- [ ] **Primary nav** — IBM Plex Mono ~0.65rem, uppercase, letter-spacing ~0.2em; active vs muted `--ink` / `--ink-mute`.
  - **Done when:** top links (Manifesto, Specimens, Products) match audit sample on dev nav at 1440.
- [ ] **Mega / product family row** — Archivo 600, uppercase, tracked (Blood Testing Instruments, etc.).
  - **Done when:** dropdown row typography matches DESIGN.md nav table, not Raleway 14px.

### Home sections

- [ ] **Section titles (h2)** — Anton display-l scale, uppercase, negative tracking (~79px at 1440 where design applies).
  - **Done when:** Featured / Index / Manufacture blocks use Anton, not Inter/Raleway at 24px.
- [ ] **Lede paragraphs** — `.body-l` / Inter with `--ink-soft`.
  - **Done when:** intro copy ≥ 18px at 1440 with soft ink color.
- [ ] **CTAs** — `.btn` IBM Plex Mono, uppercase, ~0.68rem, letter-spacing ~0.2em; solid + ghost variants.
  - **Done when:** “Explore specimens”, “Request quotation” match button spec at 375 and 1440.

### Inner pages

- [ ] **About `/about-us/`** — page title Anton display on paper (dark ink), not banner-white Lato 30px (live legacy pattern).
  - **Done when:** h1 computed font-family Anton (or display class), color `--ink`, size follows clamp scale.
- [ ] **Contact `/contact/`** — same title treatment + form labels mono/body per DESIGN.md; submit uses `.btn`.
  - **Done when:** CF7 labels/inputs/submit visually match theme; no VC shortcode leakage in rendered HTML.
- [x] **Remove VC shortcode text from DOM** — `ei_strip_legacy_builder` unwraps `vc_row` / `vc_column` / `vc_column_text` and drops `vc_gmaps`, `vc_raw_html`, and legacy `heading` / `contactboxphone` tags on render.
  - **Done when:** page source has no visible `[vc_` strings on about/contact.

---

## P2 — Catalogue templates (8209)

- [ ] **Single product** — specs table uses mono for labels/data; headings Archivo/Anton per DESIGN.md product template.
  - **Done when:** 3 sample products signed off at 375 + 1440.
- [ ] **Taxonomy / listing** — `taxonomy-products.php` / listing template typography aligned with home section scale.
  - **Done when:** category index matches Instrument Index visual language.
- [ ] **Product images** — white sheet + hairline frame on `--paper` (DESIGN.md §2.6).
  - **Done when:** no broken uploads on listing grids (backfill if needed).

---

## P3 — QA, contracts, cutover prep

- [ ] **Playwright typography contract** — extend `typography-dev-live-audit.mjs` with fixed selectors for 8209 roles (body, nav, h2 section, btn, page h1) and run in CI or `./matrix check` hook.
  - **Done when:** script passes with 8209 as reference baseline (self-comparison or snapshot file).
- [ ] **Cross-viewport pass** — home + about + contact + 1 product at all sign-off widths.
  - **Done when:** checklist table above signed in PR or issue.
- [ ] **Broken media audit** — fix or exclude known 404 thumbnails on home (see taste-skill reports).
  - **Done when:** zero broken `<img>` on home at 8209.
- [ ] **Export smoke test** — `./matrix export ei-revamp` and import into clean WP; typography unchanged.
  - **Done when:** export bundle documented in modernisation plan phase 8.

---

## After 8209 is green — local & live (not 8209 work, but blocked on above)

- [ ] **Repoint or replace `:8207`** — stop using apicona as daily driver; sync from revamp export or shared docroot strategy.
- [ ] **Live cutover** — deploy `ei` + `ei-core` + content; remove Lato/Raleway Redux enqueue; purge WP Fastest Cache / minify caches.
  - **Done when:** `typography-dev-live-audit.mjs` reports **0** typography drifts for `dev-ei-revamp-vs-live` on home/about/contact at 375 + 1440.

---

## Quick verify commands

```bash
# From electronics repo — dev vs live drift count
cd /Users/barun.tayenjam/Documents/Code/electronics/tests
node typography-dev-live-audit.mjs

# Matrix health
cd /Users/barun.tayenjam/Documents/Code/wordpress-matrix
./matrix check ei-revamp
```

---

## Notes

- **Legacy local `:8207` vs live:** typography already matches on apicona; do not invest in apicona font fixes—only 8209 + cutover.
- **Oct 2026 taste audit** on 8209 referenced Archivo Narrow on old apicona; current truth is **`ei` / 10K Agency** only.

_Last updated: 2026-11-03_
