<?php
/**
 * Single product — specimen datasheet page.
 * Mono model number, spec table, CTA buttons. No WordPress loop.
 *
 * @package Ei
 */

get_header(); ?>

<article class="ei-product-sheet">
	<header class="product-head">
		<div class="product-meta">
			<span class="product-eyebrow mono">01 · SPECIMEN</span>
			<h1 class="product-title mono">
				<span class="product-model"><?php echo esc_html( get_post_meta( get_the_ID(), 'model_number', true ) ?: '' ); ?></span>
				<?php echo esc_html( get_the_title() ); ?>
			</h1>
			<?php
			$terms = wp_get_post_terms( get_the_ID(), 'products', array( 'fields' => 'names' ) );
			if ( $terms ) :
				?>
				<p class="product-family mono" style="font-size:0.7rem;letter-spacing:0.14em;color:var(--stone);text-transform:uppercase;">
					<?php echo esc_html( implode( ' · ', $terms ) ); ?>
				</p>
			<?php endif; ?>
		</div>
		<div class="product-actions" style="display:flex;gap:12px;">
			<?php
			$brochure = get_post_meta( get_the_ID(), 'brochure_pdf', true );
			if ( $brochure ) :
				?>
				<a class="btn" href="<?php echo esc_url( $brochure ); ?>" target="_blank" rel="noopener">DOWNLOAD BROCHURE</a>
			<?php endif; ?>
			<a class="btn btn-primary" href="#contact">REQUEST QUOTATION</a>
		</div>
	</header>

	<div class="product-body">
		<div class="product-media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'ei-product', false, array( 'loading' => 'lazy' ) ); ?>
			<?php else : ?>
				<div class="product-placeholder" style="border:1px solid var(--line);aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;color:var(--stone);font-family:'IBM Plex Mono',monospace;font-size:0.7rem;letter-spacing:0.1em;">No image available</div>
			<?php endif; ?>
		</div>
		<div class="product-info">
			<?php
			$features = get_post_meta( get_the_ID(), 'features', true );
			if ( $features && is_array( $features ) ) :
				?>
				<section class="product-section">
					<h2 class="product-section-title">Features</h2>
					<ul class="product-feature-list" style="list-style:none;padding:0;">
						<?php foreach ( $features as $feature ) : ?>
							<li style="padding:6px 0;border-bottom:1px solid var(--line);font-size:0.9rem;color:var(--ink);"><?php echo esc_html( $feature ); ?></li>
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
					<div class="product-description"><?php echo wp_kses_post( get_the_content() ); ?></div>
				</section>
			<?php endif; ?>
		</div>
	</div>
</article>

<?php
get_footer();
