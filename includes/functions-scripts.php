<?php
/*
	admin script/style enqueue functions - only loaded on this plugin's own
	admin screen.
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
 * Enqueue admin CSS/JS, only on this plugin's own admin page or the shared
 * azurecurve cross-plugin menu page (both of which can render the
 * azrcrv-ui-tabs component and the plugin-index grid).
 */
function enqueue_admin_assets( $hook ) {

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only page identifier, not a state-changing action.
	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

	if ( PLUGIN_HYPHEN !== $page && 'azrcrv-plugin-menu' !== $page ) {
		return;
	}

	wp_enqueue_style(
		PLUGIN_HYPHEN . '-admin-standard',
		plugins_url( 'assets/css/admin-standard.css', PLUGIN_FILE ),
		array(),
		'2.0.0'
	);

	wp_enqueue_style(
		PLUGIN_HYPHEN . '-admin-pluginmenu',
		plugins_url( 'assets/css/admin-pluginmenu.css', PLUGIN_FILE ),
		array(),
		'2.0.0'
	);

	wp_enqueue_style(
		PLUGIN_HYPHEN . '-admin',
		plugins_url( 'assets/css/admin.css', PLUGIN_FILE ),
		array( PLUGIN_HYPHEN . '-admin-standard' ),
		'2.0.0'
	);

	wp_enqueue_script(
		PLUGIN_HYPHEN . '-admin-standard',
		plugins_url( 'assets/js/admin-standard.js', PLUGIN_FILE ),
		array(),
		'2.0.0',
		true
	);

	wp_enqueue_script(
		PLUGIN_HYPHEN . '-admin',
		plugins_url( 'assets/js/admin.js', PLUGIN_FILE ),
		array( PLUGIN_HYPHEN . '-admin-standard' ),
		'2.0.0',
		true
	);
}
