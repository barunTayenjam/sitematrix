<?php
/**
 * Fix 2 — [blogcategory] with an `exclude` attribute must not load every post
 * object in the category just to build a list of IDs.
 *
 * The exclude lookup only needs IDs, so it must ask WP_Query for
 * `fields => 'ids'` and skip found-rows / term / meta cache priming.
 * Behaviour (which posts get excluded) stays identical.
 */

tf_test( 'blogcategory exclude lookup requests post IDs only', function () {
	tf_load_function( 'blogcategory' );
	tf_reset_calls();

	ob_start();
	blogcategory( array( 'catg_slug' => 'insights', 'exclude' => 'news', 'limit' => 3 ) );
	ob_end_clean();

	$calls = tf_calls_to( 'get_posts' );
	tf_assert_true( count( $calls ) >= 1, 'blogcategory issues at least one get_posts() query' );

	$exclude_query = $calls[0][0];

	tf_assert_equals(
		'ids',
		$exclude_query['fields'] ?? null,
		'exclude lookup uses fields => ids (no WP_Post object hydration)'
	);

	tf_assert_true(
		! empty( $exclude_query['no_found_rows'] ),
		'exclude lookup sets no_found_rows (skips SQL_CALC_FOUND_ROWS)'
	);

	tf_assert_true(
		array_key_exists( 'update_post_term_cache', $exclude_query ) && $exclude_query['update_post_term_cache'] === false,
		'exclude lookup skips term cache priming'
	);

	tf_assert_true(
		array_key_exists( 'update_post_meta_cache', $exclude_query ) && $exclude_query['update_post_meta_cache'] === false,
		'exclude lookup skips meta cache priming'
	);

	tf_assert_equals(
		-1,
		$exclude_query['posts_per_page'] ?? null,
		'exclude lookup still covers every post in the category (semantics unchanged)'
	);
} );

tf_test( 'blogcategory still excludes the requested category posts', function () {
	tf_load_function( 'blogcategory' );
	tf_reset_calls();

	// get_posts returns two IDs for the exclude lookup.
	$GLOBALS['tf']['get_posts_returns'] = array();

	ob_start();
	blogcategory( array( 'catg_slug' => 'insights', 'exclude' => 'news', 'limit' => 3 ) );
	ob_end_clean();

	$calls = tf_calls_to( 'get_posts' );
	$main  = end( $calls );

	tf_assert_equals(
		array( 41, 42 ),
		$main[0]['exclude'] ?? null,
		'main query excludes the IDs found by the exclude lookup'
	);
} );
