<?php
/**
 * Single product — 10K Agency specimen datasheet.
 * Warm editorial light: model, dark data, features + spec table, CTA panel.
 *
 * @package Ei
 */

get_header(); ?><article class="datasheet-sheet">
	<div class="container">
		<?php
		$product_terms = get_the_terms( get_the_ID(), 'products' );
		$family_label  = '—';
		$family_ids    = array();
		if ( $product_terms && ! is_wp_error( $product_terms ) ) {
			$tops = array_values(
				array_filter(
					$product_terms,
					static function ( $term ) {
						return 0 === (int) $term->parent;
					}
				)
			);
			$family_source = $tops ? $tops : $product_terms;
			$family_label  = implode( ', ', wp_list_pluck( $family_source, 'name' ) );
			$family_ids    = wp_list_pluck( $product_terms, 'term_id' );
		}
		$model_number = get_post_meta( get_the_ID(), 'model_number', true );
		$model_label  = $model_number ? 'EI-' . $model_number : '—';
		?>
		<div class="datasheet-breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">10K Agency</a>
			<span class="separator">/</span>
			<span class="mono-micro">INDEX / <?php echo esc_html( $family_label ); ?> / <?php echo esc_html( $model_label ); ?></span>
		</div>

		<div class="datasheet-head">
			<div class="datasheet-meta-block">
				<span class="datasheet-model">Model <?php echo esc_html( $model_label ); ?></span>
				<h1 class="datasheet-title"><?php echo esc_html( get_the_title() ); ?></h1>
				<span class="datasheet-family">Family: <?php echo esc_html( $family_label ); ?></span>
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
				$features = array();
				if ( function_exists( 'get_field' ) ) {
					$rows = get_field( 'features' );
					if ( is_array( $rows ) ) {
						foreach ( $rows as $row ) {
							if ( is_array( $row ) && ! empty( $row['feature'] ) ) {
								$features[] = $row['feature'];
							}
						}
					}
				}
				if ( ! $features ) {
					$feature_count = (int) get_post_meta( get_the_ID(), 'features', true );
					for ( $i = 0; $i < $feature_count; $i++ ) {
						$line = get_post_meta( get_the_ID(), 'features_' . $i . '_feature', true );
						if ( $line ) {
							$features[] = $line;
						}
					}
				}
				if ( $features ) :
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
					$allowed_spec = array(
						'table' => array( 'class' => true ),
						'tbody' => array(),
						'tr'    => array(),
						'td'    => array(),
						'th'    => array(),
						'thead' => array(),
					);
					if ( false !== stripos( $specs, '<table' ) ) {
						$specs = preg_replace( '/<table\b[^>]*>/i', '<table class="spec-table">', $specs, 1 );
					} else {
						$specs = '<table class="spec-table"><tbody>' . $specs . '</tbody></table>';
					}
					?>
					<h2 class="datasheet-section-title"><span>Specifications</span></h2>
					<?php echo wp_kses( $specs, $allowed_spec ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
