#!/bin/bash
# Offline tests for `matrix check` (PHP_CodeSniffer wiring).
#
# Contract:
#   - collect_check_roots lists one root per theme and per plugin, lists loose
#     group-folder PHP files individually, and never treats wp_content as a site
#   - run_checks() gives every root its own PHPCS process (PHPCS 3.x retains
#     each processed file until exit, so one process per site blows the limit)
#   - all ignore patterns travel in a SINGLE comma-separated --ignore value:
#     PHPCS 3.5.6 only honours the first --ignore flag and silently drops the
#     rest, which used to leave elementor/wordpress-seo/etc. unscanned-but-checked
#   - MATRIX_PHPCS_MEMORY_LIMIT overrides the memory ceiling
#   - a crashing tool is reported as a crash on exit 1, and is NOT re-run
#     through the direct-docker fallback
#   - violations found still exit 0 (a lint result is not a failure)

set -uo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SANDBOX="$(mktemp -d)"
trap 'rm -rf "$SANDBOX"' EXIT

mkdir -p "$SANDBOX/bin" "$SANDBOX/logs" "$SANDBOX/state"
cp "$REPO_ROOT/tests/bash/bin/docker" "$SANDBOX/bin/docker"
cp "$REPO_ROOT/tests/bash/bin/matrix-compose-stub" "$SANDBOX/bin/matrix-compose-stub"
cp "$REPO_ROOT/tests/bash/bin/matrix-compose-stub" "$SANDBOX/bin/docker-compose"
cp "$REPO_ROOT/tests/bash/bin/matrix-compose-stub" "$SANDBOX/bin/podman-compose"
cp "$REPO_ROOT/tests/bash/bin/stub-common.sh" "$SANDBOX/bin/stub-common.sh"
chmod +x "$SANDBOX/bin/"*
export PATH="$SANDBOX/bin:$PATH"
export STUB_DIR="$SANDBOX/state"
export DOCKER_COMPOSE="matrix-compose-stub"
export CONTAINER_RUNTIME="docker"
: > "$STUB_DIR/compose-run.txt"

log_info() { :; }
log_warning() { :; }
log_success() { :; }
log_error() { :; }

# shellcheck disable=SC1090,SC1091
source "$REPO_ROOT/matrix"
set +e
set -uo pipefail

# Undo matrix sourcing side effects (same pattern as the other suites).
export PROJECT_ROOT="$SANDBOX"
export COMPOSE_FILE="$SANDBOX/docker-compose.yml"
export DOCKER_COMPOSE="matrix-compose-stub"
export CONTAINER_RUNTIME="docker"
export ACTIVITY_LOG="$SANDBOX/logs/activity.log"
printf 'services:\n  phpcs:\n    image: wpengine/phpcs:latest\n' > "$COMPOSE_FILE"

PASS=0
FAIL=0
pass() { PASS=$((PASS + 1)); }
fail() { FAIL=$((FAIL + 1)); echo "FAIL: $1"; }

assert_eq() {
    local desc="$1" expected="$2" actual="$3"
    if [[ "$expected" == "$actual" ]]; then pass; else fail "$desc - expected '$expected', got '$actual'"; fi
}
assert_contains() {
    local desc="$1" needle="$2" haystack="$3"
    if [[ "$haystack" == *"$needle"* ]]; then pass; else fail "$desc - '$needle' not found"; fi
}
assert_not_contains() {
    local desc="$1" needle="$2" haystack="$3"
    if [[ "$haystack" != *"$needle"* ]]; then pass; else fail "$desc - '$needle' should be absent"; fi
}

make_site() {
    local site="$1"
    local base="$SANDBOX/wp_$site/wp-content"
    mkdir -p "$base/themes/alpha" "$base/themes/beta" "$base/plugins/acme" "$base/plugins/zen"
    printf '<?php\n' > "$base/themes/alpha/alpha.php"
    printf '<?php\n' > "$base/themes/beta/beta.php"
    printf '<?php\n' > "$base/plugins/acme/acme.php"
    printf '<?php\n' > "$base/plugins/zen/zen.php"
    printf '<?php\n' > "$base/plugins/index.php"
    printf 'wp\n' > "$SANDBOX/wp_$site/.matrix-type"
}

reset_stub() {
    : > "$STUB_DIR/compose-run.txt"
    rm -f "$STUB_DIR/run-output.txt" "$STUB_DIR/run-exit"
}

run_count() {
    local n
    n=$(grep -c '^---$' "$STUB_DIR/compose-run.txt" 2>/dev/null)
    echo "${n:-0}"
}

# matrix resolves site trees relative to the working directory, exactly as the
# CLI does when run from the repo root.
cd "$SANDBOX" || exit 1

# --- T1: roots are one per theme/plugin, loose files listed individually ------
make_site alpha
roots="$(collect_check_roots wp_alpha)"
assert_eq "T1.1 four directories + one loose file" "5" "$(printf '%s\n' "$roots" | grep -c .)"
assert_contains "T1.2 theme root" "./wp_alpha/wp-content/themes/alpha" "$roots"
assert_contains "T1.3 plugin root" "./wp_alpha/wp-content/plugins/zen" "$roots"
assert_contains "T1.4 loose file listed individually" "./wp_alpha/wp-content/plugins/index.php" "$roots"
assert_not_contains "T1.5 group folder is not a root" "./wp_alpha/wp-content/plugins"$'\n' "$roots"$'\n'

