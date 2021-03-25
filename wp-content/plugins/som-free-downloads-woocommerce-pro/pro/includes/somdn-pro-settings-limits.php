<?php
/**
 * Free Downloads - WooCommerce - Pro Settings - Limits
 * 
 * @version 3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'somdn_settings_page_content' , 'somdn_settings_limit_settings', 10, 1 );
function somdn_settings_limit_settings( $active_section ) {
	if ( $active_section == 'limit' ) {
		somdn_limit_settings_content();
	}
}

add_action( 'somdn_settings_subtabs_after_owned' , 'somdn_settings_subtabs_limits', 50, 1 );
function somdn_settings_subtabs_limits( $active_section ) {
	$nav_active = ( $active_section == 'limit' ) ? 'current' : '' ;
	echo '<li><a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=limit" class="' . $nav_active . '">Download Limits</a> | </li>';
}

function somdn_limit_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="som-settings-gen-settings-form-wrap">
			
						<?php

							settings_fields( 'somdn_pro_limit_settings' );
							somdn_do_custom_settings_sections( 'somdn_pro_limit_settings' );
							submit_button();

						?>
			
					</div>
			
				</form>
		
			</div>

			<div class="som-settings-col-5 som-settings-guide som-settings-multi-guide" style="text-align: left;">
				<br>

				<div class="som-settings-guide-note">
					<h3 style="text-align: center;"><?php _e('Notes on Priority', 'somdn-pro'); ?></h3>

					<p><?php _e('Download limit priority is as follows:', 'somdn-pro'); ?></p>
					<ol style="text-align: left;">
						<li><?php _e('User Account', 'somdn-pro'); ?></li>
						<li><?php _e('Membership Plan', 'somdn-pro'); ?></li>
						<li><?php _e('User Role', 'somdn-pro'); ?></li>
						<li><?php _e('Global Setting', 'somdn-pro'); ?></li>
					</ol>

					<hr>

					<br>

					<p><?php _e('Any rule that excludes a specific user from any download limits will always take priority. For example: If the user has specific download limits tied to their profile, but they have a user role that has exluded download limits, then the user will have no download limits.', 'somdn-pro'); ?></p>

				</div>

			</div>


		</div>
	</div>


<?php

}

add_action( 'somdn_pro_settings', 'somdn_pro_settings_limits', 20 );
function somdn_pro_settings_limits() {

	register_setting( 'somdn_pro_limit_settings', 'somdn_pro_basic_limit_settings' );

	add_settings_section(
		'somdn_pro_basic_limit_settings_section', 
		__( 'Download Limitations', 'somdn-pro' ), 
		'somdn_pro_basic_limit_settings_section_callback', 
		'somdn_pro_limit_settings'
	);

	add_settings_field( 
		'somdn_pro_basic_limit_enable', 
		__( 'Enabled / Disabled', 'somdn-pro' ), 
		'somdn_pro_basic_limit_enable_render', 
		'somdn_pro_limit_settings', 
		'somdn_pro_basic_limit_settings_section' 
	);

	$basic_options = get_option( 'somdn_pro_basic_limit_settings' );
	$basic_enabled = isset( $basic_options['somdn_pro_basic_limit_enable'] ) ? $basic_options['somdn_pro_basic_limit_enable'] : 0 ;

	if ( $basic_enabled ) :

		add_settings_field( 
			'somdn_pro_basic_limit_type', 
			__( 'Limitations', 'somdn-pro' ), 
			'somdn_pro_basic_limit_type_render', 
			'somdn_pro_limit_settings', 
			'somdn_pro_basic_limit_settings_section' 
		);

		add_settings_field( 
			'somdn_pro_basic_limit_freq', 
			NULL, 
			'somdn_pro_basic_limit_freq_render', 
			'somdn_pro_limit_settings', 
			'somdn_pro_basic_limit_settings_section'
		);

		add_settings_field( 
			'somdn_pro_basic_limit_amount', 
			NULL, 
			'somdn_pro_basic_limit_amount_render', 
			'somdn_pro_limit_settings', 
			'somdn_pro_basic_limit_settings_section'
		);

		add_settings_field( 
			'somdn_pro_basic_limit_products', 
			NULL, 
			'somdn_pro_basic_limit_products_render', 
			'somdn_pro_limit_settings', 
			'somdn_pro_basic_limit_settings_section'
		);

		add_settings_field( 
			'somdn_pro_basic_limit_after', 
			NULL, 
			'somdn_pro_basic_limit_after_render', 
			'somdn_pro_limit_settings', 
			'somdn_pro_basic_limit_settings_section'
		);

		add_settings_field( 
			'somdn_pro_basic_limit_error', 
			__( 'Error Message', 'somdn-pro' ), 
			'somdn_pro_basic_limit_error_render', 
			'somdn_pro_limit_settings',
			'somdn_pro_basic_limit_settings_section'
		);
/*
		add_settings_field( 
			'somdn_pro_basic_limit_error_redirect', 
			__( 'Redirect Page', 'somdn-pro' ), 
			'somdn_pro_basic_limit_error_redirect_render', 
			'somdn_pro_limit_settings',
			'somdn_pro_basic_limit_settings_section'
		);
*/
		add_settings_field( 
			'somdn_pro_limit_acc_page', 
			__( 'Account Page', 'somdn-pro' ), 
			'somdn_pro_limit_acc_page_render', 
			'somdn_pro_limit_settings', 
			'somdn_pro_basic_limit_settings_section' 
		);

		$acc_page_limits_enabled = isset( $basic_options['somdn_pro_limit_acc_page'] ) ? $basic_options['somdn_pro_limit_acc_page'] : 0 ;
		if ( ! empty( $acc_page_limits_enabled ) ) {

			add_settings_field( 
				'somdn_pro_limit_acc_page_title_enable', 
				NULL, 
				'somdn_pro_limit_acc_page_title_enable_render', 
				'somdn_pro_limit_settings', 
				'somdn_pro_basic_limit_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_pro_limit_acc_page_title', 
				NULL, 
				'somdn_pro_limit_acc_page_title_render', 
				'somdn_pro_limit_settings', 
				'somdn_pro_basic_limit_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_pro_limit_acc_page_message', 
				NULL, 
				'somdn_pro_limit_acc_page_message_render', 
				'somdn_pro_limit_settings', 
				'somdn_pro_basic_limit_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

		}

	endif;

	if ( function_exists( 'somdn_memberships' ) ) :

		if ( somdn_memberships() && $basic_enabled ) :

			register_setting( 'somdn_pro_limit_settings', 'somdn_pro_membership_limit_settings' );

			add_settings_section(
				'somdn_pro_member_limit_settings_section', 
				__( 'Membership Limit Settings', 'somdn-pro' ), 
				'somdn_pro_member_limit_settings_section_callback', 
				'somdn_pro_limit_settings'
			);

			add_settings_field( 
				'somdn_pro_limit_member_exclude', 
				__( 'Exclude Memberships', 'somdn-pro' ), 
				'somdn_pro_limit_member_exclude_render', 
				'somdn_pro_limit_settings', 
				'somdn_pro_member_limit_settings_section' 
			);

		endif;

	endif;

	do_action('somdn_after_limit_settings_section', $basic_enabled);

}

