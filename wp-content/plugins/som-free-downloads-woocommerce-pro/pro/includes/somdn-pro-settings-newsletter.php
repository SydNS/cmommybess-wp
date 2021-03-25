<?php
/**
 * Free Downloads - WooCommerce - Pro Settings - Newsletter
 * 
 * @version 3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( SOMDN_PATH_PRO . 'addons/mailchimp/mailchimp-newsletter.php' );

add_action( 'somdn_settings_page_content' , 'somdn_settings_newsletter_settings', 10, 1 );
function somdn_settings_newsletter_settings( $active_section ) {

	// Only show this tab is the subscribe to newsletter option is enabled
	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_subscribe'] ) ? $options['somdn_capture_email_subscribe'] : 0 ;
	if ( empty( $value ) ) {
		return;
	}

	if ( $active_section == 'newsletter' ) {
		somdn_newsletter_settings_content();
	}

}

add_action( 'somdn_settings_subtabs_after_owned' , 'somdn_settings_subtabs_newsletter', 65, 1 );
function somdn_settings_subtabs_newsletter( $active_section ) {

	// Only show this tab is the subscribe to newsletter option is enabled
	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_capture_email_subscribe'] ) ? $options['somdn_capture_email_subscribe'] : 0 ;
	if ( empty( $value ) ) {
		return;
	}
	$nav_active = ( $active_section == 'newsletter' ) ? 'current' : '' ;
	echo '<li><a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=newsletter" class="' . $nav_active . '">Newsletter Options</a> | </li>';

}

function somdn_newsletter_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="som-settings-gen-settings-form-wrap">

						<?php

							settings_fields( 'somdn_pro_newsletter_settings' );
							somdn_do_custom_settings_sections( 'somdn_pro_newsletter_settings' );
							submit_button();

						?>

					</div>
			
				</form>
		
			</div>

		</div>
	</div>


<?php

}

add_action( 'somdn_pro_settings', 'somdn_pro_settings_newsletter', 50 );
function somdn_pro_settings_newsletter() {

	$track_options = get_option( 'somdn_pro_track_settings' );
	$capture_email_enabled = isset( $track_options['somdn_capture_email_enable'] ) ? $track_options['somdn_capture_email_enable'] : 0 ;
	$subscribe_option = isset( $track_options['somdn_capture_email_subscribe'] ) ? $track_options['somdn_capture_email_subscribe'] : 0 ;

	if ( ! empty( $capture_email_enabled ) && ! empty( $subscribe_option ) ) {

		register_setting( 'somdn_pro_newsletter_settings', 'somdn_pro_newsletter_general_settings' );

		add_settings_section(
			'somdn_pro_newsletter_general_settings_section',
			__( 'General Newsletter Settings', 'somdn-pro' ),
			'somdn_pro_newsletter_general_settings_section_callback',
			'somdn_pro_newsletter_settings'
		);

		$newsletter_options = get_option( 'somdn_pro_newsletter_settings' );
		$newsletter_general_options = get_option( 'somdn_pro_newsletter_general_settings' );

		add_settings_field(
			'somdn_newsletter_display_type',
			__( 'Display Type', 'somdn-pro' ),
			'somdn_newsletter_display_type_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section'
		);

		add_settings_field(
			'somdn_newsletter_checkbox_error', 
			NULL,
			'somdn_newsletter_checkbox_error_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section',
			array( 'class' => 'somdn-settings-table-no-top checkbox-error-setting-wrap' )
		);

		add_settings_field(
			'somdn_newsletter_text',
			__( 'Text', 'somdn-pro' ),
			'somdn_newsletter_text_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section'
		);
/*
		add_settings_field(
			'somdn_newsletter_fname',
			__( 'Fields', 'somdn-pro' ),
			'somdn_newsletter_fname_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section'
		);
*/
		add_settings_field( 
			'somdn_newsletter_fname_placeholder', 
			__( 'Fields', 'somdn-pro' ),
			'somdn_newsletter_fname_content_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section'
		);
		add_settings_field( 
			'somdn_newsletter_fname_error', 
			NULL,
			'somdn_newsletter_fname_content_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section',
			array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
		);
		add_settings_field(
			'somdn_newsletter_lname',
			NULL,
			'somdn_newsletter_lname_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section',
			array( 'class' => 'somdn-settings-table-no-top' )
		);

		$newsletter_general_options = get_option( 'somdn_pro_newsletter_general_settings' );

		$newsletter_lname_enabled = isset( $newsletter_general_options['somdn_newsletter_lname'] ) ? $newsletter_general_options['somdn_newsletter_lname'] : 0 ;
		if ( ! empty( $newsletter_lname_enabled ) ) {
			add_settings_field( 
				'somdn_newsletter_lname_placeholder',
				NULL,
				'somdn_newsletter_lname_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);
			add_settings_field( 
				'somdn_newsletter_lname_required',
				NULL,
				'somdn_newsletter_lname_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
			);
			add_settings_field( 
				'somdn_newsletter_lname_error', 
				NULL,
				'somdn_newsletter_lname_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
			);
		}

		add_settings_field(
			'somdn_newsletter_tel',
			NULL,
			'somdn_newsletter_tel_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section',
			array( 'class' => 'somdn-settings-table-no-top' )
		);

		$newsletter_tel_enabled = isset( $newsletter_general_options['somdn_newsletter_tel'] ) ? $newsletter_general_options['somdn_newsletter_tel'] : 0 ;
		if ( ! empty( $newsletter_tel_enabled ) ) {
			add_settings_field( 
				'somdn_newsletter_tel_placeholder',
				NULL,
				'somdn_newsletter_tel_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);
			add_settings_field( 
				'somdn_newsletter_tel_required',
				NULL,
				'somdn_newsletter_tel_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
			);
			add_settings_field( 
				'somdn_newsletter_tel_error', 
				NULL,
				'somdn_newsletter_tel_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
			);
		}

		add_settings_field(
			'somdn_newsletter_company',
			NULL,
			'somdn_newsletter_company_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section',
			array( 'class' => 'somdn-settings-table-no-top' )
		);

		$newsletter_company_enabled = isset( $newsletter_general_options['somdn_newsletter_company'] ) ? $newsletter_general_options['somdn_newsletter_company'] : 0 ;
		if ( ! empty( $newsletter_company_enabled ) ) {
			add_settings_field( 
				'somdn_newsletter_company_placeholder',
				NULL,
				'somdn_newsletter_company_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);
			add_settings_field( 
				'somdn_newsletter_company_required',
				NULL,
				'somdn_newsletter_company_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
			);
			add_settings_field( 
				'somdn_newsletter_company_error', 
				NULL,
				'somdn_newsletter_company_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
			);
		}

		add_settings_field(
			'somdn_newsletter_website',
			NULL,
			'somdn_newsletter_website_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section',
			array( 'class' => 'somdn-settings-table-no-top' )
		);

		$newsletter_website_enabled = isset( $newsletter_general_options['somdn_newsletter_website'] ) ? $newsletter_general_options['somdn_newsletter_website'] : 0 ;
		if ( ! empty( $newsletter_website_enabled ) ) {
			add_settings_field( 
				'somdn_newsletter_website_placeholder',
				NULL,
				'somdn_newsletter_website_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top' )
			);
			add_settings_field( 
				'somdn_newsletter_website_required',
				NULL,
				'somdn_newsletter_website_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
			);
			add_settings_field( 
				'somdn_newsletter_website_error', 
				NULL,
				'somdn_newsletter_website_content_render',
				'somdn_pro_newsletter_settings',
				'somdn_pro_newsletter_general_settings_section',
				array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
			);
		}

		add_settings_field( 
			'somdn_newsletter_confirmations', 
			__( 'Confirmations', 'somdn-pro' ),
			'somdn_newsletter_confirmations_content_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section'
		);

		add_settings_field( 
			'somdn_newsletter_confirmations_errors', 
			NULL,
			'somdn_newsletter_confirmations_content_render',
			'somdn_pro_newsletter_settings',
			'somdn_pro_newsletter_general_settings_section',
			array( 'class' => 'somdn-settings-table-no-top extra', 'context' => 'extra' )
		);

		if ( ! empty( $subscribe_option ) ) :

			// Manual selected
			if ( $subscribe_option == 'manual' ) :

				register_setting( 'somdn_pro_newsletter_settings', 'somdn_pro_newsletter_manual_settings' );
				$newsletter_manual_options = get_option( 'somdn_pro_newsletter_manual_settings' );

				add_settings_section(
					'somdn_pro_newsletter_manual_settings_section',
					__( 'Manual Settings', 'somdn-pro' ),
					'somdn_pro_newsletter_manual_settings_section_callback',
					'somdn_pro_newsletter_settings'
				);

				add_settings_field(
					'somdn_newsletter_manual_info',
					NULL,
					'somdn_newsletter_manual_info_render',
					'somdn_pro_newsletter_settings',
					'somdn_pro_newsletter_manual_settings_section',
					array( 'class' => 'somdn_newsletter_manual_info' )
				);

			endif;

			// MailChimp selected
			if ( $subscribe_option == 'mailchimp' ) :

				register_setting( 'somdn_pro_newsletter_settings', 'somdn_pro_newsletter_mailchimp_settings' );

				add_settings_section(
					'somdn_pro_newsletter_mailchimp_settings_section',
					__( 'MailChimp Settings', 'somdn-pro' ),
					'somdn_pro_newsletter_mailchimp_settings_section_callback',
					'somdn_pro_newsletter_settings'
				);

				add_settings_field(
					'somdn_newsletter_mailchimp_api_key',
					__( 'API Key', 'somdn-pro' ),
					'somdn_newsletter_mailchimp_api_key_render',
					'somdn_pro_newsletter_settings',
					'somdn_pro_newsletter_mailchimp_settings_section'
				);

				add_settings_field(
					'somdn_newsletter_mailchimp_list_id',
					__( 'List ID', 'somdn-pro' ),
					'somdn_newsletter_mailchimp_list_id_render',
					'somdn_pro_newsletter_settings',
					'somdn_pro_newsletter_mailchimp_settings_section'
				);

				add_settings_field(
					'somdn_newsletter_mailchimp_doubleoptin',
					__( 'Double Opt-In', 'somdn-pro' ),
					'somdn_newsletter_mailchimp_doubleoptin_render',
					'somdn_pro_newsletter_settings',
					'somdn_pro_newsletter_mailchimp_settings_section'
				);

			endif;

			do_action( 'somdn_pro_newsletter_addons', $subscribe_option );

		endif;

		do_action( 'somdn_pro_newsletter_settings', $subscribe_option );

	}

}

