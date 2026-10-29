<?php
/**
 * Contract: the content model the site needs exists and keeps the URLs that
 * are already indexed.
 *
 *   /product/{slug}/     single product (92 of them, indexed since 2016)
 *   /products/{term}/    product category (35 terms, hierarchical)
 *
 * Testimonial and Client are editor-managed content with no public URLs —
 * they only feed home-page sections, so they must never leak a rewrite rule.
 */

ei_test( 'product is a public CPT served from /product/{slug}/', function () {
	ei_require_core( 'inc/post-types.php' );
	Ei\Core\Post_Types::register();

	ei_assert_has_key( 'product', $GLOBALS['ei']['post_types'], 'product post type is registered' );
	$args = $GLOBALS['ei']['post_types']['product'];

	ei_assert_true( $args['public'], 'product is public' );
	ei_assert_equals( 'product', $args['rewrite']['slug'], 'product URLs stay at /product/{slug}/' );
	ei_assert_false( $args['rewrite']['with_front'], 'product URLs are not prefixed by the blog front' );
	ei_assert_true( $args['show_in_rest'], 'product is editable in the block editor' );
	ei_assert_false( $args['has_archive'], 'no /product/ archive - the catalogue lives at the /products/ page' );

	foreach ( array( 'title', 'editor', 'thumbnail', 'excerpt' ) as $support ) {
		ei_assert_true( in_array( $support, $args['supports'], true ), "product supports {$support}" );
	}
} );

ei_test( 'products taxonomy is hierarchical and served from /products/{term}/', function () {
	ei_require_core( 'inc/taxonomies.php' );
	Ei\Core\Taxonomies::register();

	ei_assert_has_key( 'products', $GLOBALS['ei']['taxonomies'], 'products taxonomy is registered' );
	$tax = $GLOBALS['ei']['taxonomies']['products'];

	ei_assert_equals( array( 'product' ), (array) $tax['object_type'], 'taxonomy is attached to product only' );

	$args = $tax['args'];
	ei_assert_true( $args['hierarchical'], 'categories nest (7 parents, 28 sub-categories)' );
	ei_assert_equals( 'products', $args['rewrite']['slug'], 'category URLs stay at /products/{term}/' );
	ei_assert_true( $args['show_in_rest'], 'categories are editable in the block editor' );
	ei_assert_true( $args['show_admin_column'], 'the admin list shows a category column' );
} );

ei_test( 'testimonial and client are private content types', function () {
	ei_require_core( 'inc/post-types.php' );
	Ei\Core\Post_Types::register();

	foreach ( array( 'testimonial', 'client' ) as $slug ) {
		ei_assert_has_key( $slug, $GLOBALS['ei']['post_types'], "{$slug} post type is registered" );
		$args = $GLOBALS['ei']['post_types'][ $slug ];
		ei_assert_false( $args['public'], "{$slug} has no public URLs" );
		ei_assert_false( $args['publicly_queryable'], "{$slug} cannot be queried on the front end" );
		ei_assert_true( $args['show_ui'], "{$slug} is editable in the admin" );
	}
} );

ei_test( 'client stores a logo image, testimonial a name and quote', function () {
	ei_require_core( 'inc/post-types.php' );
	Ei\Core\Post_Types::register();

	ei_assert_equals( array( 'title', 'thumbnail' ), $GLOBALS['ei']['post_types']['client']['supports'], 'client supports a title and logo image' );
	ei_assert_equals( array( 'title', 'editor' ), $GLOBALS['ei']['post_types']['testimonial']['supports'], 'testimonial supports a title and quote body' );
} );

ei_test( 'registration runs on init, once', function () {
	ei_require_core( 'inc/post-types.php' );
	Ei\Core\Post_Types::boot();

	ei_assert_true( isset( $GLOBALS['ei']['actions']['init'] ), 'registration hooks into init' );
	ei_assert_count( 1, $GLOBALS['ei']['actions']['init'], 'init has exactly one registration callback' );
} );
