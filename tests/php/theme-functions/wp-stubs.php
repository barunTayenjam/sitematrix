<?php
/**
 * Minimal WordPress stubs for testing the dp_evolve-Child theme functions.php
 * without a database or a running WordPress install.
 *
 * Only functions that are actually reached at include time need real bodies;
 * everything else is a recording no-op.
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 3 ) . '/' );
}

define( 'TF_REPO_ROOT', dirname( __DIR__, 3 ) );
define( 'TF_THEME_ROOT', TF_REPO_ROOT . '/wp_gz/wp-content/themes/dp_evolve-Child' );
define( 'TF_PARENT_THEME_ROOT', TF_REPO_ROOT . '/wp_gz/wp-content/themes/dp_evolve' );

$GLOBALS['tf'] = array(
	'is_admin'   => (bool) getenv( 'TF_IS_ADMIN' ),
	'actions'    => array(),
	'filters'    => array(),
	'shortcodes' => array(),
	'calls'      => array(),
);

function tf_record( $bucket, $key, $value = null ) {
	if ( ! isset( $GLOBALS['tf'][ $bucket ][ $key ] ) ) {
		$GLOBALS['tf'][ $bucket ][ $key ] = array();
	}
	$GLOBALS['tf'][ $bucket ][ $key ][] = $value;
}

function tf_called( $name, $args = array() ) {
	$GLOBALS['tf']['calls'][] = array( $name, $args );
}

/* ---------------------------------------------------------------- hooks -- */

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	tf_record( 'actions', $hook, array( $callback, $priority, $accepted_args ) );
	return true;
}

function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	tf_record( 'filters', $hook, array( $callback, $priority, $accepted_args ) );
	return true;
}

function remove_action( $hook, $callback, $priority = 10 ) {
	return true;
}

function remove_filter( $hook, $callback, $priority = 10 ) {
	return true;
}

function has_action( $hook, $callback = false ) {
	return isset( $GLOBALS['tf']['actions'][ $hook ] );
}

function add_shortcode( $tag, $callback ) {
	tf_record( 'shortcodes', $tag, $callback );
	return true;
}

function do_action( $hook ) {
	tf_called( 'do_action', array( $hook ) );
}

function apply_filters( $hook, $value ) {
	tf_called( 'apply_filters', array( $hook ) );
	return $value;
}

/* ------------------------------------------------------------- wp setup -- */

function add_image_size( $name, $width = 0, $height = 0, $crop = false ) {
	tf_called( 'add_image_size', array( $name, $width, $height, $crop ) );
}

function add_rewrite_rule( $regex, $query, $after = 'bottom' ) {
	tf_called( 'add_rewrite_rule', array( $regex, $query, $after ) );
}

function register_nav_menus( $locations = array() ) {
	tf_called( 'register_nav_menus', array( $locations ) );
}

function register_post_type( $post_type, $args = array() ) {
	tf_called( 'register_post_type', array( $post_type, $args ) );
}

function register_taxonomy( $taxonomy, $object_type, $args = array() ) {
	tf_called( 'register_taxonomy', array( $taxonomy, $object_type, $args ) );
}

function wp_oembed_add_provider( $format, $provider, $regex = false ) {
	tf_called( 'wp_oembed_add_provider', array( $format, $provider, $regex ) );
}

/* ------------------------------------------------------------ wp runtime -- */

function is_admin() {
	return (bool) $GLOBALS['tf']['is_admin'];
}

function is_user_logged_in() {
	return false;
}

function is_singular( $post_types = '' ) {
	return false;
}

function is_single( $post = '' ) {
	return false;
}

function is_page( $page = '' ) {
	return false;
}

function is_front_page() {
	return false;
}

function is_home() {
	return false;
}

function is_404() {
	return false;
}

function is_category( $category = '' ) {
	return false;
}

function is_tag( $tag = '' ) {
	return false;
}

function is_tax( $taxonomy = '', $term = '' ) {
	return false;
}

function is_archive() {
	return false;
}

function is_search() {
	return false;
}

function is_ssl() {
	return false;
}

