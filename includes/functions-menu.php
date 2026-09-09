<?php
/*
	menu functions - admin menu registration and page rendering. The
	admin-post save handler for the Settings tab lives in
	functions-settings.php, alongside the functions it calls.
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
 * Add settings link on the Plugins list page.
 */
function add_plugin_action_link( $links, $file ) {

	$this_plugin = PLUGIN_SLUG . '/' . PLUGIN_SLUG . '.php';

	if ( $file === $this_plugin ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=' . PLUGIN_HYPHEN ) ) . '"><img src="' . esc_url( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />' . esc_html__( 'Settings', 'azrcrv-siw' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}

/**
 * Add the top-level admin menu page, and a matching entry under the shared
 * azurecurve cross-plugin menu (registered in azurecurve-menu-display.php)
 * so this plugin is reachable both on its own and alongside the rest of the
 * azurecurve plugin family.
 */
function create_admin_menu() {

	add_submenu_page(
		'azrcrv-plugin-menu',
		esc_html__( 'Shortcodes in Widgets Settings', 'azrcrv-siw' ),
		esc_html__( 'Shortcodes in Widgets', 'azrcrv-siw' ),
		'manage_options',
		PLUGIN_HYPHEN,
		__NAMESPACE__ . '\\display_admin_page'
	);
}