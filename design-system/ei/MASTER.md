# Design System — Electronics India (EI)

**Project:** Electronics India — Scientific Analytical Laboratory Instruments Manufacturer
**Product Type:** B2B Manufacturing / Laboratory Equipment
**Pattern:** Trust & Authority + Conversion
**Style:** Accessible & Ethical (light, luxurious modern)

---

## Color Palette (Semantic Tokens)

| Token | Value | Role |
|-------|-------|------|
| `--color-background` | `#FAFAF9` | Page ground — warm off-white (luxury) |
| `--color-foreground` | `#111827` | Primary text — deep charcoal ink |
| `--color-card` | `#FFFFFF` | Surface cards |
| `--color-card-foreground` | `#111827` | Text on cards |
| `--color-primary` | `#1C1917` | Brand dark (headings weight) |
| `--color-on-primary` | `#FFFFFF` | — |
| `--color-secondary` | `#334155` | Secondary UI elements |
| `--color-on-secondary` | `#FFFFFF` | — |
| `--color-accent` | `#AE1010` | **EI Red** — primary CTA / active state (kept from brand) |
| `--color-on-accent` | `#FFFFFF` | Text on accent |
| `--color-muted` | `#E4E4E7` | Dividers, disabled bg |
| `--color-muted-foreground` | `#6B7280` | Secondary text, captions |
| `--color-border` | `#E5E5E5` | Borders, hairlines |
| `--color-destructive` | `#DC2626` | Error states |
| `--color-ring` | `#AE1010` | Focus ring (matches accent) |

**Contrast Notes:**
- Foreground #111827 on Background #FAFAF9 = 15.8:1 ✅
- Accent #AE1010 on White = 4.7:1 ✅ (use ≥18px bold for small UI)
- Muted-foreground #6B7280 on Background = 4.9:1 ✅
- Accent on Background = 5.1:1 ✅

---

## Typography

| Role | Font | Weights | Notes |
|------|------|---------|-------|
| **Heading / Display** | **Playfair Display** (Google Fonts) | 400, 500, 600, 700 | Luxury editorial serif — heritage + precision |
| **Body / UI** | **Inter** (Google Fonts) | 300, 400, 500, 600, 700 | Clean, highly readable, modern |

```css
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap');
```

**Type Scale (clamp fluid):**
- `--step-xs`: clamp(0.75rem, 0.72rem + 0.15vw, 0.8125rem) — 12/13px
- `--step-sm`: clamp(0.875rem, 0.84rem + 0.17vw, 0.9375rem) — 14px
- `--step-base`: clamp(1rem, 0.95rem + 0.25vw, 1.125rem) — 16px base
- `--step-lg`: clamp(1.125rem, 1.05rem + 0.37vw, 1.25rem) — 18px lead
- `--step-xl`: clamp(1.5rem, 1.3rem + 1vw, 2rem) — 24px section headings
- `--step-2xl`: clamp(2rem, 1.6rem + 2vw, 3rem) — 32/48px hero
- `--step-3xl`: clamp(3rem, 2.2rem + 4vw, 5rem) — 48/80px large hero

Line heights: body 1.6, headings 1.15, tight display 1.05.

---

## Spacing Scale

| Token | Value | Use |
|-------|-------|-----|
| `--space-1` | 4px | micro |
| `--space-2` | 8px | tight |
| `--space-3` | 16px | base unit |
| `--space-4` | 24px | component |
| `--space-5` | 32px | section inner |
| `--space-6` | 48px | section gap |
| `--space-7` | 64px | large gap |
| `--space-8` | 96px | hero vertical |

Container widths:
- `--container-narrow`: 720px (reading)
- `--container-wide`: 1180px (content)
- `--container-full`: 100vw (full-bleed)

---

## Border Radius (One Shape)

| Token | Value |
|-------|-------|
| `--radius-sm` | 4px |
| `--radius-md` | 8px |
| `--radius-lg` | 12px |
| `--radius-full` | 9999px |

**Button radius:** `--radius-md` (8px) — single shape everywhere.

---

## Shadow / Elevation (Restrained)

| Token | Value | Use |
|-------|-------|-----|
| `--shadow-sm` | `0 1px 2px 0 rgb(0 0 0 / 0.05)` | cards at rest |
| `--shadow-md` | `0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)` | hover cards |
| `--shadow-lg` | `0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)` | dropdowns, modals |

