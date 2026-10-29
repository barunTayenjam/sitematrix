<?php
/**
 * Reads a product written in Visual Composer and returns structured data.
 *
 * The legacy corpus is not uniform, so the parser is written against the shapes
 * that actually occur rather than an idealised one:
 *
 *   - the feature list is the first `<ul>` inside the row whose class is
 *     `product_feature_row`; other `<ul>`s exist (nested accessory lists) and
 *     must not leak into it;
 *   - the specification table is `<table class="specification-table-product">`.
 *     It is NOT a label/value grid: 20 of 22 rows in one product use `colspan`,
 *     rows carry section headings ("GENERAL", "STIRRER"), and tables run from
 *     one to seven model columns. Flattening that into a repeater would corrupt
 *     published specifications, so the table is carried through as HTML and
 *     sanitised at write time. The theme styles it; the editor keeps the native
 *     table tools;
 *   - some products have no page-builder wrapper at all, and some have no table.
 *
 * The parser is pure: it takes HTML and returns an array. No WordPress, no
 * database, no side effects — which is what makes it testable.
 *
 * @package Ei\Core
 */

declare( strict_types = 1 );

namespace Ei\Core\Migration;

final class Vc_Parser {

	/**
	 * @return array{
	 *     features: string[],
	 *     description: string,
	 *     specifications_table: string,
	 *     images: int[]
	 * }
	 */
	public static function parse( string $content ): array {
		$content = str_replace( array( "\r\n", "\r" ), "\n", $content );

		return array(
			'features'             => self::extract_features( $content ),
			'description'          => self::extract_description( $content ),
			'specifications_table' => self::extract_specifications_table( $content ),
			'images'               => self::extract_images( $content ),
		);
	}

	/* ------------------------------------------------------------ features -- */

	/**
	 * @return string[]
	 */
	private static function extract_features( string $content ): array {
		// Non-greedy up to the first closing vc_row: the feature row never nests
		// another vc_row (inner rows use vc_row_inner).
		if ( ! preg_match( '/\[vc_row[^\]]*product_feature_row[^\]]*\](.*?)\[\/vc_row\]/s', $content, $row ) ) {
			return array();
		}

		if ( ! preg_match( '/<ul[^>]*>(.*?)<\/ul>/is', $row[1], $list ) ) {
			return array();
		}

		preg_match_all( '/<li[^>]*>(.*?)<\/li>/is', $list[1], $items );

		$features = array();
		foreach ( $items[1] as $item ) {
			$text = self::to_text( $item );
			if ( '' !== $text ) {
				$features[] = $text;
			}
		}

		return $features;
	}

	/* ------------------------------------------------------ specifications -- */

	private static function extract_specifications_table( string $content ): string {
		if ( ! preg_match( '/<table[^>]*specification-table-product[^>]*>.*?<\/table>/is', $content, $table ) ) {
			return '';
		}

		return trim( $table[0] );
	}

	/* --------------------------------------------------------- description -- */

	private static function extract_description( string $content ): string {
		// Remove the feature row entirely — its copy is captured as features.
		$content = preg_replace( '/\[vc_row[^\]]*product_feature_row[^\]]*\].*?\[\/vc_row\]/s', '', $content );
		// Remove the specification table — it is carried through separately.
		$content = preg_replace( '/<table[^>]*specification-table-product[^>]*>.*?<\/table>/is', '', (string) $content );
		// Remove every page-builder shortcode tag. Self-closing ones (headings,
		// images) carry their payload in attributes, so the tag is all there is.
		$content = preg_replace( '/\[\/?vc_[^\]]*\]/s', '', (string) $content );

		return trim( (string) preg_replace( "/\n{3,}/", "\n\n", (string) $content ) );
	}

	/* -------------------------------------------------------------- images -- */

	/**
	 * @return int[]
	 */
	private static function extract_images( string $content ): array {
		preg_match_all( '/\[vc_single_image\b[^\]]*\bimage="(\d+)"/i', $content, $matches );

		$ids = array_map( 'intval', $matches[1] );

		return array_values( array_unique( array_filter( $ids ) ) );
	}

	/* ---------------------------------------------------------------- text -- */

	/**
	 * Convert a fragment of HTML to plain text, keeping meaningful line breaks
	 * (list items, <br>, paragraph boundaries) and decoding entities.
	 */
	public static function to_text( string $html ): string {
		$text = $html;

		$text = preg_replace( '/<br\s*\/?>/i', "\n", $text );
		$text = preg_replace( '/<li[^>]*>/i', "\n• ", $text );
		$text = preg_replace( '/<\/p>/i', "\n", $text );
		$text = preg_replace( '/<\/div>/i', "\n", $text );
		$text = strip_tags( (string) $text );
		$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		// Non-breaking spaces are noise in a text field.
		$text = str_replace( "\xC2\xA0", ' ', $text );

		$text = str_replace( array( "\r\n", "\r" ), "\n", $text );
		$text = preg_replace( '/[ \t]+/u', ' ', (string) $text );
		$text = preg_replace( '/ *\n */', "\n", (string) $text );
		$text = preg_replace( "/\n{3,}/", "\n\n", (string) $text );

		return trim( (string) $text );
	}
}
