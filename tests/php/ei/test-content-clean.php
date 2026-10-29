<?php
/**
 * Contract: legacy pages are Visual Composer shortcode soup. ei_clean_content()
 * turns a page body into readable HTML — wrapper shortcodes keep their inner
 * content, widget shortcodes (sliders, maps, raw base64 blobs) disappear
 * entirely — so every retained page renders something meaningful.
 */

ei_test( 'vc wrappers are stripped but their inner content survives', function () {
	ei_tags();

	$dirty = '[vc_row el_class="x"][vc_column width="1/2"][vc_column_text]<p>Real prose stays.</p>[/vc_column_text][/vc_column][/vc_row]';

	ei_assert_contains( 'Real prose stays.', ei_clean_content( $dirty ), 'inner content survives' );
	ei_assert_false( strpos( ei_clean_content( $dirty ), '[vc_' ) !== false, 'no vc_ shortcode remains' );
} );

ei_test( 'widget shortcodes are removed entirely, including their bodies', function () {
	ei_tags();

	$dirty = '[rev_slider alias="home"][/rev_slider]<p>Keep</p>[vc_gmaps link="#E-8_base64blob"][/vc_gmaps][vc_raw_html]JTND;base64[/vc_raw_html][contact-form-7 id="4"]';

	$clean = ei_clean_content( $dirty );

	ei_assert_contains( 'Keep', $clean, 'real content stays' );
	ei_assert_false( strpos( $clean, 'rev_slider' ) !== false, 'slider gone' );
	ei_assert_false( strpos( $clean, 'base64' ) !== false, 'base64 blob gone' );
	ei_assert_false( strpos( $clean, 'contact-form-7' ) !== false, 'form shortcode gone' );
	ei_assert_false( strpos( $clean, 'vc_gmaps' ) !== false, 'map gone' );
} );

ei_test( 'attribute-only widgets like heading and contactbox are dropped whole', function () {
	ei_tags();

	$dirty = '[heading text="Leave Us a Message"][contactboxphone phone="+91-172" fax="+91-172"]<p>Intro text</p>';

	$clean = ei_clean_content( $dirty );

	ei_assert_contains( 'Intro text', $clean, 'prose stays' );
	ei_assert_false( strpos( $clean, 'Leave Us a Message' ) !== false, 'heading widget dropped' );
	ei_assert_false( strpos( $clean, 'contactboxphone' ) !== false, 'contact box dropped' );
} );

ei_test( 'empty paragraphs left behind after stripping are removed', function () {
	ei_tags();

	$dirty = '[vc_row][vc_column]<p></p>[/vc_column][/vc_row]<p>&nbsp;</p><p>text</p>';

	ei_assert_contains( '<p>text</p>', ei_clean_content( $dirty ), 'text paragraph kept' );
	ei_assert_false( strpos( ei_clean_content( $dirty ), '<p></p>' ) !== false, 'empty paragraph gone' );
	ei_assert_false( strpos( ei_clean_content( $dirty ), '&nbsp;</p>' ) !== false, 'nbsp-only paragraph gone' );
} );
