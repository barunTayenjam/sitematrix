<?php
/**
 * Blog / general archive fallback.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<section class="ei-page-head">
	<div class="container">
		<h1><?php is_home() ? esc_html_e( 'Latest posts', 'ei' ) : the_title(); ?></h1>
	</div>
</section>

<section class="ei-section">
	<div class="container">
		<div class="ei-grid">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<article class="ei-card">
					<a class="ei-card-img<?php echo has_post_thumbnail() ? '' : ' ei-no-img'; ?>" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) ); ?></a>
					<div class="ei-card-body">
						<h3 class="ei-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="ei-card-summary"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
						<div class="ei-card-actions"><a class="btn" href="<?php the_permalink(); ?>">Read</a></div>
					</div>
				</article>
			<?php endwhile; else : ?>
				<p><?php esc_html_e( 'Nothing published yet.', 'ei' ); ?></p>
			<?php endif; ?>
		</div>

		<div style="margin-top: 2rem;"><?php the_posts_pagination(); ?></div>
	</div>
</section>

<?php get_footer(); ?>
