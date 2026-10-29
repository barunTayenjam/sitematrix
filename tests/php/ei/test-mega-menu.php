<?php
/**
 * Contract: the primary navigation's Products item opens a mega-menu panel
 * built from the products taxonomy. Pure function — the theme feeds it term
 * groups, the markup is deterministic, and no plugin is involved.
 */

ei_test( 'a mega menu renders one column per top-level category', function () {
	ei_tags();

	$groups = array(
		array(
			'name'     => 'Electrochemistry Instruments',
			'url'      => '/products/electrochemistry_instruments/',
			'children' => array(
				array( 'name' => 'pH Meter', 'url' => '/products/ph_meter/' ),
				array( 'name' => 'Conductivity Meter', 'url' => '/products/conductivity_meter/' ),
			),
		),
		array(
			'name'     => 'Pharmaceutical Instruments',
			'url'      => '/products/pharmaceutical_instruments/',
			'children' => array(),
		),
	);

	$html = ei_mega_menu( 'Products', '/products/', $groups );

	ei_assert_contains( 'ei-mega-panel', $html, 'panel class present' );
	ei_assert_contains( '>Electrochemistry Instruments</a>', $html, 'category name is a panel heading link' );
	ei_assert_contains( 'href="/products/ph_meter/"', $html, 'child link present' );
	ei_assert_contains( 'href="/products/pharmaceutical_instruments/"', $html, 'childless category still linked' );
	ei_assert_false( strpos( $html, '<ul class="ei-mega-col-list"></ul>' ) !== false, 'no empty column list' );
} );

ei_test( 'with no categories the Products item degrades to a plain link', function () {
	ei_tags();

	$html = ei_mega_menu( 'Products', '/products/', array() );

	ei_assert_contains( 'href="/products/"', $html, 'still links to the catalogue' );
	ei_assert_false( strpos( $html, 'ei-mega-panel' ) !== false, 'no empty panel markup' );
} );

ei_test( 'panel markup is a nav-friendly ul/li tree', function () {
	ei_tags();

	$groups = array(
		array(
			'name'     => 'Spectrophotometers',
			'url'      => '/products/spectrophotometer/',
			'children' => array(
				array( 'name' => 'Single Beam', 'url' => '/products/single_beam_spectrophotometer/' ),
			),
		),
	);

	$html = ei_mega_menu( 'Products', '/products/', $groups );

	ei_assert_contains( '<li class="menu-item-has-children ei-mega">', $html, 'the Products li carries both classes' );
	ei_assert_contains( '<ul class="ei-mega-panel">', $html, 'panel opens as a ul' );
	ei_assert_contains( '</ul></li>', $html, 'panel closes inside the li' );
} );
