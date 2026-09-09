<?php
/*
	activation / deactivation functions
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
 * Plugin activation. This plugin has no database table and no cron - the
 * settings option (see functions-settings.php) is created lazily on first
 * read via wp_parse_args() against the built-in defaults, so there is
 * nothing to set up here beyond a rewrite-safe flush. Kept as a real
 * function (rather than skipping the activation hook entirely) so future
 * setup steps have somewhere to go without a structural change.
 */
function activate_plugin() {
	// Nothing to do yet.
}

/**
 * Plugin deactivation. Deliberately does NOT delete the settings option -
 * deactivating a plugin should not lose the admin's configuration; that only
 * happens on uninstall (see uninstall.php).
 */
function deactivate_plugin() {
	// Nothing to do yet.
}
