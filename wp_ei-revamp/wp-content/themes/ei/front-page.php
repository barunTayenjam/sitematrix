<?php
/**
 * Front page — Electronics India Double Beam.
 *
 * Direction: Awwwards-tier analytical instrument metaphor.
 * Light-only surfaces, locked brand tokens, IBM Plex self-hosted.
 * Sections, in order:
 * 1. Hero — "Precision, measured." with product photo + spectrum bar.
 * 2. Proof strip — detector rail: EST.1983 / ISO / 90+ / 150+ / 7 families.
 * 3. Families — wavelength selector (7 bands). Active band toggles specimen readout.
 * 4. Flagship — reference vs sample comparison per family.
 * 5. CTA — "Your application, specified."
 * 6. Contact — form + source block.
 *
 * @package Ei
 */

get_header();

/* Newest product becomes the hero artefact — a real instrument, not stock art. */
$hero_product = get_posts(
	array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$hero         = $hero_product ? $hero_product[0] : null;
$hero_model   = $hero ? ei_product_model( $hero->ID ) : '';
$hero_img     = $hero ? wp_get_attachment_image(
	get_post_thumbnail_id( $hero->ID ),
	'full',
	false,
	array(
		'loading'       => 'eager',
		'fetchpriority' => 'high',
		'alt'           => esc_attr( get_the_title( $hero->ID ) ),
	)
) : '';

/* Real product photography per family — map taxonomy terms to published product thumbnails. */
$ei_family_images = array();
$ei_terms         = get_terms(
	array(
		'taxonomy'   => 'products',
		'hide_empty' => false,
		'fields'     => 'all',
	)
);
if ( ! is_wp_error( $ei_terms ) && $ei_terms ) {
	foreach ( $ei_terms as $ei_term ) {
		$ei_products = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery -- single-term featured query
					array(
						'taxonomy' => 'products',
						'terms'    => array( $ei_term->term_id ),
					),
				),
			)
		);
		if ( $ei_products && $ei_products[0] ) {
			$ei_thumb = get_post_thumbnail_id( $ei_products[0]->ID );
			if ( $ei_thumb ) {
				$ei_family_images[ strtolower( $ei_term->name ) ] = wp_get_attachment_image_url( $ei_thumb, 'full' );
			}
		}
	}
}

wp_add_inline_script(
	'ei-script',
	'window.eiFamilyImages = ' . wp_json_encode( $ei_family_images ) . ';',
	'before'
);

?>
<!-- [1] HERO -->
<section class="hero" id="s1">
	<div class="hero-inner">
		<p class="eyebrow">ANALYTICAL · SCIENTIFIC · LABORATORY · PHARMACEUTICAL INSTRUMENTS</p>
		<h1 class="hero-title"><span>Precision,</span> <em>measured.</em></h1>

		<div class="hero-grid">
			<figure class="hero-art" aria-hidden="true">
				<div class="art-frame">
					<?php echo wp_kses_post( $hero_img ); ?>
					<div class="art-overlay">
						<span class="art-model">EI-UV-2000</span>
						<span class="art-badge">NEW</span>
					</div>
				</div>
				<figcaption class="mono">UV-VISIBLE SPECTROPHOTOMETER</figcaption>
			</figure>

			<div class="hero-copy">
				<p class="hero-lede">Electronics India designs, manufactures and exports analytical, scientific, laboratory and pharmaceutical instruments — spectrophotometers, pH meters, conductivity meters, flame photometers, dissolution apparatus and moisture titrators — from Parwanoo, Himachal Pradesh, since 1983.</p>

				<div class="hero-path">
					<span class="path-label">SOURCE</span>
					<span class="path-seg"></span>
					<span class="path-label path-split">SPLITTER</span>
					<span class="path-seg"></span>
					<span class="path-label">SAMPLE</span>
					<span class="path-seg path-seg-hot"></span>
					<span class="path-label path-det">DETECTOR</span>
				</div>

				<div class="hero-actions">
					<a class="btn-solid" href="#s4">BROWSE THE CATALOGUE</a>
					<a class="btn-ghost" href="#s7">GET A QUOTE</a>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- [2] PROOF -->
<section class="proof" id="s2">
	<div class="proof-rail">
		<div class="proof-cell"><span class="count mono" data-count="1983">1983</span><span class="proof-label mono">ESTABLISHED</span></div>
		<div class="proof-cell"><span class="proof-word mono">ISO</span><span class="proof-label mono">9001 CERTIFIED</span></div>
		<div class="proof-cell"><span class="count mono" data-count="90" data-suffix="+">90+</span><span class="proof-label mono">INSTRUMENTS IN PRODUCTION</span></div>
		<div class="proof-cell"><span class="count mono" data-count="150" data-suffix="+">150+</span><span class="proof-label mono">COUNTRIES SERVED</span></div>
		<div class="proof-cell"><span class="count mono" data-count="7">7</span><span class="proof-label mono">FAMILIES</span></div>
	</div>
