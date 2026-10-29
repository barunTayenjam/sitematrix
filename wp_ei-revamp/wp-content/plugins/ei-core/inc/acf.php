<?php
/**
 * ACF wiring.
 *
 * Field groups live in acf-json/ inside this plugin and are loaded from disk,
 * never from the database. That keeps the content model reviewable in a diff,
 * identical on every environment, and impossible to "lose" with a database
 * import — which is exactly how the old site accumulated 178 theme options and
 * a page-builder dependency.
 *
 * @package Ei\Core
 */

declare( strict_types = 1 );

namespace Ei\Core;

final class Acf {

	public static function boot(): void {
		add_filter( 'acf/settings/load_json', array( self::class, 'json_paths' ) );
		add_filter( 'acf/settings/save_json', array( self::class, 'save_path' ) );
	}

	/**
	 * Search our acf-json directory last, so a theme or plugin can still supply
	 * a subgroup without being overridden by us.
	 *
	 * @param string[] $paths
	 * @return string[]
	 */
	public static function json_paths( array $paths ): array {
		$paths[] = self::json_dir();
		return $paths;
	}

	public static function save_path(): string {
		return self::json_dir();
	}

	public static function json_dir(): string {
		return EI_CORE_PATH . 'acf-json';
	}
}
