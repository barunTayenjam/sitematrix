<?php
/**
 * Generic page template.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<section class="ei-page-head">
	<div class="container">
		<h1><?php the_title(); ?></h1>
	</div>
</section>

<section class="ei-section">
	<div class="container entry-content">
		<?php echo ei_clean_content( get_the_content() ); ?>
	</div>
</section>

<?php get_footer(); ?>
