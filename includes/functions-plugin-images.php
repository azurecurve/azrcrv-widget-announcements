<?php
/**
 * Plugin image functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Custom plugin image path.
 *
 * @since 1.6.0
 */
function custom_image_path( $path ) {
	return esc_url_raw( plugin_dir_url( PLUGIN_FILE ) . 'assets/images' );
}

/**
 * Custom plugin image url.
 *
 * @since 1.6.0
 */
function custom_image_url( $url ) {
	return esc_url_raw( plugin_dir_url( PLUGIN_FILE ) . 'assets/images' );
}
