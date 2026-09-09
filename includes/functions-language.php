<?php
/*
	language functions
*/

/**
 * Declare the Namespace.
 */
namespace azurecurve\ShortcodesInWidgets;

/**
 * Prevent direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Load plugin translations.
 */
function load_languages() {
	load_plugin_textdomain( 'azrcrv-siw', false, dirname( plugin_basename( PLUGIN_FILE ) ) . '/assets/languages/' );
}