function home_url( $path = '' ) {
	return 'http://example.test' . $path;
}

function site_url( $path = '' ) {
	return 'http://example.test' . $path;
}

function get_option( $option, $default = false ) {
	return $default;
}

function get_bloginfo( $show = '' ) {
	return '';
}

function wp_redirect( $location, $status = 302 ) {
	tf_called( 'wp_redirect', array( $location, $status ) );
	return true;
}

function get_template_directory_uri() {
	return 'http://example.test/wp-content/themes/dp_evolve';
}

function get_stylesheet_directory_uri() {
	return 'http://example.test/wp-content/themes/dp_evolve-Child';
}

function get_template_directory() {
	return TF_PARENT_THEME_ROOT;
}

function get_stylesheet_directory() {
	return TF_THEME_ROOT;
}

function get_locale() {
	return 'en_US';
}

function esc_url( $url ) {
	return $url;
}

function esc_attr( $text ) {
	return $text;
}

function esc_html( $text ) {
	return $text;
}

function wp_enqueue_script() {
	tf_called( 'wp_enqueue_script', func_get_args() );
}

function wp_enqueue_style() {
	tf_called( 'wp_enqueue_style', func_get_args() );
}

function wp_register_script() {
	tf_called( 'wp_register_script', func_get_args() );
}

function wp_register_style() {
	tf_called( 'wp_register_style', func_get_args() );
}

function wp_localize_script() {
	tf_called( 'wp_localize_script', func_get_args() );
}

function wp_deregister_script() {
	tf_called( 'wp_deregister_script', func_get_args() );
}

function wp_dequeue_script() {
	tf_called( 'wp_dequeue_script', func_get_args() );
}

function wp_dequeue_style() {
	tf_called( 'wp_dequeue_style', func_get_args() );
}

function wp_is_mobile() {
	return false;
}

function wp_get_archives( $args = '' ) {
	tf_called( 'wp_get_archives', array( $args ) );
}

function get_the_ID() {
	return 1;
}

function get_queried_object_id() {
	return 1;
}

function get_post_type() {
	return 'post';
}

function get_query_var( $var, $default = '' ) {
	return $default;
}

function set_query_var( $var, $value ) {
	tf_called( 'set_query_var', array( $var, $value ) );
}

function get_permalink( $post = 0 ) {
	return 'http://example.test/?p=1';
}

function get_the_title( $post = 0 ) {
	return 'Example';
}

function get_the_category( $post = 0 ) {
	return array();
}

function get_term_by( $field, $value, $taxonomy = '' ) {
	$term = new stdClass();
	$term->term_id = 2;
	$term->term_taxonomy_id = 2;
	$term->slug = 'case-study';
	$term->name = 'Case Study';
	return $term;
}

function get_page_by_path( $path ) {
	$page = new stdClass();
	$page->ID = 3;
	return $page;
}

function get_category_link( $category ) {
	return 'http://example.test/category/';
}

function get_tag_link( $tag ) {
	return 'http://example.test/tag/';
}

function get_transient( $key ) {
	$GLOBALS['tf']['calls'][] = array( 'get_transient', array( $key ) );
	return false;
}

function set_transient( $key, $value, $expiration = 0 ) {
	$GLOBALS['tf']['calls'][] = array( 'set_transient', array( $key, $value, $expiration ) );
	return true;
}

function delete_transient( $key ) {
	return true;
}

function wp_cache_get( $key, $group = '', $force = false ) {
	return false;
}

function wp_cache_set( $key, $data, $group = '', $expire = 0 ) {
	return true;
}

function is_wp_error( $thing ) {
	return false;
}

function wp_kses_post( $content ) {
	return $content;
}

function sanitize_text_field( $value ) {
	return is_string( $value ) ? trim( strip_tags( $value ) ) : $value;
}

function wp_unslash( $value ) {
	return $value;
}

function current_user_can( $capability ) {
	return false;
}

function wp_verify_nonce( $nonce, $action = -1 ) {
	return true;
}

function check_ajax_referer( $action = -1, $query_arg = false, $stop = true ) {
	return true;
}

