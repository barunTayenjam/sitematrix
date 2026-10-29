<?php
/**
 * Post types.
 *
 * `product` is the only publicly queryable type: 92 catalogue models whose
 * URLs (/product/{slug}/) have been indexed since 2016 and must not move.
 *
 * `testimonial` and `client` exist to be picked up by home-page sections. They
 * are editor-managed but deliberately have no public URLs, so stray rewrite
 * rules can never expose a half-built archive.
 *
 * @package Ei\Core
 */

declare( strict_types = 1 );

namespace Ei\Core;

final class Post_Types {

	public static function boot(): void {
		add_action( 'init', array( self::class, 'register' ) );
	}

	public static function register(): void {
		self::register_product();
		self::register_testimonial();
		self::register_client();
	}

	private static function register_product(): void {
		register_post_type(
			'product',
			array(
				'labels'        => array(
					'name'               => __( 'Products', 'ei' ),
					'singular_name'      => __( 'Product', 'ei' ),
					'add_new'            => __( 'Add Product', 'ei' ),
					'add_new_item'       => __( 'Add New Product', 'ei' ),
					'edit_item'          => __( 'Edit Product', 'ei' ),
					'new_item'           => __( 'New Product', 'ei' ),
					'view_item'          => __( 'View Product', 'ei' ),
					'search_items'       => __( 'Search Products', 'ei' ),
					'not_found'          => __( 'No products found', 'ei' ),
					'not_found_in_trash' => __( 'No products found in Trash', 'ei' ),
					'all_items'          => __( 'All Products', 'ei' ),
					'menu_name'          => __( 'Products', 'ei' ),
				),
				'public'        => true,
				// The catalogue is the /products/ page. An archive at /product/
				// would duplicate it and add a second canonical URL.
				'has_archive'   => false,
				'rewrite'       => array(
					'slug'       => 'product',
					'with_front' => false,
				),
				'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'menu_icon'     => 'dashicons-screenoptions',
				'menu_position' => 5,
				'show_in_rest'  => true,
				'taxonomies'    => array( 'products' ),
			)
		);
	}

	private static function register_testimonial(): void {
		register_post_type(
			'testimonial',
			array(
				'labels'              => array(
					'name'          => __( 'Testimonials', 'ei' ),
					'singular_name' => __( 'Testimonial', 'ei' ),
					'add_new_item'  => __( 'Add New Testimonial', 'ei' ),
					'edit_item'     => __( 'Edit Testimonial', 'ei' ),
					'menu_name'     => __( 'Testimonials', 'ei' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'supports'            => array( 'title', 'editor' ),
				'menu_icon'           => 'dashicons-format-quote',
				'menu_position'       => 6,
			)
		);
	}

	private static function register_client(): void {
		register_post_type(
			'client',
			array(
				'labels'              => array(
					'name'          => __( 'Client Logos', 'ei' ),
					'singular_name' => __( 'Client Logo', 'ei' ),
					'add_new_item'  => __( 'Add New Client Logo', 'ei' ),
					'edit_item'     => __( 'Edit Client Logo', 'ei' ),
					'menu_name'     => __( 'Client Logos', 'ei' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'has_archive'         => false,
				'rewrite'             => false,
				'supports'            => array( 'title', 'thumbnail' ),
				'menu_icon'           => 'dashicons-building',
				'menu_position'       => 7,
			)
		);
	}
}
