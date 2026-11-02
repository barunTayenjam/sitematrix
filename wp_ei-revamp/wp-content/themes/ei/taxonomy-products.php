<?php
/**
 * Product family archive — /products/{term}/.
 * Term hero, children as TOC rows, product grid with images.
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

<section class="sheet sheet-index reveal" id="family-<?php echo esc_attr( $term->slug ); ?>">
	<header class="sheet-head">
		<span class="sheet-index mono">FAMILY</span>
		<h1 class="sheet-title"><?php echo esc_html( $term->name ); ?></h1>
		<?php if ( $term->description ) : ?>
			<p class="ei-term-desc mono" style="color:var(--stone);font-size:0.85rem;max-width:55ch;margin-top:8px;"><?php echo esc_html( $term->description ); ?></p>
		<?php endif; ?>
	</header>

	<?php if ( ! empty( $children ) && ! is_wp_error( $children ) ) : ?>
		<h2 class="sheet-index" style="font-size:0.7rem;margin-top:var(--space-l);margin-bottom:8px;">Product lines</h2>
		<nav class="toc" aria-label="Product lines">
			<?php foreach ( $children as $child ) : ?>
				<a class="toc-row" href="<?php echo esc_url( get_term_link( $child ) ); ?>">
					<span class="toc-num mono"><?php echo esc_html( $child->count ); ?></span>
					<span class="toc-name"><?php echo esc_html( $child->name ); ?></span>
					<span class="toc-dots" aria-hidden="true"></span>
					<span class="toc-arrow" aria-hidden="true">▸</span>
				</a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>

	<h2 class="sheet-index" style="font-size:0.7rem;margin-top:var(--space-l);margin-bottom:8px;">Instruments</h2>
	<div class="product-grid" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:24px;">
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
						<h3 style="font-size:1rem;margin-bottom:6px;"><a href="<?php echo esc_url( get_permalink() ); ?>" style="color:var(--ink);"><?php echo esc_html( get_the_title() ); ?></a></h3>
						<?php if ( $excerpt ) : ?>
							<p style="font-size:0.8rem;color:var(--stone);line-height:1.5;margin:0;"><?php echo esc_html( wp_trim_words( $excerpt, 14 ) ); ?></p>
						<?php endif; ?>
						<a class="btn btn-light" href="<?php echo esc_url( get_permalink() ); ?>" style="font-size:0.65rem;padding:10px 18px;">VIEW SPECIFICATIONS</a>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
		else :
			?>
			<p style="color:var(--stone);font-size:0.9rem;"><?php esc_html_e( 'No instruments listed under this family yet.', 'ei' ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( $products->max_num_pages > 1 ) : ?>
		<nav class="ei-pagination" style="margin-top:var(--space-l);display:flex;justify-content:center;gap:8px;">
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
</section>

<?php
get_footer();