function somdn_pro_newsletter_general_settings_section_callback() {
	echo __( 'General settings for subscribing users to your newsletter.', 'somdn-pro' );
}

function somdn_pro_newsletter_manual_settings_section_callback() {
}

function somdn_pro_newsletter_mailchimp_settings_section_callback() {
	echo __( 'Your mailchimp account and list settings.', 'somdn-pro' );
}

function somdn_newsletter_display_type_render() {

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$value = isset( $options['somdn_newsletter_display_type'] ) ? $options['somdn_newsletter_display_type'] : '' ; ?>

	<p class="som-mar-bot-15">Choose how to let your users subscribe to your newsletter.</p>
	
	<p class="som-mar-bot-15">
	<select name="somdn_pro_newsletter_general_settings[somdn_newsletter_display_type]" id="somdn_pro_newsletter_display_type_select">
		<option value="0" <?php selected( $value, 0 ); ?>>Checkbox with Text</option>
		<option value="3" <?php selected( $value, 3 ); ?>>Required Checkbox with Text</option>
		<option value="1" <?php selected( $value, 1 ); ?>>Text Only</option>
		<option value="2" <?php selected( $value, 2 ); ?>>None</option>
	</select>
	</p>

	<p class="description"><?php _e( '<strong>Checkbox with Text:</strong> Default. The user has to tick a box to subscribe and you can display some text.', 'somdn-pro' ); ?></p>
	<p class="description"><?php _e( '<strong>Required Checkbox with Text:</strong> The user is required to tick a box to subscribe/download and you can display some text.', 'somdn-pro' ); ?></p>
	<p class="description"><?php _e( '<strong>Text Only:</strong> Add a short line of text above the download button in the email capture window.', 'somdn-pro' ); ?></p>
	<p style="margin-bottom: 15px;" class="description"><?php _e( '<strong>None:</strong> When the user clicks download they will be subscribed. You might want to change your email capture window text to let them know.', 'somdn-pro' ); ?></p>

	<p><?php _e( 'Essentially only "Checkbox with Text" allows the user the option to subscribe. The others are forced.', 'somdn-pro' ); ?></p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_newsletter_checkbox_error_render( $args ) {

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$error = isset( $options['somdn_newsletter_checkbox_error'] ) ? $options['somdn_newsletter_checkbox_error'] : '' ; ?>

	<p><strong>Optional: </strong>Customise the error message if the checkbox isn't selected.</p>
	<p class="description"><strong>Note:</strong> Only applies if Display Type is "Required Checkbox with Text".</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>You must select the checkbox to download</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_checkbox_error]" value="<?php echo $error; ?>" style="width: 300px; max-width: 100%;">

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_newsletter_text_render() {

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$value = isset( $options['somdn_newsletter_text'] ) ? $options['somdn_newsletter_text'] : '' ; ?>

	<p class="som-mar-bot-15">Customise the "Subscribe to our newsletter" text.</p>
	
	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_text]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Subscribe to our newsletter</strong></p>
	<p class="description"><strong>Note:</strong> Only applies to "Checkbox with Text" and "Text Only" display options.</p>
	<?php

}

