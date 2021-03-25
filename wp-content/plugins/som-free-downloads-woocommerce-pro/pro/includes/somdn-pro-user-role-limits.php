<?php
/**
 * Free Downloads - WooCommerce - User Role Download Limits
 * 
 * @version 3.1.94
 */

declare(strict_types=1);

namespace SOM\FreeDownloads\Pro\DownloadLimits;

defined('WPINC') || exit;

final class UserRoleDownloadLimits
{

	private static $post_type = 'somdn_role_limit';

	public function __construct() {}

	public function init()
	{
		$this->actions();
	}

	private function actions()
	{
		$post_type = self::$post_type;

		// Settings
		add_action('somdn_after_limit_settings_section', [$this, 'settings']);

		// Admin menu
		//add_action('admin_menu', [$this, 'menu'], 99);
		//add_action('admin_head', [$this, 'menuHighlight']);

		// Custom post type
		add_action('init', [$this, 'postType'], 50);

		// Custom post type list columns (admin)
		add_action("views_edit-{$post_type}", [$this, 'backButtonListTable'], 20);
		add_filter("manage_{$post_type}_posts_columns", [$this, 'postsColumns']);
		add_action("manage_{$post_type}_posts_custom_column", [$this, 'columnsContent'], 50, 2);
		add_filter('post_row_actions', [$this, 'removeQuickEdit'], 10, 2);

		// Custom post type meta
		add_action('add_meta_boxes', [$this, 'meta']);
		add_action('do_meta_boxes', [$this, 'hideMeta']);
		add_action('edit_form_top', [$this, 'backButtonMeta']);
		add_action("save_post_$post_type", [$this, 'saveMeta']);
		add_action('admin_enqueue_scripts', [$this, 'disableAutoSavePost']);

		// Getting user limits (static methods so they can be unhooked elsewhere if needed)
		add_filter('somdn_user_custom_limits', [__CLASS__, 'getUserRoleLimits'], 50, 2);
		add_filter('somdn_user_limits_excluded', [__CLASS__, 'getUserRoleLimitsExcluded'], 50, 2);
	}

	public function postType()
	{
		$labels = [
			'name'                  => __('User Role Download Limits', 'somdn-pro'),
			'singular_name'         => __('User Role Download Limit', 'somdn-pro'),
			'menu_name'             => __('User Role Download Limits', 'somdn-pro'),
			'name_admin_bar'        => __('User Role Download Limits', 'somdn-pro'),
			'archives'              => __('User Role Download Limits', 'somdn-pro'),
			'parent_item_colon'     => __('Parent Item:', 'somdn-pro'),
			'all_items'             => __('User Role Download Limits', 'somdn-pro'),
			'add_new_item'          => __('Add New User Role Download Limit', 'somdn-pro'),
			'add_new'               => __('Add New', 'somdn-pro'),
			'new_item'              => __('New User Role Download Limit', 'somdn-pro'),
			'edit_item'             => __('User Role Download Limit', 'somdn-pro'),
			'update_item'           => __('Update User Role Download Limit', 'somdn-pro'),
			'view_item'             => __('View User Role Download Limit', 'somdn-pro'),
			'search_items'          => __('Search User Role Download Limits', 'somdn-pro'),
			'not_found'             => __('Not found', 'somdn-pro'),
			'not_found_in_trash'    => __('Not found in Trash', 'somdn-pro'),
			'featured_image'        => __('Featured Image', 'somdn-pro'),
			'set_featured_image'    => __('Set featured image', 'somdn-pro'),
			'remove_featured_image' => __('Remove featured image', 'somdn-pro'),
			'use_featured_image'    => __('Use as featured image', 'somdn-pro'),
			'insert_into_item'      => __('Insert into User Role Download Limit', 'somdn-pro'),
			'uploaded_to_this_item' => __('Uploaded to this User Role Download Limit', 'somdn-pro'),
			'items_list'            => __('User Role Download Limits list', 'somdn-pro'),
			'items_list_navigation' => __('User Role Download Limits list navigation', 'somdn-pro'),
			'filter_items_list'     => __('Filter User Role Download Limits list', 'somdn-pro'),
		];
		$rewrite = [
			'slug'                  => 'user-role-download-limits',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		];
		$capabilities = [
			'edit_post'             => 'update_core',
			'read_post'             => 'update_core',
			'delete_post'           => 'update_core',
			'edit_posts'            => 'update_core',
			'edit_others_posts'     => 'update_core',
			'delete_posts'          => 'update_core',
			'publish_posts'         => 'update_core',
			'read_private_posts'    => 'update_core',
			'create_posts' => true
		];
		$supports = [
			'title',
			'content',
			'author'
		];
		$args = [
			'label'                 => __('User Role Download Limit', 'somdn-pro'),
			'description'           => __('User Role Download Limits', 'somdn-pro'),
			'labels'                => apply_filters('somdn_role_limits_labels', $labels),
			'supports'              => ['title'],
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => 'users.php',
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'product',
			'rewrite'               => apply_filters('somdn_role_limits_rewrite', $rewrite),
			'map_meta_cap' => true
		];
		register_post_type(self::$post_type, apply_filters('somdn_role_limits_args', $args));
	}

