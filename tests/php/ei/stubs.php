<?php
/**
 * WordPress stubs for the EI suite.
 *
 * Each stub records the call so tests can assert on the public contract
 * (which post type was registered, with which arguments) without booting
 * WordPress or touching a database.
 *
 * Anything a test needs but that is not here yet should be added deliberately,
 * with the smallest body that makes the behaviour observable.
 */

define( 'EI_REPO_ROOT', dirname( __DIR__, 3 ) );
define( 'EI_SITE_ROOT', EI_REPO_ROOT . '/wp_ei-revamp' );
define( 'EI_CORE_DIR', EI_SITE_ROOT . '/wp-content/plugins/ei-core' );
define( 'EI_CORE_PATH', EI_CORE_DIR . '/' );
define( 'EI_THEME_DIR', EI_SITE_ROOT . '/wp-content/themes/ei' );
define( 'ABSPATH', EI_SITE_ROOT . '/' );

$GLOBALS['ei'] = array(
	'post_types' => array(),
	'taxonomies' => array(),
	'actions'    => array(),
	'filters'    => array(),
	'options'    => array(),
	'meta'       => array(),
	'fields'     => array(),
	'posts'      => array(),
	'updated'    => array(),
	'cli'        => array(),
);

/* ---------------------------------------------------------------- hooks -- */

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	$GLOBALS['ei']['actions'][ $hook ][] = $callback;
	return true;
}

function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	$GLOBALS['ei']['filters'][ $hook ][] = $callback;
	return true;
}

function do_action( $hook, ...$args ) {
	foreach ( $GLOBALS['ei']['actions'][ $hook ] ?? array() as $callback ) {
		$callback( ...$args );
	}
}

function apply_filters( $hook, $value, ...$args ) {
	return $value;
}

/* ----------------------------------------------------------- registration -- */

function register_post_type( $slug, $args = array() ) {
	$GLOBALS['ei']['post_types'][ $slug ] = $args;
	return (object) array( 'name' => $slug );
}

function register_taxonomy( $slug, $object_type, $args = array() ) {
	$GLOBALS['ei']['taxonomies'][ $slug ] = array(
		'object_type' => $object_type,
		'args'        => $args,
	);
	return (object) array( 'name' => $slug );
}

/* -------------------------------------------------------------- options -- */

function get_option( $key, $default = false ) {
	return array_key_exists( $key, $GLOBALS['ei']['options'] ) ? $GLOBALS['ei']['options'][ $key ] : $default;
}

function update_option( $key, $value ) {
	$GLOBALS['ei']['options'][ $key ] = $value;
	return true;
}

/* ----------------------------------------------------------------- meta -- */

function get_post_meta( $post_id, $key = '', $single = false ) {	if ( '' === $key ) {
		return $GLOBALS['ei']['meta'][ $post_id ] ?? array();
	}
	$value = $GLOBALS['ei']['meta'][ $post_id ][ $key ] ?? null;
	if ( null === $value ) {
		return $single ? '' : array();
	}
	return $single ? $value : array( $value );
}

function update_post_meta( $post_id, $key, $value ) {
	$GLOBALS['ei']['meta'][ $post_id ][ $key ] = $value;
	return true;
}

function delete_post_meta( $post_id, $key ) {
	unset( $GLOBALS['ei']['meta'][ $post_id ][ $key ] );
	return true;
}

function get_post_thumbnail_id( $post_id = 0 ) {
	return (int) ( $GLOBALS['ei']['meta'][ (int) $post_id ]['thumbnail_id'] ?? 0 );
}

function wp_json_encode( $data, $options = 0 ) {
	return json_encode( $data, $options );
}

function wp_kses( $html, $allowed = array(), $protocols = array() ) {
	if ( empty( $allowed ) ) {
		return '';
	}
	// Minimal: keep table/structure tags only; drop scripts and styles entirely.
	$out = preg_replace( '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/is', '', $html );
	$out = preg_replace( '/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/is', '', $out );
	$out = preg_replace( '/<(?!table|tbody|tr|td|th|thead|tfoot|caption|p|br|b|strong|em|i|ul|ol|li|a|span|div|h[1-6]|blockquote|sub|sup|hr)[^>]*>/i', '', $out );
	$out = preg_replace( '/(on\w+)\s*=\s*"[^"]*"/i', '', $out );
	$out = preg_replace( '/(on\w+)\s*=\s*\'[^\']*\'/i', '', $out );
	return $out;
}

function wp_get_attachment_url( $attachment_id = 0 ) {
	$meta = $GLOBALS['ei']['meta'][ (int) $attachment_id ] ?? array();
	if ( ! empty( $meta['_wp_attached_file'] ) ) {
		return '/wp-content/uploads/' . $meta['_wp_attached_file'];
	}
	return '';
}

/* ------------------------------------------------------------- queries -- */

