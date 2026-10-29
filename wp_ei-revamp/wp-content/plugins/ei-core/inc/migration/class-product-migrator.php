<?php
/**
 * Turns a legacy product row into the payload the field model expects.
 *
 * This is the one place where the old shape meets the new one. It is pure: it
 * takes a plain array and returns a plain array, so the migration can be
 * dry-run, diffed and asserted without a database.
 *
 * Two deliberate omissions:
 *
 *   short_description  left empty. The only prose many legacy products have is
 *                      the boilerplate "Specifications are subject to change"
 *                      note; promoting that to a card summary would be worse
 *                      than letting the theme fall back to the feature list.
 *   brochure_pdf       left empty. The legacy value is a URL on the old host,
 *                      and a file field holding a remote URL is not a value ACF
 *                      can resolve. The URL travels in `brochure_url` so the
 *                      media backfill can fetch it, attach it and set the field.
 *
 * @package Ei\Core
 */

declare( strict_types = 1 );

namespace Ei\Core\Migration;

require_once __DIR__ . '/class-product-title.php';
require_once __DIR__ . '/class-vc-parser.php';

final class Product_Migrator {

	/**
	 * @param array{
	 *     id: int,
	 *     title: string,
	 *     slug?: string,
	 *     excerpt?: string,
	 *     content: string,
	 *     meta: array<string, mixed>
	 * } $product
	 *
	 * @return array{
	 *     post_title: string,
	 *     post_content: string,
	 *     fields: array<string, mixed>,
	 *     brochure_url: string,
	 *     legacy: array{content: string, meta: array<string, mixed>}
	 * }
	 */
	public static function map( array $product ): array {
		$content = (string) ( $product['content'] ?? '' );
		$meta    = (array) ( $product['meta'] ?? array() );
		$parsed  = Vc_Parser::parse( $content );

		list( $name, $model ) = Product_Title::split( (string) ( $product['title'] ?? '' ) );

		$thumbnail = (int) ( $meta['thumbnail_id'] ?? 0 );

		return array(
			'post_title'   => $name,
			// The fields carry the content now. Keeping the builder HTML in
			// post_content would let it render again the moment a template
			// printed the_content().
			'post_content' => '',
			'fields'       => array(
				'model_number'         => $model,
				'short_description'    => '',
				'description'          => $parsed['description'],
				'features'             => self::features_rows( $parsed['features'] ),
				'specifications_table' => $parsed['specifications_table'],
				'gallery'              => self::gallery_ids( $parsed['images'], $thumbnail ),
				'show_on_home'         => self::truthy( $meta['show_on_home'] ?? '' ),
				'home_order'           => (int) ( $meta['order_product_home'] ?? 0 ),
			),
			'brochure_url' => (string) ( $meta['pdf_file_upload'] ?? '' ),
			'legacy'       => array(
				'title'   => (string) ( $product['title'] ?? '' ),
				'content' => $content,
				'meta'    => array(
					'pdf_file_upload'   => (string) ( $meta['pdf_file_upload'] ?? '' ),
					'thumbnail_id'      => $thumbnail,
					'show_on_home'      => (string) ( $meta['show_on_home'] ?? '' ),
					'order_product_home' => (string) ( $meta['order_product_home'] ?? '' ),
				),
			),
		);
	}

	/**
	 * @param string[] $features
	 * @return array<int, array{feature: string}>
	 */
	private static function features_rows( array $features ): array {
		$rows = array();
		foreach ( $features as $feature ) {
			$rows[] = array( 'feature' => $feature );
		}

		return $rows;
	}

	/**
	 * The featured image is already rendered first; repeating it as the first
	 * gallery item gives the reader the same picture twice.
	 *
	 * @param int[] $images
	 * @return int[]
	 */
	private static function gallery_ids( array $images, int $thumbnail ): array {
		$ids = array_values(
			array_filter(
				$images,
				static fn( int $id ): bool => $id > 0 && $id !== $thumbnail
			)
		);

		return array_values( array_unique( $ids ) );
	}

	/**
	 * The legacy flag is the string "yes", not "1" or true.
	 *
	 * @param mixed $value
	 */
	private static function truthy( $value ): int {
		if ( is_bool( $value ) ) {
			return $value ? 1 : 0;
		}

		$normalised = strtolower( trim( (string) $value ) );

		return in_array( $normalised, array( '1', 'yes', 'true', 'on' ), true ) ? 1 : 0;
	}
}
