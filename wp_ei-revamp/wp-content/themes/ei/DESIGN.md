# DESIGN.md — 10K Agency · Warm Editorial Light

Design contract for the WordPress theme rebuild. Version 7.0.0 direction.
Research basis: Awwwards light-mode winners (Malvah, BASIC/DEPT, Studio Brot, Carles Faus Arquitectura), luxury instrument manufacturers (Mettler Toledo, Anton Paar, Sartorius, Leica Geosystems), ui-ux-pro-max styles `exaggerated-minimalism`, `editorial-grid-magazine`, `kinetic-typography` (light-supported), `brutalism` (light).

---

## 1. Brand

- **Name**: 10K Agency (everywhere — nav, footer, titles, colophon). No Electronics India remnants.
- **Voice**: Precise, declarative, engineered. Short sentences. Measured claims.
- **Positioning**: Precision instruments, engineered and documented in-house. Clients are design agencies and procurement teams; the site must read as a $10K studio build, not a parts catalog.
- **One-line thesis**: *Precision, measured.* Used as the hero statement.

## 2. Design principles

1. **Light wins.** Warm paper ground everywhere. No dark sections, no black boxes, no neon.
2. **Type is the hero.** Oversized grotesque caps carry the page; serif italic carries the emotion; mono carries the data.
3. **Asymmetry over repetition.** No uniform 3-column card rows. Every section has a distinct composition.
4. **Hairlines, not shadows.** Structure comes from 1px rules and whitespace, not drop shadows and boxes.
5. **One accent.** Vermillion `#E53E3E` — used for emphasis, indices, CTAs, hover states. Never as background wash.
6. **Photos on paper.** Product cutouts sit on clean white sheets inside hairline-framed panels. White photo backgrounds vanish into the white sheet.
7. **Motion is restraint.** Reveals ≤ 700ms, ≤ 24px travel. Nothing bounces, nothing spins, no marquees.

## 3. Color tokens

| Token | Value | Role |
|---|---|---|
| `--paper` | `#F9F8F6` | Page ground |
| `--sheet` | `#FFFFFF` | Product panels, cards, raised surfaces |
| `--ink` | `#111215` | Headings, body text |
| `--ink-soft` | `#4A4A46` | Secondary text, descriptions |
| `--ink-mute` | `#8A887F` | Meta labels, disabled, timestamps |
| `--rule` | `#E2DFD8` | Hairline borders, table rules |
| `--rule-strong` | `#111215` | Heavy rules (section dividers, table header) |
| `--vermilion` | `#E53E3E` | Accent: indices, links, CTAs, hover |
| `--vermilion-ink` | `#B72727` | Accent hover / pressed (darker, AA on paper) |

Rules:
- Background is always `--paper` or `--sheet`. Never anything darker than `--rule`.
- Text on paper must be `--ink` or `--ink-soft`. `--ink-mute` only ≥ 14px mono labels.
- Vermilion on paper: large text / icons / 1px rules only for `#E53E3E`; body-size text uses `--vermilion-ink`.
- No gradients. No blur. No grain overlay. No mix-blend tricks.

## 4. Typography

Self-hosted only. Zero third-party runtime requests.

| Face | Weights | Role |
|---|---|---|
| **Archivo** (grotesque, 600/700 if available; else Anton fallback) | 600 | Display headlines, nav brand |
| **Anton** | 400 | Hero poster caps (used sparingly: hero + section titles) |
| **Playfair Display italic** *(new — download once, self-host WOFF2)* | 400 italic | Editorial accent words inside headlines |
| **IBM Plex Mono** | 400/500 | Eyebrows, labels, table data, buttons, meta |
| **Inter** | 400/600 | Body copy, long-form |

Scale:

```
--display-xl: clamp(4rem, 10vw, 12rem)   /* hero, Anton, uppercase, lh 0.9, ls -0.04em */
--display-l:  clamp(2.8rem, 6vw, 5.5rem) /* section titles, Anton uppercase, lh 1.0 */
--display-m:  clamp(1.8rem, 3.5vw, 3rem) /* card titles, Archivo 600 */
--serif-accent: clamp(2.2rem, 5vw, 6rem) /* Playfair italic accent words, lh 1.05 */
--body: 1rem/1.75 Inter
--body-l: clamp(1.1rem, 1rem + 0.8vw, 1.35rem) /* lede paragraphs */
--mono-label: 0.65rem/1 IBM Plex Mono 500, uppercase, ls 0.2em
--mono-micro: 0.55rem/1 IBM Plex Mono 400, uppercase, ls 0.25em (meta, coordinates)
```

Hero headline pattern:

```
PRECISION,            ← Anton, --display-xl, --ink
measured.             ← Playfair Display italic, --serif-accent, --vermilion-ink
```