function somdn_newsletter_fname_content_render( $args ) { 

	if ( ! empty( $args[ 'context' ] ) && 'extra' === $args[ 'context' ] )
		return;

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$placeholder = isset( $options['somdn_newsletter_fname_placeholder'] ) ? $options['somdn_newsletter_fname_placeholder'] : '' ;
	$error = isset( $options['somdn_newsletter_fname_error'] ) ? $options['somdn_newsletter_fname_error'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>First Name</strong></p>
	<p class="som-mar-bot-15"><strong>First name is always required.</strong></p>

	<p><strong>Optional: </strong>Customise the first name input field placeholder.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Your first name...</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_fname_placeholder]" value="<?php echo $placeholder; ?>" style="width: 300px; max-width: 100%;">

	<br><br>

	<p><strong>Optional: </strong>Customise the error message if no first name entered.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Please enter your first name</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_fname_error]" value="<?php echo $error; ?>" style="width: 300px; max-width: 100%;">

	<hr class="som-setting-sep sep-300">

<?php

}

function somdn_newsletter_lname_render() { 

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$value = isset( $options['somdn_newsletter_lname'] ) ? $options['somdn_newsletter_lname'] : 0 ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Last Name</strong></p>
	
	<label for="somdn_pro_newsletter_general_settings[somdn_newsletter_lname]">
	<input type="checkbox" name="somdn_pro_newsletter_general_settings[somdn_newsletter_lname]" id="somdn_pro_newsletter_general_settings[somdn_newsletter_lname]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Ask for the user's last name
	</label>

	<?php if ( empty( $value ) ) { ?>
		<p><?php _e( 'Once enabled you can customise the last name field settings.', 'somdn-pro' ); ?></p>
		<hr class="som-setting-sep sep-300">
	<?php } ?>

<?php

}

