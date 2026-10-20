#!/bin/bash
# Offline contract tests for Glossary V2 (seoinux-child).
#
# Pins:
#   - inc/glossary-v2 module, templates, lean registry
#   - Blog-parity single blocks (AI share, FAQ, bottom meta) without editorial sidebar
#   - ASAH banner padding override, editorial article/meta CSS on singles
#   - ACF FAQ location for glossary_term
#   - PHP helpers: letter grouping, TOC (fixtures/glossary-v2-php-test.php)
#
# No Docker or WordPress runtime required.

set -uo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
CHILD_THEME="$REPO_ROOT/wp_su26/wp-content/themes/seoinux-child"
GL_MODULE="$CHILD_THEME/inc/glossary-v2"

PASS=0
FAIL=0

pass() { PASS=$((PASS + 1)); }
fail() { FAIL=$((FAIL + 1)); echo "FAIL: $1"; }

assert_eq() {
    local desc="$1" expected="$2" actual="$3"
    if [[ "$expected" == "$actual" ]]; then
        pass
    else
        fail "$desc — expected '$expected', got '$actual'"
    fi
}

assert_rc() {
    local desc="$1" expected="$2"; shift 2
    local local_rc=0
    "$@" >/dev/null 2>&1 || local_rc=$?
    if [[ "$local_rc" == "$expected" ]]; then
        pass
    else
        fail "$desc — expected rc $expected, got $local_rc"
    fi
}

assert_file() {
    local desc="$1" path="$2"
    if [[ -f "$path" ]]; then
        pass
    else
        fail "$desc — missing file $path"
    fi
}

assert_grep() {
    local desc="$1" pattern="$2" file="$3"
    if grep -qE -- "$pattern" "$file" 2>/dev/null; then
        pass
    else
        fail "$desc — pattern not in $file"
    fi
}

assert_nogrep() {
    local desc="$1" pattern="$2" file="$3"
    if grep -qE -- "$pattern" "$file" 2>/dev/null; then
        fail "$desc — unexpected pattern in $file"
    else
        pass
    fi
}

# ---- T1: module files ----
assert_file "T1.1 bootstrap" "$GL_MODULE/bootstrap.php"
assert_file "T1.2 cpt" "$GL_MODULE/glossary-v2-cpt.php"
assert_file "T1.3 hooks" "$GL_MODULE/glossary-v2-hooks.php"
assert_file "T1.4 enqueue" "$GL_MODULE/glossary-v2-enqueue.php"
assert_file "T1.5 content" "$GL_MODULE/glossary-v2-content.php"
assert_file "T1.6 index template" "$CHILD_THEME/template-glossary-v2.php"
assert_file "T1.7 single template" "$CHILD_THEME/template-glossary-term-v2.php"
assert_file "T1.8 glossary css" "$CHILD_THEME/assets/css/glossary-v2/glossary.css"
assert_file "T1.9 glossary js" "$CHILD_THEME/assets/js/glossary-v2.js"
assert_file "T1.10 single partial" "$CHILD_THEME/template-parts/glossary-v2/single.php"

# ---- T2: theme bootstrap + lean registry ----
assert_grep "T2.1 functions.php requires glossary bootstrap" \
    "inc/glossary-v2/bootstrap.php" "$CHILD_THEME/functions.php"
assert_grep "T2.2 lean registry glossary-v2" \
    "'glossary-v2'" "$CHILD_THEME/inc/lean-engine/registry.php"
assert_grep "T2.3 registry index template" \
    "template-glossary-v2.php" "$CHILD_THEME/inc/lean-engine/registry.php"
assert_grep "T2.4 registry single template" \
    "template-glossary-term-v2.php" "$CHILD_THEME/inc/lean-engine/registry.php"
assert_grep "T2.5 registry prefix" \
    "'su-glosv2'" "$CHILD_THEME/inc/lean-engine/registry.php"

# ---- T3: CPT contract ----
assert_grep "T3.1 post type slug" \
    "'glossary_term'" "$GL_MODULE/glossary-v2-cpt.php"
assert_grep "T3.2 rewrite glossary" \
    "'slug'[[:space:]]*=>[[:space:]]*'glossary'" "$GL_MODULE/glossary-v2-cpt.php"
assert_grep "T3.3 glossary_topic tax" \
    "'glossary_topic'" "$GL_MODULE/glossary-v2-cpt.php"
assert_grep "T3.4 post_tag on glossary" \
    "register_taxonomy_for_object_type[[:space:]]*\\([[:space:]]*'post_tag'" "$GL_MODULE/glossary-v2-cpt.php"

# ---- T4: routing / chrome hooks ----
assert_grep "T4.1 template_include glossary" \
    "template-glossary-term-v2.php" "$GL_MODULE/glossary-v2-hooks.php"
assert_grep "T4.2 su_asah_chrome_active filter" \
    "su_asah_chrome_active" "$GL_MODULE/glossary-v2-hooks.php"
assert_grep "T4.3 body class glossary template" \
    "su-glossary-v2-template" "$GL_MODULE/glossary-v2-hooks.php"
assert_grep "T4.4 single-post class on singular" \
    "is_singular[[:space:]]*\\([[:space:]]*'glossary_term'" "$GL_MODULE/glossary-v2-hooks.php"

# ---- T5: single shell (blog blocks, no sidebar) ----
SINGLE_PART="$CHILD_THEME/template-parts/glossary-v2/single.php"
assert_grep "T5.1 ai-share partial" \
    "template-parts/ai-share" "$SINGLE_PART"
