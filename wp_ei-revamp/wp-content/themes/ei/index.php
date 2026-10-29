<?php
/**
 * Fallback index — blog or anything unmatched.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<section class="ei-page-head">
	<div class="container">
		<h1>
			<?php
			if ( is_home() && ! is_front_page() ) {
				single_post_title();
			} elseif ( is_archive() ) {
				the_archive_title();
			} else {
				esc_html_e( 'Latest', 'ei' );
			}
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
						<article class="ei-product-card ei-result-card">
							<div class="ei-card-body">
								<h3 class="ei-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="ei-card-summary"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 24 ) ); ?></p>
								<a class="btn btn--outline" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read more', 'ei' ); ?></a>
							</div>
						</article>
						<?php
					}
				endwhile;
				?>
			</div>
			<nav class="ei-pagination" aria-label="Pagination">
				<?php the_posts_pagination( array( 'mid_size' => 2 ) ); ?>
			</nav>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing here yet.', 'ei' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