function somdn_newsletter_lname_content_render( $args ) {

	if ( ! empty( $args[ 'context' ] ) && 'extra' === $args[ 'context' ] )
		return;

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$placeholder = isset( $options['somdn_newsletter_lname_placeholder'] ) ? $options['somdn_newsletter_lname_placeholder'] : '' ;
	$required = isset( $options['somdn_newsletter_lname_required'] ) ? $options['somdn_newsletter_lname_required'] : '' ;
	$error = isset( $options['somdn_newsletter_lname_error'] ) ? $options['somdn_newsletter_lname_error'] : '' ; ?>

	<p><strong>Optional: </strong>Customise the last name input field placeholder.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Your last name...</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_lname_placeholder]" value="<?php echo $placeholder; ?>" style="width: 300px; max-width: 100%;">

	<br>

	<label for="somdn_pro_newsletter_general_settings[somdn_newsletter_lname_required]">
	<input type="checkbox" name="somdn_pro_newsletter_general_settings[somdn_newsletter_lname_required]" id="somdn_pro_newsletter_general_settings[somdn_newsletter_lname_required]"
	<?php
		$checked = isset( $required ) ? checked( $required, true ) : '' ;
	?>
		value="1">
	Make last name required
	</label>

	<br><br>

	<p><strong>Optional: </strong>Customise the error message if no last name entered.</p>
	<p class="description"><strong>Note:</strong> Only applies if last name is required.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Please enter your last name</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_lname_error]" value="<?php echo $error; ?>" style="width: 300px; max-width: 100%;">

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_newsletter_tel_render() { 

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$value = isset( $options['somdn_newsletter_tel'] ) ? $options['somdn_newsletter_tel'] : 0 ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Telephone Number</strong></p>
	
	<label for="somdn_pro_newsletter_general_settings[somdn_newsletter_tel]">
	<input type="checkbox" name="somdn_pro_newsletter_general_settings[somdn_newsletter_tel]" id="somdn_pro_newsletter_general_settings[somdn_newsletter_tel]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Ask for the user's telephone number
	</label>

	<?php if ( empty( $value ) ) { ?>
		<p><?php _e( 'Once enabled you can customise the telephone field settings.', 'somdn-pro' ); ?></p>
		<hr class="som-setting-sep sep-300">
	<?php } ?>

<?php

}

