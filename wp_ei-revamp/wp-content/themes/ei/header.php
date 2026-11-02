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
	<div class="rh-meta mono" aria-hidden="true"><?php echo esc_html( get_option( 'admin_email' ) ?: 'info@electronicsindia.co.in' ); ?> · EST. 1983 · PARWANOO, HP · 31.09°N 76.97°E</div>
	<div class="rh-sheet mono">SHEET <span id="sheet-no">01</span>/05</div>
</div>

<main id="content" class="ei-main">