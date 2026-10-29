<?php
/**
 * 404 — helpful recovery, not a dead end.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<section class="ei-section" style="text-align: center; padding: var(--space-8) 0;">
	<div class="container">
		<p class="ei-eyebrow"><?php esc_html_e( 'Error 404', 'ei' ); ?></p>
		<h1><?php esc_html_e( 'Page not found', 'ei' ); ?></h1>
		<p style="max-width: 44ch; margin: var(--space-3) auto var(--space-5); color: var(--color-muted-foreground);">
			<?php esc_html_e( 'The page you were looking for has moved or no longer exists. Browse the catalogue or talk to us directly.', 'ei' ); ?>
		</p>
		<div style="display: flex; gap: var(--space-3); justify-content: center; flex-wrap: wrap;">
			<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Browse the Catalogue', 'ei' ); ?></a>
			<a class="btn btn--outline" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'ei' ); ?></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
