<?php
/**
 * Product titles carry the model number at the end, in one of two forms:
 *
 *   "Microprocessor Flame Photometer - 1385 & 1382"   separator + model
 *   "Digital TDS Meter 661(Portable)"                 trailing model
 *
 * The split is conservative: a trailing number only counts as a model when the
 * expression as a whole carries at least three digits, so "pH Meter 7" keeps
 * its name rather than inventing a model.
 *
 * @package Ei\Core
 */

declare( strict_types = 1 );

namespace Ei\Core\Migration;

final class Product_Title {

	/**
	 * @return array{0: string, 1: string} [name, model_number]
	 */
	public static function split( string $title ): array {
		$title = trim( $title );

		$by_separator = self::split_on_separator( $title );
		if ( null !== $by_separator ) {
			return $by_separator;
		}

		$by_suffix = self::split_on_suffix( $title );
		if ( null !== $by_suffix ) {
			return $by_suffix;
		}

		return array( $title, '' );
	}

	/**
	 * @return array{0: string, 1: string}|null
	 */
	private static function split_on_separator( string $title ): ?array {
		// Non-greedy name: split on the LAST separator so a model that itself
		// contains a hyphen ("EI-3948") is not mistaken for the separator.
		if ( ! preg_match( '/^(.+?)\s+[-\x{2013}]\s*(\S.*)$/u', $title, $matches ) ) {
			return null;
		}

		$name  = trim( $matches[1] );
		$model = trim( $matches[2] );

		if ( '' === $name || ! preg_match( '/\d/', $model ) ) {
			return null;
		}

		return array( $name, $model );
	}

	/**
	 * @return array{0: string, 1: string}|null
	 */
	private static function split_on_suffix( string $title ): ?array {
		// A model expression: digits, optionally more of them joined by
		// separators, optionally closing with a qualifier such as "(Portable)".
		$model = '\d[\dA-Za-z]*(?:\s*[,&+\/]\s*\d[\dA-Za-z]*)*(?:\s*\([A-Za-z][A-Za-z\s]*\))?';

		if ( ! preg_match( '/^(.+?)\s+(' . $model . ')\s*$/u', $title, $matches ) ) {
			return null;
		}

		$name  = trim( $matches[1] );
		$model = trim( $matches[2] );

		if ( '' === $name ) {
			return null;
		}

		if ( preg_match_all( '/\d/', $model ) < 3 ) {
			return null;
		}

		return array( $name, $model );
	}
}
