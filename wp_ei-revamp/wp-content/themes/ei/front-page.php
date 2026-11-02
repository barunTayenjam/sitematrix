<?php
/**
 * Front page — 10K Agency datasheet layout.
 * Five sheets: manifesto, index, specimen, trace, colophon.
 * No WordPress loop — targeted queries only.
 *
 * @package Ei
 */
get_header(); ?>

<?php
/* --- Manifesto data --- */
$hero_product  = get_posts( array( 'post_type' => 'product', 'posts_per_page' => 1, 'post_status' => 'publish', 'meta_key' => 'show_on_home', 'meta_value' => 1, 'orderby' => 'meta_value_num', 'order' => 'ASC' ) );
$hero          = $hero_product ? $hero_product[0] : null;
$hero_model    = $hero ? get_post_meta( $hero->ID, 'model_number', true ) : '';
$hero_desc     = $hero ? get_post_meta( $hero->ID, 'description', true ) : '';
$hero_thumb    = $hero ? get_post_thumbnail_id( $hero->ID ) : 0;

/* --- Index: product families --- */
$families = get_terms( array( 'taxonomy' => 'products', 'parent' => 0, 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC', 'number' => 7 ) );

/* --- Specimen: featured home products --- */
$featured = get_posts( array( 'post_type' => 'product', 'posts_per_page' => 4, 'post_status' => 'publish', 'meta_key' => 'show_on_home', 'meta_value' => 1, 'orderby' => 'meta_value_num', 'order' => 'ASC' ) );
?>

<!-- [1] MANIFESTO -->
<section class="sheet sheet-manifesto reveal" id="s1">
	<div class="corner tl" aria-hidden="true"></div>
	<div class="corner tr" aria-hidden="true"></div>
	<div class="corner bl" aria-hidden="true"></div>
	<div class="corner br" aria-hidden="true"></div>
	<div class="manifesto-grid">
		<h1 class="display">
			<span class="line-a mono"><?php bloginfo( 'name' ); ?></span>
			<span class="line-b"><?php bloginfo( 'description' ); ?><span class="ei-red">.</span></span>
			<span class="underline" aria-hidden="true"></span>
		</h1>
		<div class="lede-cols">
			<?php if ( $hero_desc ) : ?>
				<p><?php echo wp_kses_post( $hero_desc ); ?></p>
			<?php else : ?>
				<p><?php esc_html_e( 'Precision instruments, built and documented in-house. One standard: precision, measured.', 'ei' ); ?></p>
			<?php endif; ?>
			<p class="mono"><?php echo esc_html( $hero_model ? $hero_model : 'EI · Standard Series' ); ?></p>
		</div>
		<?php if ( $hero_thumb ) : ?>
			<figure class="hero-art" aria-hidden="true">
				<div class="art-frame">
					<?php echo wp_get_attachment_image( $hero_thumb, 'full', false, array( 'loading' => 'eager', 'fetchpriority' => 'high', 'width' => 800, 'height' => 600 ) ); ?>
				</div>
				<figcaption class="mono"><?php echo esc_html( $hero ? get_the_title( $hero->ID ) : 'Featured Instrument' ); ?></figcaption>
			</figure>
		<?php endif; ?>
	</div>
</section>

<!-- [2] INDEX — product families as table of contents -->
<section class="sheet sheet-index reveal" id="s2">
	<header class="sheet-head">
		<span class="sheet-index mono">02</span>
		<h2 class="sheet-title">Index of families</h2>
	</header>
	<nav class="toc" aria-label="Product families">
		<?php foreach ( $families as $i => $family ) : ?>
			<a class="toc-row" href="<?php echo esc_url( get_term_link( $family ) ); ?>">
				<span class="toc-num mono"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
				<span class="toc-name"><?php echo esc_html( $family->name ); ?></span>
				<span class="toc-dots" aria-hidden="true"></span>
				<span class="toc-note mono"><?php echo esc_html( $family->count . ' instrument' . ( $family->count !== 1 ? 's' : '' ) ); ?></span>
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
	<?php if ( $featured ) : ?>
		<?php foreach ( $featured as $product ) : ?>
			<div class="specimen">
				<div class="specimen-model">
					<span class="reg-cross rx1" aria-hidden="true"></span>
					<span class="reg-cross rx2" aria-hidden="true"></span>
					<span class="model-no mono"><?php echo esc_html( get_post_meta( $product->ID, 'model_number', true ) ?: 'EI-' . strtoupper( substr( $product->post_name, 0, 6 ) ) ); ?></span>
					<span class="model-badge mono">NEW</span>
				</div>
				<div class="specimen-data">
					<h3 class="specimen-name"><?php echo esc_html( $product->post_title ); ?></h3>
					<?php
					$specs = get_post_meta( $product->ID, 'specifications_table', true );
					if ( $specs ) : ?>
						<dl class="spec-table">
							<?php echo wp_kses( $specs, array( 'table' => array(), 'tbody' => array(), 'tr' => array(), 'td' => array(), 'th' => array(), 'thead' => array() ) ); ?>
						</dl>
					<?php endif; ?>
					<?php if ( $product->post_excerpt ) : ?>
						<p class="specimen-note"><?php echo wp_kses_post( $product->post_excerpt ); ?></p>
					<?php endif; ?>
					<a class="btn" href="<?php echo esc_url( get_permalink( $product ) ); ?>">VIEW SPECIFICATIONS</a>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</section>

<!-- [4] TRACE — canvas + facts -->
<section class="sheet sheet-trace reveal" id="s4">
	<header class="sheet-head">
		<span class="sheet-index mono">04</span>
		<h2 class="sheet-title">Live trace</h2>
	</header>
	<div class="trace-panel">
		<canvas id="scopeCanvas" width="1200" height="300"></canvas>
		<div class="trace-readouts mono">
			<span>CH1 · ±0.02 %</span>
			<span>RANGE · FULL SCALE</span>
			<span>TRACE · STABLE</span>
			<span class="led-wrap"><span class="led" aria-hidden="true"></span>READY</span>
		</div>
	</div>
	<p class="trace-facts mono">
		<?php echo esc_html( bloginfo( 'name' ) ); ?> · EST. 1983 · ISO 9001 CERTIFIED · <?php echo esc_html( count( $featured ) . '+' ); ?> INSTRUMENTS IN PRODUCTION · 150+ COUNTRIES SERVED
	</p>
</section>

<!-- [5] COLOPHON -->
<section class="sheet sheet-colophon reveal" id="s5">
	<div class="colophon-inner">
		<p class="colophon-eyebrow mono">05 · COLOPHON</p>
		<h2 class="colophon-title">Instrument up<span class="ei-red">.</span></h2>
		<div class="colophon-row">
			<div class="colophon-contact mono">
				<span><?php echo esc_html( get_option( 'admin_email' ) ?: 'info@example.com' ); ?></span>
				<span>PARWANOO, HIMACHAL PRADESH, INDIA</span>
			</div>
			<a class="btn btn-primary" href="#contact">REQUEST QUOTATION</a>
		</div>
	</div>
</section>

<?php get_footer();
