<?php
/**
 * Plugin Name:       Electronics India — Core
 * Description:       Content model, field definitions and migrations for the Electronics India site. No admin UI, no presentation.
 * Version:           0.1.0
 * Requires PHP:      8.1
 * Requires at least: 6.5
 * Author:            SiteMatrix
 * Text Domain:       ei
 *
 * @package Ei\Core
 */

declare( strict_types = 1 );

namespace Ei\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const VERSION = '0.1.0';

define( 'EI_CORE_FILE', __FILE__ );
define( 'EI_CORE_PATH', plugin_dir_path( __FILE__ ) );

require_once EI_CORE_PATH . 'inc/post-types.php';
require_once EI_CORE_PATH . 'inc/taxonomies.php';
require_once EI_CORE_PATH . 'inc/acf.php';

Post_Types::boot();
Taxonomies::boot();
Acf::boot();

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once EI_CORE_PATH . 'inc/cli/class-migrate-command.php';

	Cli\Migrate_Command::register();
}
