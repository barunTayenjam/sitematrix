<?php
/**
 * Single product — two-column sticky gallery + info panel,
 * then accordion-style sections for specs/features/description.
 *
 * @package Ei
 */
?>
<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
	$model       = ei_product_model( get_the_ID() );
	$features    = ei_product_features( get_the_ID() );
	$description = get_field( 'description', get_the_ID() );
	$spec_table  = ei_specifications_table( get_the_ID() );
	$gallery     = get_field( 'gallery', get_the_ID() );
	$brochure    = ei_brochure_url( get_the_ID() );
	$terms       = wp_get_post_terms( get_the_ID(), 'products' );
	$primary_cat = $terms && ! is_wp_error( $terms ) ? $terms[0] : null;
?>

<article class="ei-product" id="post-<?php the_ID(); ?>">
	<div class="container">

		<nav class="ei-breadcrumbs" aria-label="Breadcrumb">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'ei' ); ?></a>
			<span>/</span>
			<a href="<?php echo esc_url( home_url( '/products/' ) ); ?>"><?php esc_html_e( 'Products', 'ei' ); ?></a>
			<?php if ( $primary_cat ) : ?>
				<span>/</span>
				<a href="<?php echo esc_url( get_term_link( $primary_cat ) ); ?>"><?php echo esc_html( $primary_cat->name ); ?></a>
			<?php endif; ?>
			<span>/</span>
			<span><?php the_title(); ?></span>
		</nav>

		<div class="ei-product-hero">
			<div class="ei-product-media">
				<?php the_post_thumbnail( 'ei-product', array( 'alt' => esc_attr( get_the_title() ), 'loading' => 'eager' ) ); ?>
				<?php if ( $gallery && is_array( $gallery ) ) : ?>
					<div class="ei-thumbs">
						<?php foreach ( $gallery as $img ) : if ( ! empty( $img['url'] ) ) : ?>
							<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ?? get_the_title() ); ?>" data-full="<?php echo esc_url( $img['url'] ); ?>">
						<?php endif; endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="ei-product-meta">
				<?php if ( $model ) : ?>
					<p class="ei-badge"><?php echo esc_html( $model ); ?></p>
				<?php endif; ?>
				<h1><?php the_title(); ?></h1>

				<div class="entry-content reveal"><?php the_content(); ?></div>

				<div class="ei-product-actions reveal">
					<?php if ( $brochure ) : ?>
						<a class="btn btn--outline" href="<?php echo esc_url( $brochure ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Download brochure', 'ei' ); ?></a>
					<?php endif; ?>
					<a class="btn btn--primary" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'Request a quote', 'ei' ); ?></a>
				</div>
			</div>
		</div>

		<?php if ( $features || $description || $spec_table ) : ?>
		<div class="ei-product-sections reveal">
			<?php if ( $features ) : ?>
			<section class="ei-section">
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
			<section class="ei-section">
				<div class="container">
					<h2><?php esc_html_e( 'Description', 'ei' ); ?></h2>
					<div class="entry-content"><?php echo $description; ?></div>
				</div>
			</section>
			<?php endif; ?>

			<?php if ( $spec_table ) : ?>
			<section class="ei-section">
				<div class="container">
					<h2><?php esc_html_e( 'Specifications', 'ei' ); ?></h2>
					<div class="ei-spec-table"><?php echo $spec_table; ?></div>
				</div>
			</section>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php
		$related = new WP_Query( array(
			'post_type'      => 'product',
			'post__not_in'   => array( get_the_ID() ),
			'taxonomy'       => 'products',
			'terms'          => $primary_cat ? $primary_cat->term_id : '',
			'posts_per_page' => 4,
			'orderby'        => 'rand',
		) );
		if ( $related->have_posts() ) :
		?>
		<section class="ei-section ei-section--alt">
			<div class="container">
				<h2><?php esc_html_e( 'Related Instruments', 'ei' ); ?></h2>
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