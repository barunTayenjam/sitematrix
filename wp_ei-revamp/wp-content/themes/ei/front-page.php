<?php
/**
 * Front page — LEDGER datasheet, 5 sheets.
 * Manifesto → Index → Specimen → Trace → Colophon.
 *
 * @package Ei
 */

get_header(); ?>

<?php
/* --- Index: product families with aggregated counts (products live on child terms) --- */
$families = get_terms(
	array(
		'taxonomy'   => 'products',
		'parent'     => 0,
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
		'number'     => 7,
	)
);

/* --- Specimen: featured products --- */
$featured = get_posts(
	array(
		'post_type'      => 'product',
		'posts_per_page' => 2,
		'post_status'    => 'publish',
		'meta_key'       => 'show_on_home', // phpcs:ignore WordPress.DB.SlowDBQuery -- featured flag lookup
		'meta_value'     => 1, // phpcs:ignore WordPress.DB.SlowDBQuery -- featured flag lookup
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
	)
);

/* Helper: first N spec rows as label/value pairs. */
if ( ! function_exists( 'ei_spec_rows' ) ) {
	/**
	 * First N specification rows from the legacy HTML table as label/value pairs.
	 *
	 * @param string $html Raw specifications_table meta.
	 * @param int    $n   Rows to return.
	 * @return array[] Array of [label, value] pairs.
	 */
	function ei_spec_rows( $html, $n = 4 ) {
		if ( ! is_string( $html ) || '' === $html ) {
			return array();
		}
		preg_match_all( '/<tr>(.*?)<\/tr>/s', $html, $matches );
		$rows = array();
		foreach ( array_slice( $matches[1], 0, $n ) as $tr ) {
			preg_match_all( '/<td[^>]*>(.*?)<\/td>/s', $tr, $cells );
			if ( 2 === count( $cells[1] ) ) {
				$rows[] = array( wp_strip_all_tags( $cells[1][0] ), wp_strip_all_tags( $cells[1][1] ) );
			}
		}
		return $rows;
	}
}
?>

<!-- [1] MANIFESTO -->
<section class="sheet sheet-manifesto reveal" id="s1">
	<div class="corner tl" aria-hidden="true"></div>
	<div class="corner tr" aria-hidden="true"></div>
	<div class="corner bl" aria-hidden="true"></div>
	<div class="corner br" aria-hidden="true"></div>
	<div class="manifesto-grid">
		<h1 class="display">
			<span class="line-a">Precision</span>
			<span class="line-b">Measured<span class="ei-red">.</span></span>
			<span class="underline" aria-hidden="true"></span>
		</h1>
		<div class="lede-cols">
			<p><?php esc_html_e( 'Analytical, scientific, laboratory and pharmaceutical instruments — designed, manufactured and exported from Parwanoo, Himachal Pradesh, since 1983.', 'ei' ); ?></p>
			<p><?php esc_html_e( 'Every instrument ships with traceable calibration. Every unit is built, tested and documented in-house. One standard: precision, measured.', 'ei' ); ?></p>
		</div>
		<div class="manifesto-actions">
			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/products/' ) ); ?>">Browse the catalogue</a>
			<a class="btn" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Get a quote</a>
		</div>
	</div>
</section>

<!-- [2] INDEX — product families -->
<section class="sheet sheet-index reveal" id="s2">
	<header class="sheet-head">
		<span class="sheet-index mono">02</span>
		<h2 class="sheet-title">Index of families</h2>
	</header>
	<nav class="toc" aria-label="Product families">
		<?php foreach ( $families as $i => $family ) : ?>
			<?php
			$count = $family->count;
			$kids  = get_terms(
				array(
					'taxonomy'   => 'products',
					'parent'     => $family->term_id,
					'hide_empty' => false,
				)
			);
			if ( ! is_wp_error( $kids ) ) {
				foreach ( $kids as $kid ) {
					$count += $kid->count;
				}
			}
			?>
			<a class="toc-row" href="<?php echo esc_url( get_term_link( $family ) ); ?>">
				<span class="toc-num mono"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
				<span class="toc-name"><?php echo esc_html( $family->name ); ?></span>
				<span class="toc-dots" aria-hidden="true"></span>
				<span class="toc-note mono">
				<?php
				// translators: %d instrument|%d instruments.
				echo esc_html( sprintf( _n( '%d instrument', '%d instruments', $count, 'ei' ), $count ) );
				?>
				</span>
				<span class="toc-arrow" aria-hidden="true">▸</span>
			</a>
		<?php endforeach; ?>
	</nav>