assert_grep "T5.2 editorial faq partial" \
    "template-parts/editorial-v2/faq" "$SINGLE_PART"
assert_grep "T5.3 bottom-meta partial" \
    "template-parts/bottom-meta" "$SINGLE_PART"
assert_grep "T5.4 su-edv2-article class" \
    "su-edv2-article" "$SINGLE_PART"
assert_nogrep "T5.5 no editorial sidebar" \
    "editorial-v2/sidebar" "$SINGLE_PART"

TERM_SHELL="$CHILD_THEME/template-glossary-term-v2.php"
assert_grep "T5.6 shared CTA" \
    "su-ai-support-agents-home/cta" "$TERM_SHELL"
assert_grep "T5.7 page tail" \
    "glossary-v2/page-tail" "$TERM_SHELL"
assert_grep "T5.8 faq schema print" \
    "su_edv2_faq_schema_print" "$TERM_SHELL"

# ---- T6: integrations (AI share, bottom meta, ACF) ----
assert_grep "T6.1 ai-share glossary_term" \
    "is_singular[[:space:]]*\\([[:space:]]*array[[:space:]]*\\([[:space:]]*'post'[[:space:]]*,[[:space:]]*'glossary_term'" \
    "$CHILD_THEME/template-parts/ai-share.php"
assert_grep "T6.2 bottom meta glossary singular" \
    "is_singular[[:space:]]*\\([[:space:]]*array[[:space:]]*\\([[:space:]]*'post'[[:space:]]*,[[:space:]]*'glossary_term'" \
    "$CHILD_THEME/template-parts/bottom-meta.php"
assert_grep "T6.3 blog style meta glossary" \
    "is_singular[[:space:]]*\\([[:space:]]*'glossary_term'" \
    "$CHILD_THEME/inc/bottom-meta-share-markup.php"
assert_grep "T6.4 acf faq glossary location" \
    "'glossary_term'" "$CHILD_THEME/inc/editorial-v2/editorial-v2-acf.php"
assert_grep "T6.5 acf faq version bump" \
    "SU_EDV2_ACF_FAQ_GROUP_VERSION = '3'" "$CHILD_THEME/inc/editorial-v2/editorial-v2-acf.php"

# ---- T7: assets enqueue ----
ENQ="$GL_MODULE/glossary-v2-enqueue.php"
assert_grep "T7.1 raw glossary css" \
    "glossary-v2/glossary.css" "$ENQ"
assert_grep "T7.2 article css on single" \
    "editorial-v2/article.css" "$ENQ"
assert_grep "T7.3 meta css on single" \
    "editorial-v2/meta.css" "$ENQ"
assert_grep "T7.4 webinar banner css" \
    "webinar-v2/banner.css" "$ENQ"
assert_grep "T7.5 allowed styles meta handle" \
    "su-glosv2-edv2-meta-css" "$GL_MODULE/glossary-v2-hooks.php"

# ---- T8: CSS contracts ----
GCSS="$CHILD_THEME/assets/css/glossary-v2/glossary.css"
assert_grep "T8.1 ASAH banner padding override" \
    "#su-glossary-v2.su-asah > section.su-wbv2-banner.banner" "$GCSS"
assert_grep "T8.2 popular tags title scope" \
    "bottom-meta-wrap p.tag-title" "$GCSS"
assert_grep "T8.3 tag pill links" \
    "li.post-tag a" "$GCSS"
assert_grep "T8.4 faq block styles" \
    "su-edv2-faq" "$GCSS"
assert_grep "T8.5 mobile table scroll" \
    "su-glosv2-single__content" "$GCSS"
assert_grep "T8.6 ai-share glossary scope" \
    "#su-glossary-v2 .post-ai-share" "$GCSS"

# ---- T9: index shell ----
INDEX_SHELL="$CHILD_THEME/template-glossary-v2.php"
assert_grep "T9.1 index main id" \
    'id="su-glossary-v2"' "$INDEX_SHELL"
assert_grep "T9.2 index partial" \
    "glossary-v2/index" "$INDEX_SHELL"
assert_nogrep "T9.3 index no single-only faq in shell" \
    "editorial-v2/faq" "$INDEX_SHELL"

# ---- T10: PHP offline unit tests ----
export GLOSSV2_CHILD_THEME="$CHILD_THEME"
PHP_ERR="$(mktemp)"
PHP_OUT="$(php "$REPO_ROOT/tests/bash/fixtures/glossary-v2-php-test.php" 2>"$PHP_ERR")" || PHP_OUT=""
if [[ "$PHP_OUT" =~ ^passes=([0-9]+)$ ]]; then
    php_passes="${BASH_REMATCH[1]}"
    for _ in $(seq 1 "$php_passes"); do pass; done
else
    fail "T10 PHP unit suite — out='$PHP_OUT' err=$(cat "$PHP_ERR")"
fi
rm -f "$PHP_ERR"

# ---- T11: PHP syntax (module + templates) ----
for f in \
    "$GL_MODULE/bootstrap.php" \
    "$GL_MODULE/glossary-v2-hooks.php" \
    "$GL_MODULE/glossary-v2-enqueue.php" \
    "$GL_MODULE/glossary-v2-content.php" \
    "$GL_MODULE/glossary-v2-cpt.php" \
    "$SINGLE_PART" \
    "$TERM_SHELL" \
    "$INDEX_SHELL"
do
    base="$(basename "$f")"
    assert_rc "T11 php -l $base" 0 php -l "$f"
done

echo ""
echo "glossary-v2-test: $PASS passed, $FAIL failed"
if [[ "$FAIL" -gt 0 ]]; then
    exit 1
fi
exit 0
