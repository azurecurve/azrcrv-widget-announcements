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
	wp_register_script( 'azrcrv-admin-standard-js', esc_url_raw( plugins_url( '../assets/js/admin-standard.js', __FILE__ ) ), array(), '26.6.8', true );
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
		wp_enqueue_script( 'azrcrv-admin-standard-js' );
	}
}
