<?php
/**
 * Header — 10K Agency.
 * Glass pill nav: transparent on dark hero, white on light sheets.
 *
 * @package Ei
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ei' ); ?></a>

<header class="site-header">
	<div class="container header-inner">
		<a class="nav-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">10<span class="accent">K</span> Agency</a>
		<nav class="nav-pill" aria-label="Primary">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-menu',
					'echo'           => true,
					'items_wrap'     => '%3$s',
					'link_before'    => '',
					'link_after'     => '',
				)
			);
			?>
		</nav>
	</div>
</header>

<main id="content">
