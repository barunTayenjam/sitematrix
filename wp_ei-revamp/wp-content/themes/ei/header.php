<?php
/**
 * Header: top bar, brand, taxonomy navigation.
 *
 * @package Ei
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ei' ); ?></a>

<div class="ei-topbar">
	<div class="container">
		<span>ISO Certified</span>
		<div class="ei-top-right">
			<span>Call us now! <a href="tel:+911725033251">91-172-5033251</a></span>
			<span><a href="mailto:info@electronicsindia.co.in">info@electronicsindia.co.in</a></span>
		</div>
	</div>
</div>

<header class="ei-header">
	<div class="container">
		<div class="ei-brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url( wp_get_attachment_url( 3282 ) ); ?>" alt="<?php bloginfo( 'name' ); ?>"></a>
			<?php endif; ?>
		</div>

		<nav class="ei-nav" aria-label="<?php esc_attr_e( 'Product categories', 'ei' ); ?>">
			<?php echo ei_primary_nav(); // phpcs:ignore WordPress.Security.EscapeOutput -- built from esc_url/esc_html above ?>
		</nav>

		<div>
			<a class="btn" href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>">Get a Quote</a>
		</div>
	</div>
</header>

<main id="content" class="ei-main">
