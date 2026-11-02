<?php
/**
 * Generic page — datasheet sheet layout for legacy pages
 * (About, Contact, Quality, R&D, Clients, …).
 *
 * @package Ei
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>

	<section class="sheet sheet-page reveal" id="page-<?php echo esc_attr( get_the_ID() ); ?>">
		<header class="sheet-head">
			<span class="sheet-index mono"><?php echo esc_html( get_post_field( 'post_name', get_the_ID() ) ); ?></span>
			<h1 class="sheet-title"><?php the_title(); ?></h1>
		</header>
		<div class="page-body">
			<?php echo ei_clean_content( get_the_content() ); // phpcs:ignore WordPress.Security.EscapeOutput -- sanitised by ei_clean_content() ?>

			<?php if ( 'contact' === get_post_field( 'post_name', get_the_ID() ) ) : ?>
				<hr>
				<h2><?php esc_html_e( 'Leave us a message', 'ei' ); ?></h2>
				<?php include EI_THEME_DIR . '/inc/contact-form.php'; ?>
			<?php endif; ?>
		</div>
	</section>

<?php
endwhile;
get_footer();
