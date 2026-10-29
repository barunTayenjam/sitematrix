<?php
/**
 * Search results: products first, then pages.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<section class="ei-page-head">
	<div class="container">
		<h1><?php printf( esc_html__( 'Search results for “%s”', 'ei' ), esc_html( get_search_query() ) ); ?></h1>
		<?php get_search_form(); ?>
	</div>
</section>

<section class="ei-section">
	<div class="container">
		<div class="ei-grid">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php if ( 'product' === get_post_type() ) : ?>
					<?php ei_product_card( get_the_ID() ); ?>
				<?php else : ?>
					<article class="ei-card">
						<div class="ei-card-body">
							<h3 class="ei-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="ei-card-summary"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
						</div>
					</article>
				<?php endif; ?>
			<?php endwhile; else : ?>
				<p><?php esc_html_e( 'No results. Try a product name or model number.', 'ei' ); ?></p>
			<?php endif; ?>
		</div>
		<div style="margin-top: 2rem;"><?php the_posts_pagination(); ?></div>
	</div>
</section>

<?php get_footer(); ?>
