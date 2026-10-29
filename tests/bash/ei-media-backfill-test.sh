#!/bin/bash
# Offline tests for scripts/ei-media-backfill.sh
#
# Contract:
#   an existing non-empty file is skipped and left untouched
#   a missing file that exists on the source host is downloaded
#   a missing file that 404s is recorded as a failure, with no partial file left
#   every outcome is written to the resumable log
#
# The source host is a file:// URL, so this runs with no network.

set -uo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SANDBOX="$(mktemp -d)"
trap 'rm -rf "$SANDBOX"' EXIT

SRC="$SANDBOX/src/wp-content/uploads"
mkdir -p "$SRC/2014/09" "$SANDBOX/uploads/2014/09" "$SANDBOX/uploads/2014/10"
echo "remote data" > "$SRC/2014/09/remote-feature.jpg"
echo "old data" > "$SANDBOX/uploads/2014/09/data-pic-1.jpg"

printf '1\t2014/09/data-pic-1.jpg\n2\t2014/09/remote-feature.jpg\n3\t2014/10/missing.jpg\n' > "$SANDBOX/list.txt"

UPLOADS="$SANDBOX/uploads" \
EI_MEDIA_HOST="file://$SANDBOX/src" \
EI_BACKFILL_LOG="$SANDBOX/backfill.log" \
    bash "$REPO_ROOT/scripts/ei-media-backfill.sh" "$SANDBOX/list.txt" > "$SANDBOX/out"

PASS=0
FAIL=0
pass() { PASS=$((PASS + 1)); }
fail() { FAIL=$((FAIL + 1)); echo "FAIL: $1"; }

[ "$(cat "$SANDBOX/uploads/2014/09/data-pic-1.jpg")" = "old data" ] \
    && pass || fail "an existing file must not be overwritten"

[ -s "$SANDBOX/uploads/2014/09/remote-feature.jpg" ] \
    && pass || fail "a missing file must be downloaded"

[ ! -e "$SANDBOX/uploads/2014/10/missing.jpg" ] \
    && pass || fail "a failed download must not leave a partial file"

grep -q $'1\t2014/09/data-pic-1.jpg\tskip' "$SANDBOX/backfill.log" \
    && pass || fail "log records the skip"

grep -q $'2\t2014/09/remote-feature.jpg\tok' "$SANDBOX/backfill.log" \
    && pass || fail "log records the download"

grep -q $'3\t2014/10/missing.jpg\tfail' "$SANDBOX/backfill.log" \
    && pass || fail "log records the failure"

echo "backfill: $PASS passed, $FAIL failed"
[ "$FAIL" -eq 0 ]