Accent word sits on its own line, baseline-aligned tight against the caps block.

## 5. Layout system

- Container: `max-width: 1360px`, side padding `clamp(20px, 4vw, 64px)`.
- Baseline rule: every major section opens with a full-width 1px `--rule` top border + a mono section index (`01 — MANIFESTO`) in `--ink-mute`.
- Grid: 12-column CSS grid. Sections use asymmetric splits (7/5, 5/7, 8/4). Never equal thirds for siblings.
- Section spacing: `clamp(96px, 14vh, 160px)` vertical.
- Mobile (<768px): single column, same order, same hairlines.

## 6. Page architecture — front page

Scroll order, top to bottom. Each block is a numbered "sheet".

### 6.1 Header (all pages)
- Sticky, `--paper` background, 1px `--rule` bottom border. No blur, no pill.
- Left: `10K` in Archivo 700 + `Agency` in Playfair italic — one line, `--ink`.
- Center/right: mono links, `--ink-mute`, hover → `--ink` with 2px vermilion underline slide.
- Links: Manifesto `/#s1` · Specimens `/#s2` · Index `/products/` · Quality `/quality-control/` · R&D `/research-and-development/` · About `/about-us/` · Contact `/contact/`.
- Mobile: hamburger (2 lines, 24px), full-screen `--paper` overlay menu, Archivo caps links, staggered 40ms reveal.
- Right edge of header: mono micro label with live local time + `IST` (JS clock, no request).

### 6.2 Sheet 01 — Manifesto (hero)
- 100svh minus header. Left-aligned.
- Eyebrow: `EST. 1983 — PRECISION INSTRUMENT WORKS` (mono micro, `--ink-mute`).
- Headline: `PRECISION,` / `measured.` per §4 pattern.
- Lede: `--body-l`, `--ink-soft`, max 55ch: "10K Agency designs, manufactures and exports analytical, scientific, laboratory and pharmaceutical instruments — built, tested and documented in-house."
- Actions: `Request quotation` (vermilion solid button) + `Explore specimens` (hairline ghost button).
- Bottom hairline strip (full-width, 1px top rule): 4 mono stats separated by hairlines — `ISO 9001 CERTIFIED` · `90+ INSTRUMENTS IN PRODUCTION` · `150+ COUNTRIES SERVED` · `41 YEARS OF MANUFACTURE`. Equal-width cells, borders between.
- Decoration: none. Whitespace is the decoration.

### 6.3 Sheet 02 — Specimens (featured showcase)
Section index: `02 — SPECIMENS`. Title: `FEATURED INSTRUMENTS` (Anton `--display-l`).

Composition — asymmetric editorial grid, 12 cols:
- **Feature specimen** (cols 1–7, spans 2 rows): largest image. White `--sheet` panel, 1px `--rule` border, image contained with 32px padding. Below image: hairline, then mono model row (`MODEL EI-2375` vermilion + `UV-VIS SPECTROPHOTOMETER` ink), title (Archivo `--display-m`), 2-line excerpt, `View datasheet →` mono link.
- **Secondary specimens** (cols 8–12, rows 1–2): two stacked, same panel anatomy, smaller.
- **Index strip** (cols 1–12, below): horizontal band of the remaining featured products as text-only table rows (no images).

Hover: panel border → `--ink`; image scales 1.02; model number → `--vermilion`. No shadow lift.

Fallback: if a featured product has no thumbnail, panel renders the mono wordmark block `10K / NO SPECIMEN IMAGE` on `--sheet` — never a broken img.

### 6.4 Sheet 03 — Index (catalog table)
Section index: `03 — INDEX`. Title: `INSTRUMENT INDEX`.

Editorial catalog table, full container width:
- Columns: `NO.` (mono, 2-digit) · `MODEL` (mono, vermilion on hover) · `INSTRUMENT` (Archivo 600) · `FAMILY` (mono micro) · `→` (arrow link).
- Header row: mono micro labels, 2px `--rule-strong` bottom border.
- Rows: 1px `--rule` separators, 20px vertical padding. Hover: row background `--sheet`, model number vermilion, arrow translates 6px.
- Source: 12 most recent products (paged). `View full catalogue →` mono link below → `/products/`.
- Mobile: hide `FAMILY` column; rows stack model + name.

### 6.5 Sheet 04 — Narrative (statement band)
Section index: `04 — METHOD`.

- Giant statement, Archivo 600 / Anton mix, `clamp(2.4rem, 6vw, 5rem)`, left 8 cols:
  `Designed in-house.` (ink) newline `Tested to tolerance.` (ink) newline `Documented for life.` (Playfair italic, vermilion-ink).
