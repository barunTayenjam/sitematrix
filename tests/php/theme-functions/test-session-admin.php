<?php
/**
 * Fix 3 — `start_session()` is hooked to `init`, so WordPress admin requests
 * also start a PHP session. That takes a session-file lock for the whole
 * request and serialises concurrent wp-admin traffic.
 *
 * The session is only consumed by front-end / admin-ajax flows, so wp-admin
 * page loads must not start one.
 */

tf_test( 'start_session() does not start a session in wp-admin page loads', function () {
	tf_load_function( 'start_session' );

	tf_reset_session();

	$GLOBALS['tf']['is_admin']   = true;
	$GLOBALS['tf']['doing_ajax'] = false;

	start_session();

	tf_assert_equals(
		PHP_SESSION_NONE,
		session_status(),
		'wp-admin request leaves the PHP session closed'
	);
} );

tf_test( 'start_session() still starts a session on front-end requests', function () {
	tf_load_function( 'start_session' );

	tf_reset_session();

	$GLOBALS['tf']['is_admin']   = false;
	$GLOBALS['tf']['doing_ajax'] = false;

	start_session();

	tf_assert_equals(
		PHP_SESSION_ACTIVE,
		session_status(),
		'front-end request starts the session (resource gate needs it)'
	);

	tf_reset_session();
} );

tf_test( 'start_session() still starts a session for admin-ajax requests', function () {
	tf_load_function( 'start_session' );

	tf_reset_session();

	$GLOBALS['tf']['is_admin']   = true;
	$GLOBALS['tf']['doing_ajax'] = true;

	start_session();

	tf_assert_equals(
		PHP_SESSION_ACTIVE,
		session_status(),
		'admin-ajax request starts the session (lead/resource AJAX needs it)'
	);

	tf_reset_session();
} );
