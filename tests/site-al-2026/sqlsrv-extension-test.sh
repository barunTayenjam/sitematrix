#!/bin/bash
# TDD test for seam B: the PHP 8.3 runtime must provide the SQL Server
# extensions that salesadmin/includes/dbconnect.php calls.

set -uo pipefail

if docker exec wp_al-2026 php -r 'exit( extension_loaded( "sqlsrv" ) && extension_loaded( "pdo_sqlsrv" ) ? 0 : 1 );' 2>/dev/null; then
  echo "PASS: sqlsrv and pdo_sqlsrv are loaded on PHP 8.3"
  exit 0
else
  echo "FAIL: sqlsrv and/or pdo_sqlsrv not loaded on PHP 8.3"
  docker exec wp_al-2026 php -m 2>/dev/null | grep -i sqlsrv || true
  exit 1
fi
