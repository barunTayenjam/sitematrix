# Preprod deploy — AI Agents section + blog performance

Generated 2026-11-05 from the local `gz` site. Tested locally at
`http://localhost:8203`.

## What this contains

| File | Change |
| --- | --- |
| `wp-content/themes/dp_evolve-Child/web-php-service-template-v2.php` | Renders `feature_description` subtitle and a 2×2 CSS grid (`.why-integrate-grid`) instead of the owl-carousel. |
| `wp-content/themes/dp_evolve-Child/css/web-php-service--template-v2.css` | `.why-integrate-subtitle` and `.why-integrate-grid` styles (1 column below 767px). |
| `populate-ai-agents.php` | WP-CLI script that writes the 4 card titles/descriptions + subtitle + background colour to the AI Agent page. |
| `wp-content/themes/dp_evolve-Child/functions.php` | Adds `gz_blog_excluded_post_ids()` and uses it in the two blog AJAX queries (load-more, search). |
| `wp-content/themes/dp_evolve-Child/template-gz-bloglisting.php` | Same blog query rewrite for the initial listing. |
| `wp-content/themes/dp_evolve-Child/web-php-service-template.php` | Tab attributes `data-toggle`/`data-target` (already live on preprod — include only if you want to overwrite). |

## Why the blog files changed

The blog listing used a `meta_query` with `REGEXP` + `NOT EXISTS` and
`relation => OR`. On a 4.15M-row `wp_postmeta` table MySQL scanned ~143k meta
rows per request and applied the regex to all of them: **12.7 s TTFB**.

The rewrite resolves the 133 hidden post IDs once (`show_blog_posts` NOT LIKE
`%show_in_blog_page%`) and passes them as `post__not_in`. Result: **~1.3 s**.

Verified identical post IDs to the old query for offset 0 / 16 / 100 and for a
category-filtered case.

## Deploy steps

1. Copy the theme files over the existing ones (paths mirror the theme root):
   ```bash
   rsync -av wp-content/themes/dp_evolve-Child/ <preprod>:/path/to/wp-content/themes/dp_evolve-Child/
   ```
   (Use SFTP/cPanel File Manager if that is how preprod is normally updated.)

2. Populate the AI Agent page fields from the WordPress root on preprod:
   ```bash
   wp eval-file populate-ai-agents.php
   ```
   The script finds the page by slug `ai-agent-development-services`, so the
   post ID does not matter. It is idempotent.

3. Clear page/object/CDN caches (Cloudflare + any WordPress cache plugin), then
   reload with a hard refresh. The theme serves CSS/JS without `?ver=`, so a
   stale browser cache can hide the change.

## Verification checklist

- `https://preprod.grazitti.com/services/web-development/platforms/ai-agent-development-services/`
  - subtitle above the cards is rendered;
  - exactly 4 cards in a 2×2 grid on desktop, 1 column below 767px;
  - no owl-carousel / `.owl-stage` inside `.why-integrate`.
- `https://preprod.grazitti.com/blog/`
  - loads in about 1–2 s (was ~12 s);
  - 16 cards on the first page;
  - a post whose `show_blog_posts` is `show_in_salesforce_prediction` is absent;
  - "Load more" and blog search still return results.
- `https://preprod.grazitti.com/services/web-development/platforms/php/`
  - all four tabs switch, only one pane visible at a time (already verified
    working before this bundle).

## Not included (local-only)

- `home`/`siteurl` were changed to `http://localhost:8203` because the host's
  LAN IP moved from `192.168.31.207` to `192.168.1.39`. Do **not** deploy this.
- Wordfence `serverDNS` cache seed (`time;315360000;127.0.0.1`) is a local-only
  workaround for a Docker DNS stall; preprod does not need it.
