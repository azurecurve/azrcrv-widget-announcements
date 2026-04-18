<?php
/**
 * Setup registration activation hook, actions, filters and shortcodes.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Register activation hook.
 */
register_deactivation_hook( PLUGIN_FILE, __NAMESPACE__ . '\\clear_cron_hourly' );

// add actions.
add_action( 'admin_menu', __NAMESPACE__ . '\\create_admin_menu' );
add_action( 'init', __NAMESPACE__ . '\\create_cust_taxonomy_for_custom_post' );
add_action( 'init', __NAMESPACE__ . '\\create_custom_post_type' );
add_action( 'add_meta_boxes', __NAMESPACE__ . '\\create_tweet_metabox' );
add_action( 'save_post', __NAMESPACE__ . '\\save_tweet_metabox', 11, 2 );
add_action( 'add_meta_boxes', __NAMESPACE__ . '\\create_tweet_history_metabox' );
add_action( 'admin_menu', __NAMESPACE__ . '\\add_sidebar_metabox' );
add_action( 'save_post', __NAMESPACE__ . '\\save_sidebar_metabox', 10, 1 );
add_action( 'admin_menu', __NAMESPACE__ . '\\add_to_twitter_sidebar_metabox' );
add_action( 'save_post', __NAMESPACE__ . '\\save_to_twitter_sidebar_metabox', 10, 1 );
add_action( 'wp_insert_post', __NAMESPACE__ . '\\check_tweet', 12, 2 );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_languages' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_frontend_styles' );
add_action( 'widgets_init', __NAMESPACE__ . '\\create_widget' );
add_action( 'current_screen', __NAMESPACE__ . '\\current_screen_callback' );
add_action( 'admin_post_' . PLUGIN_UNDERSCORE . '_save_options', __NAMESPACE__ . '\\save_options' );
add_action( 'azrcrv_wa_cron_hourly_check', __NAMESPACE__ . '\\perform_cron_check' );
add_action( 'azrcrv_wa_cron_tweet_announcement', __NAMESPACE__ . '\\perform_tweet_announcement', 10, 2 );
add_action( 'transition_post_status', __NAMESPACE__ . '\\post_status_transition', 13, 3 );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_styles' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_styles' );
add_action( 'admin_init', __NAMESPACE__ . '\\register_admin_scripts' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_media_uploader' );

// add filters.
add_filter( 'plugin_action_links', __NAMESPACE__ . '\\add_plugin_action_link', 10, 2 );
$plugin_slug_for_um = plugin_basename( trim( PLUGIN_FILE ) );
add_filter( 'codepotent_update_manager_' . $plugin_slug_for_um . '_image_path', __NAMESPACE__ . '\\custom_image_path' );
add_filter( 'codepotent_update_manager_' . $plugin_slug_for_um . '_image_url', __NAMESPACE__ . '\\custom_image_url' );
