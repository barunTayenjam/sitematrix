<?php
/**
 * Theme template tags for the Electronics India revamp theme.
 *
 * All data flows through ACF fields. When a field has no value the helper
 * returns a sensible default — never false, always a string or array — so
 * templates never have to guard against null/empty.
 *
 * @package Ei
 */

if ( ! defined( 'EI_THEME_DIR' ) ) {
	define( 'EI_THEME_DIR', dirname( __FILE__ ) );
}

/** Collect the feature list as plain strings, in the order the editor set. */
function ei_product_features( $post_id ) {
	$fields = get_field( 'features', (int) $post_id );

	if ( ! is_array( $fields ) ) {
		return array();
	}

	return array_map( function ( $row ) {
		if ( is_array( $row ) ) {
			return $row['feature'] ?? '';
		}

		return (string) $row;
	}, $fields );
}

/** Return a card summary: short description first, then a short feature list. */
function ei_product_summary( $post_id ) {
	$short = get_field( 'short_description', (int) $post_id );

	if ( '' !== trim( $short ) ) {
		return $short;
	}

	$features = ei_product_features( $post_id );
	if ( ! empty( $features ) ) {
		return implode( ' · ', array_slice( $features, 0, 2 ) );
	}

	return '';
}

/** The brochure URL: prefer the ACF file field, fall back to the legacy URL. */
function ei_brochure_url( $post_id ) {
	$pdf = get_field( 'brochure_pdf', (int) $post_id );

	if ( is_array( $pdf ) && ! empty( $pdf['url'] ) ) {
		return $pdf['url'];
	}

	$legacy = get_post_meta( (int) $post_id, '_ei_brochure_url', true );

	return $legacy ?: '';
}

/** Query featured products — those flagged for the home page, in order. */
function ei_featured_products( $number = 4 ) {
	return get_posts( array(
		'post_type'      => 'product',
		'numberposts'    => (int) $number,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'   => 'show_on_home',
				'value' => 1,
				'compare' => '=',
			),
		),
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
	) );
}

/** The specification table: table markup with all scripts removed. */
function ei_specifications_table( $post_id ) {
	$table = get_field( 'specifications_table', (int) $post_id );

	if ( empty( $table ) || ! is_string( $table ) ) {
		return '';
	}

	// Strip anything that is not pure table structure.
	$allowed = array(
		'table'  => array(),
		'tbody'  => array(),
		'tr'     => array(),
		'td'     => array(),
		'th'     => array(),
		'thead'  => array(),
		'tfoot'  => array(),
		'caption' => array(),
		'col'    => array(),
		'colgroup' => array(),
		'br'     => array(),
	);

	return wp_kses( $table, $allowed );
}

/** The model number that was parsed from the product title. */
function ei_product_model( $post_id ) {
	$model = get_field( 'model_number', (int) $post_id );

	if ( '' === $model ) {
		return '';
	}

	return (string) $model;
}