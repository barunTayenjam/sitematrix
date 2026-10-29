# Electronics India — Site Modernisation Plan

**Site:** `wp_ei-revamp` (local clone of `wp_ei`) · http://192.168.31.207:8209
**Stack:** WordPress 7.1.3 · PHP 8.3 · MySQL 8.0 · Redis
**Status:** Draft for approval — 29 Oct 2026

---

## 1. Why we're doing this

The site is a brochure catalogue for a scientific-instrument manufacturer. It has
**11 pages, 92 products, 35 product categories (7 top-level, 28 sub-categories),
19 client logos, 4 testimonials**.
That content shape does not need a page builder, two mega-menu engines, a slider
engine, or 20 plugins. It currently carries all of them.

### Audit snapshot (measured on the running clone)

| Metric | Now | Target |
|---|---|---|
| Active plugins | 20 (+3 must-use) | 2 (+1 tiny custom core plugin) |
| `wp-content/plugins` size | 118 MB | ≤ 13 MB |
| `wp-content/themes` size | 58 MB | ≤ 3 MB |
| Theme | `apicona` (ThemeForest) + child | `ei` (custom) |
| Stylesheets on home | 25 `<link>` + 13 inline `<style>` | 2 (1 inline critical + 1 main) |
| Scripts on home | 27 `<script src>` | ≤ 3 (deferred) |
| HTML weight (home) | 141 KB | ≤ 60 KB |
| Third-party runtime requests | Google Fonts (2), Google Maps JS, GTM | 0 |
| Database | 126 MB | ≤ 25 MB |
| `wp_comments` | 20,846 rows / 97 MB (spam, comments unused) | 0 |
| Product content | Visual Composer shortcodes in `post_content` (91/92 identical shape) | ACF structured fields |
| Local media | 17 files present, 730 attachments registered (images broken) | 730 files, local, optimised |

### What the audit proved

- **91 of 92 products share one exact structure**: `[vc_row]` → `H3 Features` →
  `<ul>` of features + one `[vc_single_image]` → description paragraphs →
  `H3 Specifications` → `<table class="specification-table-product">` with
  N × 2 `<td>` rows → `pdf_file_upload` meta holding a brochure PDF URL.
  A reliable, testable parser can migrate all of them.
- **Every page** (11/11) and **every portfolio item** (14/14) also carry VC shortcodes.
- The theme itself emits a PHP warning on every WP-CLI call
  (`inc/visual-composer.php:2501`) — the codebase is past maintenance.
- Two mega-menu plugins (`megamenu` + `ubermenu`) and two sitemap plugins
  (`wp-dtree-30` + `wp-sitemap-page`) are installed simultaneously.
- `wordfence` (21 MB) and `wp-migrate-db` (5.7 MB) are installed but **inactive**.
- `pdf-print` alone is 31 MB, superseded by a print stylesheet.
- Media library is empty locally — the clone missed `uploads/`. The live host
  (`electronicsindia.co.in`) serves all 730 files over HTTPS.

---

## 2. Target architecture

```
wp-content/
├── plugins/
│   ├── advanced-custom-fields-pro/     # 6.4.0.1, copied from wp_sr, GPL
│   ├── contact-form-7/                 # 6.2.3 — 3 forms kept
│   └── ei-core/                        # ~150 KB, our code, no UI
│       ├── ei-core.php                 # bootstrap
│       ├── post-types.php              # product, testimonial, client
│       ├── taxonomies.php              # products (hierarchical)
│       ├── acf-json/                   # field groups as versioned JSON
│       ├── cli/class-ei-migrate.php    # wp ei migrate-* commands
│       └── migration/class-vc-parser.php
├── themes/
│   └── ei/                             # custom theme, no parent, no build step
└── mu-plugins/                         # keep perf + security hardening only
```

**Why a core plugin instead of putting CPTs in the theme:** content outlives themes.
CPT/taxonomy/field registration, ACF JSON and the migration commands live in
`ei-core`; presentation lives in the theme. Deleting the theme must not orphan 92 products.

**Why no build step:** a brochure site with 5 templates does not need a bundler.
Plain CSS with custom properties + `theme.json` tokens, one small deferred JS file.
This keeps `./matrix check` and the existing PHP tooling meaningful.

### Plugin plan — what is removed and what replaces it

