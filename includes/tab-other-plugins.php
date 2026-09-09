<?php
/*
	other plugins tab on settings page
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
 * Other Plugins tab.
 */
$plugin_array = get_option( 'azrcrv-plugin-menu' );

$plugin_list = '';
if ( is_array( $plugin_array ) ) {
	foreach ( $plugin_array as $plugin_name => $plugin_details ) {
		if ( $plugin_details['retired'] == 0 ) {
			$alternative_color = '';
			if ( isset( $plugin_details['bright'] ) and $plugin_details['bright'] == 1 ) {
				$alternative_color = 'bright-';
			}
			if ( isset( $plugin_details['premium'] ) and $plugin_details['premium'] == 1 ) {
				$alternative_color = 'premium-';
			}
			if ( ! is_plugin_active( $plugin_details['plugin_link'] ) ) {
				$plugin_list .= "<a href='{$plugin_details['dev_URL']}' class='azrcrv-{$alternative_color}plugin-index'>{$plugin_name}</a>";
			}
		}
	}
}

$tab_plugins_label = esc_html__( 'Other Plugins', 'azrcrv-siw' );
$tab_plugins       = '
<table class="form-table azrcrv-siw-settings">

	<tr>

		<td scope="row" colspan=2>

			<p>' .
				sprintf( esc_html__( '%1$s was one of the first plugin developers to start developing for ClassicPress; all plugins are available from %2$s and are integrated with the %3$s plugin for fully integrated, no hassle, updates.', 'azrcrv-siw' ), '<strong>' . DEVELOPER_SHORTNAME . '</strong>', DEVELOPER_LINK, '<a href="https://directory.classicpress.net/plugins/update-manager/">Update Manager</a>' )
			. '</p>
			<p>' .
				sprintf( esc_html__( 'Other plugins available from %s, which you are not using, are:', 'azrcrv-siw' ), '<strong>' . DEVELOPER_NAME . '</strong>' )
			. '</p>

		</td>

	</tr>

	<tr>

		<td scope="row" colspan=2>

			' . $plugin_list . '

		</td>

	</tr>

</table>';
