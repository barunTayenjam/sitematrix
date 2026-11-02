<?php
/**
 * Header: 10K Agency running head, sticky across sheets.
 * Brand logo with EI red, sheet-number mono rail, ISO/est info.
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

<!-- running head -->
<div class="running-head">
	<div class="rh-left"><a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">E<span class="ei-red">I</span></a></div>
	<nav class="rh-nav" aria-label="Primary">
		<a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Products', 'ei' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>"><?php esc_html_e( 'About', 'ei' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/clients/' ) ); ?>"><?php esc_html_e( 'Clients', 'ei' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Contact', 'ei' ); ?></a>
	</nav>
	<div class="rh-meta mono" aria-hidden="true">EST. 1983 · PARWANOO, HP · ISO 9001</div>
</div>

<main id="content" class="ei-main">
