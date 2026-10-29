<?php
/**
 * Theme setup.
 *
 * A classic theme with theme.json for design tokens. No build step, no page
 * builder: the templates read ACF fields through inc/template-tags.php.
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
} );

add_action( 'wp_enqueue_scripts', function () {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style( 'ei-style', get_stylesheet_uri(), array(), $version );
} );

/**
 * Product categories drive the primary navigation: seven top-level terms, each
 * with its sub-categories as a dropdown. Building it from the taxonomy instead
 * of a menu keeps a new category from needing two edits.
 */
function ei_primary_nav() {
	$terms = get_terms( array(
		'taxonomy'   => 'products',
		'parent'     => 0,
		'hide_empty' => false,
		'orderby'    => 'name',
	) );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	$items = array();
	foreach ( $terms as $term ) {
		$children = get_terms( array(
			'taxonomy'   => 'products',
			'parent'     => $term->term_id,
			'hide_empty' => false,
			'orderby'    => 'name',
		) );

		$submenu = '';
		if ( ! is_wp_error( $children ) && $children ) {
			$links = array();
			foreach ( $children as $child ) {
				$links[] = sprintf(
					'<li><a href="%s">%s</a></li>',
					esc_url( get_term_link( $child ) ),
					esc_html( $child->name )
				);
			}
			$submenu = '<ul class="sub-menu">' . implode( '', $links ) . '</ul>';
		}

		$items[] = sprintf(
			'<li class="%s"><a href="%s">%s</a>%s</li>',
			$submenu ? 'menu-item-has-children' : '',
			esc_url( get_term_link( $term ) ),
			esc_html( $term->name ),
			$submenu
		);
	}

	return '<ul class="ei-menu">' . implode( '', $items ) . '</ul>';
}

/** A product card, used by the home page, category pages and search. */
function ei_product_card( $post_id ) {
	$title   = get_the_title( $post_id );
	$model   = ei_product_model( $post_id );
	$summary = ei_product_summary( $post_id );
	$thumb   = get_the_post_thumbnail( $post_id, 'medium', array( 'loading' => 'lazy', 'alt' => esc_attr( $title ) ) );

	?>
	<article class="ei-card">
		<a class="ei-card-img<?php echo $thumb ? '' : ' ei-no-img'; ?>" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
			<?php echo $thumb; ?>
		</a>
		<div class="ei-card-body">
			<?php if ( $model ) : ?>
				<p class="ei-card-model"><span class="ei-badge"><?php echo esc_html( $model ); ?></span></p>
			<?php endif; ?>
			<h3 class="ei-card-title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo esc_html( $title ); ?></a></h3>
			<?php if ( $summary ) : ?>
				<p class="ei-card-summary"><?php echo esc_html( $summary ); ?></p>
			<?php endif; ?>
			<div class="ei-card-actions">
				<a class="btn" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">View details</a>
			</div>
		</div>
	</article>
	<?php
}
