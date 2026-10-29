<?php
/**
 * Taxonomies.
 *
 * One hierarchical taxonomy carries the whole catalogue structure: 7 top-level
 * categories and 28 sub-categories, served from /products/{term}/ — the same
 * URLs those categories have used since 2016.
 *
 * @package Ei\Core
 */

declare( strict_types = 1 );

namespace Ei\Core;

final class Taxonomies {

	public static function boot(): void {
		add_action( 'init', array( self::class, 'register' ), 9 );
	}

	public static function register(): void {
		register_taxonomy(
			'products',
			array( 'product' ),
			array(
				'labels'            => array(
					'name'          => __( 'Product Categories', 'ei' ),
					'singular_name' => __( 'Product Category', 'ei' ),
					'search_items'  => __( 'Search Product Categories', 'ei' ),
					'all_items'     => __( 'All Product Categories', 'ei' ),
					'parent_item'   => __( 'Parent Category', 'ei' ),
					'edit_item'     => __( 'Edit Product Category', 'ei' ),
					'update_item'   => __( 'Update Product Category', 'ei' ),
					'add_new_item'  => __( 'Add New Product Category', 'ei' ),
					'menu_name'     => __( 'Categories', 'ei' ),
				),
				'public'            => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array(
					'slug'         => 'products',
					'with_front'   => false,
					'hierarchical' => true,
				),
			)
		);
	}
}
