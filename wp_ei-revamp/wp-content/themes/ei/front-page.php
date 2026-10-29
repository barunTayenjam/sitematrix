<?php
/**
 * Front page — Electronics India.
 *
 * Hero (atmosphere photo + one CTA), proof strip, category grid,
 * featured products, closing CTA. Per MASTER.md pattern:
 * Trust & Authority + Conversion, Hero-Centric.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<!-- Hero -->
<section class="ei-hero">
	<video class="ei-hero-bg" autoplay muted loop playsinline preload="metadata" poster="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/ei-instruments-poster.jpg' ); ?>" aria-hidden="true" tabindex="-1">
		<source src="<?php echo esc_url( get_template_directory_uri() . '/assets/videos/ei-instruments.mp4' ); ?>" type="video/mp4">
	</video>
	<div class="container">
		<h1><?php esc_html_e( 'Precision Instruments', 'ei' ); ?></h1>
		<p class="ei-hero-sub"><?php esc_html_e( 'Analytical, laboratory and pharmaceutical instruments — designed and manufactured in Parwanoo, India, and trusted by labs nationwide for over four decades.', 'ei' ); ?></p>
		<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Browse the Catalogue', 'ei' ); ?></a>
	</div>
</section>

<!-- Proof strip -->
<section class="ei-proof">
	<div class="container">
		<div class="ei-proof-item reveal">
			<div class="number">40+</div>
			<p><?php esc_html_e( 'Years of manufacturing', 'ei' ); ?></p>
		</div>
		<div class="ei-proof-item reveal">
			<div class="number">90+</div>
			<p><?php esc_html_e( 'Instruments in catalogue', 'ei' ); ?></p>
		</div>
		<div class="ei-proof-item reveal">
			<div class="number">7</div>
			<p><?php esc_html_e( 'Instrument families', 'ei' ); ?></p>
		</div>
		<div class="ei-proof-item reveal">
			<div class="number">ISO</div>
			<p><?php esc_html_e( 'Certified quality systems', 'ei' ); ?></p>
		</div>
	</div>
</section>

<!-- Category grid -->
<section class="ei-section">
	<div class="container">
		<p class="ei-eyebrow reveal"><?php esc_html_e( 'What we make', 'ei' ); ?></p>
		<h2 class="ei-section-title reveal"><?php esc_html_e( 'Instrument Families', 'ei' ); ?></h2>
		<div class="ei-grid ei-cats">
			<?php
			$cats = get_terms( array(
				'taxonomy'   => 'products',
				'parent'     => 0,
				'hide_empty' => true,
				'orderby'    => 'name',
			) );
			if ( $cats && ! is_wp_error( $cats ) ) :
				foreach ( $cats as $index => $cat ) :
					$sub_count = count( get_terms( array(
						'taxonomy'   => 'products',
						'parent'     => $cat->term_id,
						'hide_empty' => true,
						'fields'     => 'ids',
					) ) );
					?>
					<a class="ei-cat-card reveal" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
						<h3><?php echo esc_html( $cat->name ); ?></h3>
						<p>
							<?php
							/* translators: %d: number of sub-categories. */
							echo esc_html( sprintf( _n( '%d product line', '%d product lines', max( $sub_count, $cat->count ), 'ei' ), max( $sub_count, $cat->count ) ) );
							?>
						</p>
					</a>
					<?php
				endforeach;
			endif;
			?>
		</div>
	</div>
</section>

<!-- Featured products -->
<section class="ei-section ei-section--alt">
	<div class="container">
		<p class="ei-eyebrow reveal"><?php esc_html_e( 'Handpicked', 'ei' ); ?></p>
		<h2 class="ei-section-title reveal"><?php esc_html_e( 'Featured Instruments', 'ei' ); ?></h2>
		<div class="ei-grid">
			<?php
			$featured = ei_featured_products( 4 );
			if ( $featured ) :
				foreach ( $featured as $post ) :
					setup_postdata( $post );
					ei_product_card( $post->ID );
				endforeach;
				wp_reset_postdata();
			else :
				/* Fallback: newest 4 products so the section is never empty. */
				$latest = get_posts( array( 'post_type' => 'product', 'numberposts' => 4, 'post_status' => 'publish' ) );
				foreach ( $latest as $post ) :
					setup_postdata( $post );
					ei_product_card( $post->ID );
				endforeach;
				wp_reset_postdata();
			endif;
			?>
		</div>
	</div>
</section>

<!-- Closing CTA -->
<section class="ei-cta">
	<div class="container">
		<h2 class="reveal"><?php esc_html_e( 'Need help choosing an instrument?', 'ei' ); ?></h2>
		<p class="reveal"><?php esc_html_e( 'Our engineers will walk you through specifications, compliance and pricing.', 'ei' ); ?></p>
		<a class="btn btn--primary reveal" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Talk to Us', 'ei' ); ?></a>
	</div>
</section>

<?php get_footer(); ?>
