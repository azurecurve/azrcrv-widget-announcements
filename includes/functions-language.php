<?php
/**
 * Language functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Load language files.
 *
 * @since 1.0.0
 */
function load_languages() {
	$plugin_rel_path = basename( dirname( PLUGIN_FILE ) ) . '../assets/languages';
	load_plugin_textdomain( 'azrcrv-wa', false, $plugin_rel_path );
}
