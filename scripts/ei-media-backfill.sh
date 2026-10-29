#!/bin/bash
# Resumable media backfill for the Electronics India revamp site.
#
# Downloads missing attachment files from the legacy host so the new site never
# issues cross-origin requests for images/PDFs. Every phase of the run is
# logged to a resumable CSV so it can be interrupted and re-run safely:
#
#   <attachment-id>\t<relative-path>\t<ok|skip|fail>
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
SITE="${SITE:-ei-revamp}"
UPLOADS="${UPLOADS:-$ROOT/wp_$SITE/wp-content/uploads}"
HOST="${EI_MEDIA_HOST:-https://electronicsindia.co.in}"
LOG="${EI_BACKFILL_LOG:-$ROOT/backups/ei-media-backfill.log}"
LIST="${1:-/dev/stdin}"

mkdir -p "$UPLOADS" "$(dirname "$LOG")"

ok=0
skip=0
fail=0
while IFS=$'\t' read -r id rel || [ -n "${rel:-}" ]; do
    [ -z "${rel:-}" ] && continue

    dest="$UPLOADS/$rel"
    if [ -s "$dest" ]; then
        skip=$((skip + 1))
        printf '%s\t%s\tskip\n' "$id" "$rel" >> "$LOG"
        continue
    fi

    url="$HOST/wp-content/uploads/$rel"
    mkdir -p "$(dirname "$dest")"
    if curl -fsSL --max-time 60 -o "$dest" "$url" 2>/dev/null; then
        ok=$((ok + 1))
        printf '%s\t%s\tok\n' "$id" "$rel" >> "$LOG"
    else
        fail=$((fail + 1))
        rm -f "$dest"
        printf '%s\t%s\tfail\n' "$id" "$rel" >> "$LOG"
    fi
done < "$LIST"

printf 'backfill: %d ok, %d skipped, %d failed\n' "$ok" "$skip" "$fail"