function somdn_pro_basic_limit_error_redirect_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$value = isset( $options['somdn_pro_basic_limit_error_redirect_render'] ) ? $options['somdn_pro_basic_limit_error_redirect_render'] : '' ;

	$args = array(
		'selected' => $value,
		'show_option_none' => 'None',
		'name' => 'somdn_pro_basic_limit_settings[somdn_pro_basic_limit_error_redirect_render]',
		'id' => 'somdn_pro_basic_limit_settings[somdn_pro_basic_limit_error_redirect_render]'
	);

	?>

	<p class="som-mar-bot-15"><strong>Optional: </strong>Page to redirect users when they've reached their download limit.</p>
	<p class="description som-mar-bot-15">You can use the shortcode <code>[download_limit_error]</code> on this page to show the above error message.</p>
	<div class="som-mar-bot-15"><?php wp_dropdown_pages( $args ); ?></div>
	<p class="description som-mar-bot-15">Default/None: <strong>Product Page</strong></p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_pro_limit_acc_page_title_enable_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$value = isset( $options['somdn_pro_limit_acc_page_title_enable'] ) ? $options['somdn_pro_limit_acc_page_title_enable'] : 0 ; ?>
	
	<label for="somdn_pro_basic_limit_settings[somdn_pro_limit_acc_page_title_enable]">
	<input type="checkbox" name="somdn_pro_basic_limit_settings[somdn_pro_limit_acc_page_title_enable]" id="somdn_pro_basic_limit_settings[somdn_pro_limit_acc_page_title_enable]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Show the section title heading
	</label>

	<p style="padding-top: 10px;"><?php _e( 'Displays a heading above the download limit section.', 'somdn-pro' ); ?></p>

	<hr class="som-setting-sep sep-300">

