<?php
/**
 * Contract: legacy Visual Composer product content becomes structured data
 * without losing any published specification.
 *
 * Fixtures are real products captured from the live database, chosen for the
 * shapes that actually occur:
 *
 *   kf-moisture-titrator  plain 2-column table, 20 rows, 8 features
 *   water-soil-analysis   4-model comparison table, 52 rows, multi-line cells
 *   colspan-models        20 of 22 rows use colspan, two header rows
 *   section-headers       rows that are group headings, not label/value pairs
 *   leak-test-apparatus   two <ul> in the document (one inside a table cell)
 *   no-thumbnail          bare table, no VC wrapper, no features at all
 *   no-pdf                no brochure (the plugin's only such product)
 *   no-specs              features only, no table (the plugin's only such product)
 *
 * Expected values were derived by reading the fixtures directly, not by
 * running the parser.
 */

ei_test( 'features come from the feature row, not from any <ul> in the document', function () {
	ei_require_core( 'inc/migration/class-vc-parser.php' );

	// This product has 11 feature bullets and a second <ul> of 3 accessory
	// bullets nested inside its spec table.
	$fixture = ei_fixture( 'products/leak-test-apparatus' );
	$result  = Ei\Core\Migration\Vc_Parser::parse( $fixture['content'] );

	ei_assert_count( 11, $result['features'], 'only the feature-row bullets are captured' );
	ei_assert_equals( 'Extremely Useful for Packing Industry', $result['features'][0], 'first feature' );
	ei_assert_false(
		in_array( 'Main Apparatus along with vacuum pump', $result['features'], true ),
		'accessory bullets nested in the spec table do not leak into features'
	);
} );

ei_test( 'features are decoded to plain text', function () {
	ei_require_core( 'inc/migration/class-vc-parser.php' );
	$result = Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/kf-moisture-titrator' )['content'] );

	ei_assert_count( 8, $result['features'], 'eight features' );
	ei_assert_equals(
		'Auto Computation of Moisture in ppm, % & mg HO.',
		$result['features'][0],
		'entities are decoded and tags stripped'
	);
} );

ei_test( 'the specification table is carried through whole', function () {
	ei_require_core( 'inc/migration/class-vc-parser.php' );
	$table = Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/kf-moisture-titrator' )['content'] )['specifications_table'];

	ei_assert_contains( '<table', $table, 'the table markup is kept' );
	ei_assert_equals( 20, ei_count_tag( $table, 'tr' ), 'all 20 rows survive' );
	ei_assert_contains( 'Measuring Range', $table, 'first label survives' );
	ei_assert_contains( '100 ppm to 100% of moisture', $table, 'first value survives' );
	ei_assert_contains( 'Instruction Manual', $table, 'the long accessory value survives' );
} );

ei_test( 'column spans and multi-model headers survive', function () {
	ei_require_core( 'inc/migration/class-vc-parser.php' );
	$table = Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/colspan-models' )['content'] )['specifications_table'];

	ei_assert_contains( 'colspan=', $table, 'spans are not flattened into empty cells' );
	ei_assert_equals( 22, ei_count_tag( $table, 'tr' ), 'every row survives' );
	ei_assert_contains( 'COMPRESSOR UNIT', $table, 'a one-cell section row survives' );
} );

ei_test( 'section heading rows survive inside the table', function () {
	ei_require_core( 'inc/migration/class-vc-parser.php' );
	$table = Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/section-headers' )['content'] )['specifications_table'];

	ei_assert_equals( 20, ei_count_tag( $table, 'tr' ), 'all rows survive' );
	foreach ( array( 'GENERAL', 'STIRRER', 'TEMPERATURE', 'SAMPLE' ) as $heading ) {
		ei_assert_contains( $heading, $table, "the {$heading} heading is preserved, not dropped as an empty row" );
	}
} );

ei_test( 'a four-model comparison table keeps its model header row', function () {
	ei_require_core( 'inc/migration/class-vc-parser.php' );
	$table = Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/water-soil-analysis' )['content'] )['specifications_table'];

	ei_assert_equals( 52, ei_count_tag( $table, 'tr' ), 'all 52 rows survive' );
	foreach ( array( '161', '191', '172' ) as $model ) {
		ei_assert_contains( $model, $table, "model column {$model} survives" );
	}
	ei_assert_contains( 'µS', $table, 'non-ASCII units survive unchanged' );
} );

ei_test( 'a product with no table yields no specification table', function () {
	ei_require_core( 'inc/migration/class-vc-parser.php' );
	$result = Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/no-specs' )['content'] );

	ei_assert_equals( '', $result['specifications_table'], 'no table is produced from nothing' );
	ei_assert_count( 7, $result['features'], 'its features are still captured' );
} );

