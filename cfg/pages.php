<?php
/**
 * This array stores the pages that will be used for the website.
 * The array must follow a certain format that will be demonstrated below.
 *
 * Reserved page name: 'index' this is homepage.
 *
 * Accepted Field Types:
 * - text 		= text field
 * - textarea 	= text area
 * - password	= password field
 * - wysiwyg	= WYSIWYG editor (tinyMCE)
 *
 * For single pages:
 * 'PAGE_NAME' => array(
 *		'FIELD_NAME'	=> 'FIELD_TYPE',
 *		'FIELD_NAME2'	=> 'FIELD_TYPE',
 *		'FIELD_NAME3'	=> 'FIELD_TYPE',
 * )
 *
 * For pages with subpages:
 * 'PAGE_NAME' => array(
 *		'FIELD_NAME'	=> 'FIELD_TYPE',
 *
 *		'subpage'		=> array(
 *			'FIELD_NAME'	=> 'FIELD_TYPE'
 *		)
 * )
 *
 * @since Version 1.3
 */
	$pages = array(
		'index'		=> array(
			'title'		=> 'text',
			'content'	=> 'wysiwyg'
		)
	);
?>