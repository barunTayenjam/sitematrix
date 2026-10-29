<?php
/**
 * Product category archive (taxonomy products).
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<?php
$term       = get_queried_object();
$intro      = get_field( 'intro', $term );
$cat_image  = get_field( 'category_image', $term );
$sub_terms  = get_terms( array( 'taxonomy' => 'products', 'parent' => $term->term_id, 'hide_empty' => false, 'orderby' => 'name' ) );
?>

<div class="ei-category-hero">
	<div class="container">
		<nav class="ei-breadcrumbs" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'ei' ); ?></a>
			<span>/</span>
			<a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Products', 'ei' ); ?></a>
			<span>/</span>
			<span><?php echo esc_html( $term->name ); ?></span>
		</nav>

		<div style="display: flex; gap: 2rem; align-items: center;">
			<?php if ( $cat_image && is_array( $cat_image ) ) : ?>
				<img src="<?php echo esc_url( $cat_image['url'] ); ?>" alt="<?php echo esc_attr( $cat_image['alt'] ?? $term->name ); ?>" style="max-height: 96px; width: auto;">
			<?php endif; ?>
			<div>
				<h1><?php echo esc_html( $term->name ); ?></h1>
				<?php if ( $intro ) : ?><div class="entry-content"><?php echo $intro; ?></div><?php endif; ?>
			</div>
		</div>

		<?php if ( ! is_wp_error( $sub_terms ) && $sub_terms ) : ?>
			<div class="ei-subcats" style="margin-top: 1.5rem;">
				<?php foreach ( $sub_terms as $sub ) : ?>
					<a href="<?php echo esc_url( get_term_link( $sub ) ); ?>"><?php echo esc_html( $sub->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<section class="ei-category-products">
	<div class="container">
		<div class="ei-grid">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ei_product_card( get_the_ID() ); endwhile; else : ?>
				<p><?php esc_html_e( 'No products in this category yet.', 'ei' ); ?></p>
			<?php endif; ?>
		</div>

		<div style="margin-top: 2rem;">
			<?php the_posts_pagination( array( 'prev_text' => '&laquo; Previous', 'next_text' => 'Next &raquo;' ) ); ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>