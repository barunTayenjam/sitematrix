<?php
/**
 * TDD tests for the salesadmin SQL Server configuration seam.
 *
 * Seam A: configuration is a public function that reads env vars and fails
 * clearly when they are missing. No credential literal may remain in source.
 *
 * Run with:
 *   docker exec wp_al-2026 php /var/www/html/tests/site-al-2026/salesadmin-config-test.php
 */

$root       = dirname( dirname( __DIR__ ) ); // repository root
$configFile = $root . '/wp_al-2026/salesadmin/includes/config.php';
$dbFile     = $root . '/wp_al-2026/salesadmin/includes/dbconnect.php';

$pass = 0;
$fail = 0;
function check( $ok, $label ) {
	global $pass, $fail;
	if ( $ok ) {
		$pass++;
		echo "PASS: $label\n";
	} else {
		$fail++;
		echo "FAIL: $label\n";
	}
}

if ( ! file_exists( $configFile ) ) {
	echo "FAIL: config.php exists (seam not implemented yet)\n";
	echo "---\nPassed: 0  Failed: 1\n";
	exit( 1 );
}
require_once $configFile;

// 1. With env vars set, the accessor returns them.
putenv( 'ALTRYX_SQLSERVER_HOST=test-host' );
putenv( 'ALTRYX_SQLSERVER_DB=test-db' );
putenv( 'ALTRYX_SQLSERVER_USER=test-user' );
putenv( 'ALTRYX_SQLSERVER_PASSWORD=test-password' );
$config = altryx_sqlserver_config();
check( $config['host'] === 'test-host', 'config reads host from env' );
check( $config['database'] === 'test-db', 'config reads database from env' );
check( $config['user'] === 'test-user', 'config reads user from env' );
check( $config['password'] === 'test-password', 'config reads password from env' );

// 2. With env vars unset, it fails clearly rather than falling back to a secret.
foreach ( array( 'HOST', 'DB', 'USER', 'PASSWORD' ) as $key ) {
	putenv( 'ALTRYX_SQLSERVER_' . $key );
}
$threw = false;
$message = '';
try {
	altryx_sqlserver_config();
} catch ( RuntimeException $e ) {
	$threw = true;
	$message = $e->getMessage();
}
check( $threw, 'missing env vars raise RuntimeException' );
check( stripos( $message, 'ALTRYX_SQLSERVER' ) !== false, 'error message names the missing env vars' );

// 3. No hardcoded credential literals remain in the connection file.
$source = file_get_contents( $dbFile );
check(
	! preg_match( '/["\']UID["\']\s*=>\s*["\'][^"\']+["\']/', $source ),
	'no hardcoded UID literal in dbconnect.php'
);
check(
	! preg_match( '/["\']PWD["\']\s*=>\s*["\'][^"\']+["\']/', $source ),
	'no hardcoded PWD literal in dbconnect.php'
);
check(
	strpos( $source, 'altryx_sqlserver_config()' ) !== false,
	'dbconnect.php obtains credentials through the config seam'
);

// 4. Marketo credentials also come from env, not source literals.
putenv( 'ALTRYX_MARKETO_HOST=https://marketo.test' );
putenv( 'ALTRYX_MARKETO_CLIENT_ID=test-client-id' );
putenv( 'ALTRYX_MARKETO_CLIENT_SECRET=test-client-secret' );
$marketo = altryx_marketo_config();
check( $marketo['host'] === 'https://marketo.test', 'Marketo host read from env' );
check( $marketo['client_id'] === 'test-client-id', 'Marketo client id read from env' );
check( $marketo['client_secret'] === 'test-client-secret', 'Marketo client secret read from env' );

$marketoSource = file_get_contents( $root . '/wp_al-2026/salesadmin/includes/customobjectupdate.php' );
check(
	! preg_match( '/\$this->clientSecret\s*=\s*["\'][^"\']+["\']/', $marketoSource ),
	'no hardcoded Marketo client secret in customobjectupdate.php'
);

echo "---\nPassed: $pass  Failed: $fail\n";
exit( $fail === 0 ? 0 : 1 );
