<?php
/*
	setup - registration of activation/deactivation hooks, actions and
	filters. The admin_post_azrcrv_siw_save_settings handler is registered
	directly in functions-settings.php, alongside the functions it calls.
	The widget-area shortcode filters are registered in
	functions-shortcodes.php, on 'widgets_init', so they aren't repeated
	here either.
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

// Activation / deactivation.
register_activation_hook( PLUGIN_FILE, __NAMESPACE__ . '\\activate_plugin' );
register_deactivation_hook( PLUGIN_FILE, __NAMESPACE__ . '\\deactivate_plugin' );

// Admin menu.
add_action( 'admin_menu', __NAMESPACE__ . '\\create_admin_menu' );
add_filter( 'plugin_action_links_' . plugin_basename( PLUGIN_FILE ), __NAMESPACE__ . '\\add_plugin_action_link', 10, 2 );

// Update Manager: tell it where to find this plugin's own icon/banner
// images (assets/images) rather than falling back to a generic default.
$plugin_slug_for_um = plugin_basename( trim( PLUGIN_FILE ) );
add_filter( 'codepotent_update_manager_' . $plugin_slug_for_um . '_image_path', __NAMESPACE__ . '\\custom_image_path' );
add_filter( 'codepotent_update_manager_' . $plugin_slug_for_um . '_image_url', __NAMESPACE__ . '\\custom_image_url' );

// Admin assets.
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_assets' );

// Language.
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_languages' );
