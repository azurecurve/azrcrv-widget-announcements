<?php
/*
	settings functions - the widget-area shortcode-support toggles are
	stored as a single option (see PRD s6), shaped as:

	array(
		'enable_widget_text'         => 0|1,
		'enable_widget_text_content' => 0|1,
		'enable_widget_custom_html'  => 0|1,
		'enable_widget_title'        => 0|1,
	)
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
 * Render the admin page (Settings/Instructions/Other Plugins, in tabs).
 */
function display_admin_page() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'azrcrv-siw' ) );
	}

	echo '<div class="wrap ' . esc_attr( PLUGIN_HYPHEN ) . '-wrap">';
	echo '<h1>';
		echo '<a href="' . esc_url_raw( DEVELOPER_RAW_LINK ) . esc_attr( PLUGIN_SHORT_SLUG ) . '/"><img src="' . esc_url_raw( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-right: 6px; height: 20px; width: 20px;" alt="' . esc_attr( DEVELOPER_NAME ) . '" /></a>';
		echo esc_html( get_admin_page_title() );
	echo '</h1>';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only status flag, not a state-changing action.
	if ( isset( $_GET['azrcrv-siw-message'] ) ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$message_key = sanitize_key( wp_unslash( $_GET['azrcrv-siw-message'] ) );
		render_admin_notice( $message_key );
	}

	require_once __DIR__ . '/tabs-output.php';

	echo '</div>';
}

/**
 * Show a dismissible admin notice for a given message key, set via a
 * redirect query arg after a save action.
 */
function render_admin_notice( $message_key ) {

	$messages = array(
		'settings-saved' => array( 'success', __( 'Settings saved.', 'azrcrv-siw' ) ),
		'invalid-nonce'  => array( 'error', __( 'Security check failed - please try again.', 'azrcrv-siw' ) ),
	);

	if ( ! isset( $messages[ $message_key ] ) ) {
		return;
	}

	list( $type, $text ) = $messages[ $message_key ];
	$css_class            = 'success' === $type ? 'notice-success' : 'notice-error';

	echo '<div class="notice ' . esc_attr( $css_class ) . ' is-dismissible"><p>' . esc_html( $text ) . '</p></div>';
}

/**
 * Build a redirect URL back to the admin page with a status message.
 */
function redirect_with_message( $message_key, $extra_args = array() ) {
	$args = array_merge(
		array(
			'page'               => PLUGIN_HYPHEN,
			'azrcrv-siw-message' => $message_key,
		),
		$extra_args
	);
	wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
	exit;
}


/**
 * The plugin's built-in default values. Only 'enable_widget_text' starts on,
 * so that upgrading from a pre-2.0.0 install (which only ever hooked
 * 'widget_text') changes no visible behaviour until the admin opts into the
 * newer toggles on the Settings tab.
 */
function get_builtin_settings() {
	return array(
		'enable_widget_text'         => 1,
		'enable_widget_text_content' => 0,
		'enable_widget_custom_html'  => 0,
		'enable_widget_title'        => 0,
	);
}

/**
 * Get the saved settings, merged over the built-in defaults so every key is
 * always present even for a fresh install or an option saved by an older
 * version of the plugin.
 */
function get_settings() {
	$stored = get_option( SETTINGS_OPTION_NAME, array() );

	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	return wp_parse_args( $stored, get_builtin_settings() );
}

/**
 * Persist the settings.
 */
function save_settings( $settings ) {
	update_option( SETTINGS_OPTION_NAME, $settings, false );
}

/**
 * Build a sanitized settings array from a submitted settings form ($_POST).
 * Each field is a checkbox, so absence in $post_data means unchecked, not
 * "leave as-is" - all four are always set explicitly from the form.
 */
function sanitize_settings_from_post( $post_data ) {
	return array(
		'enable_widget_text'         => isset( $post_data['enable_widget_text'] ) ? 1 : 0,
		'enable_widget_text_content' => isset( $post_data['enable_widget_text_content'] ) ? 1 : 0,
		'enable_widget_custom_html'  => isset( $post_data['enable_widget_custom_html'] ) ? 1 : 0,
		'enable_widget_title'        => isset( $post_data['enable_widget_title'] ) ? 1 : 0,
	);
}

/**
 * Handle the "Save Settings" form on the Settings tab.
 */
function handle_save_settings() {

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action.', 'azrcrv-siw' ) );
	}

	if ( ! isset( $_POST[ PLUGIN_HYPHEN . '-nonce' ] ) || ! check_admin_referer( PLUGIN_HYPHEN . '-save-settings', PLUGIN_HYPHEN . '-nonce' ) ) {
		redirect_with_message( 'invalid-nonce' );
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce already verified above.
	$settings = sanitize_settings_from_post( wp_unslash( $_POST ) );

	save_settings( $settings );

	redirect_with_message( 'settings-saved' );
}
add_action( 'admin_post_' . PLUGIN_UNDERSCORE . '_save_settings', __NAMESPACE__ . '\\handle_save_settings' );