# --- T2: wp_content is not a site --------------------------------------------
mkdir -p "$SANDBOX/wp_content/wp-content/plugins/ghost"
printf '<?php\n' > "$SANDBOX/wp_content/wp-content/plugins/ghost/ghost.php"
all_roots="$(collect_check_roots wp_alpha wp_content)"
assert_not_contains "T2.1 wp_content contributes no roots" "wp_content" "$all_roots"

# --- T3: one PHPCS process per root ------------------------------------------
reset_stub
run_checks alpha >/dev/null 2>&1
assert_eq "T3.1 exit 0 when violations are found" "0" "$?"
assert_eq "T3.2 one process per root" "5" "$(run_count)"

# --- T4: single comma-separated --ignore (PHPCS drops later flags) -----------
ignore_args="$(grep -c '^--ignore=' "$STUB_DIR/compose-run.txt")"
assert_eq "T4.1 one --ignore per invocation" "$(run_count)" "$ignore_args"
recorded="$(cat "$STUB_DIR/compose-run.txt")"
assert_contains "T4.2 excludes bundle patterns" "elementor" "$recorded"
assert_contains "T4.3 excludes vendor" "*/vendor/*" "$recorded"
assert_contains "T4.4 excludes are comma separated" "*/vendor/*,*/node_modules/*" "$recorded"
assert_eq "T4.5 no repeated --ignore flags" "0" "$(grep -c '^--ignore=.*,.*--ignore' "$STUB_DIR/compose-run.txt")"

# --- T5: memory ceiling is configurable --------------------------------------
reset_stub
MATRIX_PHPCS_MEMORY_LIMIT=2G run_checks alpha >/dev/null 2>&1
assert_contains "T5.1 override reaches the tool" "memory_limit=2G" "$(cat "$STUB_DIR/compose-run.txt")"
reset_stub
run_checks alpha >/dev/null 2>&1
assert_contains "T5.2 default ceiling" "memory_limit=1G" "$(cat "$STUB_DIR/compose-run.txt")"

# --- T6: report file is written under logs/phpcs ------------------------------
reset_stub
rm -rf "$SANDBOX/logs/phpcs"
run_checks alpha >/dev/null 2>&1
assert_eq "T6.1 report dir created" "1" "$([[ -d "$SANDBOX/logs/phpcs" ]] && echo 1 || echo 0)"
assert_eq "T6.2 one report per run" "1" "$(ls -1 "$SANDBOX/logs/phpcs" | grep -c 'alpha')"

# --- T7: a crashing tool exits 1, is reported, and is NOT re-run --------------
reset_stub
printf 'FILE: x.php\nFatal error: Allowed memory size of 536870912 bytes exhausted in Foo.php on line 1\n' \
    > "$STUB_DIR/run-output.txt"
echo 255 > "$STUB_DIR/run-exit"
crash_out="$(run_checks alpha 2>&1)"
crash_rc=$?
assert_eq "T7.1 crash exits 1" "1" "$crash_rc"
assert_contains "T7.2 crash is named" "crashed" "$crash_out"
assert_contains "T7.3 remediation hint" "MATRIX_PHPCS_MEMORY_LIMIT" "$crash_out"
assert_contains "T7.4 incomplete scan is stated" "incomplete" "$crash_out"
# The crashing root must be invoked once: the old run_tool_container fell
# through to the direct-docker fallback and ran the tool a second time.
assert_eq "T7.5 crashing root not re-run" "1" "$(run_count)"
assert_eq "T7.6 no direct-docker fallback" "0" "$([[ -f "$STUB_DIR/run.txt" ]] && wc -l < "$STUB_DIR/run.txt" | tr -d ' ' || echo 0)"

# --- T8: partial report still contains the crash output ----------------------
crash_report="$(ls -1t "$SANDBOX/logs/phpcs"/*alpha* | head -1)"
assert_eq "T8.1 crash output kept for diagnostics" "1" "$(grep -c 'Allowed memory size' "$crash_report")"

# --- T9: unknown site is rejected before any tool call -----------------------
reset_stub
missing_out="$(run_checks no_such_site 2>&1)"
missing_rc=$?
assert_eq "T9.1 missing site exits 1" "1" "$missing_rc"
assert_contains "T9.2 missing site message" "not found" "$missing_out"
assert_eq "T9.3 no tool call for missing site" "0" "$(run_count)"

# --- T10: non-wp stack is gated ---------------------------------------------
mkdir -p "$SANDBOX/wp_lara"
printf 'laravel\n' > "$SANDBOX/wp_lara/.matrix-type"
reset_stub
lara_out="$(run_checks lara 2>&1)"
lara_rc=$?
assert_eq "T10.1 non-wp site exits 1" "1" "$lara_rc"
assert_contains "T10.2 gate message" "requires wp" "$lara_out"
assert_eq "T10.3 no tool call for non-wp site" "0" "$(run_count)"

echo ""
echo "check tests: $PASS passed, $FAIL failed"
[[ "$FAIL" -eq 0 ]]
