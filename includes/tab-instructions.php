<?php
/**
 * Instructions tab content.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

$tab_instructions_label = esc_html__( 'Instructions', 'azrcrv-wa' );

ob_start();
?>
<table class="form-table azrcrv-settings">

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Widget Announcements Usage', 'azrcrv-wa' ); ?></h2>
		</th>
	</tr>

	<tr>
		<td scope="row" colspan="2">
			<p><?php esc_html_e( 'Widget Announcements allows you to add a widget which announces holidays, events, achievements and notable historical figures.', 'azrcrv-wa' ); ?></p>
			<p><?php esc_html_e( 'Announcements are created as a custom post type and can have details, an image and additional text after the image; images should be narrower than your widget area.', 'azrcrv-wa' ); ?></p>
			<p><?php esc_html_e( 'Announcements can be set to display:', 'azrcrv-wa' ); ?></p>
			<ul>
				<li><?php esc_html_e( 'One off', 'azrcrv-wa' ); ?></li>
				<li><?php esc_html_e( 'Monthly', 'azrcrv-wa' ); ?></li>
				<li><?php esc_html_e( 'Annually', 'azrcrv-wa' ); ?></li>
				<li><?php esc_html_e( 'Good Friday', 'azrcrv-wa' ); ?></li>
				<li><?php esc_html_e( 'Easter Sunday', 'azrcrv-wa' ); ?></li>
				<li><?php esc_html_e( 'Easter Monday', 'azrcrv-wa' ); ?></li>
				<li><?php esc_html_e( 'Monthly on the nth day (e.g. 2nd Wednesday)', 'azrcrv-wa' ); ?></li>
				<li><?php esc_html_e( 'Annually on the nth day of the month (e.g. 4th Thursday November)', 'azrcrv-wa' ); ?></li>
			</ul>
			<p><?php esc_html_e( 'When creating announcements they can be added to one or more categories; when adding a widget, select the category to include.', 'azrcrv-wa' ); ?></p>
		</td>
	</tr>

</table>
<?php
$tab_instructions = ob_get_clean();
