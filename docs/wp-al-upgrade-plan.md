# wp_al → Latest Stack Upgrade Plan (on clone `al-2026`)

**Status:** Clone running on upgraded stack (WP 7.1.5 + WC 11.3.1 + PHP 8.3.37). All pages HTTP 200, 0 PHP errors, 0 fatals, 0 stale WC template overrides. **Shop page layout broken** — WC 11 default templates differ from old WC 3.x overrides that previously customized product display.
**Date:** 2026-11-23

## Confirmed state on `al-2026` (http://192.168.31.207:8213)

| Component | Original (`al`) | Clone (`al-2026`) |
|---|---|---|
| WordPress core | 4.7.40 | **7.1.5** |
| WooCommerce | 3.0.7 | **11.3.1** |
| PHP | 7.4 | **8.3.37** |
| Theme | storefront-child 1.0.0 / storefront 2.2.3 | storefront-child 1.0.0 / storefront 2.2.3 |
| Active plugins | ACF 4.4.11, EWPSMTP 1.2.5, Woo 3.0.7, WAD 1.1, WMP 1.0.0 | ACF 6.8.12, EWPSMTP 2.15.1, Woo 11.3.1, WAD 2.32.3, WMP 1.0.0 |
| PHP errors on key pages | — | **0** |
| Container fatals | — | **0** |
| WC template overrides | — | **0 (deleted)** |
| All key pages load | — | **Yes** (HTTP 200/302) |

## Breakage analysis — what's actually wrong

1. **Shop page layout differs** (primary issue): Old WC 3.x override `archive-product.php` used custom markup (`<div class="product_wrapper">`, `<div class="product_icon">`, `<div class="product-info">`). WC 11 default `content-product.php` uses `class="woocommerce-loop-product__title"` etc. Result: products render as simple grid without custom layout, images show broken icons. **Not fatal — content is present, just styled differently.**

2. **Free-trial AJAX test needs stubs re-added**: Test stubs (`altryx_sqlserver_stub`, `altryx_marketo_stub`) were in mu-plugins but lost when restored from baseline snapshot. Infrastructure issue, not site breakage.

3. **Missing upload images**: Both original and clone lack product image files (ADP-1.png etc. not in either uploads dir). Both sites reference same missing images. Not a clone-specific regression.

## To-do (execute in order)

| # | Action | Verification |
|---|--------|--------------|
| **1** | Port custom shop layout from old WC 3.x overrides to WC 11 templates — merge `archive-product.php`, `content-product.php`, `content-single-product.php`, `cart/cart.php`, `checkout/*`, `myaccount/*`, `single-product/*` custom markup into WC 11 equivalents | `magick compare` pixel diff < baseline threshold on home/shop/product/cart |
| **2** | Re-add test stubs (mu-plugin) + config accessors for salesadmin | `tests/site-al-2026/free-trial-test.sh` passes all assertions |
| **3** | Test cart flow — add product → cart page loads | Cart page HTTP 200 with product present |
| **4** | Test checkout start → checkout page loads | Checkout HTTP 200 |
| **5** | Test free-trial AJAX flow with stubbed Marketo/SQL Server | Response contains 'created'; WP rows created |
| **6** | Test salesadmin page loads | HTTP 200 |
| **7** | Snapshot | `./matrix export al-2026 --out backups/al-2026-final.tar.gz` |
| **8** | Update docs | `docs/wp-al-upgrade-plan.md` |

## Decisions (confirmed)

- Clone name: **al-2026**
- PHP target: **8.3**
- Inactive plugins: kept deactivated
- Cutover: **deferred** — original `al` untouched on port 8212
- Theme engine: **untouched** (Storefront 2.2.3 parent)
- WC template overrides: **deleted** (old 2017-era overrides incompatible with WC 11 — will port custom markup manually)

## Rollback points

Each `backups/al-2026-*.tar.gz` bundle can restore via `./matrix import <bundle> al-2026 --yes`. Original `al` (WP 4.7.40) never modified.