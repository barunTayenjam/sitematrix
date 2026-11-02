<?php
/**
 * Front page — 10K Agency · Warm Editorial Light.
 * Sheets: Manifesto · Specimens · Index · Capabilities · Method · Colophon.
 * Real content sourced from the company's published catalogue.
 *
 * @package Ei
 */

get_header();

$featured = get_posts(
	array(
		'post_type'      => 'product',
		'posts_per_page' => 6,
		'post_status'    => 'publish',
		'meta_key'       => 'show_on_home', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		'meta_value'     => '1', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
	)
);
if ( empty( $featured ) ) {
	$featured = get_posts(
		array(
			'post_type'      => 'product',
			'posts_per_page' => 6,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
}
$index_products = get_posts(
	array(
		'post_type'      => 'product',
		'posts_per_page' => 12,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'ASC',
	)
);

/* Real instrument families, published catalogue copy. */
$capabilities = array(
	array(
		'name' => 'Blood Testing Instruments',
		'desc' => 'Photo Colorimeter, Haemoglobin Meter and Differential Blood Cell Counter for clinical laboratories.',
		'slug' => 'blood_testing_instruments',
	),
	array(
		'name' => 'Spectrophotometers',
		'desc' => 'Double Beam, Single Beam and Visible Spectrophotometers for analytical and research work.',
		'slug' => 'spectrophotometer',
	),
	array(
		'name' => 'Electrochemistry Instruments',
		'desc' => 'pH, Conductivity, TDS, Salinity, Turbidity and DO meters, Potentiometers and Colony Counters.',
		'slug' => 'electrochemistry_instruments',
	),
	array(
		'name' => 'Pharmaceutical Instruments',
		'desc' => 'Dissolution, Disintegration, Friability, Melting Point, Tablet Hardness, Leak Test and Viscometer apparatus.',
		'slug' => 'pharmaceutical_instruments',
	),
	array(
		'name' => 'Flame Photometers',
		'desc' => 'Microprocessor and dual-channel flame photometers for elemental analysis.',
		'slug' => 'flame_photometer',
	),
	array(
		'name' => 'Water Analysis Kits',
		'desc' => 'Field testing of pH, ORP, Conductivity, TDS, Salinity, Dissolved Oxygen and Turbidity.',
		'slug' => 'water-analysis-kit',
	),
	array(
		'name' => 'Flocculator / Jar Test Apparatus',
		'desc' => 'Jar test apparatus for water and waste-water treatment laboratories.',
		'slug' => 'flocculatorjar_test_apparatus',
	),
);

/* Real clients from the published client roster. */
$clients = array(
	'Tata Motors',
	'ITC',
	'Coca-Cola',
	'Pepsi',
	'Amul',
	'Dabur',
	'Godrej',
	'Larsen & Toubro',
	'Aditya Birla',
	'Ambuja Cement',
	'Bisleri',
	'IIT Kharagpur',
	'ICAR',
	'Nalco',
);
?>

<!-- Sheet 01 — Manifesto -->
<section class="sheet hero reveal" id="s1">
	<div class="container hero-content">
		<div>
			<span class="mono-micro" style="display:block;margin-bottom:24px;">Est. 1983 — Analytical · Scientific · Laboratory · Pharmaceutical</span>
			<h1 class="hero-headline">
				<span class="display-xl">Precision,</span>
				<span class="serif-accent">measured.</span>
			</h1>
			<p class="body-l hero-lede">10K Agency is an ISO 9001 certified manufacturer, supplier and exporter of analytical, scientific, laboratory and pharmaceutical instruments. Specialists in drinking water, waste water and soil analysis instrumentation — every unit built, tested and documented in-house.</p>
			<div class="hero-actions">
				<a class="btn btn-solid" href="#s2">Explore specimens</a>
				<a class="btn btn-ghost" href="#s5">Request quotation</a>
			</div>
		</div>
	</div>
	<div class="hero-stats">
		<div class="container" style="display:contents;">
			<div class="stat-cell"><span class="stat-value">Est. 1983</span><span class="stat-label">Years of manufacture</span></div>
			<div class="stat-cell"><span class="stat-value">ISO 9001</span><span class="stat-label">Certified quality system</span></div>
			<div class="stat-cell"><span class="stat-value">90+</span><span class="stat-label">Instruments in production</span></div>
			<div class="stat-cell"><span class="stat-value">7</span><span class="stat-label">Instrument families</span></div>
		</div>
	</div>
</section>

<!-- Sheet 02 — Specimens -->
<section class="sheet" id="s2">
	<div class="container">
		<div class="sheet-meta">
			<span class="sheet-index">Specimens</span>
			<span class="sheet-tag">Featured instruments</span>
		</div>
		<h2 class="display-l sheet-header-title">Featured<br>Instruments</h2>
		<div class="specimen-layout">
			<?php
			if ( ! empty( $featured ) ) {
				$feature = array_shift( $featured );
				$stacked = array_splice( $featured, 0, 2 );
				ei_specimen_panel( $feature, 'ei-product', true );
				foreach ( $stacked as $product ) {
					ei_specimen_panel( $product, 'ei-card' );
				}
			}
			?>
		</div>
		<div class="table-footer">
			<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">View full index</a>
		</div>
	</div>
</section>

<!-- Sheet 03 — Index -->
<section class="sheet" id="s3">
	<div class="container">
		<div class="sheet-meta">
			<span class="sheet-index">Index</span>
			<span class="sheet-tag">Instrument catalogue</span>
		</div>
		<h2 class="display-l sheet-header-title">Instrument<br>Index</h2>
		<div class="catalog-table-wrap reveal">
			<table class="catalog-table">
				<thead>
					<tr>
						<th>No.</th>
						<th>Model</th>
						<th>Instrument</th>
						<th>Family</th>
						<th><span style="visibility:hidden;">Go</span></th>
					</tr>
				</thead>
				<tbody>
					<?php $row_n = 1; ?>
					<?php foreach ( $index_products as $product ) : ?>
						<?php
						$model   = get_post_meta( $product->ID, 'model_number', true );
						$terms   = wp_get_post_terms( $product->ID, 'products', array( 'fields' => 'names' ) );
						$primary = ( ! empty( $terms ) && ! is_wp_error( $terms ) ) ? $terms[0] : '';
						?>
						<tr>
							<td class="col-num"><?php echo esc_html( str_pad( (string) $row_n, 2, '0', STR_PAD_LEFT ) ); ?></td>
							<td class="col-model">EI-<?php echo esc_html( $model ? $model : '—' ); ?></td>
							<td class="col-name"><a href="<?php echo esc_url( get_permalink( $product ) ); ?>"><?php echo esc_html( get_the_title( $product ) ); ?></a></td>
							<td class="col-family"><?php echo esc_html( $primary ); ?></td>
							<td class="col-action"><a href="<?php echo esc_url( get_permalink( $product ) ); ?>" aria-label="<?php echo esc_attr( sprintf( 'View %s', get_the_title( $product ) ) ); ?>">&rarr;</a></td>
						</tr>
						<?php $row_n++; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="table-footer">
			<a class="btn-text" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">View full catalogue &rarr;</a>
		</div>
	</div>
</section>

<!-- Sheet 04 — Capabilities -->
<section class="sheet" id="s4">
	<div class="container">
		<div class="sheet-meta">
			<span class="sheet-index">Capabilities</span>
			<span class="sheet-tag">Instrument families</span>
		</div>
		<h2 class="display-l sheet-header-title">What we<br>manufacture</h2>
		<nav class="family-toc" aria-label="Instrument families">
			<?php foreach ( $capabilities as $cap ) : ?>
				<a class="family-toc-row reveal" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">
					<span class="toc-name"><?php echo esc_html( $cap['name'] ); ?></span>
					<span class="toc-meta">
						<span class="toc-desc"><?php echo esc_html( $cap['desc'] ); ?></span>
						<span>&rarr;</span>
					</span>
				</a>
			<?php endforeach; ?>
		</nav>
	</div>
</section>

<!-- Sheet 05 — Method -->
<section class="sheet" id="s5a">
	<div class="container">
		<div class="sheet-meta">
			<span class="sheet-index">Method</span>
			<span class="sheet-tag">Manufacture &amp; export</span>
		</div>
		<div class="narrative-grid reveal">
			<p class="narrative-statement">
				Designed in-house.<br>
				Tested to tolerance.<br>
				<span class="serif-accent">Documented for life.</span>
			</p>
			<div class="narrative-copy">
				<p>Manufactured to international practice and norms under strict quality control, our instruments serve general laboratory, pharmaceutical, clinical and field applications — and export to markets including England, Brazil, Syria, Iran, Nepal, Bangladesh and Nigeria.</p>
				<a class="btn-text" href="<?php echo esc_url( home_url( '/quality-control/' ) ); ?>">Read about quality control &rarr;</a>
			</div>
		</div>

		<figure class="pull-quote reveal">
			<blockquote>
				<p>&ldquo;Right from the start the team gave us confidence that we didn&rsquo;t have a Plan B. They accommodated every requirement within the time frame — and instrument performance has been satisfactory.&rdquo;</p>
			</blockquote>
			<figcaption class="mono-micro">Pravin A. Makwana — General Manager, J B Pharma</figcaption>
		</figure>

		<div class="clients-strip reveal" aria-label="Selected clients">
			<span class="mono-micro clients-label">Trusted by</span>
			<ul class="clients-list">
				<?php foreach ( $clients as $client ) : ?>
					<li><?php echo esc_html( $client ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>

<!-- Sheet 06 — Colophon -->
<section class="sheet colophon-section" id="s5">
	<div class="container colophon-wrap">
		<h2 class="colophon-headline">Instrument up<span class="dot">.</span></h2>
		<div class="colophon-row">
			<div class="colophon-contact-stack">
				<a href="tel:+911725033251">+91 172 5033251</a>
				<a href="mailto:info@electronicsindia.co.in">info@electronicsindia.co.in</a>
				<span>111, Industrial Area, Phase 2, Panchkula, India</span>
			</div>
			<a class="btn btn-solid" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Request quotation</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