| Remove | Size | Replacement |
|---|---|---|
| `revslider` | 27 MB | Hero section built in theme (ACF `hero` layout: image/video + copy + CTA) |
| `js_composer` | 17 MB | ACF Pro Flexible Content + PHP section partials |
| `pdf-print` | 31 MB | Print stylesheet (`@media print`) + real PDF brochure links per product |
| `printfriendly` | — | Same print stylesheet |
| `wordfence` *(inactive)* | 21 MB | Existing `security-hardening.php` mu-plugin; host/Docker-level protection |
| `wp-migrate-db` *(inactive)* | 5.7 MB | `./matrix export` / `./matrix import` (already in the toolchain) |
| `megamenu` | 3.9 MB | Theme mega menu: native WP menus + CSS grid + ACF category images |
| `ubermenu` | 3.2 MB | Same |
| `wp-fastest-cache` | 3.1 MB | Nginx micro-cache (already configured) + Redis object cache |
| `wp-sitemap-page` | 800 KB | Hand-built `/site-map/` template + core `/wp-sitemap.xml` |
| `categories-images` | 768 KB | ACF image field on the `products` taxonomy |
| `wp-dtree-30` | — | Removed with the sitemap rebuild |
| `breadcrumb-navxt` | — | `ei_breadcrumbs()` in theme (also emits BreadcrumbList JSON-LD) |
| `taxonomy-terms-order` | — | ACF `order` number field + `pre_get_terms` sort |
| `custom-post-type-ui` | — | CPTs registered in `ei-core` |
| `google-maps-widget` | — | Locally hosted static map image + accessible address block (no Google Maps JS) |
| `envato-market` | — | Marketplace licensing for a theme we are deleting |
| `cf-post-formats` | — | No blog on this site |
| `list-custom-taxonomy-widget` | — | Custom category menu partial |
| `smtp` *(inactive)* | — | Mailpit locally; SMTP config moves to the host when live |
| `wps-hide-login` *(inactive)* | — | Not reinstalled; login hardening via nginx/Docker in production |

**Net: 20 plugins → 2 third-party + 1 custom core plugin. 118 MB → ~13 MB.**

---

## 3. Content model (ACF Pro)

### Post types

| CPT | Slug | Count | Notes |
|---|---|---|---|
| `product` | `/product/{slug}/` | 92 | **URLs preserved** — already indexed, no redirects needed |
| `testimonial` | `/testimonial/` | 4 | Not publicly browsable — used by home page sections only |
| `client` | `/client/` | 19 | Logos only, not publicly browsable |
| `portfolio` | — | 14 | **Dropped** — verified: not in any menu, no page links to it, only 2 prose mentions of the word "portfolio" on Clients/Home. See open decision D2. |

### Taxonomy

| Taxonomy | Slug | Terms | Notes |
|---|---|---|---|
| `products` | `/products/{term}/` | 35 (7 top, 28 sub) | Hierarchical, 2 levels |

Existing URL pair `/products/{category}/` (taxonomy) and `/product/{model}/` (CPT)
is confusing but **preserved** — changing it would cost every indexed product URL
for no user benefit.

### Field groups

**1. Product details** (`product`)

| Field | Type | Source in old content |
|---|---|---|
| `model_number` | text | Parsed from title suffix (`– 1918 & 1916`, `- 2760`) |
| `short_description` | textarea | `post_excerpt` |
| `description` | wysiwyg | VC `vc_column_text` paragraphs after the features row |
| `features` | repeater → `feature` (text) | `<ul><li>` in the Features row |
| `specifications` | repeater → `label`, `value` | `<table class="specification-table-product">` rows |
| `brochure_pdf` | file | `pdf_file_upload` meta (URL → attachment ID) |
| `gallery` | gallery | `_thumbnail_id` + `[vc_single_image]` image |
| `show_on_home` | true/false | `show_on_home` meta |
| `home_order` | number | `order_product_home` meta |

**2. Product category** (`products` term)

`category_image`, `intro` (wysiwyg), `icon` (SVG), `order` (number).
Replaces `categories-images` + `taxonomy-terms-order`.

**3. Page sections** — Flexible Content on `page`

Layouts: `hero`, `category_grid`, `featured_products`, `text_media`, `stat_strip`,
`client_logos`, `testimonials`, `market_cover`, `cta_banner`, `contact_panel`,
`legal_content`. Each maps to a partial in `themes/ei/sections/`.

**4. Site options** (ACF options page)

Logo (light/dark), top-bar text, phone, email, address, certification badges,
social links, footer columns, "Get in touch" panel copy.
Replaces the 178-key Redux `$apicona` array — of which only ~15 keys carry real content.

