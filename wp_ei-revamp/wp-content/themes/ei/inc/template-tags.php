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
				'key'     => 'show_on_home',
				'value'   => 1,
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

	$allowed = array(
		'table'     => array(),
		'tbody'     => array(),
		'tr'        => array(),
		'td'        => array(),
		'th'        => array(),
		'thead'     => array(),
		'tfoot'     => array(),
		'caption'   => array(),
		'col'       => array(),
		'colgroup'  => array(),
		'br'        => array(),
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

/** Strip VC/widget shortcodes; wrappers keep their inner content. */
function ei_clean_content( $html ) {
	// Strip VC/widget shortcodes: widget-style tags drop entirely (with bodies),
	// wrapper tags lose their brackets but keep the inner content.
	$out = preg_replace( '/\[vc_raw_html\][^\[]*\[\/vc_raw_html\]/s', '', $html );
	$out = preg_replace( '/\[(heading|contactboxphone|rev_slider|vc_gmaps|contact-form-7)[^\]]*\]/s', '', $out );
	$out = preg_replace( '/\[\/?(vc_row|vc_column|vc_column_text|vc_column_inner|vc_row_inner|vc_separator|heading|rev_slider|vc_gmaps)[^\]]*\]/', '', $out );

	// Strip any remaining shortcode markup.
	$out = strip_shortcodes( $out );

	// Remove empty / nbsp-only paragraphs left behind.
	$out = preg_replace( '/<p>[\s\xa0]*<\/p>/', '', $out );

	return trim( preg_replace( '/&nbsp;/', ' ', $out ) );
}

/** Build a mega‑menu panel from term groups (name, url, children[]). */
function ei_mega_menu( $anchor_text, $anchor_url, $groups ) {
	$out = sprintf(
		'<li class="menu-item-has-children ei-mega"><a href="%s">%s</a>',
		esc_url( $anchor_url ),
		esc_html( $anchor_text )
	);

	if ( empty( $groups ) ) {
		return $out . '</li>';
	}

	$out .= '<ul class="ei-mega-panel">';

	foreach ( $groups as $group ) {
		$group_name = isset( $group['name'] ) ? $group['name'] : '';
		$group_url  = isset( $group['url'] )  ? $group['url']  : '#';

		$out .= sprintf(
			'<li class="ei-mega-col-list"><h3 class="ei-mega-col-head"><a href="%s">%s</a></h3><ul>',
			esc_url( $group_url ),
			esc_html( $group_name )
		);

		if ( ! empty( $group['children'] ) ) {
			foreach ( $group['children'] as $child ) {
				$child_name = isset( $child['name'] ) ? $child['name'] : '';
				$child_url  = isset( $child['url'] )  ? $child['url']  : '#';
				$out .= sprintf(
					'<li><a href="%s">%s</a></li>',
					esc_url( $child_url ),
					esc_html( $child_name )
				);
			}
		}

		$out .= '</ul></li>';
	}

	return $out . '</ul></li>';
}

/** Primary navigation: Products mega menu from taxonomy, then static pages. */
function ei_primary_nav() {
	$groups = array();

	$top_terms = get_terms( array(
		'taxonomy'   => 'products',
		'parent'     => 0,
		'hide_empty' => false,
		'orderby'    => 'name',
	) );

	if ( ! is_wp_error( $top_terms ) && $top_terms ) {
		foreach ( $top_terms as $term ) {
			$children = get_terms( array(
				'taxonomy'   => 'products',
				'parent'     => $term->term_id,
				'hide_empty' => false,
				'orderby'    => 'name',
			) );

			if ( ! is_wp_error( $children ) && $children ) {
				$groups[] = array(
					'name'     => $term->name,
					'url'      => get_term_link( $term ),
					'children' => array_map( function ( $c ) {
						return array( 'name' => $c->name, 'url' => get_term_link( $c ) );
					}, $children ),
				);
			}
		}
	}

	if ( empty( $groups ) ) {
		$groups = array( array( 'name' => 'Products', 'url' => home_url( '/products/' ), 'children' => array() ) );
	}

	$items = array( ei_mega_menu( 'Products', home_url( '/products/' ), $groups ) );

	$static_pages = array(
		'Home'             => home_url( '/' ),
		'About Us'         => home_url( '/about-us/' ),
		'Quality Control'  => home_url( '/quality-control/' ),
		'R & D'            => home_url( '/research-and-development/' ),
		'Clients'          => home_url( '/clients/' ),
		'Contact Us'       => home_url( '/contact/' ),
		'Sitemap'          => home_url( '/sitemap/' ),
	);

	foreach ( $static_pages as $label => $url ) {
		$items[] = sprintf(
			'<li class="menu-item"><a href="%s">%s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}

	return '<ul class="ei-menu">' . implode( '', $items ) . '</ul>';
}
