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
		$nav_items = array(
			array( 'label' => __( 'Home', 'ei' ), 'url' => home_url( '/' ) ),
			array( 'label' => __( 'Products', 'ei' ), 'url' => home_url( '/products/' ) ),
			array( 'label' => __( 'R&D', 'ei' ), 'url' => home_url( '/research-and-development/' ) ),
			array( 'label' => __( 'Quality Control', 'ei' ), 'url' => home_url( '/quality-control/' ) ),
			array( 'label' => __( 'Clients', 'ei' ), 'url' => home_url( '/clients/' ) ),
			array( 'label' => __( 'About Us', 'ei' ), 'url' => home_url( '/about-us/' ) ),
			array( 'label' => __( 'Contact us', 'ei' ), 'url' => home_url( '/contact/' ) ),
		);
		foreach ( $nav_items as $item ) :
			$current = is_front_page() && $item['url'] === home_url( '/' )
				|| ( ! is_front_page() && $item['url'] === home_url( $_SERVER['REQUEST_URI'] ?? '' ) );
			?>
			<a href="<?php echo esc_url( $item['url'] ); ?>" <?php echo $current ? 'aria-current="page"' : ''; ?>>
				<?php echo esc_html( $item['label'] ); ?>
			</a>
		<?php endforeach; ?>
	</nav>
	</div>
</header>

<main id="content">