	public function menuHighlight()
	{
		global $current_screen, $parent_file, $submenu_file;
		
		$base = $current_screen->base;
		$post_type = $current_screen->post_type;

		if ($post_type == self::$post_type && ($base == 'post' || $base == 'edit')) {
			$parent_file = 'woocommerce';
			$submenu_file = 'download_now_dashboard';
			return;
		}
	}

	public function menu()
	{
		add_submenu_page(
			'users.php',
			__('User Role Download Limits', 'somdn-pro'),
			__('User Role Download Limits', 'somdn-pro'),
			'update_core',
			'edit.php?post_type=somdn_role_limit',
			NULL
		);
	}

	public function columnsContent($column_name, $post_id)
	{
		$post_type = self::$post_type;

		if ($column_name == "{$post_type}_role_column") {
			$role = get_post_meta($post_id, 'somdn_role_limit_role', true);
			$role_name = '<strong>' . __('MISSING ROLE', 'somdn-pro') . '</strong>';
			if (!empty($role)) {
				$role_name = self::getRoleDisplayName(sanitize_text_field($role)) ?? '<strong>' . __('MISSING ROLE', 'somdn-pro') . '</strong>';
			}
			echo esc_html($role_name);
		}

		if ($column_name == "{$post_type}_exclude_column") {
			$exclude = get_post_meta($post_id, 'somdn_role_limit_exclude', true);
			$exclude = empty($exclude) ? '&#10006;' : '&#10004;';
			echo $exclude;
		}

		if ($column_name == "{$post_type}_period_column") {
			$period = get_post_meta($post_id, 'somdn_role_limit_period', true);
			$period_name = '';
			if (!empty($period)) {
				$period_name = esc_html(ucfirst(somdn_get_download_frequency_name($period)));
			}
			$exclude = get_post_meta($post_id, 'somdn_role_limit_exclude', true);
			if (!empty($exclude)) {
				$period_name = __('N/A', 'somdn-pro');
			}
			echo esc_html($period_name);
		}

		if ($column_name == "{$post_type}_downloads_column") {
			$downloads = get_post_meta($post_id, 'somdn_role_limit_downloads', true);
			$downloads = empty($downloads) ? __('Unlimited', 'somdn-pro') : esc_html($downloads);
			echo esc_html($downloads);
		}

		if ($column_name == "{$post_type}_products_column") {
			$products = get_post_meta($post_id, 'somdn_role_limit_products', true);
			$products = empty($products) ? __('Unlimited', 'somdn-pro') : esc_html($products);
			echo esc_html($products);
		}

		if ($column_name == "{$post_type}_date_column") {
			echo esc_html(get_the_date(get_option('date_format')));
		}
	}

	public function postsColumns($columns)
	{
		$post_type = self::$post_type;

		$new_columns = [];

		unset($columns['date']);

		//$columns['title']  = 'Product';
		$columns["{$post_type}_role_column"] = 'Role';
		$columns["{$post_type}_exclude_column"] = 'No Limits';
		$columns["{$post_type}_period_column"] = 'Period';
		$columns["{$post_type}_downloads_column"] = 'Downloads';
		$columns["{$post_type}_products_column"] = 'Products';
		$columns["{$post_type}_date_column"] = 'Date Added';

		//$columns['somdn_tracked_total_column']  = 'Total';

		$customOrder = [
			'cb',
			'title',
			"{$post_type}_role_column",
			"{$post_type}_exclude_column",
			"{$post_type}_period_column",
			"{$post_type}_downloads_column",
			"{$post_type}_products_column",
			"{$post_type}_date_column"
		];

		foreach ($customOrder as $colname) {
			$new_columns[$colname] = $columns[$colname];
		}
		
		return $new_columns;
	}