<?php

}

function somdn_pro_limit_acc_page_title_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$value = isset( $options['somdn_pro_limit_acc_page_title'] ) ? $options['somdn_pro_limit_acc_page_title'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Section Title</strong></p>

	<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the section title heading.</p>
	
	<input type="text" name="somdn_pro_basic_limit_settings[somdn_pro_limit_acc_page_title]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<p class="description">Default: <strong>Free Download Limits</strong></p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_pro_limit_acc_page_message_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$value = isset( $options['somdn_pro_limit_acc_page_message'] ) ? $options['somdn_pro_limit_acc_page_message'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Section Message</strong></p>

	<div class="som-settings-pro-basic-limit-error-wrap">

		<p class="som-mar-bot-15"><strong>Optional: </strong>Include a short section of text below the free download limits section title.</p>

		<?php

			$editor_id = 'somdn_pro_limit_acc_page_message';
			$settings = array(
				'media_buttons' => false,
				'tinymce'=> array(
					'toolbar1' => 'bold,italic,underline,alignleft,aligncenter,alignright,alignjustify,link,undo,redo',
					'toolbar2'=> false
				),
				'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
				'editor_class' => 'required',
				'teeny' => true,
				'editor_height' => 150,
				'textarea_name' => 'somdn_pro_basic_limit_settings[somdn_pro_limit_acc_page_message]'
			);
			$content = $value;

			wp_editor( $content, $editor_id, $settings );

		?>

	</div>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_pro_basic_limit_settings_section_callback() {

	$callback = '<p>' . __( 'Limit the number of times a user can download free products.</p>', 'somdn-pro' ) . '</p>';
	echo $callback;

	$limit_options = get_option( 'somdn_pro_basic_limit_settings' );
	$limits_enabled = isset( $limit_options['somdn_pro_basic_limit_enable'] ) ? $limit_options['somdn_pro_basic_limit_enable'] : 0 ;

	if ( $limits_enabled && ! somdn_download_limits_active() ) : ?>

		<div class="somdn-limit-warning-wrap">
			<p>Warning: Download limits enabled but settings not complete.</p>
		</div>

	<?php endif;

}

function somdn_pro_member_limit_settings_section_callback() { 
	echo '<p>' . __( 'Additional settings for download limitations of your site memberships.', 'somdn-pro' ) . '</p>';
}

function somdn_pro_basic_limit_enable_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$value = isset( $options['somdn_pro_basic_limit_enable'] ) ? $options['somdn_pro_basic_limit_enable'] : 0 ; ?>
	
	<label for="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_enable]">
	<input type="checkbox" name="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_enable]" id="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_enable]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Enable download limitations
	</label>

	<?php if ( ! $value ) { ?>
		<p class="description"><?php _e( 'Note: When enabled you will be able to customise the limitations.', 'somdn-pro' ); ?></p>
	<?php } ?>

<?php

}

