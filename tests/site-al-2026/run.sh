#!/bin/bash
# Run all al-2026 upgrade regression tests.
set -uo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
RC=0
for t in "$DIR"/*-test.sh; do
  echo "===== $(basename "$t") ====="
  bash "$t" || RC=1
  echo
done

echo "===== salesadmin-config-test.php ====="
REPO="$(cd "$DIR/../.." && pwd)"
docker run --rm --entrypoint php -v "$REPO":/var/www/html -w /var/www/html \
  wordpress:cli /var/www/html/tests/site-al-2026/salesadmin-config-test.php || RC=1
echo
exit "$RC"
