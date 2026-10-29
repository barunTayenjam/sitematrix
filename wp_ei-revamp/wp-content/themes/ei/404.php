<?php
/**
 * 404.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<section class="ei-page-head">
	<div class="container">
		<h1><?php esc_html_e( 'Page not found', 'ei' ); ?></h1>
	</div>
</section>

<section class="ei-section">
	<div class="container">
		<p><?php esc_html_e( 'The page may have moved. Browse the catalogue or search for a model.', 'ei' ); ?></p>
		<?php get_search_form(); ?>
		<p style="margin-top:1.5rem;"><a class="btn" href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'View catalogue', 'ei' ); ?></a></p>
	</div>
</section>

<?php get_footer(); ?>
