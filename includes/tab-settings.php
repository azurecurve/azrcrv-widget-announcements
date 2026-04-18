<?php
/**
 * Settings tab content.
 */

/**
 * Declare the Namespace.
 */
namespace azurecurve\WidgetAnnouncements;

$to_twitter_enabled      = is_plugin_active( 'azrcrv-to-twitter/azrcrv-to-twitter.php' );
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
			<label for="to-twitter-integration"><?php esc_html_e( 'Enable To Twitter', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<?php if ( $to_twitter_enabled ) { ?>
				<label for="to-twitter-integration">
					<input name="to-twitter-integration" type="checkbox" id="to-twitter-integration" value="1" <?php checked( '1', $options['to-twitter']['integrate'] ); ?> />
					<?php
					printf(
						/* translators: 1: plugin link, 2: developer link */
						esc_html__( 'Enable integration with %1$s from %2$s?', 'azrcrv-wa' ),
						'<a href="' . esc_url( admin_url( 'admin.php?page=azrcrv-tt' ) ) . '">To Twitter</a>',
						DEVELOPER_LINK
					);
					?>
				</label>
			<?php } else { ?>
				<?php
				printf(
					/* translators: 1: plugin link, 2: developer link */
					esc_html__( '%1$s from %2$s not installed/activated.', 'azrcrv-wa' ),
					'<a href="https://development.azurecurve.co.uk/classicpress-plugins/to-twitter/">To Twitter</a>',
					DEVELOPER_LINK
				);
				?>
			<?php } ?>
		</td>
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

/**
 * To Twitter integration tab.
 */
$tab_to_twitter_label = esc_html__( 'To Twitter Integration', 'azrcrv-wa' );

ob_start();
?>
<table class="form-table azrcrv-settings">

	<tr>
		<th scope="row">
			<label for="to-twitter-tweet"><?php esc_html_e( 'Tweet', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<label for="to-twitter-tweet">
				<input name="to-twitter-tweet" type="checkbox" id="to-twitter-tweet" value="1" <?php checked( '1', $options['to-twitter']['tweet'] ); ?> />
				<?php esc_html_e( 'Send tweet at below time?', 'azrcrv-wa' ); ?>
			</label>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="to-twitter-tweet-time"><?php esc_html_e( 'Tweet Time', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<input type="time" id="to-twitter-tweet-time" name="to-twitter-tweet-time" value="<?php echo esc_attr( $options['to-twitter']['tweet-time'] ); ?>" required />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="to-twitter-retweet"><?php esc_html_e( 'Retweet', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<label for="to-twitter-retweet">
				<input name="to-twitter-retweet" type="checkbox" id="to-twitter-retweet" value="1" <?php checked( '1', $options['to-twitter']['retweet'] ); ?> />
				<?php esc_html_e( 'Send retweet at below time?', 'azrcrv-wa' ); ?>
			</label>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="to-twitter-retweet-time"><?php esc_html_e( 'Retweet Time', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<input type="time" id="to-twitter-retweet-time" name="to-twitter-retweet-time" value="<?php echo esc_attr( $options['to-twitter']['retweet-time'] ); ?>" required />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="to-twitter-retweet-prefix"><?php esc_html_e( 'Retweet Prefix', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<input name="to-twitter-retweet-prefix" type="text" id="to-twitter-retweet-prefix" value="<?php echo esc_attr( $options['to-twitter']['retweet-prefix'] ); ?>" class="regular-text" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="to-twitter-tweet-format"><?php esc_html_e( 'Tweet Format', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<input name="to-twitter-tweet-format" type="text" id="to-twitter-tweet-format" value="<?php echo esc_attr( $options['to-twitter']['tweet-format'] ); ?>" class="regular-text" />
			<p class="description"><?php esc_html_e( 'Use %t for tweet text and %h for hashtags.', 'azrcrv-wa' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="to-twitter-use-featured-image"><?php esc_html_e( 'Use Featured Image', 'azrcrv-wa' ); ?></label>
		</th>
		<td>
			<label for="to-twitter-use-featured-image">
				<input name="to-twitter-use-featured-image" type="checkbox" id="to-twitter-use-featured-image" value="1" <?php checked( '1', $options['to-twitter']['use-featured-image'] ); ?> />
				<?php esc_html_e( 'Use featured image? Only three other media images can be included in the tweet.', 'azrcrv-wa' ); ?>
			</label>
		</td>
	</tr>

</table>
<?php
$tab_to_twitter = ob_get_clean();

// Only expose To Twitter tab if plugin is active and integration enabled.
if ( ! $to_twitter_enabled || 1 !== (int) $options['to-twitter']['integrate'] ) {
	$tab_to_twitter_label = null;
	$tab_to_twitter       = null;
}
