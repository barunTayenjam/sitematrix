<?php
/**
 * Single product — 10K Agency specimen datasheet.
 * Technical Luxury: model number, dark specimen chamber, spec table, CTAs.
 *
 * @package Ei
 */

get_header(); ?>

<article class="product-sheet">
	<div class="container">
		<header class="product-head">
			<div class="product-meta">
				<span class="eyebrow">Specimen Datasheet</span>
				<h1 class="product-title">
					<span class="product-model">EI-<?php echo esc_html( get_post_meta( get_the_ID(), 'model_number', true ) ?: '—' ); ?></span>
					<span><?php echo esc_html( get_the_title() ); ?></span>
				</h1>
				<?php
				$terms = wp_get_post_terms( get_the_ID(), 'products', array( 'fields' => 'names' ) );
				if ( $terms ) :
					?>
					<p class="product-family mono"><?php echo esc_html( implode( ' · ', $terms ) ); ?></p>
				<?php endif; ?>
			</div>
			<div class="product-actions">
				<?php
				$brochure = get_post_meta( get_the_ID(), 'brochure_pdf', true );
				if ( $brochure ) :
					?>
					<a class="btn btn-primary" href="<?php echo esc_url( $brochure ); ?>" target="_blank" rel="noopener">Download Brochure</a>
				<?php endif; ?>
				<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Request Quotation</a>
			</div>
		</header>

		<div class="product-body">
			<div class="product-media">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'ei-product', false, array( 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--titanium-dim);font-family:'IBM Plex Mono',monospace;font-size:0.7rem;">No image available</div>
				<?php endif; ?>
			</div>
			<div class="product-info">
				<?php
				$features = get_post_meta( get_the_ID(), 'features', true );
				if ( $features && is_array( $features ) ) :
					?>
					<section class="product-section">
						<h2 class="product-section-title">Features</h2>
						<ul class="product-feature-list">
							<?php foreach ( $features as $feature ) : ?>
								<li><span><?php echo esc_html( $feature ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php
				$specs = get_post_meta( get_the_ID(), 'specifications_table', true );
				if ( $specs ) :
					?>
					<section class="product-section">
						<h2 class="product-section-title">Specifications</h2>
						<table class="spec-table">
							<?php
							echo wp_kses(
								$specs,
								array(
									'table' => array(),
									'tbody' => array(),
									'tr'    => array(),
									'td'    => array(),
									'th'    => array(),
									'thead' => array(),
								)
							);
							?>
						</table>
					</section>
				<?php endif; ?>

				<?php if ( get_the_content() ) : ?>
					<section class="product-section">
						<h2 class="product-section-title">Description</h2>
						<div class="page-body"><?php echo wp_kses_post( get_the_content() ); ?></div>
					</section>
				<?php endif; ?>
			</div>
		</div>
	</div>
</article>

<?php get_footer(); ?>
