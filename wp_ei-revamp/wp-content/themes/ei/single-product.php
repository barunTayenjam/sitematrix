<?php
/**
 * Single product template.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<?php
while ( have_posts() ) : the_post();
	$model       = ei_product_model( get_the_ID() );
	$features    = ei_product_features( get_the_ID() );
	$description = get_field( 'description', get_the_ID() );
	$spec_table  = ei_specifications_table( get_the_ID() );
	$gallery     = get_field( 'gallery', get_the_ID() );
	$brochure    = ei_brochure_url( get_the_ID() );
?>

<article class="ei-product" id="post-<?php the_ID(); ?>">
	<div class="container">
		<nav class="ei-breadcrumbs" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'ei' ); ?></a>
			<span>/</span>
			<a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Products', 'ei' ); ?></a>
			<?php $terms = wp_get_post_terms( get_the_ID(), 'products' ); if ( $terms && ! is_wp_error( $terms ) ) : ?>
				<span>/</span>
				<a href="<?php echo esc_url( get_term_link( $terms[0] ) ); ?>"><?php echo esc_html( $terms[0]->name ); ?></a>
			<?php endif; ?>
			<span>/</span>
			<span><?php the_title(); ?></span>
		</nav>

		<div class="ei-product-hero">
			<div class="ei-product-media">
				<?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
			</div>

			<div class="ei-product-meta">
				<?php if ( $model ) : ?>
					<p class="ei-badge"><?php echo esc_html( $model ); ?></p>
				<?php endif; ?>
				<h1><?php the_title(); ?></h1>
				<div class="entry-content"><?php the_content(); ?></div>
				<div class="ei-product-actions">
					<?php if ( $brochure ) : ?>
						<a class="btn" href="<?php echo esc_url( $brochure ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Download brochure', 'ei' ); ?></a>
					<?php endif; ?>
					<a class="btn btn-outline" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Request a quote', 'ei' ); ?></a>
				</div>
			</div>
		</div>

		<?php if ( $gallery && is_array( $gallery ) ) : ?>
		<section class="ei-product-section">
			<div class="container">
				<h2><?php esc_html_e( 'Gallery', 'ei' ); ?></h2>
				<div class="ei-gallery">
					<?php foreach ( $gallery as $img ) : if ( ! empty( $img['url'] ) ) : ?>
						<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ?? get_the_title() ); ?>">
					<?php endif; endforeach; ?>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $features ) : ?>
		<section class="ei-product-section">
			<div class="container">
				<h2><?php esc_html_e( 'Features', 'ei' ); ?></h2>
				<ul class="ei-features">
					<?php foreach ( $features as $feat ) : ?>
						<li><?php echo esc_html( $feat ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $description ) : ?>
		<section class="ei-product-section">
			<div class="container">
				<h2><?php esc_html_e( 'Description', 'ei' ); ?></h2>
				<div class="entry-content"><?php echo $description; // already sanitized by wp_kses in template tag ?></div>
			</div>
		</section>
		<?php endif; ?>

		<?php if ( $spec_table ) : ?>
		<section class="ei-product-section">
			<div class="container">
				<h2><?php esc_html_e( 'Specifications', 'ei' ); ?></h2>
				<div class="ei-spec-table"><?php echo $spec_table; // sanitized by template tag ?></div>
			</div>
		</section>
		<?php endif; ?>

		<?php
		$related = new \WP_Query( array(
			'post_type'      => 'product',
			'post__not_in'   => array( get_the_ID() ),
			'taxonomy'       => 'products',
			'terms'          => $terms ? $terms[0]->term_id : '',
			'numberposts'    => 4,
			'orderby'        => 'rand',
		) );
		if ( $related->have_posts() ) :
		?>
		<section class="ei-product-section">
			<div class="container">
				<h2><?php esc_html_e( 'Related Products', 'ei' ); ?></h2>
				<div class="ei-grid">
					<?php while ( $related->have_posts() ) : $related->the_post(); ei_product_card( get_the_ID() ); endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
		<?php endif; ?>
	</div>
</article>

<?php endwhile; ?>

<?php get_footer(); ?>