	public function removeQuickEdit($actions, $post)
	{
		if ($post->post_type == self::$post_type) {
			unset($actions['inline hide-if-no-js']);
		}
		return $actions;
	}

	public function backButtonMeta($post)
	{
		$post_type = self::$post_type;
		if ($post->post_type == $post_type) {
			$url = esc_url(get_admin_url() . "edit.php?post_type={$post_type}");
			echo '<p><a class="button" href="'.$url.'">'.__('Return to User Role Download Limits', 'somdn-pro').'</a></p>';
		}
	}

	public function backButtonListTable($views)
	{
			$url = esc_url(somdn_get_plugin_link_full_admin() . '&tab=settings&section=limit');
			echo '<p><a class="button" href="'.$url.'">'.__('Return to Download Limit Settings', 'somdn-pro').'</a></p>';
			return $views;
	}

	public function meta()
	{
		add_meta_box(
			'somdn_role_limit_details',
			__( 'Download Limit Rules', 'somdn-pro' ),
			[$this, 'outputMeta'],
			self::$post_type,
			'normal',
			'core'
		);
	}

	public function hideMeta()
	{
		global $post;
		$post_type = get_post_type($post);

		if ($post_type == self::$post_type) {
			//remove_meta_box('submitdiv', self::$post_type , 'side');
			remove_meta_box('slugdiv', self::$post_type, 'normal');
		}
	}

