# Argument — Electronics India (EI) home page

**Subject sentence.** "On this page, precise laboratory instruments become quiet confidence." — Unlike competitors who shout with bright stock photography and slider soup, EI's own material is its instruments photographed on a bench in its Parwanoo lab and its 50+ year history. The sentence still works for a generic instrument seller, so I sharpen: "Parwanoo lab instruments under one light." Only EI has that room and those benches.

**One live thing the visitor can do.** Browse one product category grid from the hero, then request a quote — one action per screen, nothing else.

**Constraints.** All assets local (no CDN, no fontshare, no third-party runtime requests), no new plugin, fast loading, dark ground with one accent (EI red #ae1010), retained taxonomy navigation (/products/{term}/), mega menu from products taxonomy.

**Design direction adopted from draw.mjs:** "The dark room" — warm black ground, lit objects with space around them, light sparse type, one accent used once per screen, pinned story on one screen, entrance motion on every section, equal margins, headlines max two lines, no eyebrow labels, no monospace, no gradients/shadows at rest, no cream/terracotta/violet, body >= 13px.

**Local font decision.** Dealt faces Gambarino/General Sans are not installed and fontshare/CDN violates the no-third-party rule. Using system-ui font stack (zero bytes, fastest load) with the dealt type scales; self-hosted Archivo Narrow is the planned upgrade (ponytail: below).

**Imagery.** EI's own product photos (uploads/2016-2020, 742 backfilled locally) are the only product pictures. Hero uses `bg-title7.jpg` (blue-tinted lab fog) desaturated/darkened so it reads as atmosphere, not a specific product. No SVG stand-ins for products — real photos exist.

**ponytail: ceiling — Gambarino/General Sans self-hosted.** add when font files land in theme/fonts/, swap font-family in theme.json + style.css.
