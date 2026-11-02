<?php
/**
 * Front page — 10K Agency.
 * Dark obsidian hero: massive type, dual-lane marquee, 4–6 featured products.
 * Light sheets below: catalogue, specimen, trace, colophon.
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
		'meta_key'       => 'show_on_home', // phpcs:ignore WordPress.DB.SlowDBQuery -- featured flag
		'meta_value'     => 1, // phpcs:ignore WordPress.DB.SlowDBQuery -- featured flag
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

/* --- Marquee words --- */
$marquee_words = array(
	'PRECISION',
	'MEASURED',
	'10K AGENCY',
	'INSTRUMENTS',
	'DESIGNED',
	'BUILT',
	'EXPORTED',
	'ISO 9001',
	'PARWANOO',
	'SINCE 1983',
);
?>

<!-- HERO — obsidian -->
<section class="hero sheet reveal" id="s1">
	<div class="hero-eyebrow">01 · MANIFESTO</div>
	<h1 class="type-reveal">
		<span class="word">PRECISION</span>
		<span class="word">MEASURED<span class="accent">.</span></span>
	</h1>
	<p class="hero-lede">10K Agency designs, manufactures and exports analytical, scientific, laboratory and pharmaceutical instruments — built, tested and documented in-house. One standard: precision, measured.</p>
	<div class="hero-actions">
		<a class="btn btn-primary" href="#s2">BROWSE THE CATALOGUE</a>
		<a class="btn btn-ghost" href="#s5">REQUEST QUOTATION</a>
	</div>
</section>

<!-- MARQUEE -->
<div class="marquee">
	<div class="marquee-lane slow">
		<?php foreach ( $marquee_words as $word ) : ?>
			<span><?php echo esc_html( $word ); ?></span>
		<?php endforeach; ?>
		<?php foreach ( $marquee_words as $word ) : ?>
			<span class="marquee-duplicate"><?php echo esc_html( $word ); ?></span>
		<?php endforeach; ?>
	</div>
	<div class="marquee-lane fast">
		<?php foreach ( array_reverse( $marquee_words ) as $word ) : ?>
			<span><?php echo esc_html( $word ); ?></span>
		<?php endforeach; ?>
		<?php foreach ( array_reverse( $marquee_words ) as $word ) : ?>
			<span class="marquee-duplicate"><?php echo esc_html( $word ); ?></span>
		<?php endforeach; ?>
	</div>
</div>

<!-- CATALOGUE — product grid -->
<section class="sheet sheet-index reveal" id="s2">
	<header class="sheet-head">
		<span class="sheet-index mono">02 · CATALOGUE</span>
		<h2 class="sheet-title">Instruments</h2>
	</header>
	<div class="product-grid">
		<?php foreach ( $featured as $product ) : ?>
			<?php
			$model   = get_post_meta( $product->ID, 'model_number', true );
			$thumb   = get_post_thumbnail_id( $product->ID );
			$excerpt = get_the_excerpt( $product );
			?>
			<article class="product-card">
				<div class="product-media">
					<?php if ( $thumb ) : ?>
						<?php
						echo wp_get_attachment_image(
							$thumb,
							'ei-card',
							false,
							array(
								'loading'  => 'lazy',
								'decoding' => 'async',
							)
						);
						?>
					<?php else : ?>
						<div class="product-placeholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--stone);font-family:'IBM Plex Mono',monospace;font-size:0.65rem;">No image</div>
					<?php endif; ?>
					<?php if ( $model ) : ?>
						<span class="product-badge"><?php echo esc_html( $model ); ?></span>
					<?php endif; ?>
				</div>
				<div class="product-body">
					<span class="product-model"><?php echo esc_html( $model ? 'EI-' . $model : '' ); ?></span>
					<h3 class="product-title"><?php echo esc_html( $product->post_title ); ?></h3>
					<?php if ( $excerpt ) : ?>
						<p style="font-size:0.8rem;color:var(--stone);line-height:1.5;margin:0;"><?php echo esc_html( wp_trim_words( $excerpt, 16 ) ); ?></p>
					<?php endif; ?>
					<a class="btn btn-light" href="<?php echo esc_url( get_permalink( $product ) ); ?>">VIEW SPECIFICATIONS</a>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>

<!-- TRACE — live canvas -->
<section class="sheet sheet-trace reveal" id="s4">
	<header class="sheet-head">
		<span class="sheet-index mono">03 · TRACE</span>
		<h2 class="sheet-title">Live Trace</h2>
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

<!-- COLOPHON -->
<section class="sheet sheet-colophon reveal" id="s5">
	<div class="colophon-inner">
		<p class="colophon-eyebrow mono">04 · COLOPHON</p>
		<h2 class="colophon-title">Instrument up<span class="accent">.</span></h2>
		<div class="colophon-row">
			<div class="colophon-contact mono">
				<span>+91 172 5033251</span>
				<span>enquiry@electronicsindia.co.in</span>
				<span>PARWANOO, HIMACHAL PRADESH, INDIA</span>
			</div>
			<a class="btn btn-primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">REQUEST QUOTATION</a>
		</div>
	</div>
</section>

<?php
get_footer();
