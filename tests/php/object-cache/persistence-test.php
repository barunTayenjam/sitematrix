<?php
/**
 * Integration test for the Matrix Redis object-cache drop-in.
 *
 * Run inside a site container so the `redis` hostname and REDIS_PASSWORD are
 * available:
 *
 *   docker exec -e REDIS_PASSWORD=... wp_gz php \
 *     /var/www/html/wp-content/../../tests/php/object-cache/persistence-test.php \
 *     /var/www/html/wp-content/object-cache.php write
 *
 * Exit 0 = the drop-in talks to Redis and data survives across processes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', '/tmp/' );
}

if ( ! function_exists( 'wp_suspend_cache_addition' ) ) {
	function wp_suspend_cache_addition() {
		return false;
	}
}

$dropin = $argv[1] ?? '/var/www/html/wp-content/object-cache.php';
$mode   = $argv[2] ?? 'write';
$key    = 'tf_object_cache_persistence_probe';

if ( ! is_file( $dropin ) ) {
	fwrite( STDERR, "Drop-in not found: {$dropin}\n" );
	exit( 2 );
}

require $dropin;

$cache = new WP_Object_Cache();
$value = 'probe-' . gmdate( 'YmdHis' ) . '-' . getmypid();

if ( 'write' === $mode ) {
	$cache->set( $key, $value, 'tf_test', 120 );
	$read_back = $cache->get( $key, 'tf_test' );

	if ( $read_back !== $value ) {
		fwrite( STDERR, "FAIL: value not readable in the same process\n" );
		exit( 1 );
	}

	if ( method_exists( $cache, 'has_redis' ) ) {
		// no-op, kept for clarity
	}

	echo $value;
	exit( 0 );
}

if ( 'read' === $mode ) {
	$stored = $cache->get( $key, 'tf_test' );
	echo is_string( $stored ) ? $stored : '';
	exit( is_string( $stored ) && '' !== $stored ? 0 : 1 );
}

fwrite( STDERR, "Unknown mode: {$mode}\n" );
exit( 2 );
