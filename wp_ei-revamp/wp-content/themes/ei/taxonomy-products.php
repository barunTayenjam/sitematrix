<?php
/**
 * Product family archive — 10K Agency.
 * Warm editorial light: TOC of child terms, asymmetric showcase or index table.
 *
 * @package Ei
 */

get_header();

$term     = get_queried_object();
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

/**
 * Render a specimen panel (featured or stacked).
 *
 * @param WP_Post $product  Product post.
 * @param string  $size     Image size slug.
 * @param bool    $featured Whether this is the large feature panel.
 */
function ei_specimen_panel( $product, $size, $featured = false ) {
	$model   = get_post_meta( $product->ID, 'model_number', true );
	$thumb   = get_post_thumbnail_id( $product->ID );
	$excerpt = get_the_excerpt( $product );
	?>
	<article class="specimen-panel reveal<?php echo $featured ? ' featured-hero' : ''; ?>">
		<div class="panel-media">
			<?php if ( $thumb ) : ?>
				<?php echo wp_get_attachment_image( $thumb, $size, false, array( 'decoding' => 'async' ) ); ?>
			<?php else : ?>
				<span class="mono-micro">10K — No specimen image</span>
			<?php endif; ?>
		</div>
		<div class="panel-body">
			<div class="panel-model-row">
				<span class="panel-model">Model EI-<?php echo esc_html( $model ? $model : '—' ); ?></span>
			</div>
			<h3 class="panel-title"><a href="<?php echo esc_url( get_permalink( $product ) ); ?>"><?php echo esc_html( get_the_title( $product ) ); ?></a></h3>
			<?php if ( $excerpt ) : ?>
				<p class="panel-excerpt"><?php echo esc_html( wp_trim_words( $excerpt, $featured ? 24 : 14 ) ); ?></p>
			<?php endif; ?>
			<div class="panel-actions">
				<a class="btn-text" href="<?php echo esc_url( get_permalink( $product ) ); ?>">View datasheet &rarr;</a>
			</div>
		</div>
	</article>
	<?php
}
?>

<section class="sheet family-sheet">
	<div class="container">
		<header class="family-head">
			<span class="mono-micro" style="display:block;margin-bottom:12px;">Family</span>
			<h1 class="display-l"><?php echo esc_html( $term->name ); ?></h1>
			<?php if ( $term->description ) : ?>
				<p class="body-l" style="margin-top:24px;"><?php echo wp_kses_post( $term->description ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( ! empty( $children ) && ! is_wp_error( $children ) ) : ?>
			<nav class="family-toc" aria-label="Product lines">
				<?php foreach ( $children as $child ) : ?>
					<a class="family-toc-row reveal" href="<?php echo esc_url( get_term_link( $child ) ); ?>">
						<span class="toc-name"><?php echo esc_html( $child->name ); ?></span>
						<span class="toc-meta">
							<span><?php echo esc_html( $child->count ); ?> instruments</span>
							<span>&rarr;</span>
						</span>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( $products->have_posts() ) : ?>
			<?php
			$featured_ids = get_posts(
				array(
					'post_type'      => 'product',
					'posts_per_page' => 3,
					'post_status'    => 'publish',
					'tax_query'      => array(
						array(
							'taxonomy' => 'products',
							'field'    => 'term_id',
							'terms'    => $term->term_id,
						),
					),
					'meta_key'       => 'show_on_home',
					'meta_value'     => 1,
				)
			);
			if ( empty( $featured_ids ) ) {
				$featured_ids = get_posts(
					array(
						'post_type'      => 'product',
						'posts_per_page' => 3,
						'post_status'    => 'publish',
						'tax_query'      => array(
							array(
								'taxonomy' => 'products',
								'field'    => 'term_id',
								'terms'    => $term->term_id,
							),
						),
						'orderby'        => 'date',
						'order'          => 'DESC',
					)
				);
			}
			?>
			<?php if ( count( $featured_ids ) >= 3 ) : ?>
				<div class="specimen-layout">
					<?php
					$feature = array_shift( $featured_ids );
					$stacked = array_splice( $featured_ids, 0, 2 );
					ei_specimen_panel( $feature, 'ei-product', true );
					foreach ( $stacked as $product ) {
						ei_specimen_panel( $product, 'ei-card' );
					}
					?>
				</div>
			<?php else : ?>
				<div class="catalog-table-wrap reveal">
					<table class="catalog-table">
						<thead>
							<tr>
								<th>No.</th>
								<th>Model</th>
								<th>Instrument</th>
								<th>Family</th>
								<th><span style="visibility:hidden;">Go</span></th>
							</tr>
						</thead>
						<tbody>
							<?php $row_n = 1; ?>
							<?php
							while ( $products->have_posts() ) :
								$products->the_post();
								?>
								<?php
								$model   = get_post_meta( get_the_ID(), 'model_number', true );
								$terms   = wp_get_post_terms( get_the_ID(), 'products', array( 'fields' => 'names' ) );
								$primary = ! empty( $terms ) ? $terms[0] : '';
								?>
								<tr>
									<td class="col-num"><?php echo esc_html( str_pad( (string) $row_n, 2, '0', STR_PAD_LEFT ) ); ?></td>
									<td class="col-model">EI-<?php echo esc_html( $model ? $model : '—' ); ?></td>
									<td class="col-name"><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></td>
									<td class="col-family"><?php echo esc_html( $primary ); ?></td>
									<td class="col-action"><a href="<?php echo esc_url( get_permalink() ); ?>" aria-label="<?php echo esc_attr( sprintf( 'View %s', get_the_title() ) ); ?>">&rarr;</a></td>
								</tr>
								<?php $row_n++; ?>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<p style="color:var(--ink-soft);"><?php esc_html_e( 'No instruments listed under this family yet.', 'ei' ); ?></p>
		<?php endif; ?>

		<?php if ( $products->max_num_pages > 1 ) : ?>
			<nav class="table-footer">
				<?php
				echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput
					array(
						'total'     => $products->max_num_pages,
						'current'   => max( 1, get_query_var( 'paged' ) ),
						'prev_text' => 'Prev',
						'next_text' => 'Next',
						'type'      => 'list',
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
