<?php
/**
 * Contract: the field model is versioned in the repository, and it matches what
 * the migration writes.
 *
 * These assertions read the ACF local-JSON files directly. That is deliberate:
 * the JSON is the contract other environments and other developers load, so a
 * field renamed in the admin but not exported — or exported but never committed
 * — has to fail here rather than silently diverging between machines.
 */

/** Load an ACF field-group definition from ei-core/acf-json. */
function ei_field_group( string $file ): array {
	$path = EI_CORE_DIR . '/acf-json/' . $file;
	if ( ! file_exists( $path ) ) {
		ei_fail( "missing field group: {$file}" );
	}
	$group = json_decode( file_get_contents( $path ), true );
	if ( ! is_array( $group ) ) {
		ei_fail( "field group is not valid JSON: {$file}" );
	}
	return $group;
}

/** Index a group's top-level fields by name. */
function ei_fields_by_name( array $group ): array {
	$indexed = array();
	foreach ( $group['fields'] ?? array() as $field ) {
		$indexed[ $field['name'] ] = $field;
	}
	return $indexed;
}

ei_test( 'the product field group matches what the parser produces', function () {
	$fields = ei_fields_by_name( ei_field_group( 'group-product-details.json' ) );

	$expected = array(
		'model_number'         => 'text',
		'short_description'    => 'textarea',
		'description'          => 'wysiwyg',
		'features'             => 'repeater',
		'specifications_table' => 'wysiwyg',
		'brochure_pdf'         => 'file',
		'gallery'              => 'gallery',
		'show_on_home'         => 'true_false',
		'home_order'           => 'number',
	);

	foreach ( $expected as $name => $type ) {
		ei_assert_has_key( $name, $fields, "product field '{$name}' exists" );
		ei_assert_equals( $type, $fields[ $name ]['type'], "product field '{$name}' is a {$type}" );
	}
} );

ei_test( 'features are edited as a repeater of single lines', function () {
	$fields = ei_fields_by_name( ei_field_group( 'group-product-details.json' ) );

	ei_assert_has_key( 'features', $fields, 'features field exists' );
	$subs = $fields['features']['sub_fields'] ?? array();

	ei_assert_count( 1, $subs, 'one sub-field per feature' );
	ei_assert_equals( 'feature', $subs[0]['name'], 'the sub-field is named feature' );
	ei_assert_equals( 'textarea', $subs[0]['type'], 'a feature can wrap onto more than one line' );
} );

ei_test( 'the brochure field only accepts PDFs', function () {
	$fields = ei_fields_by_name( ei_field_group( 'group-product-details.json' ) );
	$brochure = $fields['brochure_pdf'];

	ei_assert_equals( 'pdf', $brochure['mime_types'], 'only PDFs can be attached' );
	ei_assert_equals( 'array', $brochure['return_format'], 'the template receives attachment data, not a bare ID' );
} );

ei_test( 'the product group is attached to the product post type', function () {
	$group = ei_field_group( 'group-product-details.json' );

	ei_assert_equals( 'group_product_details', $group['key'], 'the group key is stable' );
	ei_assert_equals( array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'product' ) ) ), $group['location'], 'the group shows on products and nothing else' );
} );

ei_test( 'the category group carries image, intro, icon and order', function () {
	$group  = ei_field_group( 'group-product-category.json' );
	$fields = ei_fields_by_name( $group );

	$expected = array(
		'category_image' => 'image',
		'intro'          => 'wysiwyg',
		'icon'           => 'image',
		'order'          => 'number',
	);

	foreach ( $expected as $name => $type ) {
		ei_assert_has_key( $name, $fields, "category field '{$name}' exists" );
		ei_assert_equals( $type, $fields[ $name ]['type'], "category field '{$name}' is a {$type}" );
	}

	ei_assert_equals(
		array( array( array( 'param' => 'taxonomy', 'operator' => '==', 'value' => 'products' ) ) ),
		$group['location'],
		'the group shows on the products taxonomy only'
	);
} );

ei_test( 'testimonial and client groups carry their own attributes', function () {
	$testimonial = ei_fields_by_name( ei_field_group( 'group-testimonial.json' ) );
	foreach ( array( 'person_name' => 'text', 'role_company' => 'text', 'rating' => 'number' ) as $name => $type ) {
		ei_assert_has_key( $name, $testimonial, "testimonial field '{$name}' exists" );
		ei_assert_equals( $type, $testimonial[ $name ]['type'], "testimonial field '{$name}' is a {$type}" );
	}

	$client = ei_fields_by_name( ei_field_group( 'group-client.json' ) );
	ei_assert_has_key( 'website_url', $client, 'client field website_url exists' );
	ei_assert_equals( 'url', $client['website_url']['type'], 'client website is a URL' );
} );

ei_test( 'ACF loads field groups from the plugin, never from the database', function () {
	ei_require_core( 'inc/acf.php' );
	Ei\Core\Acf::boot();

	ei_assert_true( isset( $GLOBALS['ei']['filters']['acf/settings/load_json'] ), 'a load_json path is registered' );

	$paths = Ei\Core\Acf::json_paths( array( '/somewhere/else' ) );
	ei_assert_equals( EI_CORE_DIR . '/acf-json', end( $paths ), 'the plugin acf-json directory is searched' );
	ei_assert_equals( '/somewhere/else', $paths[0], 'existing paths are kept' );
} );
