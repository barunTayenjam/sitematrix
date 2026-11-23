#!/bin/bash
# TDD test for the free-trial AJAX seam.
#
# Seam: admin-ajax.php with external SQL Server + Marketo calls stubbed behind
# filters. The test asserts the AJAX response and the WordPress-side rows that
# the flow is responsible for creating. No external service is contacted.

set -uo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SITE="al-2026"
BASE="http://192.168.31.207:8213"
MU="$REPO_ROOT/wp_$SITE/wp-content/mu-plugins/altryx-test-stubs.php"
EMAIL="tdd-freetrial-$(date +%s)@example.test"

PASS=0
FAIL=0
pass() { PASS=$((PASS + 1)); echo "PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "FAIL: $1"; }

cleanup() {
  rm -f "$MU"
  "$REPO_ROOT/matrix" wp "$SITE" db query \
    "DELETE FROM wp_sales_company WHERE Name='TDD Test Company'; DELETE FROM wp_sales_user WHERE Email='$EMAIL';" \
    >/dev/null 2>&1 || true
}
trap cleanup EXIT

mkdir -p "$(dirname "$MU")"
cat > "$MU" <<'PHP'
<?php
// TDD-only stubs. Loaded only while the test file exists.
add_filter( 'altryx_sqlserver_stub', '__return_true' );
add_filter( 'altryx_marketo_stub', '__return_true' );
PHP

RESPONSE="$(curl -s -X POST "$BASE/wp-admin/admin-ajax.php" \
  --data-urlencode 'action=free_trial_form_submit' \
  --data-urlencode 'data[fullname]=TDD Tester' \
  --data-urlencode "data[Email]=$EMAIL" \
  --data-urlencode 'data[Phone]=5550001234' \
  --data-urlencode 'data[websiteLink]=https://example.test' \
  --data-urlencode 'data[Company]=TDD Test Company' \
  --data-urlencode 'data[commentCapture]=TDD' \
  --data-urlencode 'data[connectorDemo]=Test Connector' \
  --data-urlencode 'data[new_connectorlicensekey]=TDD-LICENSE' \
  --data-urlencode 'data[new_connectortrialexpirationdate]=2026-12-31')"

if [[ "$RESPONSE" == *created* ]]; then
  pass " AJAX response reports 'created' (stubbed Marketo)"
else
  fail " AJAX response = '$RESPONSE' (expected 'created')"
fi

check_rows() {
  local table="$1" where="$2" label="$3"
  local n
  n="$("$REPO_ROOT/matrix" wp "$SITE" db query \
    "SELECT COUNT(*) FROM $table WHERE $where;" 2>&1 | grep -E '^[0-9]+$' | tail -1)"
  if [[ "$n" =~ ^[1-9][0-9]*$ ]]; then
    pass "$label"
  else
    fail "$label (count=$n)"
  fi
}

check_rows wp_sales_company "Name='TDD Test Company'" 'WordPress company row created'
check_rows wp_sales_user "Email='$EMAIL'" 'WordPress user row created'

echo "---"
echo "Passed: $PASS  Failed: $FAIL"
[[ "$FAIL" -eq 0 ]]
