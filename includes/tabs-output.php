<?php
/*
	tab output on the admin page - markup/classes match the shared
	azrcrv-ui-tabs component used across azurecurve's plugins (see
	assets/css/admin-standard.css and assets/js/admin-standard.js), rather
	than ClassicPress's native nav-tab-wrapper.
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

$tab_settings_label     = esc_html__( 'Settings', 'azrcrv-siw' );
$tab_instructions_label = esc_html__( 'Instructions', 'azrcrv-siw' );

ob_start();
require_once __DIR__ . '/tab-settings.php';
$tab_settings = ob_get_clean();

ob_start();
require_once __DIR__ . '/tab-instructions.php';
$tab_instructions = ob_get_clean();

// tab-other-plugins.php sets $tab_plugins_label and $tab_plugins directly
// (matching the shared pattern used across azurecurve's other plugins)
// rather than being captured via ob_start(), since it builds its output as
// a string rather than echoing it.
require_once __DIR__ . '/tab-other-plugins.php';
?>

<div id="tabs" class="azrcrv-ui-tabs">
	<ul class="azrcrv-ui-tabs-nav azrcrv-ui-widget-header" role="tablist">
		<li class="azrcrv-ui-state-default azrcrv-ui-state-active" aria-controls="tab-panel-settings" aria-labelledby="tab-settings" aria-selected="true" aria-expanded="true" role="tab">
			<a id="tab-settings" class="azrcrv-ui-tabs-anchor" href="#tab-panel-settings"><?php echo $tab_settings_label; // phpcs:ignore. ?></a>
		</li>
		<li class="azrcrv-ui-state-default" aria-controls="tab-panel-instructions" aria-labelledby="tab-instructions" aria-selected="false" aria-expanded="false" role="tab">
			<a id="tab-instructions" class="azrcrv-ui-tabs-anchor" href="#tab-panel-instructions"><?php echo $tab_instructions_label; // phpcs:ignore. ?></a>
		</li>
		<li class="azrcrv-ui-state-default" aria-controls="tab-panel-plugins" aria-labelledby="tab-plugins" aria-selected="false" aria-expanded="false" role="tab">
			<a id="tab-plugins" class="azrcrv-ui-tabs-anchor" href="#tab-panel-plugins"><?php echo $tab_plugins_label; // phpcs:ignore. ?></a>
		</li>
	</ul>
	<div id="tab-panel-settings" class="azrcrv-ui-tabs-scroll" role="tabpanel" aria-hidden="false">
		<fieldset>
			<legend class="screen-reader-text"><?php echo $tab_settings_label; // phpcs:ignore. ?></legend>
			<?php echo $tab_settings; // phpcs:ignore. ?>
		</fieldset>
	</div>
	<div id="tab-panel-instructions" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
		<fieldset>
			<legend class="screen-reader-text"><?php echo $tab_instructions_label; // phpcs:ignore. ?></legend>
			<?php echo $tab_instructions; // phpcs:ignore. ?>
		</fieldset>
	</div>
	<div id="tab-panel-plugins" class="azrcrv-ui-tabs-scroll azrcrv-ui-tabs-hidden" role="tabpanel" aria-hidden="true">
		<fieldset>
			<legend class="screen-reader-text"><?php echo $tab_plugins_label; // phpcs:ignore. ?></legend>
			<?php echo $tab_plugins; // phpcs:ignore. ?>
		</fieldset>
	</div>
</div>
<?php
/*
	donate button
*/
?>
<div class="azrcrv-donate">
	<?php esc_html_e( 'Support', 'azrcrv-siw' ); ?>
	azurecurve | Development
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="MCJQN9SJZYLWJ">
		<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
		<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
	</form>
	<span>
		<?php esc_html_e( 'You can help support the development of our free plugins by donating a small amount of money.', 'azrcrv-siw' ); ?>
	</span>
</div>
