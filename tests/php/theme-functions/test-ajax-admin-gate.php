<?php
/**
 * Fix 1 — the theme's AJAX handler for tag processing must only be registered
 * for admin-ajax requests. On a normal front-end request it must not hook at
 * all, otherwise every page load pays for an unbounded full-text search query.
 */

tf_test( 'AJAX tag handler is NOT registered on front-end requests', function () {
	$probe = tf_probe( false );

	tf_assert_equals( null, $probe['error'], 'functions.php loads without error (front-end)' );
	tf_assert_false(
		tf_has_action( $probe, 'wp_ajax_handle_tag_processing_request' ),
		'wp_ajax_handle_tag_processing_request not registered on front-end'
	);
	tf_assert_false(
		tf_has_action( $probe, 'wp_ajax_nopriv_handle_tag_processing_request' ),
		'wp_ajax_nopriv_handle_tag_processing_request not registered on front-end'
	);
} );

tf_test( 'AJAX tag handler IS registered for admin requests', function () {
	$probe = tf_probe( true );

	tf_assert_equals( null, $probe['error'], 'functions.php loads without error (admin)' );
	tf_assert_true(
		tf_has_action( $probe, 'wp_ajax_handle_tag_processing_request' ),
		'wp_ajax_handle_tag_processing_request registered in admin'
	);
	tf_assert_true(
		tf_has_action( $probe, 'wp_ajax_nopriv_handle_tag_processing_request' ),
		'wp_ajax_nopriv_handle_tag_processing_request registered in admin'
	);
} );
