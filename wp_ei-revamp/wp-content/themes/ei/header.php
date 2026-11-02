<?php
/**
 * Header — 10K Agency.
 * Technical Luxury: minimal glass header with top-level links only.
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
<a class="skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'ei' ); ?></a>

<header class="site-header">
	<div class="container header-inner">
		<a class="nav-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">10<span class="accent">K</span> Agency</a>
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
					'label' => __( 'Catalogue', 'ei' ),
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
				$current = is_front_page() && '#s1' === substr( $item['url'], -3 );
				?>
				<a href="<?php echo esc_url( $item['url'] ); ?>" <?php echo $current ? 'aria-current="page"' : ''; ?>>
					<?php echo esc_html( $item['label'] ); ?>
				</a>
			<?php endforeach; ?>
		</nav>
		<button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
			<span></span>
			<span></span>
			<span></span>
		</button>
	</div>
</header>

<main id="content">
