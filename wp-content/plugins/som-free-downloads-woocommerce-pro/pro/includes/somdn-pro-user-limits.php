<?php
/**
 * Free Downloads - WooCommerce - User Download Limits
 * 
 * @version 3.2.0
 */

declare(strict_types=1);

namespace SOM\FreeDownloads\Pro\DownloadLimits;

defined('WPINC') || exit;

final class UserProfileDownloadLimits
{
	public function __construct() {}

	public function init()
	{
		$this->actions();
	}

	private function actions()
	{
		// Settings
		add_action('somdn_after_limit_settings_section', [$this, 'settings'], 20);

		// User profile meta
		add_action('show_user_profile', [$this, 'outputProfileLimits'], 50);
		add_action('edit_user_profile', [$this, 'outputProfileLimits'], 50);
		add_action('personal_options_update', [$this, 'saveProfileLimits']);
		add_action('edit_user_profile_update', [$this, 'saveProfileLimits']);

		// Getting user limits (static methods so they can be unhooked elsewhere if needed)
		add_filter('somdn_user_custom_limits', [__CLASS__, 'getUserProfileLimits'], 5, 2);
		add_filter('somdn_user_limits_excluded', [__CLASS__, 'getUserProfileLimitsExcluded'], 5, 2);
	}

	public function outputProfileLimits($user)
	{

		$user_id = $user->ID;
		if (!current_user_can('update_core')) {
			return;
		}

		$global_limit_options = get_option('somdn_pro_basic_limit_settings');
		$global_limits_freq = $global_limit_options['somdn_pro_basic_limit_freq'] ?? '';
		if (empty($global_limits_freq)) {
			$global_freq_name = '';
		} else {
			$global_freq_name = ucfirst(somdn_get_download_frequency_name($global_limits_freq));
		}

		$limits_enabled = $global_limit_options['somdn_pro_basic_limit_enable'] ?? 0 ;

		if (!$limits_enabled) {
			return;
		}

		$excluded  = get_user_meta($user_id, 'somdn_user_limit_exclude', true);
		$period    = get_user_meta($user_id, 'somdn_user_limit_period', true);
		$downloads = get_user_meta($user_id, 'somdn_user_limit_downloads', true);
		$products  = get_user_meta($user_id, 'somdn_user_limit_products', true);
		$error     = get_user_meta($user_id, 'somdn_user_limit_error', true);
		if (empty($error)) {
			$error = '';
		}

		$disabled = empty($excluded) ? '' : ' disabled';

		?>

		<h3><?php _e('User Download Limits', 'somdn-pro'); ?></h3>

		<p><strong><?php _e('Set custom download limit rules for this specific user.', 'somdn-pro'); ?></strong></p>

		<?php
			$url = somdn_get_plugin_link_full_admin() . '&tab=settings&section=limit';
			$text = sprintf(
				__('See <a href="%s" target="_blank">Download Limit settings</a> for more info.', 'somdn-pro'),
				esc_url($url)
			);
		?>
		<p style="padding-bottom: 10px;"><?php echo $text; ?></p>

		<?php if ($limits_enabled && !somdn_download_limits_active()) { ?>
			<div class="somdn-role-limit-warning-wrap">
				<p><strong><?php _e('Warning: Global download limit settings are not complete. No download limits will apply.', 'somdn-pro'); ?></strong></p>
			</div>
		<?php } ?>

		<table class="form-table">

			<tr>
				<th><?php _e('No Download Limits', 'somdn-pro'); ?></th>
				<td>
					<label for="somdn_user_limit_exclude" style="vertical-align: initial;">
						<input type="checkbox" id="somdn_user_limit_exclude" name="somdn_user_limit_exclude" value="1" <?php checked(1, $excluded); ?>>
						<?php _e('Exclude this user from all download limitations.', 'somdn-pro'); ?>
					</label>
				</td>
			</tr>

			<tr>
				<th><label for="somdn_user_limit_period" ><?php _e('Download limit period', 'somdn-pro'); ?></label></th>
				<td>
						<select name="somdn_user_limit_period" id="somdn_user_limit_period""<?php echo $disabled; ?>>
							<option value="" <?php selected($period, ''); ?> class="somdn_invalid_select"><?php _e('Please choose...', 'somdn-pro'); ?></option>
							<option value="1" <?php selected($period, 1); ?>><?php _e('Day', 'somdn-pro'); ?></option>
							<option value="2" <?php selected($period, 2); ?>><?php _e('Week', 'somdn-pro'); ?></option>
							<option value="3" <?php selected($period, 3); ?>><?php _e('Month', 'somdn-pro'); ?></option>
							<option value="4" <?php selected($period, 4); ?>><?php _e('Year', 'somdn-pro'); ?></option>
						</select>
						<p class="description"><?php printf(__('If blank the global setting %s will be used.', 'somdn-pro'), '<strong>'.esc_html($global_freq_name).'</strong>'); ?></p>
				</td>
			</tr>

			<tr>
				<th><label for="somdn_user_limit_downloads" ><?php _e('Number of downloads', 'somdn-pro'); ?></label></th>
				<td>
						<input type="number" min="0" max="1000" value="<?php echo $downloads; ?>" name="somdn_user_limit_downloads" id="somdn_user_limit_downloads" placeholder="0 - 1000" class="somdn-number-input"<?php echo $disabled; ?> style="margin-right: 10px;">
						<span class="somdn-setting-left-col"><?php _e('Leave blank for unlimited.', 'somdn-pro'); ?></span>
						<p class="description"><?php _e('Limit the number of times a download can be requested.', 'somdn-pro'); ?></p>
				</td>
			</tr>

			<tr>
				<th><label for="somdn_user_limit_products" ><?php _e('Number of products', 'somdn-pro'); ?></label></th>
				<td>
						<input type="number" min="0" max="1000" value="<?php echo $products; ?>" name="somdn_user_limit_products" id="somdn_user_limit_products" placeholder="0 - 1000" class="somdn-number-input"<?php echo $disabled; ?> style="margin-right: 10px;">
						<span class="somdn-setting-left-col"><?php _e('Leave blank for unlimited.', 'somdn-pro'); ?></span>
						<p class="description"><?php _e('Limit the number of different products that can be downloaded.', 'somdn-pro'); ?></p>
						<p class="description"><?php _e('If a product limit is set, the number of downloads will apply per product.', 'somdn-pro'); ?></p>
				</td>
			</tr>

			<?php $style = empty($excluded) ? '' : 'display: none;'; ?>
			<tr id="somdn_user_limit_error_row" style="<?php echo $style; ?>">
				<th><?php _e('Limit reached message', 'somdn-pro'); ?></th>
				<td>

					<p><?php _e('Customise the limit reached message for this user.', 'somdn-pro'); ?></p>

					<div id="somdn_user_limit_error_wrap" style="max-width: 450px; width: 100%;">

						<?php

							$editor_id = 'somdn_user_limit_error';
							$settings = array(
								'media_buttons' => false,
								'tinymce'=> array(
									'toolbar1' => 'bold,italic,link,undo,redo',
									'toolbar2'=> false
								),
								'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
								'editor_class' => 'required',
								'teeny' => true,
								'editor_height' => 180,
								'textarea_name' => 'somdn_user_limit_error'
							);
							$content = stripslashes($error);

							wp_editor($content, $editor_id, $settings);

						?>

					</div>

				</td>
			</tr>


		</table>

	<?php
	}

