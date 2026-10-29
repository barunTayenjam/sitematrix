<?php
/**
 * Contract: the theme reads product data through template tags, never directly
 * from ACF fields, so every product renders something useful regardless of how
 * complete its fields are.
 *
 * These are the functions single-product.php, the category grid and the home
 * page call. Keeping them as the only data path means the fallback rules live
 * in one place — and are testable without a database.
 */
ei_test( 'the feature list is a list of strings in published order', function () {
	ei_tags();
	$GLOBALS['ei']['fields'][42]['features'] = array(
		array( 'feature' => 'Touch screen display' ),
		array( 'feature' => 'Auto range' ),
	);

	ei_assert_equals(
		array( 'Touch screen display', 'Auto range' ),
		ei_product_features( 42 ),
		'repeater rows become plain strings'
	);

	$GLOBALS['ei']['fields'][7]['features'] = '';
	ei_assert_equals( array(), ei_product_features( 7 ), 'a product with no features yields an empty list, not a false' );
} );

ei_test( 'a card summary falls back to the first features when none is written', function () {
	ei_tags();

	$GLOBALS['ei']['fields'][1] = array(
		'short_description' => '',
		'features'          => array(
			array( 'feature' => 'Full color touch display' ),
			array( 'feature' => 'Auto computation of results' ),
			array( 'feature' => 'Third feature is not used' ),
		),
	);
	ei_assert_equals(
		'Full color touch display · Auto computation of results',
		ei_product_summary( 1 ),
		'first two features, joined, no trailing separator'
	);

	$GLOBALS['ei']['fields'][2] = array(
		'short_description' => 'Bench titrator for moisture.',
		'features'          => array(),
	);
	ei_assert_equals( 'Bench titrator for moisture.', ei_product_summary( 2 ), 'a written short description wins' );

	$GLOBALS['ei']['fields'][3] = array( 'short_description' => '', 'features' => array() );
	ei_assert_equals( '', ei_product_summary( 3 ), 'no summary is invented from nothing' );
} );

ei_test( 'the brochure URL prefers an attached file and falls back to the legacy URL', function () {
	ei_tags();

	$GLOBALS['ei']['fields'][10]['brochure_pdf'] = array( 'url' => 'https://local.test/wp-content/uploads/2019/08/3948.pdf' );
	$GLOBALS['ei']['meta'][10]['_ei_brochure_url'] = 'https://electronicsindia.co.in/old.pdf';
	ei_assert_equals( 'https://local.test/wp-content/uploads/2019/08/3948.pdf', ei_brochure_url( 10 ), 'the local file wins' );

	unset( $GLOBALS['ei']['fields'][11]['brochure_pdf'] );
	$GLOBALS['ei']['meta'][11]['_ei_brochure_url'] = 'https://electronicsindia.co.in/2019/08/3948.pdf';
	ei_assert_equals( 'https://electronicsindia.co.in/2019/08/3948.pdf', ei_brochure_url( 11 ), 'the legacy URL is the fallback' );

	ei_assert_equals( '', ei_brochure_url( 12 ), 'a product with no brochure gets an empty string, not a false' );
} );

ei_test( 'the featured products query asks for flagged products in home order', function () {
	ei_tags();
	$GLOBALS['ei']['posts'] = array();

	ei_featured_products( 8 );

	$calls = ei_calls_to_get_posts();
	ei_assert_count( 1, $calls, 'one query' );

	$args = $calls[0];
	ei_assert_equals( 'product', $args['post_type'], 'only products' );
	ei_assert_equals( 8, $args['numberposts'], 'the limit is respected' );
	ei_assert_equals( 'meta_value_num', $args['orderby'], 'ordered by the numeric home order' );
	ei_assert_equals( 'ASC', $args['order'], 'lower order first' );
	ei_assert_equals(
		array( array( 'key' => 'show_on_home', 'value' => 1, 'compare' => '=' ) ),
		$args['meta_query'],
		'only products flagged for the home page'
	);
} );

ei_test( 'the specification table keeps its markup but loses scripts', function () {
	ei_tags();
	$GLOBALS['ei']['fields'][20]['specifications_table'] =
		'<table class="table"><tbody><tr><td>Range</td><td>0 - 14 pH<script>evil()</script></td></tr></tbody></table>';

	$rendered = ei_specifications_table( 20 );

	ei_assert_contains( '<table', $rendered, 'the table survives' );
	ei_assert_contains( '0 - 14 pH', $rendered, 'the values survive' );
	ei_assert_false( strpos( $rendered, '<script' ) !== false, 'scripts do not' );
	ei_assert_false( strpos( $rendered, 'evil()' ) !== false, 'script bodies do not' );
} );

ei_test( 'the model number is exposed as a badge, empty when there is none', function () {
	ei_tags();

	$GLOBALS['ei']['fields'][30]['model_number'] = '1385 & 1382';
	ei_assert_equals( '1385 & 1382', ei_product_model( 30 ), 'the model number is returned' );

	$GLOBALS['ei']['fields'][31]['model_number'] = '';
	ei_assert_equals( '', ei_product_model( 31 ), 'an empty model stays empty' );
} );
