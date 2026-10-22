<?php
/**
 * Assertions + helpers for the theme-functions TDD suite.
 */

$GLOBALS['tf_test'] = array(
	'pass' => 0,
	'fail' => 0,
	'current' => '',
);

if ( ! defined( 'TF_REPO_ROOT' ) ) {
	define( 'TF_REPO_ROOT', dirname( __DIR__, 3 ) );
}
if ( ! defined( 'TF_THEME_ROOT' ) ) {
	define( 'TF_THEME_ROOT', TF_REPO_ROOT . '/wp_gz/wp-content/themes/dp_evolve-Child' );
}

function tf_test( $name, callable $fn ) {
	$GLOBALS['tf_test']['current'] = $name;
	try {
		$fn();
	} catch ( Throwable $e ) {
		tf_fail( 'uncaught ' . get_class( $e ) . ': ' . $e->getMessage() );
	}
}

function tf_ok( $message = '' ) {
	$GLOBALS['tf_test']['pass']++;
	echo "  ok   - {$message}\n";
}

function tf_fail( $message ) {
	$GLOBALS['tf_test']['fail']++;
	echo "  FAIL - {$message}\n";
}

function tf_assert_true( $cond, $message ) {
	if ( $cond ) {
		tf_ok( $message );
	} else {
		tf_fail( $message );
	}
}

function tf_assert_false( $cond, $message ) {
	tf_assert_true( ! $cond, $message );
}

function tf_assert_equals( $expected, $actual, $message ) {
	if ( $expected === $actual ) {
		tf_ok( $message );
	} else {
		tf_fail( $message . ' (expected ' . var_export( $expected, true ) . ', got ' . var_export( $actual, true ) . ')' );
	}
}

function tf_summary() {
	$t = $GLOBALS['tf_test'];
	echo "\n" . str_repeat( '-', 60 ) . "\n";
	printf( "%d passed, %d failed\n", $t['pass'], $t['fail'] );
	return $t['fail'] === 0 ? 0 : 1;
}

/**
 * Run probe.php in a subprocess and return the decoded result.
 */
function tf_probe( $is_admin = false ) {
	$php  = PHP_BINARY;
	$probe = __DIR__ . '/probe.php';
	$env  = array(
		'TF_IS_ADMIN' => $is_admin ? '1' : '0',
		'PATH'        => getenv( 'PATH' ),
		'HOME'        => getenv( 'HOME' ),
	);

	$descriptors = array(
		1 => array( 'pipe', 'w' ),
		2 => array( 'pipe', 'w' ),
	);

	$proc = proc_open(
		array( $php, $probe ),
		$descriptors,
		$pipes,
		null,
		$env
	);

	if ( ! is_resource( $proc ) ) {
		throw new RuntimeException( 'Unable to start probe process' );
	}

	$stdout = stream_get_contents( $pipes[1] );
	$stderr = stream_get_contents( $pipes[2] );
	fclose( $pipes[1] );
	fclose( $pipes[2] );
	$code = proc_close( $proc );

	$decoded = json_decode( $stdout, true );
	if ( ! is_array( $decoded ) ) {
		throw new RuntimeException(
			"Probe produced invalid JSON (exit {$code}).\nSTDOUT:\n{$stdout}\nSTDERR:\n{$stderr}"
		);
	}
	$decoded['_exit'] = $code;
	$decoded['_stderr'] = $stderr;

	return $decoded;
}

/**
 * Does the probe result contain a registered action for $hook?
 */
function tf_has_action( array $probe, $hook ) {
	return isset( $probe['actions'][ $hook ] ) && count( $probe['actions'][ $hook ] ) > 0;
}

/**
 * Extract the exact source of a top-level function from functions.php.
 */
function tf_function_source( $file, $name ) {
	$code   = file_get_contents( $file );
	$tokens = token_get_all( $code );
	$count  = count( $tokens );

	for ( $i = 0; $i < $count; $i++ ) {
		if ( ! is_array( $tokens[ $i ] ) || $tokens[ $i ][0] !== T_FUNCTION ) {
			continue;
		}

		$j = $i + 1;
		// Skip whitespace.
		while ( $j < $count && is_array( $tokens[ $j ] ) && $tokens[ $j ][0] === T_WHITESPACE ) {
			$j++;
		}

		if ( $j >= $count || ! is_array( $tokens[ $j ] ) || $tokens[ $j ][0] !== T_STRING ) {
			continue;
		}

		if ( $tokens[ $j ][1] !== $name ) {
			continue;
		}

		// Find the opening brace.
		$start = $j;
		while ( $start < $count && $tokens[ $start ] !== '{' ) {
			$start++;
		}

		$depth = 0;
		$end   = $start;
		for ( $k = $start; $k < $count; $k++ ) {
			if ( $tokens[ $k ] === '{' ) {
				$depth++;
			} elseif ( $tokens[ $k ] === '}' ) {
				$depth--;
				if ( $depth === 0 ) {
					$end = $k;
					break;
				}
			}
		}

		$out = '';
		for ( $k = $i; $k <= $end; $k++ ) {
			$out .= is_array( $tokens[ $k ] ) ? $tokens[ $k ][1] : $tokens[ $k ];
		}

		return $out;
	}

	return null;
}

/**
 * Extract a function from functions.php and eval it so it can be called
 * in isolation with stubs.
 */
function tf_load_function( $name ) {
	static $loaded = array();
	if ( isset( $loaded[ $name ] ) ) {
		return true;
	}
	if ( function_exists( $name ) ) {
		$loaded[ $name ] = true;
		return true;
	}
	$source = tf_function_source( TF_THEME_ROOT . '/functions.php', $name );
	if ( $source === null ) {
		throw new RuntimeException( "Function {$name}() not found in functions.php" );
	}
	eval( $source );
	$loaded[ $name ] = true;
	return true;
}

/**
 * Reset recorded get_posts / WP_Query calls.
 */
function tf_reset_calls() {
	$GLOBALS['tf']['calls'] = array();
}

/**
 * All recorded calls to a given function name.
 */
function tf_calls_to( $name ) {
	$out = array();
	foreach ( $GLOBALS['tf']['calls'] as $call ) {
		if ( $call[0] === $name ) {
			$out[] = $call[1];
		}
	}
	return $out;
}

/**
 * Fully reset PHP's session state so session tests start from zero.
 */
function tf_reset_session() {
	if ( session_status() === PHP_SESSION_ACTIVE ) {
		session_destroy();
	}
	session_id( '' );
}
