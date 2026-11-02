<?php
/**
 * Theme setup — 10K Agency Technical Luxury.
 * All assets self-hosted. Zero third-party runtime requests.
 *
 * @package Ei
 */

define( 'EI_THEME_DIR', get_template_directory() );

/**
 * Render a specimen panel (featured or stacked).
 * Accepts either a single post ID or the output of a WP_Query loop.
 *
 * @param WP_Post|int $product Post ID or object.
 * @param string      $size    Image size registered via add_image_size().
 * @param bool        $featured Whether this is the large feature panel.
 */
if ( ! function_exists( 'ei_specimen_panel' ) ) {
	function ei_specimen_panel( $product, $size, $featured = false ) {
		$model   = is_object( $product ) ? get_post_meta( $product->ID, 'model_number', true ) : get_post_meta( (int) $product, 'model_number', true );
		$thumb   = is_object( $product ) ? get_post_thumbnail_id( $product->ID ) : false;
		$excerpt = is_object( $product ) ? get_the_excerpt( $product ) : '';
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
				<h3 class="panel-title"><a href="<?php echo esc_url( is_object( $product ) ? get_permalink( $product ) : get_permalink( (int) $product ) ); ?>"><?php echo esc_html( is_object( $product ) ? get_the_title( $product ) : apply_filters( 'the_title', '', (int) $product ) ); ?></a></h3>
				<?php if ( $excerpt ) : ?>
					<p class="panel-excerpt"><?php echo esc_html( wp_trim_words( $excerpt, $featured ? 24 : 14 ) ); ?></p>
				<?php endif; ?>
				<div class="panel-actions">
					<a class="btn-text" href="<?php echo esc_url( is_object( $product ) ? get_permalink( $product ) : get_permalink( (int) $product ) ); ?>">View datasheet &rarr;</a>
				</div>
			</div>
		</article>
		<?php
	}
}

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'image' ) );
		register_nav_menus(
			array(
				'primary' => __( 'Primary menu', 'ei' ),
			)
		);
		add_image_size( 'ei-card', 400, 280, true );
		add_image_size( 'ei-chamber', 500, 300, true );
		add_image_size( 'ei-product', 800, 600, true );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'ei-style', get_stylesheet_uri(), array(), null );
		wp_enqueue_script( 'ei-script', get_template_directory_uri() . '/assets/ei.js', array(), null, true );
	}
);

/* Contact Form 7: ensure submission stays in-page */
add_filter( 'wpcf7_mail_sent', 'ei_cf7_redirect_on_success' );
function ei_cf7_redirect_on_success( $output ) {
	// Keep it inline — no page refresh
	return $output;
}