	public function saveProfileLimits($user_id)
	{

		if (!current_user_can('update_core')) {
			return;
		}

		if (isset($_POST['somdn_user_limit_exclude'])) {
			update_user_meta($user_id, 'somdn_user_limit_exclude', sanitize_text_field($_POST['somdn_user_limit_exclude']));
		} else {
			update_user_meta($user_id, 'somdn_user_limit_exclude', NULL);
		}

		//printDebug($_POST['somdn_user_limit_exclude'], 'somdn_user_limit_exclude');
		//printDebug(sanitize_text_field($_POST['somdn_user_limit_exclude']), 'sanitize_text_field');
		//printDebug($_POST, '_POST', true);

		if (isset($_POST['somdn_user_limit_period'])) {
			update_user_meta($user_id, 'somdn_user_limit_period', sanitize_text_field($_POST['somdn_user_limit_period']));
		} else {
			update_user_meta($user_id, 'somdn_user_limit_period', NULL);
		}

		if (isset($_POST['somdn_user_limit_downloads'])) {
			update_user_meta($user_id, 'somdn_user_limit_downloads', sanitize_text_field($_POST['somdn_user_limit_downloads']));
		} else {
			update_user_meta($user_id, 'somdn_user_limit_downloads', NULL);
		}

		if (isset($_POST['somdn_user_limit_products'])) {
			update_user_meta($user_id, 'somdn_user_limit_products', sanitize_text_field($_POST['somdn_user_limit_products']));
		} else {
			update_user_meta($user_id, 'somdn_user_limit_products', NULL);
		}

		if (isset($_POST['somdn_user_limit_error']) && !isset($_POST['somdn_user_limit_exclude'])) {
			update_user_meta($user_id, 'somdn_user_limit_error', wp_kses_post($_POST['somdn_user_limit_error']));
		} else {
			update_user_meta($user_id, 'somdn_user_limit_error', NULL);
		}

	}