function wp_die( $message = '' ) {
	throw new RuntimeException( 'wp_die: ' . ( is_string( $message ) ? $message : 'non-string' ) );
}

function wp_send_json( $response ) {
	throw new RuntimeException( 'wp_send_json' );
}

function wp_send_json_success( $response = null ) {
	throw new RuntimeException( 'wp_send_json_success' );
}

function wp_send_json_error( $response = null ) {
	throw new RuntimeException( 'wp_send_json_error' );
}

function __( $text, $domain = 'default' ) {
	return $text;
}

function _e( $text, $domain = 'default' ) {
	echo $text;
}

function esc_html__( $text, $domain = 'default' ) {
	return $text;
}

function esc_attr__( $text, $domain = 'default' ) {
	return $text;
}

function _x( $text, $context, $domain = 'default' ) {
	return $text;
}

function get_field( $field, $post_id = false ) {
	return '';
}

function get_post_meta( $post_id, $key = '', $single = false ) {
	return '';
}

function update_post_meta( $post_id, $key, $value ) {
	return true;
}

function metadata_exists( $meta_type, $object_id, $meta_key ) {
	return false;
}

function is_paged() {
	return false;
}

function paginate_links( $args = '' ) {
	return '';
}

function get_search_query() {
	return '';
}

function wp_reset_postdata() {
	tf_called( 'wp_reset_postdata' );
}

function wp_reset_query() {
	tf_called( 'wp_reset_query' );
}

function body_class( $class = '' ) {
	tf_called( 'body_class', array( $class ) );
}

function language_attributes() {
	echo 'lang="en-US"';
}

function bloginfo( $show = '' ) {
	echo '';
}

function wp_head() {
	tf_called( 'wp_head' );
}

function wp_footer() {
	tf_called( 'wp_footer' );
}

function wp_title( $sep = '&raquo;', $display = true, $seplocation = '' ) {
	return '';
}

function has_post_thumbnail( $post = null ) {
	return false;
}

function the_post_thumbnail( $size = 'post-thumbnail', $attr = '' ) {
	tf_called( 'the_post_thumbnail', array( $size ) );
}

function get_the_post_thumbnail_url( $post = null, $size = 'post-thumbnail' ) {
	return '';
}

function wp_get_attachment_image_src( $attachment_id, $size = 'thumbnail', $icon = false ) {
	return array( '', 0, 0 );
}

function get_posts( $args = array() ) {
	tf_called( 'get_posts', array( $args ) );

	if ( isset( $args['fields'] ) && 'ids' === $args['fields'] ) {
		return isset( $GLOBALS['tf']['get_posts_ids'] ) ? $GLOBALS['tf']['get_posts_ids'] : array( 41, 42 );
	}

	return isset( $GLOBALS['tf']['get_posts_returns'] ) ? $GLOBALS['tf']['get_posts_returns'] : array();
}

function get_terms( $args = array(), $deprecated = '' ) {
	$GLOBALS['tf']['calls'][] = array( 'get_terms', array( $args ) );
	return array();
}

function wp_list_categories( $args = '' ) {
	tf_called( 'wp_list_categories', array( $args ) );
}

function get_the_date( $format = '', $post = null ) {
	return '';
}

function get_the_time( $format = '', $post = null ) {
	return '';
}

function get_post_time( $format = '', $gmt = false, $post = null, $translate = false ) {
	return '';
}

function date_i18n( $format, $timestamp = false, $gmt = false ) {
	return date( $format, $timestamp === false ? time() : $timestamp );
}

function mysql2date( $format, $date, $translate = true ) {
	return $date;
}

function get_post( $post = null, $output = 'OBJECT', $filter = 'raw' ) {
	$p = new stdClass();
	$p->ID = 1;
	$p->post_title = 'Example';
	$p->post_content = '';
	$p->post_type = 'post';
	$p->post_status = 'publish';
	return $p;
}

function get_pages( $args = array() ) {
	return array();
}

function wp_get_post_parent_id( $post = null ) {
	return 0;
}

function get_the_permalink( $post = 0 ) {
	return 'http://example.test/?p=1';
}