**5. Testimonial** — `person_name`, `role_company`, `quote`, `rating`.
**6. Client** — `logo`, `name`, `url`.

ACF field groups ship as **JSON in `ei-core/acf-json/`** so the model is versioned,
reviewable, and reproducible on any environment.

---

## 4. Theme: `ei`

**Direction:** evolve the existing brand — keep the red (`#ae1010`, 7.3:1 on white,
passes AA) and the Electronics India logo; modernise type, spacing, grid, cards,
nav and mobile behaviour. No new identity — recognition is the site's main asset.

### Design tokens (`theme.json` + CSS custom properties)

```
--ei-red: #ae1010   --ei-red-dark: #8c0d0d   --ei-ink: #14110f
--ei-body: #3b3733  --ei-surface: #ffffff    --ei-surface-alt: #f7f5f2
--ei-border: #e3dfd9  --ei-radius: 6px
space scale 4/8/12/16/24/32/48/64/96 · type scale 0.875 → 3rem (fluid clamp)
```

**Typography:** one self-hosted variable family for UI/body (candidates: Inter
Tight, Manrope) + one condensed display face for product model numbers.
Both downloaded once as `.woff2` (SIL OFL) into `themes/ei/assets/fonts/`,
declared with `font-display: swap` and `size-adjust` fallbacks.
**Replaces Lato + Raleway from Google Fonts.**

**Icons:** inline SVG sprite (`assets/icons.svg`) with ~20 symbols.
Replaces the Font Awesome bundle and `kwicon` font that Apicona ships.

### Templates

| Template | Contents |
|---|---|
| `front-page.php` | hero · category grid (7 top-level) · featured products (8) · about · client logos · testimonials · market cover · CTA |
| `page.php` | Flexible-content section renderer |
| `single-product.php` | breadcrumb · title + model no. · gallery (CSS-only zoom) · description · features list · specifications table · brochure download · enquiry CTA · related models · prev/next |
| `taxonomy-products.php` | category hero · intro · subcategory cards · model grid |
| `archive-product.php` | full catalogue: A–Z / by-category switch |
| `page-templates/contact.php` | form + address + locally hosted static map |
| `page-templates/site-map.php` | category/product tree (static, no plugin) |
| `page-templates/clients.php` | logo wall |
| `page-templates/dealer.php` | CF7 dealer form + territory copy |
| `search.php`, `searchform.php` | Product search (rename "Doctor's Search" → "Search products") |
| `404.php`, `header.php`, `footer.php` | shell |

### Menu

Native WP menus only. The 22-item primary menu becomes:
`Home · Products (mega: 7 categories + images) · About · Quality Control · R&D · Clients · Contact`
`Footer`: Dealer · Product Categories · Market Cover · Get in Touch — as registered menu locations.

Mega menu is six category cards with ACF images inside a CSS grid, keyboard-operable,
`aria-expanded` state, works without JS.

---

## 5. Migration

### 5.1 Product content — scripted, tested, reversible

`wp ei migrate-products --dry-run --limit=5` → diff preview
`wp ei migrate-products --apply --backup` → writes ACF fields, leaves
`post_content` untouched behind a `_ei_legacy_content` copy for diffing.

Parser rules derived from the real corpus:

1. Extract the first `<ul>` inside the `product_feature_row` → `features[]`.
2. Extract `<table class="specification-table-product">` rows → `specifications[]`
   (2-column; multi-line cells preserved with `<br>`).
3. Every remaining `vc_column_text` paragraph after the features row → `description`.
4. `[vc_single_image image="ID"]` → gallery item; keep `_thumbnail_id` as primary.
5. `pdf_file_upload` URL → resolve to attachment ID → `brochure_pdf`; if the file is
   missing, keep the external URL in a `brochure_url` text field.
6. Title suffix (`– 1918 & 1916`) → `model_number`; title prefix stays as `post_title`.
7. `show_on_home` / `order_product_home` → boolean / number fields.

**Tests (PHPUnit, no WP bootstrap):** fixture files captured from 6 real products
(short list, long list, 20+ spec rows, multi-line spec cell, no image, no PDF).
Assertions: feature count, spec row count, exact label/value pairs, idempotency
(running twice produces identical field data).

### 5.2 Pages

11 pages are free-form VC. Each is rebuilt once by hand in the flexible-content
editor (≈ 2–3 hours total): Home, About, Quality Control, R&D, Clients, Products,
Contact, Become a Dealer, Site Map, Privacy, T&Cs.
Business copy is carried over verbatim — **no rewriting** in this project.

