<?php
/**
 * Product family archive — /products/{term}/.
 * Technical Luxury: product-line TOC rows, Framed Specimen Chambers grid.
 *
 * @package Ei
 */

get_header(); ?>

<?php
$term = get_queried_object();

$children = get_terms(
	array(
		'taxonomy'   => 'products',
		'parent'     => $term->term_id,
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

$products = new WP_Query(
	array(
		'post_type'      => 'product',
		'posts_per_page' => 12,
		'paged'          => max( 1, get_query_var( 'paged' ) ),
		'tax_query'      => array(
			array(
				'taxonomy' => 'products',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		),
	)
);
?>

<section class="sheet family-archive reveal" id="family-<?php echo esc_attr( $term->slug ); ?>" style="background: var(--obsidian-elevated); border-top: 1px solid var(--glass-border);">
	<div class="container">
		<header class="family-head">
			<span class="eyebrow">Family</span>
			<h1 class="sheet-title"><?php echo esc_html( $term->name ); ?></h1>
			<?php if ( $term->description ) : ?>
				<p class="mono" style="color:var(--titanium-dim);font-size:0.85rem;max-width:55ch;margin-top:var(--space-xs);"><?php echo wp_kses_post( $term->description ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( ! empty( $children ) && ! is_wp_error( $children ) ) : ?>
			<h2 class="sheet-index" style="font-size:0.62rem;margin-top:var(--space-xl);margin-bottom:var(--space-s);">Product lines</h2>
			<nav class="family-toc" aria-label="Product lines">
				<?php foreach ( $children as $child ) : ?>
					<a class="toc-row" href="<?php echo esc_url( get_term_link( $child ) ); ?>">
						<span class="toc-num mono"><?php echo esc_html( $child->count ); ?></span>
						<span class="toc-name"><?php echo esc_html( $child->name ); ?></span>
						<span class="toc-arrow mono">▸</span>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<h2 class="sheet-index" style="font-size:0.62rem;margin-top:var(--space-xl);margin-bottom:var(--space-s);">Instruments</h2>
		<div class="specimen-grid">
			<?php if ( $products->have_posts() ) : ?>
				<?php
				while ( $products->have_posts() ) :
					$products->the_post();
					?>
					<?php
					$model   = get_post_meta( get_the_ID(), 'model_number', true );
					$thumb   = get_post_thumbnail_id( get_the_ID() );
					$excerpt = get_the_excerpt();
					?>
					<article class="chamber reveal">
						<div class="chamber-media">
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
								<div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--titanium-dim);font-family:'IBM Plex Mono',monospace;font-size:0.6rem;">No image</div>
							<?php endif; ?>
							<div class="chamber-specs">
								<span class="chamber-model">EI-<?php echo esc_html( $model ?: '—' ); ?></span>
								<div class="chamber-tags">
									<?php
									$tags = wp_get_post_terms( get_the_ID(), 'products', array( 'fields' => 'names' ) );
									foreach ( (array) $tags as $term_name ) :
										?>
										<span class="chamber-tag"><?php echo esc_html( $term_name ); ?></span>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
						<div class="chamber-body">
							<h3 class="chamber-title"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h3>
							<?php if ( $excerpt ) : ?>
								<p class="chamber-desc"><?php echo esc_html( wp_trim_words( $excerpt, 20 ) ); ?></p>
							<?php endif; ?>
							<a class="btn btn-glass" href="<?php echo esc_url( get_permalink() ); ?>">View Specifications</a>
						</div>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<p style="color:var(--titanium-dim);font-size:0.9rem;"><?php esc_html_e( 'No instruments listed under this family yet.', 'ei' ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $products->max_num_pages > 1 ) : ?>
			<nav class="ei-pagination" style="margin-top:var(--space-xl);display:flex;justify-content:center;gap:8px;">
				<?php
				echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput -- paginate_links returns safe HTML.
					array(
						'total'     => $products->max_num_pages,
						'current'   => max( 1, get_query_var( 'paged' ) ),
						'prev_text' => 'PREV',
						'next_text' => 'NEXT',
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
