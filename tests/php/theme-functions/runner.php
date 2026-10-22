<?php
/**
 * Tiny TDD runner for the theme-functions test suite.
 *
 * Usage: php tests/php/theme-functions/run-tests.php
 */

require __DIR__ . '/wp-stubs.php';
require __DIR__ . '/assert.php';

$tests = glob( __DIR__ . '/test-*.php' );
sort( $tests );

foreach ( $tests as $test ) {
	require $test;
}

exit( tf_summary() );