### 5.3 Media

- 730 attachment rows → 730 files. Fetch from `https://electronicsindia.co.in`
  with a resumable script (CSV log of URL → status → bytes), rewriting the host.
- Verify byte size + MIME, then `wp media regenerate --only-missing`.
- Convert PNG/JPEG > 200 KB to WebP with `sharp` (house pattern in
  `scripts/`), keep originals, rewrite `_wp_attached_file` only after checksum QA.
- Add `srcset` via WP's native responsive images; fix the old hard-coded
  `srcset` strings in the child theme HTML.
- Audit `alt` text: 730 images, expect most to be empty → derive alt from the
  parent product/model name where possible, flag the rest for a human pass.

### 5.4 Database clean-up

- Delete 20,846 spam comments + 72,445 orphaned `wp_commentmeta` rows (97 MB → 0).
- Disable comments site-wide (no blog, no engagement).
- Drop the **27 `wp_wf*` tables** left behind by the inactive Wordfence plugin.
- Drop the **7 `wp_wpforms_*` tables** left by a page-builder plugin that is no longer installed.
- Drop the **8 `wp_revslider_*` tables** (2 sliders, 21 slides) after hero content is captured.
- Delete stale transients + `_vc_post_settings` / `_wpb_*` postmeta after migration.
- `OPTIMIZE TABLE` on `wp_posts`, `wp_postmeta`, `wp_options`.

---

## 6. Non-functional targets (acceptance criteria)

| Area | Target | How verified |
|---|---|---|
| Third-party requests | **0** | Playwright request log on home/product/category — assert every host is local |
| Requests (home) | ≤ 20 total | same |
| CSS | ≤ 2 files, ≤ 45 KB uncompressed | `du` + network log |
| JS | ≤ 3 files, ≤ 25 KB uncompressed, all `defer` | same |
| jQuery | not loaded on front end | `document.querySelector` check in Playwright |
| LCP (local) | < 2.0 s desktop / < 2.8 s throttled mobile | Lighthouse |
| CLS | < 0.05 | Lighthouse |
| A11y | WCAG 2.2 AA, axe-core 0 violations | axe + manual keyboard pass |
| Contrast | red-on-white 7.3:1, body text ≥ 4.5:1 | computed |
| SEO | titles/meta/OG/canonical/JSON-LD on every template; core sitemap; 301 map | crawl + Rich Results test |
| Print | product page prints specs + brochure link, no chrome | browser print preview |
| No-JS | nav, menu, gallery, forms usable | Playwright with JS disabled |
| PHP | `./matrix check ei-revamp` clean for the new theme + plugin | matrix |
| Tests | parser unit suite green; Playwright smoke suite green | CI |

---

## 7. Execution phases

| # | Phase | Work | Exit criteria | Est. |
|---|---|---|---|---|
| **0** | Safety net | Fresh `./matrix export` of both sites; commit a baseline; capture "before" screenshots + Lighthouse + request log of every template | Restorable bundle exists; baseline metrics recorded | 0.5 d |
| **1** | Foundations | `.gitignore` exception so `themes/ei` + `plugins/ei-core` are tracked; scaffold theme (`style.css`, `functions.php`, `theme.json`, `assets/`); scaffold `ei-core`; install ACF Pro locally (copy 6.4.0.1 from `wp_sr`); register CPTs/taxonomies; write field groups to `acf-json/` | Admin shows Product/Testimonial/Client + all field groups; ACF JSON round-trips | 1 d |
| **2** | Data model + migration | Write `VcParser`, unit tests with real fixtures; `wp ei migrate-products` (dry-run → apply); migrate title/model numbers, specs, features, PDFs; map categories; set term images + order | 92/92 products have parsed fields; `--dry-run` diff clean; unit tests green; spot-check 10 products against old pages | 2.5 d |
| **3** | Media localisation | Fetch 730 files from live host with resumable CSV log; regenerate thumbnails; WebP conversion; alt-text pass; rewrite the 2 absolute remote image URLs found in content | 0 broken images on any template; 0 external image hosts | 1.5 d |
| **4** | Theme build | Header/nav/mega menu, footer, tokens, fonts, icon sprite; `single-product`, `taxonomy-products`, `archive-product`; flexible sections; contact/dealer/sitemap/clients templates; search; 404; print CSS | All 11 pages + 92 products render on the new theme with the old theme disabled | 4 d |
| **5** | Decommission plugins | Remove in this order, smoke-testing each: `printfriendly` → `pdf-print` → `wp-fastest-cache` → `breadcrumb-navxt` → `categories-images` + `taxonomy-terms-order` → `wp-dtree-30` + `wp-sitemap-page` → `megamenu` + `ubermenu` → `js_composer` → `revslider` → inactive/unused | `wp plugin list` shows ACF, CF7, ei-core only; every template still correct | 1 d |
| **6** | Page rebuild | Re-author 11 pages in flexible content from captured copy; rebuild menus; CF7 forms restyled (Enquiry, Dealer, Contact); mail routing verified via Mailpit | Page-by-page visual diff signed off | 1 d |
| **7** | Hardening | Meta/OG/canonical/JSON-LD; `wp-sitemap.xml`; 301 map; lazy-loading; cache headers; Redis object cache enabled; DB cleanup (§5.4); axe + Lighthouse runs | §6 table fully green | 1.5 d |
| **8** | QA + cutover | Full click-through on desktop/tablet/mobile; content diff vs live; staged export → import on `wp_ei`; go-live checklist | Sign-off, then `./matrix export ei-revamp` as the release artefact | 1.5 d |