function somdn_newsletter_tel_content_render( $args ) {

	if ( ! empty( $args[ 'context' ] ) && 'extra' === $args[ 'context' ] )
		return;

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$placeholder = isset( $options['somdn_newsletter_tel_placeholder'] ) ? $options['somdn_newsletter_tel_placeholder'] : '' ;
	$required = isset( $options['somdn_newsletter_tel_required'] ) ? $options['somdn_newsletter_tel_required'] : '' ;
	$error = isset( $options['somdn_newsletter_tel_error'] ) ? $options['somdn_newsletter_tel_error'] : '' ; ?>

	<p><strong>Optional: </strong>Customise the telephone input field placeholder.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Your telephone number...</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_tel_placeholder]" value="<?php echo $placeholder; ?>" style="width: 300px; max-width: 100%;">

	<br>

	<label for="somdn_pro_newsletter_general_settings[somdn_newsletter_tel_required]">
	<input type="checkbox" name="somdn_pro_newsletter_general_settings[somdn_newsletter_tel_required]" id="somdn_pro_newsletter_general_settings[somdn_newsletter_tel_required]"
	<?php
		$checked = isset( $required ) ? checked( $required, true ) : '' ;
	?>
		value="1">
	Make telephone number required
	</label>

	<br><br>

	<p><strong>Optional: </strong>Customise the error message if no telephone number entered.</p>
	<p class="description"><strong>Note:</strong> Only applies if telephone number is required.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Please enter your telephone number</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_tel_error]" value="<?php echo $error; ?>" style="width: 300px; max-width: 100%;">

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_newsletter_company_render() {

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$value = isset( $options['somdn_newsletter_company'] ) ? $options['somdn_newsletter_company'] : 0 ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Company Name</strong></p>
	
	<label for="somdn_pro_newsletter_general_settings[somdn_newsletter_company]">
	<input type="checkbox" name="somdn_pro_newsletter_general_settings[somdn_newsletter_company]" id="somdn_pro_newsletter_general_settings[somdn_newsletter_company]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Ask for the user's company name
	</label>

	<?php if ( empty( $value ) ) { ?>
		<p><?php _e( 'Once enabled you can customise the company name field settings.', 'somdn-pro' ); ?></p>
		<hr class="som-setting-sep sep-300">
	<?php } ?>

<?php

}