	public function outputMeta($post)
	{

		$post_id = $post->ID;

		$used_roles = self::getAllRolesUsedByRoleRules();

		$role      = get_post_meta($post_id, 'somdn_role_limit_role', true);
		$excluded  = get_post_meta($post_id, 'somdn_role_limit_exclude', true);
		$period    = get_post_meta($post_id, 'somdn_role_limit_period', true);
		$downloads = get_post_meta($post_id, 'somdn_role_limit_downloads', true);
		$products  = get_post_meta($post_id, 'somdn_role_limit_products', true);
		$error     = get_post_meta($post_id, 'somdn_role_limit_error', true);
		if (empty($error)) {
			$error = '';
		}

		$disabled = empty($excluded) ? '' : ' disabled';

		$global_limit_options = get_option('somdn_pro_basic_limit_settings');
		$global_limits_freq = $global_limit_options['somdn_pro_basic_limit_freq'] ?? '';
		if (empty($global_limits_freq)) {
			$global_freq_name = '';
		} else {
			$global_freq_name = ucfirst(somdn_get_download_frequency_name($global_limits_freq));
		}

		wp_nonce_field('_somdn_role_limit_nonce', 'somdn_role_limit_nonce'); ?>

		<style>#minor-publishing #visibility {display: none;}</style>

		<div class="options_group">
			<fieldset class="form-field post_name_field">
				<span class="somdn-woo-meta-span pad-25">

					<p style="font-size: 16px;"><strong><?php _e('Set custom download limit rules for a specific user role.', 'somdn-pro'); ?></strong></p>
					<?php
						$url = somdn_get_plugin_link_full_admin() . '&tab=settings&section=limit';
						$text = sprintf(
							__('See <a href="%s" target="_blank">Download Limit settings</a> for more info.', 'somdn-pro'),
							esc_url($url)
						);
					?>
					<p style="padding-bottom: 10px;"><?php echo $text; ?></p>
					<hr>
				</span>
			</fieldset>
		</div>

		<?php
		$limit_options = get_option('somdn_pro_basic_limit_settings');
		$limits_enabled = $limit_options['somdn_pro_basic_limit_enable'] ?? 0 ;

		if ($limits_enabled && !somdn_download_limits_active()) { ?>

			<div class="somdn-role-limit-warning-wrap">
				<p><strong><?php _e('Warning: Global download limit settings are not complete. No download limits will apply.', 'somdn-pro'); ?></strong></p>
			</div>

		<?php } ?>

		<div class="options_group">
			<fieldset class="form-field post_name_field">

				<span class="somdn-woo-meta-span">

					<span class="somdn-woo-meta-span pad-25">
						<span class="somdn-setting-left-col"><strong><?php _e('User Role', 'somdn-pro'); ?></strong></span>
						<select name="somdn_role_limit_role" id="somdn_role_limit_role" style="float: none;" required>
							<option value="" <?php selected($role, ''); ?> class="somdn_invalid_select"><?php _e('Please choose...', 'somdn-pro'); ?></option>
							<?php
								$roles = self::getAllRoles();
								foreach ($roles as $key => $value) {
									$option_disabled = '';
									$name = esc_html($value);
									if (in_array($key, $used_roles) && $role != $key) {
										$option_disabled = ' disabled';
										$name .= ' '. __('(in use)', 'somdn-pro');
									}
									if ($role == $key) {
										$name .= ' '. __('(current)', 'somdn-pro');
									}
									echo '<option value="'.esc_html($key).'"'.selected($role, $key).$option_disabled.'>'.$name.'</option>';
								}
							?>
						</select>
					</span>

					<span class="somdn-woo-meta-span pad-25">
						<span class="somdn-setting-left-col"><strong><?php _e('No Download Limits', 'somdn-pro'); ?></strong></span>
						<label for="somdn_role_limit_exclude">
							<input type="checkbox" id="somdn_role_limit_exclude" name="somdn_role_limit_exclude" value="1" <?php checked(1, $excluded); ?>>
							<span style="padding-left: 5px; display: inline-block;"><?php _e('Exclude this user role from all download limitations.', 'somdn-pro'); ?>
							</span>
						</label>
					</span>

					<span class="somdn-woo-meta-span pad-25">
						<span class="somdn-setting-left-col"><strong><?php _e('Download limit period', 'somdn-pro'); ?></strong></span>
						<select name="somdn_role_limit_period" id="somdn_role_limit_period"<?php echo $disabled; ?>>
							<option value="" <?php selected($period, ''); ?> class="somdn_invalid_select"><?php _e('Please choose...', 'somdn-pro'); ?></option>
							<option value="1" <?php selected($period, 1); ?>><?php _e('Day', 'somdn-pro'); ?></option>
							<option value="2" <?php selected($period, 2); ?>><?php _e('Week', 'somdn-pro'); ?></option>
							<option value="3" <?php selected($period, 3); ?>><?php _e('Month', 'somdn-pro'); ?></option>
							<option value="4" <?php selected($period, 4); ?>><?php _e('Year', 'somdn-pro'); ?></option>
						</select>
						<p class="description"><?php printf(__('If blank the global setting %s will be used.', 'somdn-pro'), '<strong>'.esc_html($global_freq_name).'</strong>'); ?></p>
					</span>

					<span class="somdn-woo-meta-span pad-25">
						<span class="somdn-setting-left-col"><strong><?php _e('Number of downloads', 'somdn-pro'); ?></strong></span>
						<input type="number" min="0" max="1000" value="<?php echo $downloads; ?>" name="somdn_role_limit_downloads" id="somdn_role_limit_downloads" placeholder="0 - 1000" class="somdn-number-input"<?php echo $disabled; ?> style="margin-right: 10px;">
						<span class="somdn-setting-left-col"><?php _e('Leave blank for unlimited.', 'somdn-pro'); ?></span>
						<p class="description"><?php _e('Limit the number of times a download can be requested.', 'somdn-pro'); ?></p>
					</span>

					<span class="somdn-woo-meta-span pad-25">
						<span class="somdn-setting-left-col"><strong><?php _e('Number of products', 'somdn-pro'); ?></strong></span>
						<input type="number" min="0" max="1000" value="<?php echo $products; ?>" name="somdn_role_limit_products" id="somdn_role_limit_products" placeholder="0 - 1000" class="somdn-number-input"<?php echo $disabled; ?> style="margin-right: 10px;">
						<span class="somdn-setting-left-col"><?php _e('Leave blank for unlimited.', 'somdn-pro'); ?></span>

						<p class="description"><?php _e('Limit the number of different products that can be downloaded.', 'somdn-pro'); ?></p>
						<p class="description"><?php _e('If a product limit is set, the number of downloads will apply per product.', 'somdn-pro'); ?></p>
					</span>

				</span>

				<?php $style = empty($excluded) ? 'block' : 'none'; ?>

				<span class="somdn-woo-meta-span" id="somdn_role_limit_error_wrap" style="display: <?php echo $style; ?>;">

					<span class="somdn-woo-meta-span" style="font-size: 14px;"><strong><?php _e('Customise the limit reached message for this role.', 'somdn-pro'); ?></strong></span>

						<?php

							$editor_id = 'somdn_role_limit_error';
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
								'textarea_name' => 'somdn_role_limit_error'
							);
							$content = stripslashes($error);

