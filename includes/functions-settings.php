<?php
/**
 * Settings functions.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

/**
 * Get options including defaults.
 *
 * @since 1.1.0
 */
function get_option_with_defaults( $option_name ) {

	$defaults = array(
		'widget'          => array(
			'width'  => 300,
			'height' => 300,
		),
		'to-twitter'      => array(
			'integrate'          => 0,
			'tweet'              => 0,
			'retweet'            => 0,
			'retweet-prefix'     => 'ICYMI:',
			'tweet-format'       => '%t %h',
			'tweet-time'         => '10:00',
			'retweet-time'       => '16:00',
			'use-featured-image' => 1,
		),
		'toggle-showhide' => array(
			'integrate' => 0,
		),
	);

	$options = get_option( $option_name, $defaults );

	$options = recursive_parse_args( $options, $defaults );

	return $options;
}

/**
 * Recursively parse options to merge with defaults.
 *
 * @since 1.1.0
 */
function recursive_parse_args( $args, $defaults ) {
	$new_args = (array) $defaults;

	foreach ( $args as $key => $value ) {
		if ( is_array( $value ) && isset( $new_args[ $key ] ) ) {
			$new_args[ $key ] = recursive_parse_args( $value, $new_args[ $key ] );
		} else {
			$new_args[ $key ] = $value;
		}
	}

	return $new_args;
}

/**
 * Display Settings page.
 *
 * @since 1.0.0
 */
function display_options() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'azrcrv-wa' ) );
	}

	// Retrieve plugin configuration options from database.
	$options = get_option_with_defaults( PLUGIN_HYPHEN );

	echo '<div id="' . esc_attr( PLUGIN_HYPHEN ) . '-general" class="wrap">';

		echo '<h1>';
			echo '<a href="' . esc_url( DEVELOPER_RAW_LINK . PLUGIN_SHORT_SLUG . '/' ) . '"><img src="' . esc_url( plugins_url( '../assets/images/logo.svg', __FILE__ ) ) . '" style="padding-right: 6px; height: 20px; width: 20px;" alt="azurecurve" /></a>';
			echo esc_html( get_admin_page_title() );
		echo '</h1>';

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['settings-updated'] ) ) {
		echo '<div class="notice notice-success is-dismissible">
				<p><strong>' . esc_html__( 'Settings have been saved.', 'azrcrv-wa' ) . '</strong></p>
			</div>';
	}

		require_once 'tab-settings.php';
		require_once 'tab-instructions.php';
		require_once 'tab-other-plugins.php';
		require_once 'tabs-output.php';
	?>

	</div>
	<?php
}

/**
 * Save settings.
 *
 * @since 1.0.0
 */
function save_options() {
	// Check that user has proper security level.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permissions to perform this action', 'azrcrv-wa' ) );
	}
	// Check that nonce field created in configuration form is present.
	if ( ! empty( $_POST ) && check_admin_referer( PLUGIN_HYPHEN, PLUGIN_HYPHEN . '-nonce' ) ) {

		// Retrieve original plugin options array.
		$options          = get_option_with_defaults( PLUGIN_HYPHEN );
		$original_options = $options;

		$option_name = 'widget-width';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['widget']['width'] = (int) sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'widget-height';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['widget']['height'] = (int) sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'to-twitter-integration';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['to-twitter']['integrate'] = 1;
		} else {
			$options['to-twitter']['integrate'] = 0;
		}

		$option_name = 'to-twitter-tweet';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['to-twitter']['tweet'] = 1;
		} else {
			$options['to-twitter']['tweet'] = 0;
		}

		$option_name = 'to-twitter-tweet-time';
		if ( isset( $_POST[ $option_name ] ) ) {
			$tweet_time                        = preg_replace( '([^0-9-:-])', '', wp_unslash( $_POST[ $option_name ] ) );
			$options['to-twitter']['tweet-time'] = sanitize_text_field( $tweet_time );
		}

		$option_name = 'to-twitter-retweet';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['to-twitter']['retweet'] = 1;
		} else {
			$options['to-twitter']['retweet'] = 0;
		}

		$option_name = 'to-twitter-retweet-time';
		if ( isset( $_POST[ $option_name ] ) ) {
			$retweet_time                          = preg_replace( '([^0-9-:-])', '', wp_unslash( $_POST[ $option_name ] ) );
			$options['to-twitter']['retweet-time'] = sanitize_text_field( $retweet_time );
		}

		$option_name = 'to-twitter-retweet-prefix';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['to-twitter']['retweet-prefix'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'to-twitter-tweet-format';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['to-twitter']['tweet-format'] = sanitize_text_field( wp_unslash( $_POST[ $option_name ] ) );
		}

		$option_name = 'to-twitter-use-featured-image';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['to-twitter']['use-featured-image'] = 1;
		} else {
			$options['to-twitter']['use-featured-image'] = 0;
		}

		$option_name = 'toggle-showhide-integration';
		if ( isset( $_POST[ $option_name ] ) ) {
			$options['toggle-showhide']['integrate'] = 1;
		} else {
			$options['toggle-showhide']['integrate'] = 0;
		}

		// Store updated options array to database.
		update_option( PLUGIN_HYPHEN, $options );

		// Schedule or clear cron based on To Twitter integration.
		if ( 1 === $options['to-twitter']['integrate'] ) {
			wp_schedule_event( strtotime( '00:01:00' ), 'hourly', 'azrcrv_wa_cron_hourly_check' );
		} else {
			wp_clear_scheduled_hook( 'azrcrv_wa_cron_hourly_check' );
		}

		$response = '';
		if ( 0 === (int) $original_options['to-twitter']['integrate'] && 1 === (int) $options['to-twitter']['integrate'] ) {
			$response = '&i';
		}

		// Redirect the page to the configuration form that was processed.
		wp_safe_redirect( add_query_arg( 'page', PLUGIN_HYPHEN . '&settings-updated' . $response, admin_url( 'admin.php' ) ) );
		exit;
	}
}
