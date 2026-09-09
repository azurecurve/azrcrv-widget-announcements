<?php

// Check that code was called from ClassicPress with uninstallation constant declared.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Hard-coded rather than referencing the plugin's constants: ClassicPress
// calls uninstall.php standalone, without loading the main plugin file
// first, so those constants are never defined here.
$options = array( 'azrcrv-siw' );

/**
 * Delete the plugin's own options for the current site.
 *
 * This plugin has no database table and no cron event, so there is nothing
 * else to clean up. Deliberately does NOT touch any widget content that
 * contains shortcodes - uninstalling should never rewrite site content.
 */
function azrcrv_siw_uninstall_cleanup( $options ) {
	foreach ( $options as $option ) {
		delete_option( $option );
	}
}

// Remove from single site.
if ( ! is_multisite() ) {

	azrcrv_siw_uninstall_cleanup( $options );

	// Remove from every site on a multisite network.
} else {
	global $wpdb;

	$site_ids         = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	$original_site_id = get_current_blog_id();

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( $site_id );

		azrcrv_siw_uninstall_cleanup( $options );
	}

	switch_to_blog( $original_site_id );

	foreach ( $options as $option ) {
		delete_site_option( $option );
	}
}
