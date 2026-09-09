<?php
/*
	settings tab - lets the admin choose which widget areas get shortcode
	support (PRD s6). Only 'enable_widget_text' is on by default, so
	upgrading from a pre-2.0.0 install changes no visible behaviour until
	these are changed here.
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

$settings = get_settings();
?>

<p><?php esc_html_e( 'Choose which widget areas should have shortcodes expanded. Changes apply immediately after saving.', 'azrcrv-siw' ); ?></p>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">

	<input type="hidden" name="action" value="<?php echo esc_attr( PLUGIN_UNDERSCORE ); ?>_save_settings" />
	<?php wp_nonce_field( PLUGIN_HYPHEN . '-save-settings', PLUGIN_HYPHEN . '-nonce' ); ?>

	<table class="form-table azrcrv-siw-settings" role="presentation">
		<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'Text Widget (legacy/classic mode)', 'azrcrv-siw' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="enable_widget_text" value="1" <?php checked( $settings['enable_widget_text'], 1 ); ?> />
						<?php esc_html_e( 'Expand shortcodes in the classic Text widget, and in any third-party widget that applies the "widget_text" filter.', 'azrcrv-siw' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Text Widget (visual/block mode)', 'azrcrv-siw' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="enable_widget_text_content" value="1" <?php checked( $settings['enable_widget_text_content'], 1 ); ?> />
						<?php esc_html_e( 'Expand shortcodes in the visual-mode/block-based Text widget.', 'azrcrv-siw' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'ClassicPress/WordPress already does this for the built-in visual Text widget by default, and safely avoids double-processing if both this and the option above are enabled - this option mainly helps third-party widgets. See the Instructions tab for details.', 'azrcrv-siw' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Custom HTML Widget', 'azrcrv-siw' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="enable_widget_custom_html" value="1" <?php checked( $settings['enable_widget_custom_html'], 1 ); ?> />
						<?php esc_html_e( 'Expand shortcodes in the Custom HTML widget.', 'azrcrv-siw' ); ?>
					</label>
					<p class="description"><?php esc_html_e( 'Only enable this if you understand your Custom HTML widgets - shortcode processing runs across the whole widget content, including any inline <script> blocks, so a stray "[" in JavaScript could be misinterpreted as a shortcode.', 'azrcrv-siw' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Widget Titles', 'azrcrv-siw' ); ?></th>
				<td>
					<label>
						<input type="checkbox" name="enable_widget_title" value="1" <?php checked( $settings['enable_widget_title'], 1 ); ?> />
						<?php esc_html_e( 'Expand shortcodes in widget titles.', 'azrcrv-siw' ); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>

	<?php submit_button( __( 'Save Settings', 'azrcrv-siw' ) ); ?>
</form>