- Right 4 cols (bottom-aligned): short body paragraph, `--ink-soft`, 3 sentences about QA + documentation.
- No image, no card. Type + whitespace only.

### 6.6 Sheet 05 — Colophon (contact close)
Section index: `05 — COLOPHON`.

- Huge wordmark: `INSTRUMENT UP.` — Anton `--display-xl`, ink; final period vermilion.
- Row: left = contact stack (mono: phone, email, WhatsApp, address — plain text with `<br>`s rendered as real breaks, no escaped HTML); right = `Request quotation` vermilion button → `/contact/`.
- This is a section of the front page (id `s5`), visually echoed by the site footer.

## 7. Footer (all pages)

`--paper`, 2px `--rule-strong` top border.

- Grid 3 zones: brand block (wordmark + one-line dek) · nav column (same links as header) · contact column (phone / email / WhatsApp / address, mono labels + Inter values).
- Address renders real line breaks (`<br>` in PHP template, not inside `esc_html__`).
- Bottom bar: 1px `--rule` top border, mono micro: `© {Y} 10K AGENCY` · `PRECISION, MEASURED` · back-to-top link `↑ TOP`.

## 8. Inner templates

### 8.1 single-product.php — Specimen datasheet
- Breadcrumb strip: mono micro `INDEX / {FAMILY} / {MODEL}` under a 1px rule.
- Head: eyebrow `SPECIMEN DATASHEET`; title = model (mono vermilion) + product name (Anton `--display-l`); family line mono; actions right (brochure button if exists + ghost quotation button).
- Body grid 7/5: left = `--sheet` panel, image contained, padding 32px, hairline border; right = Features (checkless list, hairline-separated rows, 4px vermilion tick bar per item) + Specifications table (§10 style).
- Description below, max 65ch.
- Related strip: 3 sibling products as text rows (same anatomy as index rows).

### 8.2 taxonomy-products.php — Family archive
- Head: eyebrow `FAMILY`; term name Anton `--display-l`; description 55ch.
- Child-lines TOC: full-width rows — count (Archivo 600, large, `--rule` colored until hover → ink), line name, arrow. 1px rules between.
- Products: same asymmetric showcase as Sheet 02 (feature + stacked) when ≥3 with images; else index table.
- Pagination: mono `PREV / NEXT` with hairline box.

### 8.3 page.php — Legacy pages + contact
- Eyebrow: page slug uppercased. Title: page title Anton `--display-l`.
- Body max 72ch, Inter, `--ink-soft`.
- Contact page: CF7 shortcode rendered inside a `--sheet` panel with hairline border and CF7-class restyling (§10). Native form fallback only if CF7 inactive.
- CF7 markup targets: `.wpcf7` panel, inputs `background: --sheet; border: 1px --rule; focus: border --vermilion + 3px rgba(229,62,62,0.12) ring`, labels mono micro, submit = vermilion button. Success/error messages: mono, vermilion-ink / ink.

### 8.4 404.php
- Anton `--display-xl` `404.` + Playfair italic `lost signal.` + mono link `← Return to index`.

## 9. Motion & interaction

- Global easing: `cubic-bezier(0.22, 1, 0.36, 1)`. Durations: micro 180ms, standard 280ms, reveal 600ms.
- Scroll reveals: opacity 0→1 + translateY 20px→0, IntersectionObserver, once, rootMargin 100px. **Content visible without JS and by default** (the `.reveal` blocking-opacity bug from v6 must not recur: gate hidden state behind `documentElement.js` class added by script, and never on `prefers-reduced-motion: reduce`).
- Link underline slides: scaleX origin right→left, 240ms.
- Buttons: solid → background `--vermilion-ink` on hover; ghost → border `--ink`, background `--sheet`.
- Image hover: scale 1.02, 600ms.
- Header clock: updates every second, `Intl.DateTimeFormat` local time, no request.
- Marquees, parallax, cursors, canvas toys: **none**.
- `prefers-reduced-motion: reduce`: all reveals shown instantly, image scale off, smooth scroll off.

## 10. Components

### Buttons
- Base: mono 500, `--mono-label`, padding 16px 32px, min-height 52px, radius 2px, inline-flex.
- `btn-solid`: bg `--vermilion`, text `#FFFFFF`; hover bg `--vermilion-ink`.
- `btn-ghost`: transparent, 1px `--rule-strong` border, `--ink` text; hover bg `--sheet`, border `--ink`.
- `btn-text`: mono link with `→`, hover arrow +6px, color `--vermilion-ink`.

### Panels (specimen frames)
- `background: --sheet; border: 1px solid --rule; border-radius: 2px;`
- Media area: `padding: 32px; background: --sheet;` image `object-fit: contain`, max 90% box.
- Caption area: 1px `--rule` top border, padding 20px 24px.

