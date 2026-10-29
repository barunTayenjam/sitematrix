<?php
/**
 * `wp ei migrate-products [--post-id=<id>] [--dry-run]`
 *
 * Converts legacy product rows into the field model. Runs are idempotent: a
 * product whose stored fields already match the mapping is reported as
 * unchanged and not written, so the command can be re-run after a partial
 * failure without touching finished work.
 *
 * The plan is computed for both modes. `--dry-run` only skips the writes, which
 * is what makes the dry run trustworthy — it is the same code path.
 *
 * @package Ei\Core
 */

declare( strict_types = 1 );

namespace Ei\Core\Cli;

use Ei\Core\Migration\Product_Migrator;

require_once __DIR__ . '/../migration/class-product-migrator.php';

final class Migrate_Command {

	public static function register(): void {
		\WP_CLI::add_command( 'ei migrate-products', array( self::class, 'command' ) );
	}

	/**
	 * Migrate legacy Visual Composer products into structured fields.
	 *
	 * ## OPTIONS
	 *
	 * [--post-id=<id>]
	 * : Migrate a single product.
	 *
	 * [--dry-run]
	 * : Report what would change without writing anything.
	 *
	 * [--format=<format>]
	 * : table (default) or json.
	 *
	 * ## EXAMPLES
	 *
	 *     wp ei migrate-products --dry-run
	 *     wp ei migrate-products --post-id=2760
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Flags.
	 */
	public static function command( array $args = array(), array $assoc_args = array() ): void {
		$report = self::run( $assoc_args );

		if ( 'json' === ( $assoc_args['format'] ?? '' ) ) {
			\WP_CLI::line( (string) wp_json_encode( $report ) );
			return;
		}

		foreach ( $report['items'] as $item ) {
			$state = $item['changed'] ? 'would migrate' : 'unchanged';
			if ( ! $report['dry_run'] && $item['changed'] ) {
				$state = 'migrated';
			}
			\WP_CLI::line( sprintf( '%s  %-8d %s', $state, $item['id'], $item['title'] ) );
		}

		$summary = sprintf(
			'%d scanned, %d %s, %d unchanged',
			$report['scanned'],
			$report['changed'],
			$report['dry_run'] ? 'to migrate' : 'migrated',
			$report['unchanged']
		);

		if ( $report['dry_run'] ) {
			\WP_CLI::log( 'Dry run: nothing was written. ' . $summary );
		} else {
			\WP_CLI::success( $summary );
		}
	}

	/**
	 * Apply the migration, with no WordPress side effects beyond the writes.
	 *
	 * @return array{scanned: int, changed: int, unchanged: int, dry_run: bool, items: array<int, array{id: int, title: string, changed: bool}>}
	 */
	public static function apply(): array {
		return self::run( array() );
	}

	/**
	 * @param array<string, mixed> $options
	 * @return array{scanned: int, changed: int, unchanged: int, dry_run: bool, items: array<int, array{id: int, title: string, changed: bool}>}
	 */
	public static function run( array $options = array() ): array {
		$dry_run = ! empty( $options['dry-run'] );
		$force   = ! empty( $options['force'] );
		$post_id = isset( $options['post-id'] ) ? (int) $options['post-id'] : 0;

		$report = array(
			'scanned'   => 0,
			'changed'   => 0,
			'unchanged' => 0,
			'dry_run'   => $dry_run,
			'items'     => array(),
		);

		foreach ( self::products( $post_id ) as $post ) {
			$report['scanned']++;

			$id         = (int) $post->ID;
			$is_migrated = self::is_migrated( $id, $post );

			// A migrated product is never re-derived from its own output. The
			// title it now carries has had the model number split off, so
			// mapping it again would report a different payload and blank the
			// model field. Only an explicit --force rebuilds from the snapshot.
			if ( ! $force && $is_migrated ) {
				$report['unchanged']++;
				$report['items'][] = array(
					'id'      => $id,
					'title'   => (string) $post->post_title,
					'changed' => false,
				);
				continue;
			}

			$mapped = self::map_post( $post );

			// A forced re-run still only writes when the payload actually
			// differs, so the report keeps meaning "this product changed".
			$changed = ! $is_migrated || self::differs( $id, $post, $mapped );

			$report['items'][] = array(
				'id'      => $id,
				'title'   => $mapped['post_title'],
				'changed' => $changed,
			);

			if ( ! $changed ) {
				$report['unchanged']++;
				continue;
			}

			$report['changed']++;

			if ( ! $dry_run ) {
				self::write( $id, $post, $mapped );
			}
		}

		return $report;
	}