**Total ≈ 14.5 working days.**

Phases 1–3 are strictly sequential. Phase 4 can start once the content model is
frozen (end of phase 2). Phase 6 depends on 4. Phase 7 depends on 5–6.

---

## 8. Risks

| Risk | Impact | Mitigation |
|---|---|---|
| Parser mis-reads an irregular product (1 of 92 differs) | Wrong specs published | Unit tests over real fixtures + dry-run diff + manual spot-check of all 92 (fast: one table view) |
| Live media host blocks bulk download | Broken images block launch | Resumable CSV log, polite rate limit, committable retry; fallback: request a files archive |
| Old URL structure lost | SEO traffic drop | URLs preserved by design (`/product/{slug}/`, `/products/{term}/`); 301 map for the handful of changes (portfolio, sitemap) |
| ACF Pro licensing | Legal/update risk | Local GPL copy already in use on 6 sibling sites in this matrix; register the licence key when going live |
| Plugin removal breaks a page we forgot | Visible regression | Remove one plugin at a time with a template-by-template smoke test; Phases 0 and 8 bundles allow instant rollback |
| Theme code lands outside git | Unrecoverable work | Phase 1 first task: `.gitignore` negation rules + verify with `git status` before any code is written |
| Client expects copy/design changes | Scope creep | Explicitly out of scope: content rewriting, new logo, new IA. Tracked as a follow-up engagement |

**Out of scope:** copywriting, photography, multilingual, e-commerce, blog, CRM
integration, hosting migration, analytics changes.

---

## 9. Decisions needed from you

| # | Decision | Default if you don't answer |
|---|---|---|
| **D1** | Keep Google Tag Manager (GTM-MF58M67)? It is the only remaining third-party request after this work. | Keep it, but load it only after consent/`requestIdleCallback`; flag it as the one documented exception to "0 third-party requests" |
| **D2** | Drop the `portfolio` CPT (14 items, not reachable from any menu or page)? | Keep the data, unregister the CPT, no public URLs |
| **D3** | Fonts: accept the proposed pair, or supply brand fonts? | Inter Tight (UI) + Archivo Narrow (model numbers), both OFL, self-hosted |
| **D4** | Push the new theme + `ei-core` to a git remote, or keep local-only? | Local commits on `main`; no push |

---

## 10. Definition of done

1. `wp plugin list` → `advanced-custom-fields-pro`, `contact-form-7`, `ei-core`, plus must-use hardening only.
2. `wp-content/plugins` ≤ 13 MB, `wp-content/themes` ≤ 3 MB, database ≤ 25 MB.
3. Home page: 0 external hosts, ≤ 20 requests, ≤ 45 KB CSS, ≤ 25 KB JS, no jQuery.
4. All 11 pages, 92 products, 35 categories render correctly on desktop, tablet, mobile, and with JS disabled.
5. Every product has structured features, specs, gallery and a working brochure PDF.
6. axe-core: 0 violations on the 6 key templates; keyboard-only navigation works end to end.
7. `./matrix check ei-revamp` passes for `themes/ei` and `plugins/ei-core`.
8. Parser unit tests + Playwright smoke suite green.
9. `./matrix export ei-revamp` produces a bundle that imports cleanly into a fresh site.