$GLOBALS['ei']['get_posts_args'] = array();

function get_posts( $args = array() ) {
	$GLOBALS['ei']['get_posts_args'][] = $args;
	$posts = $GLOBALS['ei']['posts'];

	if ( isset( $args['p'] ) ) {
		$posts = array_values( array_filter( $posts, static fn( $p ) => (int) $p->ID === (int) $args['p'] ) );
	}
	if ( isset( $args['post__in'] ) ) {
		$posts = array_values( array_filter( $posts, static fn( $p ) => in_array( (int) $p->ID, array_map( 'intval', $args['post__in'] ), true ) ) );
	}
	if ( isset( $args['numberposts'] ) && $args['numberposts'] > 0 ) {
		$posts = array_slice( $posts, 0, (int) $args['numberposts'] );
	}

	return $posts;
}

function wp_update_post( $postarr, $wp_error = false ) {
	$id = (int) ( is_array( $postarr ) ? ( $postarr['ID'] ?? 0 ) : 0 );
	$GLOBALS['ei']['updated'][] = $postarr;

	foreach ( $GLOBALS['ei']['posts'] as $post ) {
		if ( (int) $post->ID !== $id ) {
			continue;
		}
		foreach ( (array) $postarr as $key => $value ) {
			if ( 'ID' === $key ) {
				continue;
			}
			$property = 'post_' . preg_replace( '/^post_/', '', (string) $key );
			$post->{$property} = $value;
		}
	}

	return $id;
}

/* ----------------------------------------------------------------- ACF -- */

function get_field( $selector, $post_id = false, $format = true ) {
	$id = (int) $post_id;
	return $GLOBALS['ei']['fields'][ $id ][ $selector ] ?? false;
}

function update_field( $selector, $value, $post_id = 0 ) {
	$GLOBALS['ei']['fields'][ (int) $post_id ][ $selector ] = $value;
	return true;
}

/* ----------------------------------------------------------------- CLI -- */

/**
 * Enough of WP_CLI that a command class can register itself and report.
 * Registered commands are recorded rather than dispatched; tests call the
 * callable directly.
 */
if ( ! class_exists( 'WP_CLI' ) ) {
	class WP_CLI {
		public static function add_command( $name, $callable ) {
			$GLOBALS['ei']['cli'][ $name ] = $callable;
			return true;
		}

		public static function log( $message ) {
			$GLOBALS['ei']['log'][] = $message;
		}

		public static function success( $message ) {
			$GLOBALS['ei']['log'][] = $message;
		}

		public static function warning( $message ) {
			$GLOBALS['ei']['log'][] = 'WARN: ' . $message;
		}

		public static function error( $message ) {
			throw new RuntimeException( (string) $message );
		}

		public static function line( $message = '' ) {
			$GLOBALS['ei']['log'][] = $message;
		}
	}
}

define( 'WP_CLI', true );

/* ------------------------------------------------------------ i18n / esc -- */
function __( $text, $domain = 'default' ) {
	return $text;
}

function _x( $text, $context, $domain = 'default' ) {
	return $text;
}

function esc_html__( $text, $domain = 'default' ) {
	return htmlspecialchars( $text, ENT_QUOTES );
}

function esc_attr( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES );
}

function esc_html( $text ) {
	return htmlspecialchars( (string) $text, ENT_QUOTES );
}

function esc_url( $url ) {
	return (string) $url;
}

function wp_strip_all_tags( $text ) {
	return trim( preg_replace( '/<[^>]*>/', ' ', (string) $text ) );
}

function sanitize_text_field( $text ) {
	return trim( preg_replace( '/[\r\n\t]+/', ' ', strip_tags( (string) $text ) ) );
}

/* ---------------------------------------------------------------- misc -- */

function is_admin() {
	return false;
}

function wp_parse_args( $args, $defaults = array() ) {
	return array_merge( $defaults, (array) $args );
}

/** Reset the recorded WordPress calls between tests. */
function ei_reset() {
	$GLOBALS['ei'] = array(
		'post_types'        => array(),
		'taxonomies'        => array(),
		'actions'           => array(),
		'filters'           => array(),
		'options'           => array(),
		'meta'              => array(),
		'fields'            => array(),
		'posts'             => array(),
		'updated'           => array(),
		'cli'               => array(),
		'log'               => array(),
		'get_posts_args'    => array(),
	);
}

/** Load a file from ei-core by relative path, once. */
function ei_require_core( $relative ) {
	static $loaded = array();
	$path = EI_CORE_DIR . '/' . ltrim( $relative, '/' );
	if ( ! isset( $loaded[ $path ] ) ) {
		if ( ! file_exists( $path ) ) {
			throw new RuntimeException( "Missing ei-core file: {$relative}" );
		}
		require_once $path;
		$loaded[ $path ] = true;
	}
}
