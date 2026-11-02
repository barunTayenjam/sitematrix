<?php
/**
 * Theme setup — 10K Agency Technical Luxury.
 * All assets self-hosted. Zero third-party runtime requests.
 *
 * @package Ei
 */

define( 'EI_THEME_DIR', get_template_directory() );

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'image' ) );
		register_nav_menus(
			array(
				'primary' => __( 'Primary menu', 'ei' ),
			)
		);
		add_image_size( 'ei-card', 400, 280, true );
		add_image_size( 'ei-chamber', 500, 300, true );
		add_image_size( 'ei-product', 800, 600, true );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'ei-style', get_stylesheet_uri(), array(), null );
		wp_enqueue_script( 'ei-script', get_template_directory_uri() . '/assets/ei.js', array(), null, true );
	}
);

/* Contact Form 7: ensure submission stays in-page */
add_filter( 'wpcf7_mail_sent', 'ei_cf7_redirect_on_success' );
function ei_cf7_redirect_on_success( $output ) {
	// Keep it inline — no page refresh
	return $output;
}
