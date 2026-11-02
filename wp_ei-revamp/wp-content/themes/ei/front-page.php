<?php
/**
 * Front page — 10K Agency · Warm Editorial Light.
 * Sheets: 01 Manifesto · 02 Specimens (asymmetric) · 03 Index table · 04 Method · 05 Colophon.
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

?>

<!-- Sheet 01 — Manifesto -->
<section class="sheet hero reveal" id="s1">
	<div class="container hero-content">
		<div>
			<span class="mono-micro" style="display:block;margin-bottom:24px;">Est. 1983 — Precision Instrument Works</span>
			<h1 class="hero-headline">
				<span class="display-xl">Precision,</span>
				<span class="serif-accent">measured.</span>
			</h1>
			<p class="body-l hero-lede">10K Agency designs, manufactures and exports analytical, scientific, laboratory and pharmaceutical instruments — built, tested and documented in-house. One standard: precision, measured.</p>
			<div class="hero-actions">
				<a class="btn btn-solid" href="#s2">Explore specimens</a>
				<a class="btn btn-ghost" href="#s5">Request quotation</a>
			</div>
		</div>
	</div>
	<div class="hero-stats">
		<div class="container" style="display:contents;">
			<div class="stat-cell"><span class="stat-value">ISO 9001</span><span class="stat-label">Certified quality system</span></div>
			<div class="stat-cell"><span class="stat-value">90+</span><span class="stat-label">Instruments in production</span></div>
			<div class="stat-cell"><span class="stat-value">150+</span><span class="stat-label">Countries served</span></div>
			<div class="stat-cell"><span class="stat-value">41</span><span class="stat-label">Years of manufacture</span></div>
		</div>
	</div>
</section>

<!-- Sheet 02 — Specimens -->
<section class="sheet" id="s2">
	<div class="container">
		<div class="sheet-meta">
			<span class="sheet-index">02 — Specimens</span>
			<span class="sheet-tag">Featured instruments</span>
		</div>
		<h2 class="display-l sheet-header-title">Featured<br>Instruments</h2>
		<div class="specimen-layout">
			<?php
			if ( ! empty( $featured ) ) {
				$feature  = array_shift( $featured );
				$stacked  = array_splice( $featured, 0, 2 );
				$stripped = $featured;
				ei_specimen_panel( $feature, 'ei-product', true );
				foreach ( $stacked as $product ) {
					ei_specimen_panel( $product, 'ei-card' );
				}
			}
			?>
		</div>
		<?php if ( ! empty( $stripped ) ) : ?>
			<div class="table-footer">
				<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">View full index</a>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- Sheet 03 — Index -->
<section class="sheet" id="s3">
	<div class="container">
		<div class="sheet-meta">
			<span class="sheet-index">03 — Index</span>
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
						$primary = ! empty( $terms ) ? $terms[0] : '';
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

<!-- Sheet 04 — Method -->
<section class="sheet" id="s4">
	<div class="container">
		<div class="sheet-meta">
			<span class="sheet-index">04 — Method</span>
			<span class="sheet-tag">How we build</span>
		</div>
		<div class="narrative-grid reveal">
			<p class="narrative-statement">
				Designed in-house.<br>
				Tested to tolerance.<br>
				<span class="serif-accent">Documented for life.</span>
			</p>
			<div class="narrative-copy">
				<p>Every instrument is engineered, calibrated and verified against its published specification before it leaves the works. Datasheets, calibration certificates and spare-part schedules ship with each unit — for the life of the instrument.</p>
				<a class="btn-text" href="<?php echo esc_url( home_url( '/quality-control/' ) ); ?>">Read about quality control &rarr;</a>
			</div>
		</div>
	</div>
</section>

<!-- Sheet 05 — Colophon -->
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
