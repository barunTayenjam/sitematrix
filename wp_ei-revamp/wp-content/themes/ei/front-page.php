<?php
/**
 * Front page template.
 *
 * Hero, category grid, featured products, testimonials, client logos.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<section class="ei-hero">
	<div class="container">
		<h1><?php esc_html_e( 'Electronics India — Scientific Instruments', 'ei' ); ?></h1>
		<p><?php esc_html_e( 'Manufacturer, supplier and exporter of analytical, scientific laboratory and pharmaceutical instruments in India.', 'ei' ); ?></p>
		<a class="btn btn-outline" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">View catalogue</a>
	</div>
</section>

<section class="ei-section">
	<div class="container">
		<div class="ei-eyebrow"><?php esc_html_e( 'Products by category', 'ei' ); ?></div>
		<h2 class="ei-section-title"><?php esc_html_e( 'Popular Categories', 'ei' ); ?></h2>
		<div class="ei-subcats">
			<?php foreach ( get_terms( array( 'taxonomy' => 'products', 'parent' => 0, 'hide_empty' => false, 'orderby' => 'name' ) ) as $term ) : ?>
				<a class="current" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<div class="ei-grid">
			<?php $count = 0; foreach ( get_terms( array( 'taxonomy' => 'products', 'parent' => 0, 'hide_empty' => false, 'orderby' => 'name', 'number' => 4 ) ) as $term ) : $products = new \WP_Query( array( 'post_type' => 'product', 'taxonomy' => 'products', 'terms' => $term->term_id, 'numberposts' => 2, 'orderby' => 'rand' ) ); while ( $products->have_posts() ) : $products->the_post(); $count++; ei_product_card( $post->ID ); endwhile; endforeach; ?>
		</div>
	</div>
</section>

<section class="ei-section">
	<div class="container">
		<div class="ei-eyebrow"><?php esc_html_e( 'Featured', 'ei' ); ?></div>
		<h2 class="ei-section-title"><?php esc_html_e( 'Featured Products', 'ei' ); ?></h2>
		<div class="ei-grid">
			<?php $featured = ei_featured_products( 6 ); foreach ( $featured as $post ) : setup_postdata( $post ); ei_product_card( $post->ID ); endforeach; wp_reset_postdata(); ?>
		</div>
	</div>
</section>

<section class="ei-section">
	<div class="container">
		<div class="ei-eyebrow"><?php esc_html_e( 'Testimonials', 'ei' ); ?></div>
		<h2 class="ei-section-title"><?php esc_html_e( 'What our customers say', 'ei' ); ?></h2>
		<div class="ei-testimonial">
			<blockquote><?php echo esc_html( get_the_title( 30 ) ); // first testimonial ?></blockquote>
			<cite><?php echo esc_html( 'Client' ); ?></cite>
		</div>
	</div>
</section>

<section class="ei-section">
	<div class="container">
		<div class="ei-eyebrow"><?php esc_html_e( 'Clients', 'ei' ); ?></div>
		<h2 class="ei-section-title"><?php esc_html_e( 'Trusted by', 'ei' ); ?></h2>
		<div class="ei-clients">
			<?php $clients = get_posts( array( 'post_type' => 'client', 'numberposts' => 8, 'post_status' => 'any' ) ); foreach ( $clients as $client ) : ?>
				<?php $img = get_the_post_thumbnail( $client->ID, 'thumbnail', array( 'alt' => '' ) ); ?>
				<?php echo $img ? '<li>' . $img . '</li>' : ''; ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>