function somdn_newsletter_company_content_render( $args ) {

	if ( ! empty( $args[ 'context' ] ) && 'extra' === $args[ 'context' ] )
		return;

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$placeholder = isset( $options['somdn_newsletter_company_placeholder'] ) ? $options['somdn_newsletter_company_placeholder'] : '' ;
	$required = isset( $options['somdn_newsletter_company_required'] ) ? $options['somdn_newsletter_company_required'] : '' ;
	$error = isset( $options['somdn_newsletter_company_error'] ) ? $options['somdn_newsletter_company_error'] : '' ; ?>

	<p><strong>Optional: </strong>Customise the company name input field placeholder.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Your company name...</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_company_placeholder]" value="<?php echo $placeholder; ?>" style="width: 300px; max-width: 100%;">

	<br>

	<label for="somdn_pro_newsletter_general_settings[somdn_newsletter_company_required]">
	<input type="checkbox" name="somdn_pro_newsletter_general_settings[somdn_newsletter_company_required]" id="somdn_pro_newsletter_general_settings[somdn_newsletter_company_required]"
	<?php
		$checked = isset( $required ) ? checked( $required, true ) : '' ;
	?>
		value="1">
	Make company name required
	</label>

	<br><br>

	<p><strong>Optional: </strong>Customise the error message if no company name entered.</p>
	<p class="description"><strong>Note:</strong> Only applies if company name is required.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Please enter your company name</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_company_error]" value="<?php echo $error; ?>" style="width: 300px; max-width: 100%;">

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_newsletter_website_render() {

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$value = isset( $options['somdn_newsletter_website'] ) ? $options['somdn_newsletter_website'] : 0 ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Website Address</strong></p>
	
	<label for="somdn_pro_newsletter_general_settings[somdn_newsletter_website]">
	<input type="checkbox" name="somdn_pro_newsletter_general_settings[somdn_newsletter_website]" id="somdn_pro_newsletter_general_settings[somdn_newsletter_website]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Ask for the user's website
	</label>

	<?php if ( empty( $value ) ) { ?>
		<p><?php _e( 'Once enabled you can customise the website field settings.', 'somdn-pro' ); ?></p>
		<hr class="som-setting-sep sep-300">
	<?php } ?>

<?php

}

