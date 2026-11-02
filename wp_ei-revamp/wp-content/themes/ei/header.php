<?php
/**
 * Header — 10K Agency · Warm Editorial Light.
 * Paper ground, hairline bottom rule, mono nav, live clock.
 *
 * @package Ei
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script>document.documentElement.classList.add('js');</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<a class="skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'ei' ); ?></a>

<header class="site-header">
	<div class="container header-inner">
		<a class="nav-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="brand-bold">10K</span><span class="brand-serif">Agency</span>
		</a>
		<nav class="nav-menu" aria-label="Primary">
			<?php
			$nav_items = array(
				array(
					'label' => __( 'Manifesto', 'ei' ),
					'url'   => home_url( '/#s1' ),
				),
				array(
					'label' => __( 'Specimens', 'ei' ),
					'url'   => home_url( '/#s2' ),
				),
				array(
					'label' => __( 'Index', 'ei' ),
					'url'   => home_url( '/products/' ),
				),
				array(
					'label' => __( 'Quality', 'ei' ),
					'url'   => home_url( '/quality-control/' ),
				),
				array(
					'label' => __( 'R&D', 'ei' ),
					'url'   => home_url( '/research-and-development/' ),
				),
				array(
					'label' => __( 'About', 'ei' ),
					'url'   => home_url( '/about-us/' ),
				),
				array(
					'label' => __( 'Contact', 'ei' ),
					'url'   => home_url( '/contact/' ),
				),
			);
			foreach ( $nav_items as $item ) :
				$is_anchor = (int) ( strpos( $item['url'], '#s1' ) !== false && is_front_page() );
				?>
				<a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $is_anchor ? ' aria-current="page"' : ''; ?>>
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
		<div class="header-clock" aria-hidden="true">
			<span class="clock-time" id="headerClockTime">--:--:--</span>
			<span>IST</span>
		</div>
		<button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="siteNav">
			<span></span>
			<span></span>
			<span></span>
		</button>
	</div>
</header>

<main id="content">
