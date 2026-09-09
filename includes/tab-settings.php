<?php
/**
 * Settings tab content.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

$toggle_showhide_enabled = is_plugin_active( 'azrcrv-toggle-showhide/azrcrv-toggle-showhide.php' );

$tab_settings_label = PLUGIN_NAME . ' ' . esc_html__( 'Settings', 'azrcrv-wa' );

ob_start();
?>
<table class="form-table azrcrv-settings">

	<tr>
		<th scope="row" colspan="2">
			<label for="explanation">
				<?php echo esc_html( PLUGIN_NAME . ' ' . __( 'allows you to add a widget which can be used to announce holidays, events, achievements and notable historical figures in a widget.', 'azrcrv-wa' ) ); ?>
			</label>
		</th>
	</tr>

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Widget Defaults', 'azrcrv-wa' ); ?></h2>
		</th>
	</tr>

	<tr>
		<th scope="row">
			<label for="widget-width"><?php esc_html_e( 'Width', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<input name="widget-width" type="number" min="1" id="widget-width" value="<?php echo esc_attr( $options['widget']['width'] ); ?>" class="small-text" /> px
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="widget-height"><?php esc_html_e( 'Height', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<input name="widget-height" type="number" min="1" id="widget-height" value="<?php echo esc_attr( $options['widget']['height'] ); ?>" class="small-text" /> px
		</td>
	</tr>

	<tr>
		<th scope="row" colspan="2" class="azrcrv-settings-section-heading">
			<h2 class="azrcrv-settings-section-heading"><?php esc_html_e( 'Integration', 'azrcrv-wa' ); ?></h2>
		</th>
	</tr>

	<tr>
		<th scope="row">
			<label for="toggle-showhide-integration"><?php esc_html_e( 'Enable Toggle Show/Hide', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<?php if ( $toggle_showhide_enabled ) { ?>
				<label for="toggle-showhide-integration">
					<input name="toggle-showhide-integration" type="checkbox" id="toggle-showhide-integration" value="1" <?php checked( '1', $options['toggle-showhide']['integrate'] ); ?> />
					<?php
					printf(
						/* translators: 1: plugin link, 2: developer link */
						esc_html__( 'Enable integration with %1$s from %2$s?', 'azrcrv-wa' ),
						'<a href="' . esc_url( admin_url( 'admin.php?page=azrcrv-tsh' ) ) . '">Toggle Show/Hide</a>',
						DEVELOPER_LINK
					);
					?>
				</label>
			<?php } else { ?>
				<?php
				printf(
					/* translators: 1: plugin link, 2: developer link */
					esc_html__( '%1$s from %2$s not installed/activated.', 'azrcrv-wa' ),
					'<a href="https://development.azurecurve.co.uk/classicpress-plugins/toggle-showhide/">Toggle Show/Hide</a>',
					DEVELOPER_LINK
				);
				?>
			<?php } ?>
		</td>
	</tr>

</table>
<?php
$tab_settings = ob_get_clean();