function somdn_newsletter_website_content_render( $args ) {

	if ( ! empty( $args[ 'context' ] ) && 'extra' === $args[ 'context' ] )
		return;

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$placeholder = isset( $options['somdn_newsletter_website_placeholder'] ) ? $options['somdn_newsletter_website_placeholder'] : '' ;
	$required = isset( $options['somdn_newsletter_website_required'] ) ? $options['somdn_newsletter_website_required'] : '' ;
	$error = isset( $options['somdn_newsletter_website_error'] ) ? $options['somdn_newsletter_website_error'] : '' ; ?>

	<p><strong>Optional: </strong>Customise the website input field placeholder.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Your website...</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_website_placeholder]" value="<?php echo $placeholder; ?>" style="width: 300px; max-width: 100%;">

	<br>

	<label for="somdn_pro_newsletter_general_settings[somdn_newsletter_website_required]">
	<input type="checkbox" name="somdn_pro_newsletter_general_settings[somdn_newsletter_website_required]" id="somdn_pro_newsletter_general_settings[somdn_newsletter_website_required]"
	<?php
		$checked = isset( $required ) ? checked( $required, true ) : '' ;
	?>
		value="1">
	Make website address required
	</label>

	<br><br>

	<p><strong>Optional: </strong>Customise the error message if no website entered.</p>
	<p class="description"><strong>Note:</strong> Only applies if website is required.</p>
	<p class="description" style="margin-bottom: 15px;">Default: <strong>Please enter your website</strong></p>

	<input type="text" name="somdn_pro_newsletter_general_settings[somdn_newsletter_website_error]" value="<?php echo $error; ?>" style="width: 300px; max-width: 100%;">

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_newsletter_confirmations_content_render( $args ) {

	if ( ! empty( $args[ 'context' ] ) && 'extra' === $args[ 'context' ] )
		return;

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$confirm = isset( $options['somdn_newsletter_confirmations'] ) ? $options['somdn_newsletter_confirmations'] : '' ;
	$errors = isset( $options['somdn_newsletter_confirmations_errors'] ) ? $options['somdn_newsletter_confirmations_errors'] : '' ; ?>

	<label for="somdn_pro_newsletter_general_settings[somdn_newsletter_confirmations]">
	<input type="checkbox" name="somdn_pro_newsletter_general_settings[somdn_newsletter_confirmations]" id="somdn_pro_newsletter_general_settings[somdn_newsletter_confirmations]"
	<?php
		$checked = isset( $confirm ) ? checked( $confirm, true ) : '' ;
	?>
		value="1">
	Receive a confirmation email when a user downloads and subscribes
	</label>

	<br><br>

	<label for="somdn_pro_newsletter_general_settings[somdn_newsletter_confirmations_errors]">
	<input type="checkbox" name="somdn_pro_newsletter_general_settings[somdn_newsletter_confirmations_errors]" id="somdn_pro_newsletter_general_settings[somdn_newsletter_confirmations_errors]"
	<?php
		$checked = isset( $errors ) ? checked( $errors, true ) : '' ;
	?>
		value="1">
	Receive an email if a subscription generated an error
	</label>
	<p class="description"><strong>Note:</strong> These kinds of errors are not displayed to the user.</p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_newsletter_manual_info_render() { ?>

	<p style="margin-bottom: 15px; font-size: 15px;">Manual newsletter setting is handy if you manually email your customers from OutLook for example.</p>

	<p style="margin-bottom: 15px; font-size: 15px;">Because you've selected "manual" as your newsletter option you can get a user's email address from the tracked download and add it to your contact list.</p>

	<?php

}

function somdn_newsletter_mailchimp_api_key_render() {

	$options = get_option( 'somdn_pro_newsletter_mailchimp_settings' );
	$value = isset( $options['somdn_newsletter_mailchimp_api_key'] ) ? $options['somdn_newsletter_mailchimp_api_key'] : '' ; ?>
	
	<input type="text" name="somdn_pro_newsletter_mailchimp_settings[somdn_newsletter_mailchimp_api_key]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<p class="som-mar-bot-15"><strong>This connects to your MailChimp account.</strong></p>

	<p class="som-mar-bot-15"><a href="https://kb.mailchimp.com/integrations/api-integrations/about-api-keys" target="_blank">Click here</a> to see how to obtain your MailChimp API key.</p>
	<?php

}

function somdn_newsletter_mailchimp_list_id_render() {

	$options = get_option( 'somdn_pro_newsletter_mailchimp_settings' );
	$value = isset( $options['somdn_newsletter_mailchimp_list_id'] ) ? $options['somdn_newsletter_mailchimp_list_id'] : '' ; ?>
	
	<input type="text" name="somdn_pro_newsletter_mailchimp_settings[somdn_newsletter_mailchimp_list_id]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;" >
	<p class="som-mar-bot-15"><strong>This is the specific list you want people to be subscribed to.</strong></p>

	<p class="som-mar-bot-15"><a href="https://kb.mailchimp.com/lists/manage-contacts/find-your-list-id" target="_blank">Click here</a> to see how to obtain your MailChimp List ID.</p>
	<?php

}

function somdn_newsletter_mailchimp_doubleoptin_render() { 

	$options = get_option( 'somdn_pro_newsletter_mailchimp_settings' );
	$value = isset( $options['somdn_newsletter_mailchimp_doubleoptin'] ) ? $options['somdn_newsletter_mailchimp_doubleoptin'] : 0 ; ?>

	<label for="somdn_pro_newsletter_mailchimp_settings[somdn_newsletter_mailchimp_doubleoptin]">
	<input type="checkbox" name="somdn_pro_newsletter_mailchimp_settings[somdn_newsletter_mailchimp_doubleoptin]" id="somdn_pro_newsletter_mailchimp_settings[somdn_newsletter_mailchimp_doubleoptin]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Tick if you use the "double opt-in" subscriber setting in your list
	</label>

	<p style="margin-bottom: 15px;" class="description">More info on double opt-in <a href="https://kb.mailchimp.com/lists/signup-forms/about-double-opt-in" target="_blank">here</a>.</p>

<?php

}