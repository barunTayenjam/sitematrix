<?php
/**
 * Contract: a legacy product becomes the structured content the new theme
 * renders, and nothing that was published is lost in the move.
 *
 * The mapper is the single place where legacy shape meets the field model, so
 * these assertions are the specification for the whole migration.
 *
 * Fixtures (see test-vc-parser.php for the shapes they cover):
 *   kf-moisture-titrator  clean case: model in the title, table, 8 features
 *   water-soil-analysis   inline image differs from the featured image
 *   leak-test-apparatus   the only captured product flagged show_on_home=yes
 *   no-pdf                no brochure
 *   no-specs              no table, no prose
 */

/** Map a fixture through the migrator. */
function ei_map( string $name ): array {
	ei_require_core( 'inc/migration/class-product-migrator.php' );
	return Ei\Core\Migration\Product_Migrator::map( ei_product( $name ) );
}

ei_test( 'the title is split into a clean name and a model number', function () {
	$mapped = ei_map( 'kf-moisture-titrator' );

	ei_assert_equals( 'Microprocessor K.F. Moisture Titrator', $mapped['post_title'], 'the model number leaves the title' );
	ei_assert_equals( '2760', $mapped['fields']['model_number'], 'the model number becomes a field' );
} );

ei_test( 'the product body is emptied because the fields now carry it', function () {
	$mapped = ei_map( 'kf-moisture-titrator' );

	ei_assert_equals( '', $mapped['post_content'], 'no page-builder HTML is left in post_content' );
} );

ei_test( 'features become repeater rows in their published order', function () {
	$mapped = ei_map( 'kf-moisture-titrator' );
	$rows   = $mapped['fields']['features'];

	ei_assert_count( 8, $rows, 'eight repeater rows' );
	ei_assert_equals( array( 'feature' => 'Auto Computation of Moisture in ppm, % & mg HO.' ), $rows[0], 'a row is a feature key/value pair' );
	ei_assert_equals( array( 'feature' => 'User Defined Setup Files for Ease of Operation' ), end( $rows ), 'order is preserved' );
} );

ei_test( 'the specification table is carried through untouched', function () {
	$mapped = ei_map( 'kf-moisture-titrator' );
	$table  = $mapped['fields']['specifications_table'];

	ei_assert_contains( '<table', $table, 'the table markup is kept' );
	ei_assert_equals( 20, preg_match_all( '/<tr\b/i', $table ), 'all 20 rows survive' );
	ei_assert_contains( 'Measuring Range', $table, 'labels survive' );
} );

ei_test( 'the inline image joins the gallery only when it is not the featured image', function () {
	// kf's only inline image is the featured image, already shown first.
	ei_assert_equals( array(), ei_map( 'kf-moisture-titrator' )['fields']['gallery'], 'a duplicate of the featured image is not repeated' );

	// water-soil has a distinct product shot inside the content.
	ei_assert_equals( array( 2626 ), ei_map( 'water-soil-analysis' )['fields']['gallery'], 'a distinct inline image becomes gallery content' );
} );

ei_test( "legacy 'yes' becomes a true flag and the home order becomes an integer", function () {
	$flagged = ei_map( 'leak-test-apparatus' );
	ei_assert_equals( 1, $flagged['fields']['show_on_home'], "'yes' is true" );
	ei_assert_equals( 1, $flagged['fields']['home_order'], 'the order is an integer' );

	$unflagged = ei_map( 'kf-moisture-titrator' );
	ei_assert_equals( 0, $unflagged['fields']['show_on_home'], 'an empty flag is false' );
	ei_assert_equals( 0, $unflagged['fields']['home_order'], 'a missing order is 0, not null' );
} );

ei_test( 'the brochure URL is reported apart from the file field', function () {
	$with    = ei_map( 'kf-moisture-titrator' );
	$without = ei_map( 'no-pdf' );

	ei_assert_contains( '.pdf', $with['brochure_url'], 'the legacy PDF URL is surfaced' );
	ei_assert_false( isset( $with['fields']['brochure_pdf'] ) && '' !== $with['fields']['brochure_pdf'], 'the file field is not filled with a remote URL' );

	ei_assert_equals( '', $without['brochure_url'], 'a product without a brochure reports none' );
} );

ei_test( 'the legacy HTML and meta are preserved so nothing is lost', function () {
	$fixture = ei_product( 'water-soil-analysis' );
	$legacy  = ei_map( 'water-soil-analysis' )['legacy'];

	ei_assert_equals( $fixture['content'], $legacy['content'], 'the original post content is kept verbatim' );
	ei_assert_equals( $fixture['meta']['pdf_file_upload'], $legacy['meta']['pdf_file_upload'], 'the original meta is kept' );
	ei_assert_equals( $fixture['meta']['thumbnail_id'], $legacy['meta']['thumbnail_id'], 'the featured image id is kept' );
} );

ei_test( 'no short description is invented from body copy', function () {
	// water-soil's only prose is the boilerplate "Specifications are subject to
	// change" note. Copying it into a card would be worse than leaving it empty
	// and letting the theme fall back to the feature list.
	ei_assert_equals( '', ei_map( 'water-soil-analysis' )['fields']['short_description'], 'the note is not promoted to a summary' );
	ei_assert_equals( '', ei_map( 'kf-moisture-titrator' )['fields']['short_description'], 'short descriptions are left for an editor' );
} );

ei_test( 'a product with no table or features maps without inventing any', function () {
	$mapped = ei_map( 'no-specs' );

	ei_assert_equals( '', $mapped['fields']['specifications_table'], 'no table is produced' );
	ei_assert_count( 7, $mapped['fields']['features'], 'its features are still mapped' );
} );

ei_test( 'mapping is idempotent', function () {
	$fixture = ei_product( 'water-soil-analysis' );
	ei_require_core( 'inc/migration/class-product-migrator.php' );

	$once  = Ei\Core\Migration\Product_Migrator::map( $fixture );
	$twice = Ei\Core\Migration\Product_Migrator::map( $fixture );

	ei_assert_equals( $once, $twice, 'mapping the same product twice yields the same payload' );
} );

ei_test( 'mapping does not mutate its input', function () {
	$fixture = ei_product( 'kf-moisture-titrator' );
	$before  = $fixture;

	ei_map( 'kf-moisture-titrator' );

	ei_assert_equals( $before, $fixture, 'the source row is untouched' );
} );
