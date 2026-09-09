<?php
/**
 * Setup registration activation hook, actions, filters and shortcodes.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

// add actions.
add_action( 'admin_menu', __NAMESPACE__ . '\\create_admin_menu' );
add_action( 'init', __NAMESPACE__ . '\\create_cust_taxonomy_for_custom_post' );
add_action( 'init', __NAMESPACE__ . '\\create_custom_post_type' );
add_action( 'admin_menu', __NAMESPACE__ . '\\add_sidebar_metabox' );
add_action( 'save_post', __NAMESPACE__ . '\\save_sidebar_metabox', 10, 1 );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_languages' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_frontend_styles' );
add_action( 'widgets_init', __NAMESPACE__ . '\\create_widget' );
add_action( 'current_screen', __NAMESPACE__ . '\\current_screen_callback' );
add_action( 'admin_post_' . PLUGIN_UNDERSCORE . '_save_options', __NAMESPACE__ . '\\save_options' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_styles' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_styles' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_scripts' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );

// add filters.
add_filter( 'plugin_action_links', __NAMESPACE__ . '\\add_plugin_action_link', 10, 2 );
$plugin_slug_for_um = plugin_basename( trim( PLUGIN_FILE ) );
add_filter( 'codepotent_update_manager_' . $plugin_slug_for_um . '_image_path', __NAMESPACE__ . '\\custom_image_path' );
add_filter( 'codepotent_update_manager_' . $plugin_slug_for_um . '_image_url', __NAMESPACE__ . '\\custom_image_url' );
