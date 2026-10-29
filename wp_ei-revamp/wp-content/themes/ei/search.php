<?php
/**
 * Search results.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<section class="ei-page-head">
	<div class="container">
		<h1>
			<?php
			/* translators: %s: search query. */
			printf( esc_html__( 'Search results for “%s”', 'ei' ), esc_html( get_search_query() ) );
			?>
		</h1>
	</div>
</section>

<section class="ei-section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="ei-grid">
				<?php
				while ( have_posts() ) : the_post();
					if ( 'product' === get_post_type() ) {
						ei_product_card( get_the_ID() );
					} else {
						?>
						<article class="ei-result-card">
							<h3 class="ei-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p class="ei-card-summary"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 24 ) ); ?></p>
							<a class="btn btn--outline" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'ei' ); ?></a>
						</article>
						<?php
					}
				endwhile;
				?>
			</div>
			<nav class="ei-pagination" aria-label="Search pagination">
				<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
			</nav>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found. Try a different keyword, or browse the catalogue.', 'ei' ); ?></p>
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Browse the Catalogue', 'ei' ); ?></a>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