ei_test( 'content with no page-builder wrapper at all still parses', function () {
	ei_require_core( 'inc/migration/class-vc-parser.php' );
	$result = Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/no-thumbnail' )['content'] );

	ei_assert_equals( 9, ei_count_tag( $result['specifications_table'], 'tr' ), 'a bare table is read like any other' );
	ei_assert_count( 0, $result['features'], 'it has no feature row' );
	ei_assert_contains( 'Specifications are subject to change', $result['description'], 'prose outside the table becomes the description' );
} );

ei_test( 'description keeps the prose and drops every builder artefact', function () {
	ei_require_core( 'inc/migration/class-vc-parser.php' );
	$result = Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/kf-moisture-titrator' )['content'] );

	ei_assert_contains( 'volumetric titrator', $result['description'], 'body copy survives' );
	ei_assert_false( strpos( $result['description'], '[vc_' ) !== false, 'no shortcode tags remain' );
	ei_assert_false( strpos( $result['description'], 'specification-table-product' ) !== false, 'the spec table does not leak into the description' );
	ei_assert_false( strpos( $result['description'], 'Auto Computation of Moisture' ) !== false, 'features do not leak into the description' );
} );

ei_test( 'inline images are collected from the builder shortcodes', function () {
	ei_require_core( 'inc/migration/class-vc-parser.php' );

	ei_assert_equals( array( 3275 ), Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/kf-moisture-titrator' )['content'] )['images'], 'single image shortcode' );
	ei_assert_equals( array(), Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/no-thumbnail' )['content'] )['images'], 'no image shortcode' );
	ei_assert_equals( array( 2621 ), Ei\Core\Migration\Vc_Parser::parse( ei_fixture( 'products/no-pdf' )['content'] )['images'], 'image without a brochure' );
} );

ei_test( 'model numbers are split off the product title after a separator', function () {
	ei_require_core( 'inc/migration/class-product-title.php' );

	$cases = array(
		'Microprocessor Flame Photometer - 1385 & 1382' => array( 'Microprocessor Flame Photometer', '1385 & 1382' ),
		'Dual Channel Flame Photometer –391/392' => array( 'Dual Channel Flame Photometer', '391/392' ),
		'Microprocessor Dissolution Test Apparatus – 1918 & 1916' => array( 'Microprocessor Dissolution Test Apparatus', '1918 & 1916' ),
		'Water Quality Analyzer for Viscosity/Viscometer - EI-3948' => array( 'Water Quality Analyzer for Viscosity/Viscometer', 'EI-3948' ),
	);

	foreach ( $cases as $title => $expected ) {
		list( $name, $model ) = Ei\Core\Migration\Product_Title::split( $title );
		ei_assert_equals( $expected[0], $name, "name for: {$title}" );
		ei_assert_equals( $expected[1], $model, "model for: {$title}" );
	}
} );

ei_test( 'model numbers are split off a trailing model expression', function () {
	ei_require_core( 'inc/migration/class-product-title.php' );

	$cases = array(
		'Microprocessor K.F. Moisture Titrator 2760' => array( 'Microprocessor K.F. Moisture Titrator', '2760' ),
		'Digital TDS Meter 661(Portable)'            => array( 'Digital TDS Meter', '661(Portable)' ),
		'Digital pH Meter 131, 132(Portable)'        => array( 'Digital pH Meter', '131, 132(Portable)' ),
		'Digital Conductivity Meter 601, 602 & 611'  => array( 'Digital Conductivity Meter', '601, 602 & 611' ),
		'Microprocessor Spectrophotometer (Vis) Single 1305' => array( 'Microprocessor Spectrophotometer (Vis) Single', '1305' ),
	);

	foreach ( $cases as $title => $expected ) {
		list( $name, $model ) = Ei\Core\Migration\Product_Title::split( $title );
		ei_assert_equals( $expected[0], $name, "name for: {$title}" );
		ei_assert_equals( $expected[1], $model, "model for: {$title}" );
	}
} );

ei_test( 'a trailing short number is not treated as a model', function () {
	ei_require_core( 'inc/migration/class-product-title.php' );

	list( $name, $model ) = Ei\Core\Migration\Product_Title::split( 'pH Meter 7' );
	ei_assert_equals( 'pH Meter 7', $name, 'the name is left untouched rather than guessed at' );
	ei_assert_equals( '', $model, 'a one-digit tail is not a model number' );
} );
