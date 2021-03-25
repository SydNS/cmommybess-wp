<?php
/**
 * Free Downloads - WooCommerce - Pro Settings - Download Tracking
 * 
 * @version 3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'somdn_settings_page_content' , 'somdn_settings_tracking_settings', 10, 1 );
function somdn_settings_tracking_settings( $active_section ) {
	if ( $active_section == 'tracking' ) {
		somdn_track_settings_content();
	}
}

add_action( 'somdn_settings_subtabs_after_owned' , 'somdn_settings_subtabs_tracking', 50, 1 );
function somdn_settings_subtabs_tracking( $active_section ) {
	$nav_active = ( $active_section == 'tracking' ) ? 'current' : '' ;
	echo '<li><a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=tracking" class="' . $nav_active . '">Tracking & Email Capture</a> | </li>';
}

function somdn_track_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12">
	
				<form action="options.php" class="som-settings-settings-form" method="post">

					<div class="som-settings-gen-settings-form-wrap">

						<?php

							settings_fields( 'somdn_pro_track_settings' );
							somdn_do_custom_settings_sections( 'somdn_pro_track_settings' );
							submit_button();

						?>

					</div>

				</form>

			</div>

		</div>
	</div>


<?php

}

add_action( 'updated_option', 'somdn_force_tracked_downloads_update', 50, 3 );
function somdn_force_tracked_downloads_update( $option, $old_value, $value ) {
	if ( $option == 'somdn_pro_basic_limit_settings' ) {
		if ( somdn_download_limits_active() ) {
			$track_options = get_option( 'somdn_pro_track_settings' );
			$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : 0 ;
			if ( ! $track_enabled ) {
				$track_options['somdn_pro_track_enable'] = true;
				update_option( 'somdn_pro_track_settings', $track_options );
			}
		}
	}
	if ( $option == 'somdn_pro_track_settings' ) {
		if ( somdn_download_limits_active() ) {
			$track_options = get_option( 'somdn_pro_track_settings' );
			$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : 0 ;
			if ( ! $track_enabled ) {
				$track_options['somdn_pro_track_enable'] = true;
				update_option( 'somdn_pro_track_settings', $track_options );
			}
		}
	}
}

function somdn_force_tracking() {
	$force_track = array();
	$force_track['somdn_pro_track_enable'] = true;
	update_option( 'somdn_pro_track_settings', $force_track );
}

add_action( 'somdn_pro_settings', 'somdn_pro_settings_tracking', 35 );
function somdn_pro_settings_tracking() {

	register_setting( 'somdn_pro_track_settings', 'somdn_pro_track_settings' );

	add_settings_section(
		'somdn_pro_track_settings_section', 
		__( 'Track Free Downloads', 'somdn-pro' ), 
		'somdn_pro_track_settings_section_callback', 
		'somdn_pro_track_settings'
	);

	add_settings_field( 
		'somdn_pro_track_enable', 
		__( 'Track Free Downloads', 'somdn-pro' ), 
		'somdn_pro_track_enable_render', 
		'somdn_pro_track_settings', 
		'somdn_pro_track_settings_section' 
	);

	add_settings_field( 
		'somdn_capture_email_enable', 
		__( 'Capture Emails', 'somdn-pro' ), 
		'somdn_capture_email_enable_render', 
		'somdn_pro_track_settings', 
		'somdn_pro_track_settings_section'
	);

	$track_options = get_option( 'somdn_pro_track_settings' );
	$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : 0 ;
	if ( ! empty( $track_enabled ) ) {

		$capture_email_enabled = isset( $track_options['somdn_capture_email_enable'] ) ? $track_options['somdn_capture_email_enable'] : 0 ;
		if ( ! empty( $capture_email_enabled ) ) {

		add_settings_field( 
			'somdn_capture_email_users_enable', 
			NULL, 
			'somdn_capture_email_users_enable_render', 
			'somdn_pro_track_settings', 
			'somdn_pro_track_settings_section'
		);

			add_settings_field( 
				'somdn_capture_email_subscribe', 
				NULL, 
				'somdn_capture_email_subscribe_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_capture_email_title', 
				NULL, 
				'somdn_capture_email_title_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_capture_email_placeholder', 
				NULL, 
				'somdn_capture_email_placeholder_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_capture_email_error_none', 
				NULL, 
				'somdn_capture_email_errors_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_capture_email_error_invalid', 
				NULL, 
				'somdn_capture_email_errors_render',
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
			);

			add_settings_field( 
				'somdn_capture_email_title_bg', 
				NULL,
				'somdn_capture_email_title_bg_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_capture_email_title_colour', 
				NULL,
				'somdn_capture_email_title_colour_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_capture_email_body', 
				NULL,
				'somdn_capture_email_body_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_capture_email_button', 
				NULL,
				'somdn_capture_email_button_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_capture_email_demo', 
				NULL,
				'somdn_capture_email_demo_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

		}
	}

	add_settings_field( 
		'somdn_download_history_enable', 
		__( 'Download History', 'somdn-pro' ), 
		'somdn_download_history_enable_render', 
		'somdn_pro_track_settings', 
		'somdn_pro_track_settings_section' 
	);

	$track_options = get_option( 'somdn_pro_track_settings' );
	$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : 0 ;
	if ( ! empty( $track_enabled ) ) {

		$track_options = get_option( 'somdn_pro_track_settings' );
		$download_history_enabled = isset( $track_options['somdn_download_history_enable'] ) ? $track_options['somdn_download_history_enable'] : 0 ;
		if ( ! empty( $download_history_enabled ) ) {

			add_settings_field( 
				'somdn_download_history_hide_purchase', 
				NULL, 
				'somdn_download_history_hide_purchase_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_download_history_title_enable', 
				NULL, 
				'somdn_download_history_title_enable_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_download_history_title', 
				NULL, 
				'somdn_download_history_title_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_download_history_message', 
				NULL, 
				'somdn_download_history_message_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

			add_settings_field( 
				'somdn_download_history_button', 
				NULL, 
				'somdn_download_history_button_render', 
				'somdn_pro_track_settings', 
				'somdn_pro_track_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);

		}

	}

}

function somdn_pro_track_enable_render() {

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_pro_track_enable'] ) ? $options['somdn_pro_track_enable'] : 0 ; ?>
	
	<label for="somdn_pro_track_settings[somdn_pro_track_enable]">
	<input type="checkbox" name="somdn_pro_track_settings[somdn_pro_track_enable]" id="somdn_pro_track_settings[somdn_pro_track_enable]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Enable detailed free download tracking
	</label>

	<?php if ( $value ) { ?>
		<p class="description"><a href="edit.php?post_type=somdn_tracked">Click here</a> to see download records.</p>
	<?php } ?>

<?php

}

function somdn_download_history_enable_render() {

	$track_options = get_option( 'somdn_pro_track_settings' );
	$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : 0 ;
	if ( empty( $track_enabled ) ) { ?>
		<p class="description"><?php _e( 'Enable free download tracking to show account download history.', 'somdn-pro' ); ?></p>
		<?php return;
	}

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_download_history_enable'] ) ? $options['somdn_download_history_enable'] : 0 ; ?>
	
	<label for="somdn_pro_track_settings[somdn_download_history_enable]">
	<input type="checkbox" name="somdn_pro_track_settings[somdn_download_history_enable]" id="somdn_pro_track_settings[somdn_download_history_enable]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Show free download history on user account page
	</label>

	<p class="description"><?php printf( __( 'Shows in the downloads section of the %s shortcode.', 'somdn-pro' ), '<span class="description">[woocommerce_my_account]</span>' ); ?></p>
	<p style="padding-top: 10px;"><?php _e( 'Only includes products the user has already downloaded for free and is still eligible to download.', 'somdn-pro' ); ?></p>

	<?php if ( ! $value ) { ?>
		<p class="description"><?php _e( 'Note: When enabled you will be able to customise the display.', 'somdn-pro' ); ?></p>
	<?php } else { ?>
		<hr class="som-setting-sep sep-300">
	<?php } ?>

<?php

}

function somdn_download_history_hide_purchase_render() {

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_download_history_hide_purchase'] ) ? $options['somdn_download_history_hide_purchase'] : 0 ; ?>
	
	<label for="somdn_pro_track_settings[somdn_download_history_hide_purchase]">
	<input type="checkbox" name="somdn_pro_track_settings[somdn_download_history_hide_purchase]" id="somdn_pro_track_settings[somdn_download_history_hide_purchase]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Hide purchased downloads section from account page
	</label>

	<p class="description"><strong>Note:</strong> Only applies if the user has no purchased downloads.</p>

	<hr class="som-setting-sep sep-300">

<?php

}

function somdn_download_history_title_enable_render() {

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_download_history_title_enable'] ) ? $options['somdn_download_history_title_enable'] : 0 ; ?>
	
	<label for="somdn_pro_track_settings[somdn_download_history_title_enable]">
	<input type="checkbox" name="somdn_pro_track_settings[somdn_download_history_title_enable]" id="somdn_pro_track_settings[somdn_download_history_title_enable]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Show the section title heading
	</label>

	<p style="padding-top: 10px;"><?php _e( 'Displays a heading above the download list.', 'somdn-pro' ); ?></p>

	<hr class="som-setting-sep sep-300">

<?php

}

function somdn_download_history_title_render() {

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_download_history_title'] ) ? $options['somdn_download_history_title'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Section Title</strong></p>

	<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the section title heading.</p>
	
	<input type="text" name="somdn_pro_track_settings[somdn_download_history_title]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<p class="description">Default: <strong>Free Downloads</strong></p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_download_history_message_render() {

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_download_history_message'] ) ? $options['somdn_download_history_message'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Section Message</strong></p>

	<div class="som-settings-pro-basic-limit-error-wrap">

		<p class="som-mar-bot-15"><strong>Optional: </strong>Include a short section of text below the free downloads section title.</p>

		<?php

			$editor_id = 'somdn_download_history_message';
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
				'textarea_name' => 'somdn_pro_track_settings[somdn_download_history_message]'
			);
			$content = $value;

			wp_editor( $content, $editor_id, $settings );

		?>

	</div>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_download_history_button_render() { 

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_download_history_button'] ) ? $options['somdn_download_history_button'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Button Text</strong></p>

	<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the download button text.</p>
	
	<input type="text" name="somdn_pro_track_settings[somdn_download_history_button]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<p class="description">Default: <strong>Download</strong></p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_capture_email_demo_render() { ?>

	<div style="padding: 15px 0 20px;">
		<p><strong style="font-size: 18px;">Default capture window appearance</strong></p>
	</div>
	<?php $somdn_image_01 = plugins_url( '/assets/images/somdn-capture-emails.jpg', dirname( __FILE__ ) ); ?>
	<div class="som-settings-guide-img" style="text-align: left;">
		<img src="<?php echo $somdn_image_01; ?>">
	</div>

<?php }


function somdn_capture_email_enable_render() {

	$track_options = get_option( 'somdn_pro_track_settings' );
	$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : 0 ;
	if ( empty( $track_enabled ) ) { ?>
		<p class="description"><?php _e( 'Enable free download tracking to capture guest emails.', 'somdn-pro' ); ?></p>
		<?php return;
	}

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_enable'] ) ? $options['somdn_capture_email_enable'] : 0 ; ?>
	
	<label for="somdn_pro_track_settings[somdn_capture_email_enable]">
	<input type="checkbox" name="somdn_pro_track_settings[somdn_capture_email_enable]" id="somdn_pro_track_settings[somdn_capture_email_enable]"
	<?php
		$checked = isset( $options['somdn_capture_email_enable'] ) ? checked( $options['somdn_capture_email_enable'], true ) : '' ;
	?>
		value="1">
	Ask guest users for their email address before downloading
	</label>
	<?php if ( empty( $value ) ) { ?>
		<p class="description"><?php _e( 'Once enabled you can also enable the option to show the email capture window for registered users, and customise the capture box.', 'somdn-pro' ); ?></p>
	<?php } else { ?>
		<hr class="som-setting-sep sep-300">
	<?php } ?>	

	<?php

}

function somdn_capture_email_users_enable_render() {

	$track_options = get_option( 'somdn_pro_track_settings' );
	$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : 0 ;
	if ( empty( $track_enabled ) ) { ?>
		<p class="description"><?php _e( 'Enable free download tracking to capture guest emails.', 'somdn-pro' ); ?></p>
		<?php return;
	}

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_users_enable'] ) ? $options['somdn_capture_email_users_enable'] : 0 ; ?>
	
	<label for="somdn_pro_track_settings[somdn_capture_email_users_enable]">
	<input type="checkbox" name="somdn_pro_track_settings[somdn_capture_email_users_enable]" id="somdn_pro_track_settings[somdn_capture_email_users_enable]"
	<?php
		$checked = isset( $options['somdn_capture_email_users_enable'] ) ? checked( $options['somdn_capture_email_users_enable'], true ) : '' ;
	?>
		value="1">
	Show the email capture window for registered users
	</label>
	<p class="description"><?php _e( 'Note: The email capture window will already populate the email address of registered users.', 'somdn-pro' ); ?></p>

	<hr class="som-setting-sep sep-300">	

	<?php

}

function somdn_capture_email_subscribe_render() {

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_subscribe'] ) ? $options['somdn_capture_email_subscribe'] : 0 ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Newsletter Subscription Option</strong></p>

	<p class="som-mar-bot-15"><strong>Optional: </strong>Choose if you want to subscribe users to your newsletter.</p>

	<?php
		$sub_options = array(
			'manual' => 'Manual',
			'mailchimp' => 'MailChimp'
		);
		$somdn_sub_options = apply_filters( 'somdn_sub_options', $sub_options );
	?>
	
	<p class="som-mar-bot-15">
	<select name="somdn_pro_track_settings[somdn_capture_email_subscribe]">
		<option value="0">— None —</option>
		<?php foreach ( $somdn_sub_options as $option_key => $option_name ) {
			echo '<option value="' . $option_key . '" ' . selected( $value, $option_key, false ) . '>' . $option_name . '</option>';
		} ?>
	</select>
	</p>

	<?php if ( empty( $value ) ) { ?>

		<p class="description"><?php _e( 'Note: If selected you can customise on the "Newsletter Options" setting page.', 'somdn-pro' ); ?></p>

	<?php } else { ?>

	<?php $newsletter_url = somdn_get_plugin_link_full() . '&tab=settings&section=newsletter'; ?>

		<p class="som-mar-bot-15"><?php printf( __( 'You can customise this setting on the <a href="%s">Newsletter Options</a> setting page.', 'somdn-pro' ), $newsletter_url ); ?></p>

	<?php } ?>

	<p class="description"><?php _e( 'Note: "Manual" is just if you\'re manually adding people to a newsletter without a 3rd party service.', 'somdn-pro' ); ?></p>

	<br>

	<?php if ( empty( $value ) ) { ?>

		<p><strong><?php _e( 'If you just want to take extra information like first name etc without a newsletter, enable the "Manual" option.<br> Then you can customise the fields on the Newsletter Options setting page. Make sure you select "None" as the display type.', 'somdn-pro' ); ?></strong></p>

	<?php } else { ?>

	<?php $newsletter_url = somdn_get_plugin_link_full() . '&tab=settings&section=newsletter'; ?>

		<p>
			<strong>
			<?php printf( __( 'If you just want to take extra information like first name etc without a newsletter, enable the "Manual" option.<br> Then you can customise the fields on the <a href="%s">Newsletter Options</a> setting page. Make sure you select "None" as the display type.', 'somdn-pro' ), $newsletter_url ); ?>
			<strong>
		</p>

	<?php } ?>





	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_capture_email_title_render() { 

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_title'] ) ? $options['somdn_capture_email_title'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Box Title</strong></p>

	<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the capture email box title.</p>
	
	<input type="text" name="somdn_pro_track_settings[somdn_capture_email_title]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<p class="description">Default: <strong>Download FREE!</strong></p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_capture_email_placeholder_render() { 

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_placeholder'] ) ? $options['somdn_capture_email_placeholder'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Email Placeholder</strong></p>

	<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the email address input field placeholder.</p>

	<input type="text" name="somdn_pro_track_settings[somdn_capture_email_placeholder]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<p class="description">Default: <strong>Email address...</strong></p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_capture_email_errors_render( $args ) {

	if ( ! empty( $args[ 'context' ] ) && 'extra' === $args[ 'context' ] )
		return;

	$options = get_option( 'somdn_pro_track_settings' );
	$error_none = isset( $options['somdn_capture_email_error_none'] ) ? $options['somdn_capture_email_error_none'] : '' ;
	$error_invalid = isset( $options['somdn_capture_email_error_invalid'] ) ? $options['somdn_capture_email_error_invalid'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Email Error Messages</strong></p>

	<p><strong>Optional: </strong>Customise the error message if no email address entered.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Please enter your email address</strong></p>

	<input type="text" name="somdn_pro_track_settings[somdn_capture_email_error_none]" value="<?php echo $error_none; ?>" style="width: 300px; max-width: 100%;">

	<br><br>

	<p><strong>Optional: </strong>Customise the error message if an invalid email address is entered.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Please enter a valid email address</strong></p>

	<input type="text" name="somdn_pro_track_settings[somdn_capture_email_error_invalid]" value="<?php echo $error_invalid; ?>" style="width: 300px; max-width: 100%;">

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_capture_email_title_bg_render() {

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_title_bg'] ) ? $options['somdn_capture_email_title_bg'] : '#2679ce' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Title Background Colour</strong></p>

	<div class="somdn-wp-picker-container">
		<input type="text" name="somdn_pro_track_settings[somdn_capture_email_title_bg]" id="somdn-capture-emails-bg-colour" value="<?php echo $value; ?>" class="somdn-colour-picker" data-default-color="#2679ce">
	</div>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_capture_email_title_colour_render() {

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_title_colour'] ) ? $options['somdn_capture_email_title_colour'] : '#fff' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Title Font Colour</strong></p>

	<div class="somdn-wp-picker-container">
		<input type="text" name="somdn_pro_track_settings[somdn_capture_email_title_colour]" id="somdn-capture-emails-title-colour" value="<?php echo $value; ?>" class="somdn-colour-picker" data-default-color="#fff">
	</div>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_capture_email_body_render() {

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_body'] ) ? $options['somdn_capture_email_body'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Box Text</strong></p>

	<div class="som-settings-pro-basic-limit-error-wrap">

		<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the capture email message. Use <strong>{product}</strong> to include the product name.</p>

		<?php

			$editor_id = 'somdn_capture_email_body';
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
				'textarea_name' => 'somdn_pro_track_settings[somdn_capture_email_body]'
			);
			$content = $value;

			wp_editor( $content, $editor_id, $settings );

		?>

	</div>

	<br>
	<p class="description"><strong>Default:</strong> To download (product title) today just enter your email address!</p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_capture_email_button_render() { 

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_button'] ) ? $options['somdn_capture_email_button'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Button Text</strong></p>

	<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the download button text.</p>
	
	<input type="text" name="somdn_pro_track_settings[somdn_capture_email_button]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<p class="description">Default: <strong>Submit & Download</strong></p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_pro_track_settings_section_callback() { 
	echo '<p>' . __( 'Create a detailed record each time a free download is made, enable guest email capture, and enable free download history display on user accounts.', 'somdn-pro' ) . '</p>';
	echo '<p class="description">' . __( 'This feature is forced on if download limitations are enabled.', 'somdn-pro' ) . '</p><br>';
}

add_action( 'restrict_manage_posts', 'somdn_tracked_filter', 10 );
function somdn_tracked_filter( $post_type ) {

	if ( 'somdn_tracked' !== $post_type ) {
		return;
	}

	$search_term = '';
	if ( isset( $_REQUEST['somdn_tracked_user_filter'] ) ) {
		$search_term = sanitize_text_field( $_REQUEST['somdn_tracked_user_filter'] );
	}

	echo '<input type="text" class="somdn-tracked-input-filter" id="somdn_tracked_user_filter" name="somdn_tracked_user_filter" value="' . $search_term . '" placeholder="User Login / ID / IP Address">';

}

add_filter( 'parse_query', 'somdn_tracked_filter_query', 99 );
function somdn_tracked_filter_query( $query ){

	// Return if not in the admin dashboard and not the main query
	if ( !( is_admin() && $query->is_main_query() ) ) { 
		return $query;
	}
	// If not a free download log and the filte ris not set, return
	if ( ! ( 'somdn_tracked' === $query->query['post_type'] && isset( $_REQUEST['somdn_tracked_user_filter'] ) ) ){
		return $query;
	}
	
	// Check for the search term and sanitize it
	$search_term = isset( $_REQUEST['somdn_tracked_user_filter'] ) ? sanitize_text_field( $_REQUEST['somdn_tracked_user_filter'] ) : '' ;

	// Check for empty and zero value
	if ( ! ( isset( $search_term ) && $search_term != '' ) ) {
		// Nothing to process, return original query
		return $query;		
	}

	// Something is being filtered for, let's get on with it

	$no_results = false;

	// First check if we're searching for an IP Address
	if ( filter_var( $search_term, FILTER_VALIDATE_IP ) !== false ) {
		// Search term is an IP
		$meta_key = 'somdn_user_ip';
		//modify the query_vars.
		$query->set( 'meta_query', array(
				'key'     => $meta_key,
				'value'   => $search_term,
				'compare' => '='
			)
		);
	// Check if a number is being searched for
	} elseif ( filter_var( $search_term, FILTER_VALIDATE_INT, array( 'min_range' => 0 ) ) !== FALSE ) {
		// is a number (assume User ID)
		$user_id = intval( $search_term);
		$user = get_user_by( 'ID', $user_id );
		if ( ! empty( $user ) ) {
			//$user_id = $user->user_login;
			$query->set( 'author', $user_id );
		} else {
			$no_results = true;
		}
	// Default, just search for a user by username
	} else {
		// user string (assume User login)
		$user = get_user_by( 'login', $search_term );
		if ( ! empty( $user ) ) {
			$user_id = $user->ID;
			$query->set( 'author__in', $user_id );
		} else {
			$no_results = true;
		}
	}
/*
echo '<pre>';
print_r($query);
echo '</pre>';
exit;
*/
	if ( $no_results == true ) {
		$query->set( 'post__in', array( 0 ) );
	}

	return $query;
}