function somdn_pro_basic_limit_type_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$value = isset( $options['somdn_pro_basic_limit_type'] ) ? $options['somdn_pro_basic_limit_type'] : 0 ; ?>

	<span class="somdn-setting-left-col">Limit downloads by</span>

	<select name="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_type]">
		<option value="0">Please choose...</option>
		<option value="1" <?php selected( $value, 1 ); ?>>User ID</option>
		<option value="2" <?php selected( $value, 2 ); ?>>IP Address</option>
	</select>

	<?php 

		$gen_options = get_option( 'somdn_gen_settings' );
		$require_login = isset( $gen_options['somdn_require_login'] ) ? $gen_options['somdn_require_login'] : false ;

		if ( $value == 1 && ! $require_login ) { ?>
			<br><br>
			<p><strong>Make sure you set <em>Only show the button to logged in users</em> on the <a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings">general settings</a> page.</strong></p><br>
		<?php } else {
			echo '<br><br>';
		}

	?>

	<p class="description"><strong><?php _e( 'Limiting by User ID only works if the "Only show the button to logged in users" general settings option is set.', 'somdn-pro' ); ?></strong></p>
	<p class="description"><?php _e( 'Any limits selected will mean downloads will be tracked in the database.', 'somdn-pro' ); ?></p>
	<p class="description">If no option selected, no limits apply.</p>

	<?php

}

function somdn_pro_basic_limit_freq_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$freq_value = isset( $options['somdn_pro_basic_limit_freq'] ) ? $options['somdn_pro_basic_limit_freq'] : '' ; ?>

	<div class="somdn-setting-group-wrap">
		<span class="somdn-setting-left-col">Download limit period</span>
		<select name="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_freq]" id="somdn_pro_basic_limit_freq">
			<option value="" <?php selected( $freq_value, '' ); ?> class="somdn_invalid_select">Please choose...</option>
			<option value="1" <?php selected( $freq_value, 1 ); ?>>Day</option>
			<option value="2" <?php selected( $freq_value, 2 ); ?>>Week</option>
			<option value="3" <?php selected( $freq_value, 3 ); ?>>Month</option>
			<option value="4" <?php selected( $freq_value, 4 ); ?>>Year</option>
		</select>
	</div>

	<p class="description">Set download limits for each day/week/month/year.</p>
	<p class="description">If no option selected, no limits apply.</p>

	<?php

}

function somdn_pro_basic_limit_amount_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$amount_value = isset( $options['somdn_pro_basic_limit_amount'] ) ? $options['somdn_pro_basic_limit_amount'] : '' ; ?>

	<div class="somdn-setting-group-wrap">
		<span class="somdn-setting-left-col">Number of downloads</span>
		<input type="number" min="0" max="1000" value="<?php echo $amount_value; ?>" name="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_amount]" id="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_amount]" class="somdn-number-input" placeholder="0 - 1000">
		<span class="description">Leave blank for unlimited.</span>
	</div>

	<p class="description">Limit the number of times a download can be requested.</p>

	<?php

}

function somdn_pro_basic_limit_products_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$amount_value = isset( $options['somdn_pro_basic_limit_products'] ) ? $options['somdn_pro_basic_limit_products'] : '' ; ?>

	<div class="somdn-setting-group-wrap">
		<span class="somdn-setting-left-col">Number of products</span>
		<input type="number" min="0" max="1000" value="<?php echo $amount_value; ?>" name="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_products]" id="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_products]" class="somdn-number-input" placeholder="0 - 1000">
		<span class="description">Leave blank for unlimited.</span>
	</div>

	<p class="description">Limit the number of different products that can be downloaded.</p>
	<p class="description">If a product limit is set, the number of downloads will apply per product.</p>

	<?php

}

