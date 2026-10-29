<?php
/**
 * Theme setup — Electronics India.
 * Classic PHP theme + theme.json + ACF. No build step.
 *
 * @package Ei
 */

define( 'EI_THEME_DIR', get_template_directory() );

require_once EI_THEME_DIR . '/inc/template-tags.php';

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );

	register_nav_menus( array(
		'primary' => __( 'Primary menu', 'ei' ),
		'footer'  => __( 'Footer menu', 'ei' ),
	) );

	/* Image sizes used by templates */
	add_image_size( 'ei-card', 400, 280, true );
	add_image_size( 'ei-hero', 1920, 800, true );
	add_image_size( 'ei-product', 800, 600, true );
} );

/* Enqueue: stylesheet + Google Fonts (already @import in style.css) + small JS */
add_action( 'wp_enqueue_scripts', function () {
	$version = wp_get_theme()->get( 'Version' );

	/* Main stylesheet (includes Google Fonts @import) */
	wp_enqueue_style( 'ei-style', get_stylesheet_uri(), array(), $version );

	/* Lightweight JS: nav click-toggle, scroll reveal, reduced-motion guard */
	wp_enqueue_script( 'ei-script', get_template_directory_uri() . '/assets/ei.js', array(), $version, true );
} );

/* SVG as <img> alt safety — no emoji icons */
add_filter( 'wp_kses_allowed_html', function ( $tags ) {
	$tags['svg'] = array(
		'xmlns'      => true,
		'viewBox'    => true,
		'width'      => true,
		'height'     => true,
		'fill'       => true,
		'stroke'     => true,
		'stroke-width' => true,
		'stroke-linecap' => true,
		'stroke-linejoin' => true,
		'aria-hidden'    => true,
		'focusable'      => true,
	);
	$tags['path']  = array( 'd' => true, 'fill' => true, 'stroke' => true );
	$tags['circle'] = array( 'cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true );
	$tags['rect'] = array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'fill' => true, 'stroke' => true );
	$tags['line'] = array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true );
	$tags['polyline'] = array( 'points' => true, 'fill' => true, 'stroke' => true );
	$tags['polygon'] = array( 'points' => true, 'fill' => true, 'stroke' => true );
	return $tags;
} );

/** A product card — used on home, category pages, search, related. */
function ei_product_card( $post_id ) {
	$title   = get_the_title( $post_id );
	$model   = ei_product_model( $post_id );
	$summary = ei_product_summary( $post_id );
	$thumb   = get_the_post_thumbnail( $post_id, 'ei-card', array( 'loading' => 'lazy', 'alt' => esc_attr( $title ) ) );

	?>
	<article class="ei-product-card">
		<?php if ( $thumb ) : ?>
			<a class="ei-card-media" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
				<?php echo $thumb; ?>
			</a>
		<?php endif; ?>
		<div class="ei-card-body">
			<?php if ( $model ) : ?>
				<p class="ei-card-model"><?php echo esc_html( $model ); ?></p>
			<?php endif; ?>
			<h3 class="ei-card-title">
				<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>
			<?php if ( $summary ) : ?>
				<p class="ei-card-summary"><?php echo esc_html( $summary ); ?></p>
			<?php endif; ?>
			<a class="btn btn--outline" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php esc_html_e( 'View details', 'ei' ); ?></a>
		</div>
	</article>
	<?php
}

/* ---------- REST endpoint for native contact form (no CF7) ---------- */
add_action( 'rest_api_init', function () {
	register_rest_route( 'ei/v1', '/contact', array(
		'methods'  => 'POST',
		'callback' => 'ei_contact_endpoint',
		'permission_callback' => '__return_true',
		'args' => array(
			'name'    => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
			'email'   => array( 'required' => true, 'sanitize_callback' => 'sanitize_email' ),
			'phone'   => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
			'subject' => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
			'message' => array( 'required' => true, 'sanitize_callback' => 'sanitize_textarea_field' ),
		),
	) );
} );

function ei_contact_endpoint( \WP_REST_Request $request ) {
	$to      = get_option( 'admin_email' );
	$subject = $request->get_param( 'subject' ) ?: 'Website Enquiry — ' . get_bloginfo( 'name' );
	$name    = $request->get_param( 'name' );
	$email   = $request->get_param( 'email' );
	$phone   = $request->get_param( 'phone' );
	$message = $request->get_param( 'message' );

	$body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\n\n{$message}";
	$headers = array( 'From: ' . $name . ' <' . $email . '>', 'Reply-To: ' . $email );

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( $sent ) {
		return new \WP_REST_Response( array( 'success' => true, 'message' => 'Message sent.' ), 200 );
	}
	return new \WP_REST_Error( 'mail_failed', 'Could not send message.', array( 'status' => 500 ) );
}

/* ---------- Preload hero image on front page ---------- */
add_action( 'wp_head', function () {
	if ( is_front_page() ) {
		$uploads = wp_get_upload_dir();
		echo '<link rel="preload" as="image" href="' . esc_url( $uploads['baseurl'] . '/2016/10/bg-title7.jpg' ) . '">';
	}
} );

/* ---------- ACF JSON load path (already set in ei-core, but safe) ---------- */
add_filter( 'acf/settings/load_json', function ( $paths ) {
	$paths[] = EI_THEME_DIR . '/acf-json';
	return $paths;
} );

/* Shortcode for native contact form */
add_shortcode( 'ei-contact-form', function () {
	ob_start();
	include EI_THEME_DIR . '/inc/contact-form.php';
	return ob_get_clean();
} );