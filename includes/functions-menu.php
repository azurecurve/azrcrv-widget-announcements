<?php
/**
 * Menu functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Add action link on plugins page.
 *
 * @since 1.0.0
 */
function add_plugin_action_link( $links, $file ) {

	$this_plugin = PLUGIN_SLUG . '/' . PLUGIN_SLUG . '.php';

	if ( $file === $this_plugin ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=' . PLUGIN_HYPHEN ) ) . '"><img src="' . esc_url( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-top: 2px; margin-right: -5px; height: 16px; width: 16px;" alt="azurecurve" />' . esc_html__( 'Settings', 'azrcrv-wa' ) . '</a>';
		array_unshift( $links, $settings_link );
	}

	return $links;
}

/**
 * Add to menu.
 *
 * @since 1.0.0
 */
function create_admin_menu() {

	// Add settings to announcements CPT submenu.
	add_submenu_page(
		'edit.php?post_type=widget-announcement',
		PLUGIN_NAME . ' ' . esc_html__( 'Settings', 'azrcrv-wa' ),
		esc_html__( 'Settings', 'azrcrv-wa' ),
		'manage_options',
		PLUGIN_HYPHEN,
		__NAMESPACE__ . '\\display_options'
	);

	// Add settings to azurecurve menu.
	add_submenu_page(
		'azrcrv-plugin-menu',
		PLUGIN_NAME . ' ' . esc_html__( 'Settings', 'azrcrv-wa' ),
		PLUGIN_NAME,
		'manage_options',
		PLUGIN_HYPHEN,
		__NAMESPACE__ . '\\display_options'
	);
}
