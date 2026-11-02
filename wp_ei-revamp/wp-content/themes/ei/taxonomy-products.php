<?php
/**
 * Taxonomy archive — /products/{term}/.
 *
 * Current term hero, its children (if any), and its products.
 * Top-level term with children shows a family grid; a leaf term
 * goes straight to the product list.
 *
 * @package Ei
 */

?>

<?php get_header(); ?>

<?php
$list_term = get_queried_object();

$children = get_terms(
	array(
		'taxonomy'   => 'products',
		'parent'     => $list_term->term_id,
		'hide_empty' => false,
		'orderby'    => 'name',
	)
);
?>

<section class="ei-page-head">
	<div class="container">
		<nav class="ei-breadcrumbs" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'ei' ); ?></a>
			<span>/</span>
			<a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Products', 'ei' ); ?></a>
			<span>/</span>
			<span aria-current="page"><?php echo esc_html( $list_term->name ); ?></span>
		</nav>
		<h1><?php echo esc_html( $list_term->name ); ?></h1>
		<?php if ( $list_term->description ) : ?>
			<p class="ei-term-desc"><?php echo esc_html( $term->description ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php if ( ! empty( $children ) && ! is_wp_error( $children ) ) : ?>
<section class="ei-section">
	<div class="container">
		<p class="ei-eyebrow reveal"><?php esc_html_e( 'Product lines', 'ei' ); ?></p>
		<div class="ei-cats">
			<?php foreach ( $children as $child ) : ?>
				<a class="ei-cat-card reveal" href="<?php echo esc_url( get_term_link( $child ) ); ?>">
					<h3><?php echo esc_html( $child->name ); ?></h3>
					// translators: %d instrument|%d instruments.
				<p>
				<?php
				// translators: %d instrument|%d instruments.
				echo esc_html( sprintf( _n( '%d instrument', '%d instruments', $child->count, 'ei' ), $child->count ) );
				?>
				</p>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="ei-section<?php echo empty( $children ) ? '' : ' ei-section--alt'; ?>">
	<div class="container">
		<p class="ei-eyebrow reveal"><?php esc_html_e( 'Instruments', 'ei' ); ?></p>
		<h2 class="ei-section-title reveal"><?php echo esc_html( $list_term->name ); ?></h2>
		<div class="ei-grid">
			<?php
			$query = new WP_Query(
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
			if ( $query->have_posts() ) :
				while ( $query->have_posts() ) :
					$query->the_post();
					ei_product_card( get_the_ID() );
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<p><?php esc_html_e( 'No instruments listed under this category yet.', 'ei' ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $query->max_num_pages > 1 ) : ?>
		<nav class="ei-pagination" aria-label="Product pagination">
			<?php
			echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput -- paginate_links returns safe HTML.
				array(
					'total'     => $query->max_num_pages,
					'current'   => max( 1, get_query_var( 'paged' ) ),
					'prev_text' => '&laquo;',
					'next_text' => '&raquo;',
				)
			);
			?>
		</nav>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
