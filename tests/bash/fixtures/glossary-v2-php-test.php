<?php
/**
 * Offline PHP unit checks for Glossary V2 helpers (no WordPress bootstrap).
 *
 * Invoked by tests/bash/glossary-v2-test.sh
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wordpress/');
}

function add_action(...$args): void {}
function register_post_type(...$args): void {}
function register_taxonomy(...$args): void {}
function register_taxonomy_for_object_type(...$args): void {}
function get_option(string $option, $default = false) { return $default; }
function update_option(...$args): bool { return true; }
function flush_rewrite_rules(...$args): void {}

function __(string $text, string $domain = 'default'): string
{
    return $text;
}

function _x(string $text, string $context, string $domain = 'default'): string
{
    return $text;
}

function wp_strip_all_tags(string $text): string
{
    return strip_tags($text);
}

function sanitize_title(string $title): string
{
    $title = strtolower($title);
    $title = preg_replace('/[^a-z0-9]+/', '-', $title) ?? '';
    return trim($title, '-');
}

function esc_attr(string $text): string
{
    return $text;
}

$child = getenv('GLOSSV2_CHILD_THEME');
if (!is_string($child) || $child === '' || !is_dir($child)) {
    fwrite(STDERR, "GLOSSV2_CHILD_THEME not set\n");
    exit(2);
}

require $child . '/inc/glossary-v2/glossary-v2-cpt.php';
require $child . '/inc/glossary-v2/glossary-v2-content.php';

$failures = [];
$passes   = 0;

function glosv2_assert(bool $ok, string $message): void
{
    global $failures, $passes;
    if ($ok) {
        ++$passes;
        return;
    }
    $failures[] = $message;
}

// Letter grouping
glosv2_assert(su_glosv2_letter_from_title('AI Agents') === 'A', 'letter AI Agents => A');
glosv2_assert(su_glosv2_letter_from_title('9 Lives') === '#', 'letter 9 Lives => #');
glosv2_assert(su_glosv2_letter_from_title('') === '#', 'empty title => #');
glosv2_assert(su_glosv2_letter_from_title('Øre') === '#', 'non-ascii lead => #');

$letters = su_glosv2_index_letters();
glosv2_assert(count($letters) === 27, 'index letters count 27');
glosv2_assert($letters[0] === 'A' && $letters[25] === 'Z' && $letters[26] === '#', 'index letters A-Z#');

// TOC slug uniqueness
$used = [];
glosv2_assert(su_glosv2_toc_unique_slug('Overview', $used) === 'overview', 'toc slug overview');
glosv2_assert(su_glosv2_toc_unique_slug('Overview', $used) === 'overview-2', 'toc slug dedupe');

$html = '<h2 id="intro" class="su-glosv2-toc-section">Intro</h2><h3 id="sub">Sub</h3>';
$toc  = su_glosv2_toc_from_content_html($html, true);
glosv2_assert(count($toc) === 3, 'toc with overview + 2 headings');
glosv2_assert($toc[0]['id'] === 'su-glosv2-overview', 'toc overview id');
glosv2_assert($toc[1]['text'] === 'Intro', 'toc intro text');

if ($failures !== []) {
    foreach ($failures as $f) {
        fwrite(STDERR, "PHP FAIL: $f\n");
    }
    exit(1);
}

echo "passes=$passes";
exit(0);
