<?php
/**
 * Simple W3B Template
 * 
 * This is a very simple w3bsite template engine with limited helpers and a crude CMS.
 * Our aim is to create a template engine that does not use any database, to be useful 
 * for CMS sites but has no sql storage in their hosting package.
 * 
 * - the site does not use a database, it all stored in an encrypted flat file.
 * 
 * @author Earl Evan Amante <earl.amante@w3bkit.com>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2015 W3Bkit
 *
	 * @version 1.3
	 */
	 
	 /**
	  * This will include then load the main class of the template engine.
	  */
	require_once('lib/w3b.class.php');
	
	/**
	 * This function will prepare and print the output of the template on screen.
	 */
	$w3b->run();
?>