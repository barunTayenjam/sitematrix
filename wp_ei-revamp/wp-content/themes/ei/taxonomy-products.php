<?php
/**
 * Product family archive — /products/{term}/.
 * Term hero, children (sub-families) as TOC rows, product grid.
 *
 * @package Ei
 */

get_header(); ?>

<?php
$list_term = get_queried_object();

/* Child families */
$children = get_terms(
	array(
		'taxonomy'   => 'products',
		'parent'     => $list_term->term_id,
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	)
);

/* Products in this family */
$products = new WP_Query(
	array(
		'post_type'      => 'product',
		'posts_per_page' => 12,
		'paged'          => max( 1, get_query_var( 'paged' ) ),
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery -- single-term product query
			array(
				'taxonomy' => 'products',
				'field'    => 'term_id',
				'terms'    => $list_term->term_id,
			),
		),
	)
);
?>

<section class="sheet sheet-index reveal" id="family-<?php echo esc_attr( $list_term->slug ); ?>">
	<header class="sheet-head">
		<span class="sheet-index mono">FAMILY</span>
		<h1 class="sheet-title"><?php echo esc_html( $list_term->name ); ?></h1>
		<?php if ( $list_term->description ) : ?>
			<p class="ei-term-desc mono" style="color:var(--stone);font-size:0.85rem;max-width:55ch;"><?php echo esc_html( $list_term->description ); ?></p>
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
	<div class="specimen-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px;">
		<?php if ( $products->have_posts() ) : ?>
			<?php
			while ( $products->have_posts() ) :
				$products->the_post();
				?>
				<article class="product-card">
					<div class="model mono" style="font-size:0.65rem;letter-spacing:0.1em;color:var(--stone);text-transform:uppercase;">
						<?php echo esc_html( get_post_meta( get_the_ID(), 'model_number', true ) ?: '' ); ?>
					</div>
					<h3 style="font-size:1rem;margin-bottom:6px;"><a href="<?php echo esc_url( get_permalink() ); ?>" style="color:var(--ink);"><?php echo esc_html( get_the_title() ); ?></a></h3>
					<?php if ( get_the_excerpt() ) : ?>
						<p style="font-size:0.8rem;color:var(--stone);line-height:1.5;"><?php echo wp_kses_post( get_the_excerpt() ); ?></p>
					<?php endif; ?>
					<a class="btn" href="<?php echo esc_url( get_permalink() ); ?>" style="font-size:0.7rem;padding:8px 16px;">VIEW SPECIFICATIONS</a>
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
