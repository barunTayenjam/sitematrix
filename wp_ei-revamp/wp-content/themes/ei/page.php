<?php
/**
 * Generic page template — slim hero + clean content.
 * Retains every legacy page (About, Contact, Quality, R&D, Clients, …)
 * with Visual Composer soup stripped via ei_clean_content().
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<?php
while ( have_posts() ) : the_post();
	$has_hero = '' !== trim( get_post_meta( get_the_ID(), '_ei_show_hero', true ) ) || in_array( get_the_ID(), array( 1551, 3288, 1544, 3299, 1536, 1546, 1542, 1536 ) );
?>

<?php if ( $has_hero ) : ?>
<section class="ei-page-head">
	<div class="container">
		<h1><?php the_title(); ?></h1>
	</div>
</section>
<?php endif; ?>

<section class="ei-section">
	<div class="container">
		<?php echo ei_clean_content( get_the_content() ); // phpcs:ignore WordPress.Security.EscapeOutput -- sanitised by ei_clean_content() ?>

		<?php if ( 'contact' === get_post_field( 'post_name', get_the_ID() ) ) : ?>
			<hr>
			<h2><?php esc_html_e( 'Leave us a message', 'ei' ); ?></h2>
			<?php include EI_THEME_DIR . '/inc/contact-form.php'; ?>
		<?php endif; ?>
	</div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