function somdn_pro_basic_limit_after_render() { ?>

	<hr class="som-setting-sep sep-300 bottom">
	
	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Examples</strong></p>

	<ol class="somdn-setting-example-list">
		<li>
			<p><strong>Downloads 20, Products 0</strong></p>
			<p class="description">20 total downloads.</p>
		</li>
		<li>
			<p><strong>Downloads 5, Products 5</strong></p>
			<p class="description">5 downloads of up to 5 different products.</p>
		</li>
		<li>
			<p><strong>Downloads 0, Products 5</strong></p>
			<p class="description">Unlimited downloads of up to 5 different products.</p>
		</li>
	</ol>

	<?php

}

function somdn_pro_basic_limit_error_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$value = isset( $options['somdn_pro_basic_limit_error'] ) ? $options['somdn_pro_basic_limit_error'] : '' ; ?>

	<div class="som-settings-pro-basic-limit-error-wrap">

		<?php

			$editor_id = 'somdn_pro_basic_limit_error';
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
				'textarea_name' => 'somdn_pro_basic_limit_settings[somdn_pro_basic_limit_error]'
			);
			$content = stripslashes( $value );

			wp_editor( $content, $editor_id, $settings );

		/*

		<textarea rows="4" style="width: 500px; max-width: 100%;" name="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_error]" id="somdn_pro_basic_limit_settings[somdn_pro_basic_limit_error]"><?php esc_textarea( $value ); ?></textarea>

		*/

		?>

	</div>

	<br><p class="description"><strong>Optional: </strong>Customise the download limit reached error message.</p><br>
	<p class="description"><strong>Default for limited downloads, unlimited products:</strong><br>Your free download limit is (Number of downloads) downloads per (Download limit period).</p><br>
	<p class="description"><strong>Default for limited downloads, limited products:</strong><br>Your free download limit is (Number of downloads) downloads for (Number of products) products per (Download limit period).</p><br>
	<p class="description"><strong>Default for unlimited downloads, limited products:</strong><br>Your free download limit is (Number of products) products per (Download limit period).</p><br>

	<?php

}

function somdn_pro_limit_acc_page_render() {

	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$value = isset( $options['somdn_pro_limit_acc_page'] ) ? $options['somdn_pro_limit_acc_page'] : 0 ; ?>
	
	<label for="somdn_pro_basic_limit_settings[somdn_pro_limit_acc_page]">
	<input type="checkbox" name="somdn_pro_basic_limit_settings[somdn_pro_limit_acc_page]" id="somdn_pro_basic_limit_settings[somdn_pro_limit_acc_page]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Show customer download limits on their account page
	</label>

	<p class="description"><?php printf( __( 'Shows in the downloads section of the %s shortcode.', 'somdn-pro' ), '<span class="description">[woocommerce_my_account]</span>' ); ?></p>
	<?php if ( ! $value ) { ?>
		<p class="description"><?php _e( 'Note: When enabled you will be able to customise the display.', 'somdn-pro' ); ?></p>
	<?php } ?>

<?php

}

function somdn_pro_limit_member_exclude_render() {

	$options = get_option( 'somdn_pro_membership_limit_settings' );
	$value = isset( $options['somdn_pro_limit_member_exclude'] ) ? $options['somdn_pro_limit_member_exclude'] : 0 ; ?>
	
	<label for="somdn_pro_membership_limit_settings[somdn_pro_limit_member_exclude]">
	<input type="checkbox" name="somdn_pro_membership_limit_settings[somdn_pro_limit_member_exclude]" id="somdn_pro_membership_limit_settings[somdn_pro_limit_member_exclude]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Exclude memberships from limitations
	</label>

	<p class="description">
		<?php _e( 'Note: When enabled you can select which memberships are exlcuded from limitations in the <a href="edit.php?post_type=wc_membership_plan">Membership Plans</a> page.', 'somdn-pro' ); ?>
		</p>

<?php

}