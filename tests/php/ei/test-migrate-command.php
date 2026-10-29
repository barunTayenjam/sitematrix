<?php
/**
 * Contract: `wp ei migrate-products` is safe to run, safe to re-run, and says
 * what it did.
 *
 * The command is the only thing that writes during the content migration, so
 * two properties matter more than speed:
 *
 *   --dry-run   reports the same plan but writes nothing
 *   idempotent  a second run changes nothing and says so
 *
 * Both are asserted here against stubbed WordPress, so the guarantees hold
 * without a database.
 */

/** Build a stub post object from a fixture. */
function ei_post_from_fixture( string $name ) {
	$product = ei_product( $name );

	$post               = new stdClass();
	$post->ID           = $product['id'];
	$post->post_type    = 'product';
	$post->post_title   = $product['title'];
	$post->post_name    = $product['slug'];
	$post->post_excerpt = $product['excerpt'];
	$post->post_content = $product['content'];

	foreach ( $product['meta'] as $key => $value ) {
		$GLOBALS['ei']['meta'][ $post->ID ][ $key ] = $value;
	}

	return $post;
}

/** Load the command class once. */
function ei_migrate_command(): string {
	ei_require_core( 'inc/migration/class-product-migrator.php' );
	ei_require_core( 'inc/cli/class-migrate-command.php' );

	return 'Ei\Core\Cli\Migrate_Command';
}

ei_test( 'the command registers itself with WP-CLI', function () {
	$class = ei_migrate_command();
	$class::register();

	ei_assert_has_key( 'ei migrate-products', $GLOBALS['ei']['cli'], 'the command is registered' );
} );

ei_test( 'a dry run reports the plan and writes nothing', function () {
	$class = ei_migrate_command();
	$GLOBALS['ei']['posts'] = array( ei_post_from_fixture( 'kf-moisture-titrator' ) );

	$report = $class::run( array( 'dry-run' => true ) );

	ei_assert_equals( 1, $report['scanned'], 'one product was scanned' );
	ei_assert_equals( 1, $report['changed'], 'one product would change' );
	ei_assert_true( $report['dry_run'], 'the report says it was a dry run' );
	ei_assert_count( 0, $GLOBALS['ei']['updated'], 'no post was updated' );
	ei_assert_equals( array(), $GLOBALS['ei']['fields'], 'no field was written' );
} );

ei_test( 'applying writes the title, fields and legacy snapshot', function () {
	$class = ei_migrate_command();
	$post  = ei_post_from_fixture( 'kf-moisture-titrator' );
	$GLOBALS['ei']['posts'] = array( $post );

	$report = $class::apply();
	$id     = $post->ID;

	ei_assert_equals( 1, $report['changed'], 'one product changed' );
	ei_assert_false( $report['dry_run'], 'the report says it wrote' );

	$updated = end( $GLOBALS['ei']['updated'] );
	ei_assert_equals( 'Microprocessor K.F. Moisture Titrator', $updated['post_title'], 'the model number leaves the title' );
	ei_assert_equals( '', $updated['post_content'], 'the builder HTML leaves the body' );

	ei_assert_equals( '2760', get_field( 'model_number', $id ), 'the model number is in its field' );
	ei_assert_count( 8, get_field( 'features', $id ), 'the feature repeater is populated' );
	ei_assert_contains( 'Measuring Range', get_field( 'specifications_table', $id ), 'the specification table is stored' );

	$legacy = get_post_meta( $id, '_ei_legacy', true );
	ei_assert_true( is_array( $legacy ), 'a legacy snapshot is stored' );
	ei_assert_contains( 'product_feature_row', $legacy['content'], 'the snapshot holds the original HTML' );
} );

ei_test( 'a second run changes nothing', function () {
	$class = ei_migrate_command();
	$post  = ei_post_from_fixture( 'kf-moisture-titrator' );
	$GLOBALS['ei']['posts'] = array( $post );

	$class::apply();
	$writes_after_first = count( $GLOBALS['ei']['updated'] );

	$second = $class::apply();

	ei_assert_equals( 0, $second['changed'], 'nothing changed on the second run' );
	ei_assert_equals( 1, $second['unchanged'], 'the product is reported as already migrated' );
	ei_assert_equals( $writes_after_first, count( $GLOBALS['ei']['updated'] ), 'no extra post update was issued' );
} );

ei_test( 'the brochure URL is stored so the file can be fetched later', function () {
	$class = ei_migrate_command();
	$post  = ei_post_from_fixture( 'kf-moisture-titrator' );
	$GLOBALS['ei']['posts'] = array( $post );

	$class::apply();

	$url = get_post_meta( $post->ID, '_ei_brochure_url', true );
	ei_assert_contains( '.pdf', $url, 'the legacy PDF URL is kept on the product' );
} );

ei_test( 'a re-run never re-derives from an already-rewritten title', function () {
	$class = ei_migrate_command();
	$post  = ei_post_from_fixture( 'kf-moisture-titrator' );
	$GLOBALS['ei']['posts'] = array( $post );

	$class::apply();
	// Forced re-runs are how a mapping bug gets repaired. They must not treat
	// the cleaned title as the source, or the model number disappears.
	$forced = $class::run( array( 'force' => true ) );

	ei_assert_equals( '2760', get_field( 'model_number', $post->ID ), 'the model number survives a forced re-run' );
	ei_assert_equals( 'Microprocessor K.F. Moisture Titrator', $post->post_title, 'the title is not truncated again' );
	ei_assert_equals( 0, $forced['changed'], 'nothing changed' );
} );

ei_test( 'an editor edit is not reverted by a later run', function () {
	$class = ei_migrate_command();
	$post  = ei_post_from_fixture( 'kf-moisture-titrator' );
	$GLOBALS['ei']['posts'] = array( $post );

	$class::apply();
	$post->post_title = 'K.F. Moisture Titrator (revised)';

	$report = $class::apply();

	ei_assert_equals( 0, $report['changed'], 'the migrated product is left alone' );
	ei_assert_equals( 'K.F. Moisture Titrator (revised)', $post->post_title, 'the edited title stays' );
} );

ei_test( 'a post id limits the run to one product', function () {
	$class = ei_migrate_command();
	$one   = ei_post_from_fixture( 'kf-moisture-titrator' );
	$two   = ei_post_from_fixture( 'tds-meter' );
	$GLOBALS['ei']['posts'] = array( $one, $two );

	$report = $class::run( array( 'post-id' => $two->ID ) );

	ei_assert_equals( 1, $report['scanned'], 'only the requested product is scanned' );
	ei_assert_equals( $two->ID, $report['items'][0]['id'], 'the report names that product' );
} );

ei_test( 'the report is machine-readable', function () {
	$class = ei_migrate_command();
	$GLOBALS['ei']['posts'] = array( ei_post_from_fixture( 'no-pdf' ) );

	$report = $class::run( array( 'dry-run' => true ) );
	$json   = json_encode( $report );

	ei_assert_true( false !== $json, 'the report encodes as JSON' );
	ei_assert_has_key( 'scanned', $report, 'scanned count' );
	ei_assert_has_key( 'changed', $report, 'changed count' );
	ei_assert_has_key( 'unchanged', $report, 'unchanged count' );
	ei_assert_has_key( 'items', $report, 'per-product detail' );
} );