### Tables (index + specs)
- Header: mono micro, 2px `--rule-strong` bottom.
- Rows: 1px `--rule`; hover bg `--sheet`.
- Specs table: first col mono micro `--ink-mute` 35% width, second col Inter `--ink`.

### Forms
- Inputs: `--sheet` bg, 1px `--rule`, 2px radius, padding 14px 16px, Inter 0.95rem.
- Focus: border `--vermilion`, ring `0 0 0 3px rgba(229,62,62,0.12)`. `:focus-visible` global 2px `--vermilion` outline offset 3px.
- Labels: mono micro `--ink-mute`. Errors: `--vermilion-ink`.

## 11. Accessibility

- Contrast: `--ink` on `--paper` ≈ 17:1; `--ink-soft` ≥ 7:1; `--ink-mute` ≥ 4.5:1 at label sizes (verify with tool after build). Vermilion `#E53E3E` on paper ≈ 4.0:1 — body-size vermilion text uses `--vermilion-ink` (`#B72727`, ≥ 5:1).
- All interactive elements: visible focus ring, keyboard reachable, ≥ 44px touch target.
- Images: meaningful alt from product title; decorative none used.
- Heading order: one `h1` per page, sections `h2`, cards `h3`.
- Skip link: paper bg, ink text, vermilion border.
- Reduced motion honored (§9).

## 12. Performance & technical constraints

- WordPress classic theme, PHP templates only. No build step.
- **Zero third-party runtime requests**: fonts local (`assets/fonts/`), no CDN, no analytics, no external images. `font-display: swap`.
- Fonts: keep Anton, Archivo, Inter, IBM Plex Mono; **add Playfair Display italic (WOFF2, ~30–60KB) downloaded once at build time and committed**.
- Images: WordPress generated sizes; `loading="lazy"` allowed on below-fold images only (feature specimen + above-fold cards load eager to avoid blank panels in screenshots/first paint).
- JS: single `assets/ei.js`, vanilla, no dependencies. IntersectionObserver + clock + mobile nav + CF7 graceful no-op. No jQuery.
- No canvas oscilloscope. Delete scope code and its copy.
- phpcs `WordPress` standard: 0 errors target (warnings ≤ 1 known `slow_db_query` on `show_on_home` meta).
- WP-CLI compatible with matrix tooling; templates escape all output (`esc_html`, `esc_url`, `wp_kses_post`); no raw `<br>` inside `esc_html__()`.

## 13. Build plan (order)

1. Source Playfair Display italic WOFF2 (400 italic; add 700 italic only if needed) → commit to `assets/fonts/`.
2. Rewrite `style.css` from this contract: tokens → base → type → header → sheets 01–05 → panels/table/buttons/forms → inner templates → footer → a11y → responsive (375px pass).
3. Rewrite `header.php` (§6.1) and `footer.php` (§7).
4. Rewrite `front-page.php` (§6.2–6.6) — queries unchanged (show_on_home meta → fallback latest 6).
5. Rewrite `single-product.php` (§8.1), `taxonomy-products.php` (§8.2), `page.php` (§8.3), add `404.php` (§8.4).
6. Rewrite `assets/ei.js`: reveals (JS-gated), clock, mobile nav. Remove scope canvas, remove native-form AJAX (CF7 handles it; keep fallback static submit).
7. `functions.php`: keep enqueue + theme supports; drop CF7 no-op filter.
8. php -l all templates → phpcs → fix to 0 errors.
9. Visual verify at 375px and 1440px (Playwright), check: hero pattern, feature/secondary grid, index table hover, contact CF7 panel, footer address breaks, no dark blocks, fonts loaded, zero external requests (network tab).
10. Commit `feat(ei): Warm Editorial Light rebuild — v7 per DESIGN.md` and push.

## 14. Verification checklist (gate to done)

- [ ] No dark sections anywhere; every background is `--paper`/`--sheet`.
- [ ] Hero renders `PRECISION,` / `measured.` pattern with Playfair italic.
- [ ] Featured grid is asymmetric (7/5) — not uniform cards.
- [ ] Index table renders 12 rows with hover states.
- [ ] All product photos sit on `--sheet` white panels — no halos on gray.
- [ ] Address shows real line breaks; no literal `<br>` text.
- [ ] Mobile 375px: hamburger overlay, stacked sheets, table columns collapse per §6.4.
- [ ] 1440px: container 1360px, whitespace scale per §5.
- [ ] Network: only same-origin requests (fonts, images, CSS, JS).
- [ ] phpcs 0 errors; `php -l` clean on all templates.
- [ ] Reduced-motion: everything visible without animation.
- [ ] Brand: zero "Electronics India" strings in theme (grep clean).
