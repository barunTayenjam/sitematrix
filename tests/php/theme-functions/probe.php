<?php
/**
 * Loads the theme's functions.php inside the stubbed WordPress environment and
 * prints the recorded hook/shortcode state as JSON.
 *
 * Environment:
 *   TF_IS_ADMIN=1  simulate an admin request
 *   TF_QUIET=1     suppress PHP notices in the JSON output
 */

error_reporting( E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING );

require __DIR__ . '/wp-stubs.php';

$theme_dir = dirname( __DIR__, 3 ) . '/wp_gz/wp-content/themes/dp_evolve-Child';

// functions.php includes framework files with paths relative to the theme.
$cwd = getcwd();
chdir( $theme_dir );

ob_start();
$error = null;
try {
	require $theme_dir . '/functions.php';
} catch ( Throwable $e ) {
	$error = $e->getMessage();
}
$output = ob_get_clean();

chdir( $cwd );

$result = array(
	'error'      => $error,
	'is_admin'   => (bool) $GLOBALS['tf']['is_admin'],
	'actions'    => $GLOBALS['tf']['actions'],
	'filters'    => $GLOBALS['tf']['filters'],
	'shortcodes' => $GLOBALS['tf']['shortcodes'],
	'calls'      => $GLOBALS['tf']['calls'],
	'output_len' => strlen( $output ),
);

echo json_encode( $result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
