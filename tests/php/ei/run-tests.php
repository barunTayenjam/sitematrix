<?php
/**
 * Runner for the EI (Electronics India) suite.
 *
 * Usage: php tests/php/ei/run-tests.php
 *        php tests/php/ei/run-tests.php post-types   # only test-post-types.php
 */

require __DIR__ . '/stubs.php';
require __DIR__ . '/assert.php';
require __DIR__ . '/helpers.php';

$filter = $argv[1] ?? null;
$files  = glob( __DIR__ . '/test-*.php' );
sort( $files );

foreach ( $files as $file ) {
	$name = basename( $file, '.php' );
	if ( $filter && false === strpos( $name, $filter ) ) {
		continue;
	}

	echo "\n{$name}\n";
	ei_reset();
	require $file;
}

exit( ei_summary() );
