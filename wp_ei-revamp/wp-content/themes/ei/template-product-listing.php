<?php
/**
 * Template Name: Product Listing
 * Template Post Type: page
 *
 * Product catalog archive page — grid of all published products.
 *
 * @package Ei
 */
get_header(); ?>

<section class="ei-section">
	<div class="container">
		<p class="ei-eyebrow reveal"><?php esc_html_e( 'Product catalogue', 'ei' ); ?></p>
		<h1 class="ei-section-title reveal"><?php echo esc_html( get_the_title() ); ?></h1>
		<p class="ei-section-lede reveal"><?php echo esc_html( get_the_content() ); ?></p>
		<div class="ei-grid">
			<?php
			$products = get_posts(
				array(
					'post_type'      => 'product',
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'orderby'        => 'title',
					'order'          => 'ASC',
				)
			);
			if ( $products ) :
				foreach ( $products as $product ) :
					setup_postdata( $product );
					ei_product_card( $product->ID );
				endforeach;
				wp_reset_postdata();
			else : ?>
				<p><?php esc_html_e( 'No products listed.', 'ei' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php get_footer();