No colored glows, no heavy depth.

---

## Motion

- **Duration**: 150ms (micro), 250ms (standard), 400ms (section enter)
- **Easing**: `cubic-bezier(0.2, 0, 0, 1)` (Material standard)
- **Reduced motion**: All transitions → 0.01ms, no parallax, no auto-play video
- **GSAP**: Not used — vanilla `IntersectionObserver` reveal + CSS transitions only

---

## Components

### Button (one shape, two variants)
```css
.btn {
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  padding: 12px 24px; font-size: var(--step-base); font-weight: 500;
  border-radius: var(--radius-md); border: 2px solid transparent;
  cursor: pointer; transition: all 150ms ease; text-decoration: none;
}
.btn--primary { background: var(--color-accent); color: var(--color-on-accent); border-color: var(--color-accent); }
.btn--primary:hover { filter: brightness(0.95); }
.btn--outline { background: transparent; color: var(--color-accent); border-color: var(--color-accent); }
.btn--outline:hover { background: var(--color-accent); color: var(--color-on-accent); }
```

### Input
```css
.input { width: 100%; padding: 12px 16px; font-size: var(--step-base);
  border: 1px solid var(--color-border); border-radius: var(--radius-md);
  background: var(--color-card); color: var(--color-foreground);
  transition: border-color 150ms, box-shadow 150ms; }
.input:focus { outline: none; border-color: var(--color-ring); box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-ring) 25%, transparent); }
```

### Mega Menu
- `ul.ei-menu` — horizontal flex, gap 32px
- Products `li` has `ul.ei-mega-panel` absolute, full-width of container, grid columns auto-fit minmax(180px, 1fr)
- Columns: `li.ei-mega-col-list` — h3 heading + ul children
- Reduced motion: instant show/hide

---

## Page Structures

### Home (`front-page.php`)
1. **Hero** — Full-bleed atmosphere photo (lab), headline + sub + primary CTA (Get Quote)
2. **Proof** — Stats strip (ISO, 50+ years, 92 products, 7 categories) + Trust badges
3. **Solution Overview** — Category cards grid (7 top-level terms with counts), each links to `/products/{term}/`
4. **Featured Products** — 4 products flagged `show_on_home`, card: image + title + model + short features
5. **CTA** — Centered sentence + Get Quote button

### Products Catalogue (`taxonomy-products.php`)
- Breadcrumb
- Category hero: term name + description (if any)
- Filter/sort bar (future)
- Product grid: 4-col desktop, 2-col tablet, 1-col mobile
- Pagination

### Product Single (`single-product.php`)
- Breadcrumb
- Gallery (left, sticky on desktop) — main image + thumbnails
- Info panel (right): Title, Model badge, Short description / features fallback, Spec table, Brochure download, Get Quote CTA
- Tabs/accordions for Features / Specifications / Downloads

### Static Pages (`page.php`, About, Contact, Quality, R&D, Clients, Sitemap, Privacy, Terms)
- Slim hero: Page title + breadcrumb
- Clean content (via `ei_clean_content()`)
- Contact: native form (no CF7) → posts to `/wp-json/ei/v1/contact`

---

## Anti-Patterns (Do Not Use)
- Dark mode forced (light only, luxurious)
- Playful/bright gradients, neon, purple/pink
- Hidden credentials / missing certifications
- Hero sliders, carousels, auto-rotating content
- Emoji as icons (use inline SVG — Lucide / Heroicons style)
- Text < 16px body
- Gray-on-gray low contrast
- Animating width/height (transform/opacity only)

---

## Pre-Delivery Checklist (from pro-rules.md)

- [ ] Contrast 4.5:1 minimum all text
- [ ] Focus visible (3px ring accent color)
- [ ] 44×44px touch targets all interactive
- [ ] `prefers-reduced-motion` respected
- [ ] Responsive at 375, 768, 1024, 1440
- [ ] No horizontal scroll
- [ ] CLS < 0.1 (reserve image space with aspect-ratio)
- [ ] Font display=swap (Google Fonts URL includes it)
- [ ] Skip link present
- [ ] Alt text on all images
- [ ] ARIA labels on icon-only controls