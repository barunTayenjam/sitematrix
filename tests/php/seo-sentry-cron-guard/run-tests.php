<?php
/**
 * Regression suite for wp-content/mu-plugins/seo-sentry-cron-guard.php.
 *
 * The guard exists because SEO Sentry's admin_init catch-up runs a ~20s
 * check chunk synchronously when no tick is scheduled — and WP-Cron
 * unschedules the tick before running it, so that window is hit routinely.
 *
 * Usage: php tests/php/seo-sentry-cron-guard/run-tests.php
 */

$GLOBALS['t'] = array( 'pass' => 0, 'fail' => 0 );

function t_ok( $m ) {
	$GLOBALS['t']['pass']++;
	echo "  ok   - {$m}\n";
}

function t_fail( $m ) {
	$GLOBALS['t']['fail']++;
	echo "  FAIL - {$m}\n";
}

function t_same( $expected, $actual, $m ) {
	if ( $expected === $actual ) {
		t_ok( $m );
	} else {
		t_fail( $m . ' (expected ' . var_export( $expected, true ) . ', got ' . var_export( $actual, true ) . ')' );
	}
}

// ---------------------------------------------------------------- WP stubs

$GLOBALS['wp'] = array(
	'actions'   => array(),
	'options'   => array(),
	'next_tick' => false,
	'scheduled' => array(),
	'spawned'   => 0,
);

function add_action( $hook, $cb, $priority = 10 ) {
	$GLOBALS['wp']['actions'][ $hook ][ $priority ] = $cb;
}

function get_option( $name, $default = false ) {
	return array_key_exists( $name, $GLOBALS['wp']['options'] ) ? $GLOBALS['wp']['options'][ $name ] : $default;
}

function wp_next_scheduled( $hook ) {
	return $GLOBALS['wp']['next_tick'];
}

function wp_schedule_single_event( $timestamp, $hook, $args = array() ) {
	$GLOBALS['wp']['scheduled'][] = array( 'at' => $timestamp, 'hook' => $hook );
	return true;
}

function spawn_cron() {
	$GLOBALS['wp']['spawned']++;
}

function t_reset( $queue, $next_tick ) {
	$GLOBALS['wp']['options']   = array();
	$GLOBALS['wp']['scheduled'] = array();
	$GLOBALS['wp']['spawned']   = 0;
	$GLOBALS['wp']['next_tick'] = $next_tick;

	if ( null !== $queue ) {
		$GLOBALS['wp']['options']['seo_sentry_run_queue'] = $queue;
	}
}

function t_guard() {
	return $GLOBALS['wp']['actions']['admin_init'][9];
}

// ---------------------------------------------------------------- load guard

$guard = dirname( __DIR__, 3 ) . '/wp_gz/wp-content/mu-plugins/seo-sentry-cron-guard.php';
if ( ! file_exists( $guard ) ) {
	fwrite( STDERR, "Guard file not found: {$guard}\n" );
	exit( 1 );
}
require $guard;

echo "seo-sentry cron guard\n";

// 1. Registered before the plugin's catch-up (priority 10).
echo "\nregistration\n";
t_same( true, isset( $GLOBALS['wp']['actions']['admin_init'][9] ), 'hooked on admin_init' );
t_same( 9, array_key_first( $GLOBALS['wp']['actions']['admin_init'] ), 'runs at priority 9, before catch-up' );

// 2. No queue: nothing to do.
echo "\nno queue\n";
t_reset( null, false );
t_guard()();
t_same( 0, count( $GLOBALS['wp']['scheduled'] ), 'schedules nothing' );
t_same( 0, $GLOBALS['wp']['spawned'], 'does not spawn cron' );

// 3. Queue but the tick is already scheduled: leave it alone.
echo "\ntick already scheduled\n";
t_reset( array( 'started_at' => time() - 600, 'pending_ids' => array( 1, 2 ) ), time() + 10 );
t_guard()();
t_same( 0, count( $GLOBALS['wp']['scheduled'] ), 'does not double-schedule' );
t_same( 0, $GLOBALS['wp']['spawned'], 'does not spawn cron' );

// 4. The stall condition: queue pending, no tick scheduled.
echo "\nstalled queue (the 20s admin case)\n";
t_reset( array( 'started_at' => time() - 600, 'pending_ids' => array( 1, 2 ) ), false );
$before = time();
t_guard()();

t_same( 1, count( $GLOBALS['wp']['scheduled'] ), 'schedules exactly one background tick' );
$event = $GLOBALS['wp']['scheduled'][0] ?? array();
t_same( 'seo_sentry_run_tick', $event['hook'] ?? null, 'schedules the plugin tick hook' );
t_same( true, ( $event['at'] ?? 0 ) >= $before + 25, 'defers past the running chunk (~20s)' );
t_same( 1, $GLOBALS['wp']['spawned'], 'spawns cron so the tick runs off-request' );

// 5. Queue entries without started_at are ignored (plugin would ignore them too).
echo "\nincomplete queue\n";
t_reset( array( 'pending_ids' => array( 1 ) ), false );
t_guard()();
t_same( 0, count( $GLOBALS['wp']['scheduled'] ), 'ignores a queue with no started_at' );

echo "\n" . str_repeat( '-', 60 ) . "\n";
printf( "%d passed, %d failed\n", $GLOBALS['t']['pass'], $GLOBALS['t']['fail'] );
exit( $GLOBALS['t']['fail'] === 0 ? 0 : 1 );
