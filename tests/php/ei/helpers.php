<?php
/**
 * Shared fixture loaders for the EI suite.
 *
 * Fixtures in fixtures/products/*.json are real rows captured from the legacy
 * database. They are the evidence behind every parser and migration test, so
 * the loaders live here rather than in one test file — several suites need the
 * same row in the same shape.
 */

/** Load a raw fixture as an array. */
function ei_fixture( string $name ): array {
	$path = __DIR__ . '/fixtures/' . $name . '.json';
	if ( ! file_exists( $path ) ) {
		ei_fail( "missing fixture: {$name}" );
	}

	$decoded = json_decode( (string) file_get_contents( $path ), true );
	if ( ! is_array( $decoded ) ) {
		ei_fail( "fixture is not valid JSON: {$name}" );
	}

	return $decoded;
}

/**
 * Load a product fixture in the shape the migrator consumes — the same keys a
 * row of wp_posts plus the handful of meta the legacy site used.
 */
function ei_product( string $name ): array {
	$fixture = ei_fixture( 'products/' . $name );

	return array(
		'id'      => (int) $fixture['id'],
		'title'   => (string) $fixture['title'],
		'slug'    => (string) ( $fixture['slug'] ?? '' ),
		'excerpt' => (string) ( $fixture['excerpt'] ?? '' ),
		'content' => (string) $fixture['content'],
		'meta'    => (array) $fixture['meta'],
	);
}

/** Count opening tags in a fragment, as an independent measure of structure. */
function ei_count_tag( string $html, string $tag ): int {
	return preg_match_all( '/<' . $tag . '\b[^>]*>/i', $html );
}

/** Recorded get_posts() calls, for asserting on the query a helper issues. */
function ei_calls_to_get_posts(): array {
	return $GLOBALS['ei']['get_posts_args'];
}
