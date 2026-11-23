#!/bin/bash
# TDD tests for the al-2026 WooCommerce template-override upgrade.
#
# Seam B (WC system status): WooCommerce's own System Status report is a public
# interface. An active theme must not ship overrides older than the WC core
# templates. This test reads that report via WP-CLI and fails while any
# override is outdated.
#
# Seam A (rendered pages): added in a later cycle.

set -uo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SITE="al-2026"
MATRIX="$REPO_ROOT/matrix"

PASS=0
FAIL=0
pass() { PASS=$((PASS + 1)); echo "PASS: $1"; }
fail() { FAIL=$((FAIL + 1)); echo "FAIL: $1"; }

# Query WooCommerce System Status through its public controller and emit a
# machine-readable count. This is the observation seam, not implementation.
read_wc_status() {
  "$MATRIX" wp "$SITE" eval '
    require_once WC_ABSPATH . "includes/admin/class-wc-admin-status.php";
    require_once WC_ABSPATH . "includes/rest-api/Controllers/Version2/class-wc-rest-system-status-v2-controller.php";
    delete_transient( "wc_system_status_theme_info" );
    $c = new WC_REST_System_Status_V2_Controller();
    $info = $c->get_theme_info();
    $n = 0;
    foreach ( $info["overrides"] as $o ) {
      if ( "" !== $o["version"] && "" !== $o["core_version"] && version_compare( $o["version"], $o["core_version"], "<" ) ) {
        $n++;
        echo "OUTDATED=" . $o["file"] . "\n";
      }
    }
    echo "OUTDATED_COUNT=" . $n . "\n";
  ' 2>/dev/null
}

STATUS="$(read_wc_status)"

COUNT="$(printf '%s\n' "$STATUS" | sed -n 's/^OUTDATED_COUNT=//p' | tail -1)"

if [[ -z "$COUNT" ]]; then
  fail "WC system status report is queryable (got no OUTDATED_COUNT)"
  printf '%s\n' "$STATUS" | tail -5
elif [[ "$COUNT" -eq 0 ]]; then
  pass "no outdated WooCommerce template overrides (count=0)"
else
  fail "WC reports $COUNT outdated template override(s)"
  printf '%s\n' "$STATUS" | grep '^OUTDATED=' | sed 's/^/    /'
fi

echo "---"
echo "Passed: $PASS  Failed: $FAIL"
[[ "$FAIL" -eq 0 ]]