</section>

<!-- [3] SPECIMEN — featured products -->
<section class="sheet sheet-specimen reveal" id="s3">
	<header class="sheet-head">
		<span class="sheet-index mono">03</span>
		<h2 class="sheet-title">Selected specimens</h2>
	</header>
	<?php foreach ( $featured as $product ) : ?>
		<?php
		$model    = get_post_meta( $product->ID, 'model_number', true );
		$spec_obj = ei_spec_rows( get_post_meta( $product->ID, 'specifications_table', true ), 4 );
		$excerpt  = get_the_excerpt( $product );
		?>
		<article class="specimen">
			<div class="specimen-model">
				<span class="reg-cross rx1" aria-hidden="true"></span>
				<span class="reg-cross rx2" aria-hidden="true"></span>
				<span class="model-no mono"><?php echo esc_html( $model ? 'EI-' . $model : get_the_title( $product ) ); ?></span>
			</div>
			<div class="specimen-data">
				<h3 class="specimen-name"><?php echo esc_html( get_the_title( $product ) ); ?></h3>
				<?php if ( $spec_obj ) : ?>
					<dl class="spec-table">
						<?php foreach ( $spec_obj as $row ) : ?>
							<div class="spec-row-t">
								<dt class="mono"><?php echo esc_html( $row[0] ); ?></dt>
								<dd><?php echo esc_html( $row[1] ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
				<?php if ( $excerpt ) : ?>
					<p class="specimen-note"><?php echo esc_html( wp_trim_words( $excerpt, 24 ) ); ?></p>
				<?php endif; ?>
				<a class="btn" href="<?php echo esc_url( get_permalink( $product ) ); ?>">View specifications</a>
			</div>
		</article>
	<?php endforeach; ?>
</section>

<!-- [4] TRACE -->
<section class="sheet sheet-trace reveal" id="s4">
	<header class="sheet-head">
		<span class="sheet-index mono">04</span>
		<h2 class="sheet-title">Live trace</h2>
	</header>
	<div class="trace-panel">
		<canvas id="scopeCanvas" width="1200" height="300" aria-hidden="true"></canvas>
		<div class="trace-readouts mono">
			<span>CH1 · ±0.02 %</span>
			<span>RANGE · FULL SCALE</span>
			<span>TRACE · STABLE</span>
			<span class="led-wrap"><span class="led" aria-hidden="true"></span>READY</span>
		</div>
	</div>
	<p class="trace-facts mono">EST. 1983 · ISO 9001 CERTIFIED · 90+ INSTRUMENTS IN PRODUCTION · 150+ COUNTRIES SERVED</p>
</section>

<!-- [5] COLOPHON -->
<section class="sheet sheet-colophon reveal" id="s5">
	<div class="colophon-inner">
		<p class="colophon-eyebrow mono">05 · COLOPHON</p>
		<h2 class="colophon-title">Instrument up<span class="ei-red">.</span></h2>
		<div class="colophon-row">
			<div class="colophon-contact mono">
				<span>+91 172 5033251</span>
				<span>enquiry@electronicsindia.co.in</span>
				<span>PARWANOO, HIMACHAL PRADESH, INDIA</span>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Request quotation</a>
		</div>
	</div>
</section>

<?php
get_footer();