function get_author_posts_url( $author_id ) {
	return 'http://example.test/author/';
}

function get_avatar_url( $id_or_email, $args = null ) {
	return '';
}

function the_permalink( $post = 0 ) {
	echo 'http://example.test/?p=1';
}

function the_title( $before = '', $after = '', $display = true ) {
	if ( $display ) {
		echo $before . 'Example' . $after;
	}
	return 'Example';
}

function the_excerpt() {
	echo '';
}

function the_content( $more_link_text = null, $strip_teaser = false ) {
	echo '';
}

function get_the_excerpt( $post = null ) {
	return '';
}

function get_the_content( $more_link_text = null, $strip_teaser = false, $post = null ) {
	return '';
}

function wpautop( $text, $br = true ) {
	return $text;
}

function shortcode_exists( $tag ) {
	return isset( $GLOBALS['tf']['shortcodes'][ $tag ] );
}

function do_shortcode( $content, $ignore_html = false ) {
	return $content;
}

function add_meta_box() {
	tf_called( 'add_meta_box', func_get_args() );
}

function current_time( $type, $gmt = 0 ) {
	return date( 'Y-m-d H:i:s' );
}

function wp_next_scheduled( $hook ) {
	return false;
}

function wp_schedule_event( $timestamp, $recurrence, $hook ) {
	return true;
}

function wp_create_nonce( $action = -1 ) {
	return 'nonce';
}

function admin_url( $path = '', $scheme = 'admin' ) {
	return 'http://example.test/wp-admin/' . ltrim( $path, '/' );
}

function wp_create_category( $cat_name ) {
	return 1;
}

function wp_insert_post( $postarr ) {
	return 1;
}

function wp_set_post_categories( $post_id, $categories ) {
	return true;
}

function wp_get_post_categories( $post_id ) {
	return array();
}

function wp_set_object_terms( $object_id, $terms, $taxonomy, $append = false ) {
	return array();
}

function wp_get_object_terms( $object_id, $taxonomy, $args = array() ) {
	return array();
}

function get_the_terms( $post, $taxonomy ) {
	return array();
}

function get_field_object( $field, $post_id = false ) {
	return false;
}

function wp_nav_menu() {
	tf_called( 'wp_nav_menu', func_get_args() );
}

function register_sidebar() {
	tf_called( 'register_sidebar', func_get_args() );
}

function dynamic_sidebar( $index = 1 ) {
	return false;
}

function is_active_sidebar( $index ) {
	return false;
}

function get_search_form() {
	return '';
}

function wp_page_menu() {
	tf_called( 'wp_page_menu', func_get_args() );
}

function get_theme_mod( $name, $default = false ) {
	return $default;
}

function set_theme_mod( $name, $value ) {
	return true;
}

function wp_is_post_revision( $post ) {
	return false;
}

function wp_save_post_revision() {
	return 0;
}

function is_preview() {
	return false;
}

function is_admin_bar_showing() {
	return false;
}

function add_theme_support() {
	tf_called( 'add_theme_support', func_get_args() );
}

function current_theme_supports( $feature ) {
	return true;
}

function wp_parse_args( $args, $defaults = array() ) {
	if ( is_object( $args ) ) {
		$parsed_args = get_object_vars( $args );
	} elseif ( is_array( $args ) ) {
		$parsed_args =& $args;
	} else {
		parse_str( $args, $parsed_args );
	}
	if ( is_array( $defaults ) && $defaults ) {
		return array_merge( $defaults, $parsed_args );
	}
	return $parsed_args;
}

function wp_json_encode( $data, $options = 0, $depth = 512 ) {
	return json_encode( $data, $options, $depth );
}

function absint( $maybeint ) {
	return abs( (int) $maybeint );
}

function wp_parse_url( $url, $component = -1 ) {
	return parse_url( $url, $component );
}

function wp_get_current_user() {
	$u = new stdClass();
	$u->ID = 0;
	$u->display_name = '';
	return $u;
}

function get_current_user_id() {
	return 0;
}

function wp_set_current_user() {}

