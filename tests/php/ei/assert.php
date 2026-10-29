<?php
/**
 * Minimal assertion helpers for the EI (Electronics India) test suite.
 *
 * House style: no PHPUnit, no database, no WordPress bootstrap — the suite runs
 * with plain `php tests/php/ei/run-tests.php` and must stay runnable offline.
 */

$GLOBALS['ei_suite'] = array(
	'pass'     => 0,
	'fail'     => 0,
	'failures' => array(),
	'current'  => '',
);

/**
 * Register and immediately run one test. A test is a named closure; any
 * assertion failure inside it fails that test and execution continues to the
 * next one, so a run always reports the full picture.
 */
function ei_test( $name, callable $fn ) {
	$GLOBALS['ei_suite']['current'] = $name;

	try {
		$fn();
		$GLOBALS['ei_suite']['pass']++;
		echo "  ok    {$name}\n";
	} catch ( Throwable $e ) {
		$GLOBALS['ei_suite']['fail']++;
		$GLOBALS['ei_suite']['failures'][] = array( $name, $e->getMessage() );
		echo "  FAIL  {$name}\n";
		echo "        {$e->getMessage()}\n";
	}
}

function ei_fail( $message ) {
	throw new RuntimeException( $message );
}

function ei_assert_true( $condition, $message ) {
	if ( ! $condition ) {
		ei_fail( $message );
	}
}

function ei_assert_false( $condition, $message ) {
	ei_assert_true( ! $condition, $message );
}

function ei_assert_equals( $expected, $actual, $message ) {
	if ( $expected !== $actual ) {
		ei_fail(
			$message
			. "\n        expected: " . ei_dump( $expected )
			. "\n        actual:   " . ei_dump( $actual )
		);
	}
}

function ei_assert_count( $expected, $actual, $message ) {
	ei_assert_equals( $expected, count( $actual ), $message . "\n        items: " . ei_dump( $actual ) );
}

function ei_assert_has_key( $key, $array, $message ) {
	if ( ! is_array( $array ) || ! array_key_exists( $key, $array ) ) {
		ei_fail( $message . "\n        missing key: {$key}\n        available: " . ei_dump( array_keys( (array) $array ) ) );
	}
}

function ei_assert_contains( $needle, $haystack, $message ) {
	if ( false === strpos( (string) $haystack, (string) $needle ) ) {
		ei_fail( $message . "\n        looked for: {$needle}" );
	}
}

function ei_dump( $value ) {
	$json = json_encode( $value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR );
	return false === $json ? gettype( $value ) : $json;
}

/** Print the summary and return a shell exit code. */
function ei_summary() {
	$suite = $GLOBALS['ei_suite'];

	echo "\n" . str_repeat( '-', 60 ) . "\n";
	printf( "%d passed, %d failed\n", $suite['pass'], $suite['fail'] );

	if ( $suite['fail'] ) {
		echo "\nFailures:\n";
		foreach ( $suite['failures'] as $failure ) {
			echo "  - {$failure[0]}\n    {$failure[1]}\n";
		}
		return 1;
	}

	return 0;
}
