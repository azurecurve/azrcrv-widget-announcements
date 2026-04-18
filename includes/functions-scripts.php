<?php
/**
 * Script functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Register admin scripts.
 *
 * @since 1.6.0
 */
function register_admin_scripts() {
	wp_register_script( PLUGIN_HYPHEN . '-admin-jquery', esc_url_raw( plugins_url( '../assets/jquery/admin.js', __FILE__ ) ), array( 'jquery' ), '1.0.0', true );
	wp_register_script( 'azrcrv-admin-standard-jquery', esc_url_raw( plugins_url( '../assets/jquery/admin-standard.js', __FILE__ ) ), array(), '22.3.2', true );
}

/**
 * Enqueue admin scripts.
 *
 * @since 1.6.0
 */
function enqueue_admin_scripts() {
	global $pagenow;

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['page'] ) && ( $_GET['page'] === PLUGIN_HYPHEN || $_GET['page'] === 'azrcrv-plugin-menu' ) || $pagenow === 'profile.php' || $pagenow === 'edit-user.php' ) {
		wp_enqueue_script( PLUGIN_HYPHEN . '-admin-jquery' );
		wp_enqueue_script( 'azrcrv-admin-standard-jquery' );
	}
}

/**
 * Enqueue media uploader.
 *
 * @since 1.0.0
 */
function enqueue_media_uploader() {
	global $pagenow;

	if ( isset( $_GET['page'] ) && $_GET['page'] === PLUGIN_HYPHEN ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		} else {
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_style( 'thickbox' );
		}
	}
}
