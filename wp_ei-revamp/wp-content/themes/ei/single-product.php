<?php
/**
 * Single product — 10K Agency specimen datasheet.
 * Warm editorial light: model, dark data, features + spec table, CTA panel.
 *
 * @package Ei
 */

get_header(); ?><article class="datasheet-sheet">
	<div class="container">
		<div class="datasheet-breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">10K Agency</a>
			<span class="separator">/</span>
			<span class="mono-micro">INDEX / <?php echo esc_html( ucfirst( strtolower( get_the_term_list( '', 'products', '', ', ', '' ) ) ) ); ?></span>
		</div>

		<div class="datasheet-head">
			<div class="datasheet-meta-block">
				<span class="datasheet-model">Model EI-<?php echo esc_html( get_post_meta( get_the_ID(), 'model_number', true ) ?: '—' ); ?></span>
				<h1 class="datasheet-title"><?php echo esc_html( get_the_title() ); ?></h1>
				<span class="datasheet-family">Family: 
				<?php
				echo esc_html(
					get_terms(
						array(
							'taxonomy'   => 'products',
							'hide_empty' => false,
						)
					) ? implode(
						', ',
						wp_list_pluck(
							get_terms(
								array(
									'taxonomy'   => 'products',
									'hide_empty' => false,
								)
							),
							'name'
						)
					) : '—'
				);
				?>
				</span>
			</div>
			<div class="datasheet-actions">
				<?php $brochure = get_post_meta( get_the_ID(), 'brochure_pdf', true ); ?>
				<?php if ( $brochure ) : ?>
					<a class="btn btn-solid" href="<?php echo esc_url( $brochure ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Download brochure', 'ei' ); ?></a>
				<?php endif; ?>
				<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Request quotation</a>
			</div>
		</div>

		<div class="datasheet-body">
			<div class="datasheet-media-panel reveal">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php echo wp_get_attachment_image( get_post_thumbnail_id(), 'single-product', false, array( 'decoding' => 'async' ) ); ?>
				<?php else : ?>
					<span class="mono-micro">10K / No product image</span>
				<?php endif; ?>
			</div>

			<div class="datasheet-info">
				<?php /* Features section */ ?>
				<?php
				$features = get_post_meta( get_the_ID(), 'features', true );
				if ( $features && is_array( $features ) ) :
					?>
					<h2 class="datasheet-section-title"><span>Features</span></h2>
					<ul class="feature-ticks">
						<?php foreach ( $features as $feature ) : ?>
							<li><?php echo esc_html( $feature ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php /* Specifications table */ ?>
				<?php
				$specs = get_post_meta( get_the_ID(), 'specifications_table', true );
				if ( $specs ) :
					?>
					<h2 class="datasheet-section-title"><span>Specifications</span></h2>
					<table class="spec-table reveal">
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
				<?php endif; ?>

				<?php /* Description */ ?>
				<?php if ( get_the_content() ) : ?>
					<section class="page-body reveal" style="max-width: 65ch;">
						<?php the_content(); ?>
					</section>
				<?php endif; ?>
			</div>
		</div>
	</div>
</article>

<?php get_footer(); ?>
