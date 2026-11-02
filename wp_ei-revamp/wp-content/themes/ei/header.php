<?php
/**
 * Header: top bar, brand, taxonomy navigation + Get a Quote CTA.
 * Updated for Double Beam design.
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
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ei' ); ?></a>

<!-- top bar -->
<div class="topbar">
	<span>ISO 9001 CERTIFIED</span>
	<span class="tb-sep">/</span>
	<span>EST. 1983 · PARWANOO, HP</span>
	<span class="tb-right">
		<a href="tel:+911725033251">+91 172 5033251</a>
		<a href="mailto:info@electronicsindia.co.in">INFO@ELECTRONICSINDIA.CO.IN</a>
	</span>
</div>

<header class="masthead">
	<div class="header-inner">
		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				echo '<img src="' . esc_url( wp_get_attachment_url( 3282 ) ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" width="200" height="37">';
			}
			?>
		</a>
		<nav class="mainnav">
			<button class="navlink mono mega-trigger" aria-expanded="false" aria-controls="mega">INSTRUMENTS <span class="nav-x" aria-hidden="true">+</span></button>
			<a class="navlink mono" href="#s5">FLAGSHIPS</a>
			<a class="navlink mono" href="#s7">CONTACT</a>
		</nav>
		<a class="btn-solid mono" href="#s7">GET QUOTE</a>
	</div>
</header>

<!-- mega menu overlay -->
<div class="mega" id="mega" hidden>
	<div class="mega-backdrop" aria-hidden="true"></div>
	<nav class="mega-inner" aria-label="Instrument families">
		<div class="mega-col">
			<span class="mega-head">FAMILIES</span>
			<a class="mega-row" href="#s4">Spectrophotometers</a>
			<a class="mega-row" href="#s4">Electrochemistry</a>
			<a class="mega-row" href="#s4">Pharmaceutical</a>
			<a class="mega-row" href="#s4">Blood Testing</a>
			<a class="mega-row" href="#s4">Flame Photometers</a>
			<a class="mega-row" href="#s4">Melting Point Apparatus</a>
			<a class="mega-row" href="#s4">Moisture Titrators</a>
		</div>
		<div class="mega-col">
			<span class="mega-head">COMPANY</span>
			<a class="mega-row" href="<?php echo esc_url( home_url( '/about-us/' ) ); ?>">About</a>
			<a class="mega-row" href="<?php echo esc_url( home_url( '/quality-control/' ) ); ?>">Calibration Lab</a>
			<a class="mega-row" href="<?php echo esc_url( home_url( '/careers/' ) ); ?>">Careers</a>
			<a class="mega-row" href="#s7">Contact</a>
		</div>
		<div class="mega-col">
			<span class="mega-head">SUPPORT</span>
			<a class="mega-row" href="#">Datasheets</a>
			<a class="mega-row" href="#">Manuals</a>
			<a class="mega-row" href="#">Software</a>
			<a class="mega-row" href="#">Service Request</a>
		</div>
	</nav>
</div>

<main id="content" class="ei-main">
