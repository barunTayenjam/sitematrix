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
					'subs' => array(
						array( 'Photo Colorimeter', 'photo_colorimeter' ),
						array( 'Haemoglobin Meter', 'haemoglobin_meter' ),
						array( 'Differential Blood Cell Counter', 'differential_blood_cell_counter' ),
					),
				),
				array(
					'name' => 'Spectrophotometers',
					'slug' => 'spectrophotometer',
					'subs' => array(
						array( 'Double Beam Spectrophotometer', 'double_beam_spectrophotometer' ),
						array( 'Single Beam Spectrophotometer', 'single_beam_spectrophotometer' ),
						array( 'Visible Spectrophotometer', 'visible_spectrophotometer' ),
					),
				),
				array(
					'name' => 'Electrochemistry Instruments',
					'slug' => 'electrochemistry_instruments',
					'subs' => array(
						array( 'pH Meter', 'ph_meter' ),
						array( 'Conductivity Meter', 'conductivity_meter' ),
						array( 'TDS Meter', 'tds-meter' ),
						array( 'Salinity Meter', 'salinity_meter' ),
						array( 'Turbidity Meter', 'turbidity_meter' ),
						array( 'DO Meter', 'do_meter' ),
						array( 'Potentiometer', 'potentio_meter' ),
						array( 'Colony Counter', 'colony_counter' ),
					),
				),
				array(
					'name' => 'Pharmaceutical Instruments',
					'slug' => 'pharmaceutical_instruments',
					'subs' => array(
						array( 'Dissolution Apparatus', 'dissolution_test_apparatus' ),
						array( 'Disintegration Apparatus', 'disintegration_test_apparatus' ),
						array( 'Friability Apparatus', 'friability_test_apparatus' ),
						array( 'Melting Point Apparatus', 'melting_point_apparatus' ),
						array( 'Tablet Hardness Tester', 'tablet-hardness-tester' ),
						array( 'Leak Test Apparatus', 'leak_test_apparatus' ),
						array( 'Viscometer', 'viscometer-pharmaceutical_instruments' ),
					),
				),
				array(
					'name' => 'Flame Photometers',
					'slug' => 'flame_photometer',
					'subs' => array(
						array( 'Microprocessor Flame Photometer 1385 & 1382', 'flame_photometer' ),
						array( 'Dual Channel Flame Photometer', 'flame_photometer' ),
					),
				),
				array(
					'name' => 'Water Analysis Kits',
					'slug' => 'water-analysis-kit',
					'subs' => array(
						array( 'Water & Soil Analysis Kit', 'water-analysis-kit' ),
						array( 'Water Proof Meter 7200', 'water-analysis-kit' ),
					),
				),
				array(
					'name' => 'Flocculator / Jar Test Apparatus',
					'slug' => 'flocculatorjar_test_apparatus',
					'subs' => array(
						array( 'Jar Test Apparatus 1922, 1924 & 1926', 'flocculatorjar_test_apparatus' ),
					),
				),
			);
			$request_path = trailingslashit( (string) ( wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/', PHP_URL_PATH ) ?: '/' ) );
			foreach ( $nav_items as $item ) :
				if ( ! empty( $item['mega'] ) ) :
					$products_current = is_page( 'products' ) || is_singular( 'product' ) || is_tax( 'products' );
					?>
					<li class="nav-item has-children">
						<a href="<?php echo esc_url( $item['url'] ); ?>" aria-haspopup="true" aria-expanded="false"<?php echo $products_current ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></a>
						<ul class="mega-menu">
							<?php foreach ( $products_mega as $fam ) : ?>
								<li class="mega-col">
									<span class="mega-eyebrow">Family</span>
									<a class="mega-family" href="<?php echo esc_url( home_url( '/products/' . $fam['slug'] . '/' ) ); ?>"><?php echo esc_html( $fam['name'] ); ?></a>
									<ul class="mega-sub">
										<?php foreach ( $fam['subs'] as $sub ) : ?>
											<li><a href="<?php echo esc_url( home_url( '/products/' . $sub[1] . '/' ) ); ?>"><?php echo esc_html( $sub[0] ); ?></a></li>
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
					$item_path = trailingslashit( (string) ( wp_parse_url( $item['url'], PHP_URL_PATH ) ?: '/' ) );
					$item_hash = (string) wp_parse_url( $item['url'], PHP_URL_FRAGMENT );
					$current   = ! $item_hash && $item_path === $request_path;
					if ( is_front_page() && 's1' === $item_hash ) {
						$current = true;
					}
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
