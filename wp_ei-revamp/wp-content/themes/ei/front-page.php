<?php
/**
 * Front page — 10K Agency.
 * Technical Luxury: obsidian hero, massive Anton type, Framed Specimen Chambers, Trace oscilloscope, Colophon.
 *
 * @package Ei
 */

get_header(); ?>

<?php
/* --- Featured products (show_on_home = 1), limit 6 --- */
$featured = get_posts(
	array(
		'post_type'      => 'product',
		'posts_per_page' => 6,
		'post_status'    => 'publish',
		'meta_key'       => 'show_on_home',
		'meta_value'     => 1,
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
?>

<!-- HERO — obsidian full viewport -->
<section class="hero sheet reveal" id="s1">
	<div class="container">
		<span class="eyebrow">01 · Manifesto</span>
		<h1>
			<span class="word">Precision</span>
			<span class="word"><span class="accent">Measured.</span></span>
		</h1>
		<p class="hero-lede">
			10K Agency designs, manufactures and exports analytical, scientific, laboratory and pharmaceutical instruments — built, tested and documented in-house.
			One standard: precision, measured.
		</p>
		<div class="hero-actions">
			<a class="btn btn-primary" href="#s2">Explore Specimens</a>
			<a class="btn btn-ghost" href="#s5">Request Quotation</a>
		</div>
	</div>
</section>

<!-- SPECIMEN CHAMBERS — Framed product showcase -->
<section class="sheet reveal" id="s2" style="background: var(--obsidian-elevated); border-top: 1px solid var(--glass-border);">
	<div class="container">
		<header class="sheet-head">
			<span class="sheet-index">02 · Specimens</span>
			<h2 class="sheet-title">Featured Instruments</h2>
		</header>
		<div class="specimen-grid">
			<?php
			foreach ( $featured as $product ) :
				$model   = get_post_meta( $product->ID, 'model_number', true );
				$thumb   = get_post_thumbnail_id( $product->ID );
				$excerpt = get_the_excerpt( $product );
				$tags    = wp_get_post_terms( $product->ID, 'products', array( 'fields' => 'names' ) );
				?>
				<article class="chamber reveal" data-product="<?php echo esc_attr( $product->ID ); ?>">
					<div class="chamber-media">
						<?php if ( $thumb ) : ?>
							<?php
							echo wp_get_attachment_image(
								$thumb,
								'ei-chamber',
								false,
								array(
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
							?>
						<?php else : ?>
							<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--titanium-dim);font-family:'IBM Plex Mono',monospace;font-size:0.6rem;">No image</div>
						<?php endif; ?>
						<div class="chamber-specs">
							<span class="chamber-model">EI-<?php echo esc_html( $model ?: '—' ); ?></span>
							<div class="chamber-tags">
								<?php foreach ( (array) $tags as $term_name ) : ?>
									<span class="chamber-tag"><?php echo esc_html( $term_name ); ?></span>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
					<div class="chamber-body">
						<h3 class="chamber-title"><a href="<?php echo esc_url( get_permalink( $product ) ); ?>"><?php echo esc_html( $product->post_title ); ?></a></h3>
						<?php if ( $excerpt ) : ?>
							<p class="chamber-desc"><?php echo esc_html( wp_trim_words( $excerpt, 22 ) ); ?></p>
						<?php endif; ?>
						<a class="btn btn-glass" href="<?php echo esc_url( get_permalink( $product ) ); ?>">View Specifications</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- TRACE — Oscilloscope panel -->
<section class="sheet reveal" id="s3" style="background: var(--obsidian); border-top: 1px solid var(--glass-border);">
	<div class="container">
		<header class="sheet-head">
			<span class="sheet-index">03 · Trace</span>
			<h2 class="sheet-title">Live Oscilloscope</h2>
		</header>
		<div class="trace-panel">
			<canvas id="scopeCanvas" width="1280" height="280" aria-hidden="true"></canvas>
			<div class="trace-readouts mono">
				<span>CH1 · ±0.012%</span>
				<span>RANGE · FULL SCALE</span>
				<span>TIMEBASE · 2ms/div</span>
				<span class="led">STABLE</span>
			</div>
		</div>
		<p class="trace-facts mono">
			EST. 1983 · ISO 9001 CERTIFIED · 90+ INSTRUMENTS IN PRODUCTION · 150+ COUNTRIES SERVED
		</p>
	</div>
</section>

<!-- COLOPHON -->
<section class="sheet sheet-colophon reveal" id="s5">
	<div class="container colophon-inner">
		<h2 class="colophon-title">Instrument up<span class="accent">.</span></h2>
		<div class="colophon-row">
			<div class="colophon-contact mono">
				<span><?php esc_html_e( '+91 172 5033251', 'ei' ); ?></span>
				<span><?php esc_html_e( 'info@electronicsindia.co.in', 'ei' ); ?></span>
				<span><?php esc_html_e( 'PARWANOO, HIMACHAL PRADESH, INDIA', 'ei' ); ?></span>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Request Quotation</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
