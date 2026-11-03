<?php
/**
 * Header — 10K Agency · Warm Editorial Light.
 * Paper ground, hairline bottom rule, mono nav, live clock, mega menu.
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
				array( 'label' => __( 'Manifesto', 'ei' ), 'url' => home_url( '/#s1' ) ),
				array( 'label' => __( 'Specimens', 'ei' ), 'url' => home_url( '/#s2' ) ),
				array( 'label' => __( 'Products', 'ei' ), 'url' => home_url( '/products/' ), 'mega' => true ),
				array( 'label' => __( 'Quality', 'ei' ), 'url' => home_url( '/quality-control/' ) ),
				array( 'label' => __( 'R&D', 'ei' ), 'url' => home_url( '/research-and-development/' ) ),
				array( 'label' => __( 'About', 'ei' ), 'url' => home_url( '/about-us/' ) ),
				array( 'label' => __( 'Contact', 'ei' ), 'url' => home_url( '/contact/' ) ),
			);
			$products_mega = array(
				array(
					'name' => 'Blood Testing Instruments',
					'slug' => 'blood_testing_instruments',
					'subs' => array( 'Photo Colorimeter', 'Haemoglobin Meter', 'Differential Blood Cell Counter' ),
				),
				array(
					'name' => 'Spectrophotometers',
					'slug' => 'spectrophotometer',
					'subs' => array( 'Double Beam Spectrophotometer', 'Single Beam Spectrophotometer', 'Visible Spectrophotometer' ),
				),
				array(
					'name' => 'Electrochemistry Instruments',
					'slug' => 'electrochemistry_instruments',
					'subs' => array( 'pH Meter', 'Conductivity Meter', 'TDS Meter', 'Salinity Meter', 'Turbidity Meter', 'DO Meter', 'Potentiometer', 'Colony Counter' ),
				),
				array(
					'name' => 'Pharmaceutical Instruments',
					'slug' => 'pharmaceutical_instruments',
					'subs' => array( 'Dissolution Apparatus', 'Disintegration Apparatus', 'Friability Apparatus', 'Melting Point Apparatus', 'Tablet Hardness Tester', 'Leak Test Apparatus', 'Viscometer' ),
				),
				array(
					'name' => 'Flame Photometers',
					'slug' => 'flame_photometer',
					'subs' => array( 'Microprocessor Flame Photometer 1385 & 1382', 'Dual Channel Flame Photometer' ),
				),
				array(
					'name' => 'Water Analysis Kits',
					'slug' => 'water-analysis-kit',
					'subs' => array( 'Water & Soil Analysis Kit', 'Water Proof Meter 7200' ),
				),
				array(
					'name' => 'Flocculator / Jar Test Apparatus',
					'slug' => 'flocculatorjar_test_apparatus',
					'subs' => array( 'Jar Test Apparatus 1922, 1924 & 1926' ),
				),
			);
			foreach ( $nav_items as $item ) :
				if ( ! empty( $item['mega'] ) ) :
					?>
					<li class="nav-item has-children">
						<a href="<?php echo esc_url( $item['url'] ); ?>" aria-haspopup="true" aria-expanded="false"><?php echo esc_html( $item['label'] ); ?></a>
						<ul class="mega-menu">
							<?php foreach ( $products_mega as $fam ) : ?>
								<li class="mega-col">
									<span class="mega-eyebrow">Family</span>
									<a class="mega-family" href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php echo esc_html( $fam['name'] ); ?></a>
									<ul class="mega-sub">
										<?php foreach ( $fam['subs'] as $sub ) : ?>
											<li><a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php echo esc_html( $sub ); ?></a></li>
										<?php endforeach; ?>
									</ul>
								</li>
							<?php endforeach; ?>
							<li class="mega-foot">
								<a class="btn-text" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">View full catalogue &rarr;</a>
							</li>
						</ul>
					</li>
				<?php else :
					$current = ( '/' === parse_url( $item['url'], PHP_URL_PATH ) ) && is_front_page();
					?>
					<li class="nav-item">
						<a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></a>
					</li>
				<?php endif;
			endforeach; ?>
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