function wp_enqueue_media() {}

function wp_editor() {}

function wp_upload_dir() {
	return array(
		'basedir' => sys_get_temp_dir(),
		'baseurl' => 'http://example.test/uploads',
		'path'    => sys_get_temp_dir(),
		'url'     => 'http://example.test/uploads',
	);
}

function wp_max_upload_size() {
	return 64 * 1024 * 1024;
}

function size_format( $bytes, $decimals = 0 ) {
	return $bytes . ' B';
}

function wp_get_environment_type() {
	return 'production';
}

function get_bloginfo_url() {
	return 'http://example.test';
}

function get_stylesheet_uri() {
	return 'http://example.test/style.css';
}

function get_stylesheet_directory_uri_test() {
	return '';
}

/* ---------------------------------------------------------------- classes -- */

if ( ! class_exists( 'Walker' ) ) {
	class Walker {
		public function start_lvl( &$output, $depth = 0, $args = null ) {}
		public function end_lvl( &$output, $depth = 0, $args = null ) {}
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {}
		public function end_el( &$output, $item, $depth = 0, $args = null ) {}
	}
}

if ( ! class_exists( 'Walker_Nav_Menu' ) ) {
	class Walker_Nav_Menu extends Walker {
		public function start_lvl( &$output, $depth = 0, $args = null ) {}
		public function end_lvl( &$output, $depth = 0, $args = null ) {}
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {}
		public function end_el( &$output, $item, $depth = 0, $args = null ) {}
	}
}

if ( ! class_exists( 'WP_Widget' ) ) {
	class WP_Widget {
		public function __construct( $id_base = '', $name = '', $widget_options = array(), $control_options = array() ) {}
		public function widget( $args, $instance ) {}
		public function form( $instance ) {}
		public function update( $new_instance, $old_instance ) { return $new_instance; }
	}
}

if ( ! class_exists( 'WP_Query' ) ) {
	class WP_Query {
		public $posts = array();
		public $post = null;
		public $max_num_pages = 1;
		public $found_posts = 0;
		public $args = array();
		public function __construct( $args = array() ) {
			$this->args = $args;
			$GLOBALS['tf']['calls'][] = array( 'WP_Query', array( $args ) );
		}
		public function have_posts() { return false; }
		public function the_post() {}
		public function rewind_posts() {}
	}
}

/* ------------------------------------------------- shortcode/test helpers -- */

function shortcode_atts( $pairs, $atts, $shortcode = '' ) {
	$atts = (array) $atts;
	$out  = array();
	foreach ( $pairs as $name => $default ) {
		if ( array_key_exists( $name, $atts ) ) {
			$out[ $name ] = $atts[ $name ];
		} else {
			$out[ $name ] = $default;
		}
	}
	return $out;
}

function setup_postdata( $post ) {
	tf_called( 'setup_postdata', array( is_object( $post ) ? ( $post->ID ?? null ) : $post ) );
	return true;
}

function get_post_thumbnail_id( $post = null ) {
	return 0;
}

function wp_get_attachment_url( $attachment_id = 0 ) {
	return 'http://example.test/image.jpg';
}

function get_category_by_slug( $slug ) {
	return null;
}

function strip_tags_content( $text ) {
	return $text;
}

function wp_kses( $string, $allowed_html, $allowed_protocols = array() ) {
	return $string;
}

function sanitize_title( $title, $fallback_title = '', $context = 'save' ) {
	return strtolower( preg_replace( '/[^a-z0-9]+/i', '-', $title ) );
}

function url_to_postid( $url ) {
	return 0;
}

function get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) {
	return null;
}

function wp_doing_ajax() {
	return ! empty( $GLOBALS['tf']['doing_ajax'] );
}

function wp_doing_cron() {
	return false;
}

function is_blog_admin() {
	return (bool) $GLOBALS['tf']['is_admin'];
}

function wp_tag_cloud( $args = '' ) {
	tf_called( 'wp_tag_cloud', array( $args ) );
	return '';
}

function get_the_category_list( $separator = '', $parents = '', $post_id = false ) {
	return '';
}
