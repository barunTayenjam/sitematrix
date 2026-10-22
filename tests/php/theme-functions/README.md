# Theme functions TDD suite (dp_evolve-Child)

Tests for the optimisations applied to
`wp_gz/wp-content/themes/dp_evolve-Child/functions.php`.

## Run

```bash
php tests/php/theme-functions/run-tests.php
```

Exit code `0` means every assertion passed. The suite needs no database, no
WordPress install and no Docker — `wp-stubs.php` provides the WordPress
functions the theme touches at include time.

## How it works

| File | Purpose |
| --- | --- |
| `wp-stubs.php` | Minimal WordPress API stubs + call recorder (`$GLOBALS['tf']`). |
| `probe.php` | Loads the real `functions.php` in the stub environment and prints the recorded hooks as JSON. Run as a subprocess so each scenario starts clean. |
| `assert.php` | Assertions, subprocess probe helper, and `tf_load_function()` which extracts a single function from `functions.php` (via the PHP tokenizer) and evals it so it can be called in isolation. |
| `runner.php` | Discovers and runs every `test-*.php`. |

## Coverage

| Test | Optimisation |
| --- | --- |
| `test-ajax-admin-gate.php` | The tag-processing AJAX handler is only registered in admin-ajax context. |
| `test-blogcategory-query.php` | `[blogcategory]`'s exclude lookup asks for `fields => ids` and skips found-rows / term / meta cache priming, while still excluding every matching post. |
| `test-session-admin.php` | `start_session()` no longer opens a PHP session on wp-admin page loads, but still does for front-end and admin-ajax requests. |
| `test-shortcode-queries.php` | `ubermenu_post_grid()` sets `suppress_filters => true`; the `[term_tag]` cloud renders 5 tags. |

## Related

`tests/php/object-cache/persistence-test.php` verifies the Redis object-cache
drop-in authenticates and persists data across processes. It must run inside a
site container:

```bash
docker cp tests/php/object-cache/persistence-test.php wp_gz:/tmp/
docker exec -e REDIS_PASSWORD=... wp_gz php /tmp/persistence-test.php \
  /var/www/html/wp-content/object-cache.php write
docker exec -e REDIS_PASSWORD=... wp_gz php /tmp/persistence-test.php \
  /var/www/html/wp-content/object-cache.php read
```

Both calls must exit `0` and print the same probe value.