	public function settings($limits_enabled = false)
	{
		if ($limits_enabled == true) {
			register_setting('somdn_pro_limit_settings', 'somdn_user_limit_settings');

			add_settings_section(
				'somdn_user_limit_settings_section',
				__('User Limit Settings', 'somdn-pro'),
				[$this, 'sectionCallback'],
				'somdn_pro_limit_settings'
			);

			add_settings_field(
				'somdn_user_limit_settings',
				__('User Rules', 'somdn-pro'),
				[$this, 'renderSetting'],
				'somdn_pro_limit_settings',
				'somdn_user_limit_settings_section',
				['class' => 'somdn_setting_no_th somdn_setting_no_bot']
			);

		}
	}

	public function renderSetting()
	{
		$url = get_admin_url() . 'users.php';
		echo '<p>';
		printf(
			__('Set download limit rules for individual users by editing their user profile from the <a href="%s">users</a> admin page.'),
			esc_url($url)
		);
		echo '</p>';
	}

	public function sectionCallback() {}

	public static function getUserProfileDownloadLimits(int $user_id): array
	{

		$user = get_userdata($user_id);
		if (!$user) {
			return [];
		}

		$limits = [
			'excluded'       => get_user_meta($user_id, 'somdn_user_limit_exclude', true),
			'limit_amount'   => get_user_meta($user_id, 'somdn_user_limit_downloads', true),
			'limit_products' => get_user_meta($user_id, 'somdn_user_limit_products', true),
			'limit_freq'     => get_user_meta($user_id, 'somdn_user_limit_period', true),
			'limit_error'    => get_user_meta($user_id, 'somdn_user_limit_error', true)
		];

		foreach ($limits as $name => $limit_value) {
			if (!empty($limit_value)) {
				return $limits;
			}
		}

		return [];
	}

	public static function getUserProfileLimitsExcluded(bool $excluded, int $user_id): bool
	{
		// If $excluded is already populated then return it.
		if ($excluded == true) {
			return $excluded;
		}

		$user = get_userdata($user_id);
		if (!$user) {
			return $excluded;
		}

		$user_limits = self::getUserProfileDownloadLimits($user_id);
		if (!empty($user_limits)) {
			if ($user_limits['excluded'] == true) {
				$excluded = true;
			}
			return $excluded;
		}

		return $excluded;
	}

	public static function getUserProfileLimits(array $limits, int $user_id): array
	{

		// If $limits is already populated then return it.
		if (!empty($limits)) {
			return $limits;
		}

		$user = get_userdata($user_id);
		if (!$user) {
			return $limits;
		}

		$user_limits = self::getUserProfileDownloadLimits($user_id);
		if (!empty($user_limits)) {
			$limits = $user_limits;
			return $limits;
		}

		return $limits;

	}

}

(new UserProfileDownloadLimits())->init();