	/**
	 * A product counts as migrated once its body is empty and the original
	 * content has been snapshotted — the pair that can only exist after a
	 * successful write.
	 */
	private static function is_migrated( int $post_id, object $post ): bool {
		if ( '' !== trim( (string) $post->post_content ) ) {
			return false;
		}

		$legacy = get_post_meta( $post_id, '_ei_legacy', true );

		return is_array( $legacy ) && isset( $legacy['content'] );
	}

	/**
	 * Does the live row already hold this payload?
	 *
	 * @param array<string, mixed> $mapped
	 */
	private static function differs( int $post_id, object $post, array $mapped ): bool {
		if ( trim( (string) $post->post_content ) !== trim( (string) $mapped['post_content'] ) ) {
			return true;
		}

		foreach ( $mapped['fields'] as $name => $expected ) {
			if ( self::normalise( get_field( $name, $post_id ) ) !== self::normalise( $expected ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * ACF returns numbers and booleans in the field's type while the mapping
	 * produces PHP natives, so compare on normalised scalars.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	private static function normalise( $value ) {
		if ( is_array( $value ) ) {
			$normalised = array_map( array( self::class, 'normalise' ), $value );
			ksort( $normalised );
			return $normalised;
		}

		if ( is_bool( $value ) ) {
			return $value ? '1' : '0';
		}

		return is_scalar( $value ) ? (string) $value : '';
	}

	/**
	 * @return object[]
	 */
	private static function products( int $post_id ): array {
		$args = array(
			'post_type'   => 'product',
			'post_status' => 'any',
			'numberposts' => -1,
			'orderby'     => 'ID',
			'order'       => 'ASC',
		);

		if ( $post_id > 0 ) {
			$args['p'] = $post_id;
		}

		return (array) get_posts( $args );
	}

	/**
	 * Build the mapping for a stored post.
	 *
	 * Once a product has been migrated, the mapping is rebuilt from the legacy
	 * snapshot rather than the live row: the live title has had its model number
	 * split off, so re-reading it would produce a different answer every run.
	 *
	 * @return array<string, mixed>
	 */
	private static function map_post( object $post ): array {
		$post_id = (int) $post->ID;
		$legacy  = get_post_meta( $post_id, '_ei_legacy', true );
		$legacy  = is_array( $legacy ) && isset( $legacy['content'] ) ? $legacy : null;

		$meta = is_array( $legacy['meta'] ?? null ) ? $legacy['meta'] : array(
			'pdf_file_upload'    => (string) get_post_meta( $post_id, 'pdf_file_upload', true ),
			'thumbnail_id'       => (int) get_post_thumbnail_id( $post_id ),
			'show_on_home'       => (string) get_post_meta( $post_id, 'show_on_home', true ),
			'order_product_home' => (string) get_post_meta( $post_id, 'order_product_home', true ),
		);

		return Product_Migrator::map(
			array(
				'id'      => $post_id,
				'title'   => (string) ( $legacy['title'] ?? $post->post_title ),
				'slug'    => (string) ( $post->post_name ?? '' ),
				'excerpt' => (string) ( $post->post_excerpt ?? '' ),
				'content' => (string) ( $legacy['content'] ?? $post->post_content ),
				'meta'    => $meta,
			)
		);
	}

	/**
	 * @param array<string, mixed> $mapped
	 */
	private static function write( int $post_id, object $post, array $mapped ): void {
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_title'   => $mapped['post_title'],
				'post_content' => $mapped['post_content'],
			)
		);

		update_post_meta( $post_id, '_ei_legacy', $mapped['legacy'] );

		if ( '' !== $mapped['brochure_url'] ) {
			update_post_meta( $post_id, '_ei_brochure_url', $mapped['brochure_url'] );
		}

		foreach ( $mapped['fields'] as $name => $value ) {
			update_field( $name, $value, $post_id );
		}
	}
}
