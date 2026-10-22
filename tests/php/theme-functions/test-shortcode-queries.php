<?php
/**
 * Fix 4 — shortcode query hygiene.
 *
 * a) ubermenu_post_grid() runs get_posts() with `suppress_filters => false`,
 *    which runs every `posts_*` filter on a fixed grid query where none are
 *    needed. It must opt out.
 *
 * b) The [term_tag] cloud renders 9 tags although the shortcode is named
 *    "limit_to_five_tags". It must render 5.
 */

tf_test( 'ubermenu_post_grid opts out of post filters', function () {
	tf_load_function( 'ubermenu_post_grid' );
	tf_reset_calls();
	$GLOBALS['tf']['get_posts_returns'] = array();

	ob_start();
	ubermenu_post_grid( array( 'num' => 4 ) );
	ob_end_clean();

	$calls = tf_calls_to( 'get_posts' );
	tf_assert_true( count( $calls ) === 1, 'ubermenu_post_grid issues one get_posts() query' );

	$args = $calls[0][0];
	tf_assert_true(
		isset( $args['suppress_filters'] ) && $args['suppress_filters'] === true,
		'grid query sets suppress_filters => true'
	);
} );

tf_test( 'term_tag cloud is limited to five tags', function () {
	tf_load_function( 'limit_to_five_tags' );
	tf_reset_calls();

	ob_start();
	limit_to_five_tags();
	ob_end_clean();

	$calls = tf_calls_to( 'wp_tag_cloud' );
	tf_assert_true( count( $calls ) === 1, 'term_tag renders a tag cloud' );

	$args = $calls[0][0];
	tf_assert_equals( 5, $args['number'] ?? null, 'tag cloud requests 5 tags' );
} );