							wp_editor($content, $editor_id, $settings);

						?>

				</span>

			</fieldset>
		</div>

	<?php

	}

	public function saveMeta($post_id)
	{

		if (defined('DOING_AUTOSAVE' ) && DOING_AUTOSAVE) return;
		if (!isset( $_POST['somdn_role_limit_nonce'] ) || !wp_verify_nonce($_POST['somdn_role_limit_nonce'], '_somdn_role_limit_nonce')) return;
		if (!current_user_can('update_core', $post_id)) return;

		if (isset($_POST['somdn_role_limit_role'])) {

			$role = sanitize_text_field($_POST['somdn_role_limit_role']);

			// Validate there isn't already a rule set for this role

			$role_rule = self::getRoleLimitRuleByRole($role);

			if ($role_rule) {

				if ($role_rule != $post_id) {

					$role_name = self::getRoleDisplayName($role);
					$title = esc_html(get_the_title($role_rule));

					$update_error = sprintf(
						__('The %1s user role is already being used by the %2s download limit rule, ID number %3s.', 'somdn-pro'),
						'<strong>'.$role_name.'</strong>',
						'<strong>'.$title.'</strong>',
						'<strong>'.$role_rule.'</strong>'
					);

					wp_die($update_error, 'User Role Download Limit Error', ['back_link' => true]);

				}

			}

			// All ok
			update_post_meta($post_id, 'somdn_role_limit_role', $role);

		} else {
			update_post_meta($post_id, 'somdn_role_limit_role', NULL);
		}

		if (isset($_POST['somdn_role_limit_exclude'])) {
			update_post_meta($post_id, 'somdn_role_limit_exclude', sanitize_text_field($_POST['somdn_role_limit_exclude']));
		} else {
			update_post_meta($post_id, 'somdn_role_limit_exclude', NULL);
		}

		if (isset($_POST['somdn_role_limit_downloads'])) {
			update_post_meta($post_id, 'somdn_role_limit_downloads', sanitize_text_field($_POST['somdn_role_limit_downloads']));
		} else {
			update_post_meta($post_id, 'somdn_role_limit_downloads', NULL);
		}

		if (isset($_POST['somdn_role_limit_products'])) {
			update_post_meta($post_id, 'somdn_role_limit_products', sanitize_text_field($_POST['somdn_role_limit_products']));
		} else {
			update_post_meta($post_id, 'somdn_role_limit_products', NULL);
		}

		if (isset($_POST['somdn_role_limit_period'])) {
			update_post_meta($post_id, 'somdn_role_limit_period', sanitize_text_field($_POST['somdn_role_limit_period']));
		} else {
			update_post_meta($post_id, 'somdn_role_limit_period', NULL);
		}

		if (isset($_POST['somdn_role_limit_error']) && !isset($_POST['somdn_role_limit_exclude'])) {
			update_post_meta($post_id, 'somdn_role_limit_error', wp_kses_post($_POST['somdn_role_limit_error']));
		} else {
			update_post_meta($post_id, 'somdn_role_limit_error', NULL);
		}

	}

	public function settings($limits_enabled = false)
	{
		if ($limits_enabled == true) {
			register_setting('somdn_pro_limit_settings', 'somdn_role_limit_settings');

			add_settings_section(
				'somdn_role_limit_settings_section',
				__('User Role Limit Settings', 'somdn-pro'),
				[$this, 'sectionCallback'],
				'somdn_pro_limit_settings'
			);

			add_settings_field(
				'somdn_role_limit_settings',
				__('Role Rules', 'somdn-pro'),
				[$this, 'renderSetting'],
				'somdn_pro_limit_settings',
				'somdn_role_limit_settings_section',
				['class' => 'somdn_setting_no_th somdn_setting_no_bot']
			);

		}
	}

	public function renderSetting()
	{
		$url = get_admin_url() . 'edit.php?post_type=somdn_role_limit';
		echo '<p>';
		printf(
			__('<a href="%s">Click here</a> to set custom download limit rules for user roles.'),
			esc_url($url)
		);
		echo '</p>';
	}

	public function sectionCallback() {}

	public function disableAutoSavePost() {
		if (self::$post_type == get_post_type()) {
			wp_dequeue_script('autosave');
		}
	}

	public static function getAllRoles(): array
	{
		$cleaned_roles = [];
		$roles = wp_roles()->roles;
		foreach ($roles as $key => $value) {
			$cleaned_roles[$key] = $value['name'];
		}
		return $cleaned_roles;
	}

	public static function getRoleDisplayName(string $role): string
	{
		$role = sanitize_text_field($role);
		$roles = self::getAllRoles();
		$role_name = esc_html($roles[$role] ?? '');
		return $role_name;
	}

	public static function getAllRoleLimitRules(): array
	{
		$role_rules = [];
		$args = [
			'fields' => 'ids',
			'post_type' => self::$post_type,
			'post_status' => 'publish',
			'posts_per_page' => -1,
		];

		$query = new \WP_Query($args);
		wp_reset_postdata();
		$role_rules = $query->posts;
		return $role_rules;
	}

	public static function getAllRolesUsedByRoleRules(): array
	{
		$used_roles = [];
		$rules = self::getAllRoleLimitRules();
		if (!empty($rules)) {
			foreach ($rules as $rule_id) {
				$used_role = get_post_meta($rule_id, 'somdn_role_limit_role', true);
				$used_roles[] = $used_role;
			}
		}
		return $used_roles;
	}

	public static function getRoleLimitRuleByRole(string $role): int
	{
		$role = sanitize_text_field($role);

		$role_rule = 0;

		$args = [
			'fields' => 'ids',
			'post_type' => self::$post_type,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => [
				[
					'key' => 'somdn_role_limit_role',
					'value' => sanitize_text_field($role),
					'compare' => '=',
				]
			]
		];

		$query = new \WP_Query($args);
		wp_reset_postdata();

		if ($query->found_posts) {
			$role_rule = intval($query->posts[0]);
		}

		return $role_rule;
	}

	public static function getRoleDownloadLimits(string $role): array
	{
		$role = sanitize_text_field($role);

		$limits = [];

		$limit_rule = self::getRoleLimitRuleByRole($role);

		if ($limit_rule > 0) {

			$limits = [
				'id'             => $limit_rule,
				'role'           => get_post_meta($limit_rule, 'somdn_role_limit_role', true),
				'excluded'       => get_post_meta($limit_rule, 'somdn_role_limit_exclude', true),
				'limit_amount'   => get_post_meta($limit_rule, 'somdn_role_limit_downloads', true),
				'limit_products' => get_post_meta($limit_rule, 'somdn_role_limit_products', true),
				'limit_freq'     => get_post_meta($limit_rule, 'somdn_role_limit_period', true),
				'limit_error'    => get_post_meta($limit_rule, 'somdn_role_limit_error', true)
			];

		}

		return $limits;
	}

	public static function getUserRoleLimitsExcluded(bool $excluded, int $user_id): bool
	{

		// If $excluded is already populated then return it.
		if ($excluded == true) {
			return $excluded;
		}

		$user = get_userdata($user_id);
		if (!$user) {
			return $excluded;
		}

		$user_roles = $user->roles;

		// Although users only have a single role, some plugins allow multiple.
		// So we want to look through each one to see if they have limits set.
		foreach ($user_roles as $user_role) {
			$role_limits = self::getRoleDownloadLimits($user_role);
			if (!empty($role_limits)) {
				if ($role_limits['excluded'] == true) {
					$excluded = true;
				}
				return $excluded;
			}
		}

		return $excluded;

	}

	public static function getUserRoleLimits(array $limits, int $user_id): array
	{

		// If $limits is already populated then return it.
		if (!empty($limits)) {
			return $limits;
		}

		$user = get_userdata($user_id);
		if (!$user) {
			return $limits;
		}

		$user_roles = $user->roles;

		// Although users only have a single role, some plugins allow multiple.
		// So we want to look through each one to see if they have limits set.
		foreach ($user_roles as $user_role) {
			$role_limits = self::getRoleDownloadLimits($user_role);
			if (!empty($role_limits)) {
				$limits = $role_limits;
				return $limits;
			}
		}

		return $limits;

	}

}

(new UserRoleDownloadLimits())->init();