</section>

<!-- [3] FAMILIES -->
<section class="mono-section" id="s4">
	<header class="sec-head">
		<span class="sec-no mono">01 — CATALOGUE</span>
		<h2 class="sec-title">Seven families. One standard.</h2>
		<p class="sec-dek">Every instrument is built on the same bench discipline. Select a family to view its flagship.</p>
	</header>
	<div class="wavestrip" role="tablist" aria-label="Product families">
		<button class="band mono" role="tab" aria-selected="true"  data-band="0"><span class="band-nm">UV-VIS</span><span class="band-name">SPECTROPHOTOMETERS</span></button>
		<button class="band mono" role="tab" aria-selected="false" data-band="1"><span class="band-nm">pH</span><span class="band-name">ELECTROCHEMISTRY</span></button>
		<button class="band mono" role="tab" aria-selected="false" data-band="2"><span class="band-nm">USP</span><span class="band-name">PHARMACEUTICAL</span></button>
		<button class="band mono" role="tab" aria-selected="false" data-band="3"><span class="band-nm">CFU</span><span class="band-name">BLOOD TESTING</span></button>
		<button class="band mono" role="tab" aria-selected="false" data-band="4"><span class="band-nm">Na/K</span><span class="band-name">FLAME PHOTOMETERS</span></button>
		<button class="band mono" role="tab" aria-selected="false" data-band="5"><span class="band-nm">°C</span><span class="band-name">MELTING POINT</span></button>
		<button class="band mono" role="tab" aria-selected="false" data-band="6"><span class="band-nm">ppm</span><span class="band-name">MOISTURE TITRATORS</span></button>
	</div>
	<div class="band-readout mono" aria-live="polite"><span class="led" aria-hidden="true"></span><span id="bandReadout">SPECTROPHOTOMETERS · SINGLE & DOUBLE BEAM · 190–1100 NM</span></div>
</section>

<!-- [4] FLAGSHIPS -->
<section class="specimens" id="s5">
	<header class="sec-head">
		<span class="sec-no mono">02 — FLAGSHIP</span>
		<h2 class="sec-title">Reference versus instrument.</h2>
		<p class="sec-dek">The reference column holds the accepted standard. The EI column holds the flagship.</p>
	</header>
	<div id="specimenHost"></div>
	<div class="spec-actions">
		<a class="btn-solid" href="#s7">REQUEST QUOTATION</a>
		<a class="btn-ghost" href="#s4">BACK TO FAMILIES</a>
	</div>
</section>

<!-- [5] CTA -->
<section class="cta" id="s6">
	<div class="cta-inner">
		<h2 class="cta-title">Your application,<br><em>specified.</em></h2>
		<p class="cta-dek">Send us your sample — application, volume, compliance scope — and our engineers will spec the instrument, accessories and calibration chain.</p>
		<div class="hero-actions">
			<a class="btn-solid" href="#s7">START A CONVERSATION</a>
		</div>
	</div>
</section>

<!-- [6] CONTACT -->
<section class="contact" id="s7">
	<header class="sec-head">
		<span class="sec-no mono">03 — CONTACT</span>
		<h2 class="sec-title">Talk to the source.</h2>
	</header>
	<div class="contact-grid">
		<form class="c-form" novalidate>
			<div class="c-row"><label class="mono" for="cn">NAME</label><input id="cn" type="text" required></div>
			<div class="c-row"><label class="mono" for="ce">EMAIL</label><input id="ce" type="email" required></div>
			<div class="c-row"><label class="mono" for="cp">APPLICATION</label><input id="cp" type="text" placeholder="e.g. pharma QC, water testing"></div>
			<div class="c-row"><label class="mono" for="cm">SAMPLE BRIEF</label><textarea id="cm" rows="4"></textarea></div>
			<button class="btn-solid" type="submit">SEND ▸</button>
		</form>
		<div class="c-block mono">
			<span class="c-label">DIRECT</span>
			<a href="tel:+911725033251">+91 172 5033251</a>
			<a href="mailto:info@electronicsindia.co.in">INFO@ELECTRONICSINDIA.CO.IN</a>
			<span class="c-label">WORKS</span>
			<span>PARWANOO, DIST. SOLAN</span>
			<span>HIMACHAL PRADESH 173220, INDIA</span>
		</div>
	</div>
</section>

<?php get_footer(); ?>
