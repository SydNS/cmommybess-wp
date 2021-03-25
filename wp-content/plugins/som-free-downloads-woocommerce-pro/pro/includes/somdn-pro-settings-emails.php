<?php
/**
 * Free Downloads - WooCommerce - Pro Settings - Emails
 * 
 * @version 3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'somdn_settings_subtabs_after_multiple' , 'somdn_settings_subtabs_emails', 15, 1 );
function somdn_settings_subtabs_emails( $active_section ) {
	$nav_active = ( $active_section == 'emails' ) ? 'current' : '' ;
	echo '<li><a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=emails" class="' . $nav_active . '">Emails</a> | </li>';
}

add_action( 'somdn_settings_page_content' , 'somdn_settings_content_emails', 10 );
function somdn_settings_content_emails( $active_section ) {
	if ( 'emails' == $active_section ) {
		somdn_emails_settings_content();
	}
}

function somdn_emails_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-9">

				<form action="options.php" class="som-settings-settings-form" method="post">

					<div class="som-settings-gen-settings-form-wrap">

						<?php

							settings_fields( 'somdn_email_settings' );
							//settings_fields( 'somdn_email_settings' );
							//somdn_do_download_type_settings_content();
							//somdn_do_custom_settings_sections( 'somdn_email_settings', true );
							somdn_do_email_global_settings_content();
							somdn_do_email_settings_content();
							submit_button();

						?>

					</div>

				</form>

			</div>

		</div>
	</div>

<?php

}

add_action( 'admin_init', 'somdn_settings_emails' );
function somdn_settings_emails() {

	register_setting( 'somdn_email_settings', 'somdn_email_settings' );

	add_settings_section(
		'somdn_email_settings_section',
		__( 'Email Settings', 'somdn-pro' ),
		'somdn_email_settings_section_callback',
		'somdn_email_settings'
	);

	add_settings_field(
		'somdn_email_settings_sender_name',
		'<label for="somdn_email_settings[somdn_email_settings_sender_name]">From name</label>',
		'somdn_email_settings_sender_name_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

	add_settings_field(
		'somdn_email_settings_sender_address',
		'<label for="somdn_email_settings[somdn_email_settings_sender_address]">From address</label>',
		'somdn_email_settings_sender_address_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

	add_settings_field(
		'somdn_email_settings_content_type',
		'<label for="somdn_email_settings[somdn_email_settings_content_type]">Content type</label>',
		'somdn_email_settings_content_type_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

	//register_setting( 'somdn_email_settings', 'somdn_email_settings' );
/*
	add_settings_section(
		'somdn_email_download_url_settings_section',
		'Hello',
		'somdn_email_download_url_settings_section_callback',
		'somdn_email_settings'
	);
*/

	// Download Links

	add_settings_field(
		'somdn_email_download_url_subject',
		NULL,
		'somdn_email_settings_empty_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

	add_settings_field(
		'somdn_email_download_url_heading',
		NULL,
		'somdn_email_settings_empty_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

	add_settings_field(
		'somdn_email_download_url_message',
		NULL,
		'somdn_email_settings_empty_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

	// New Free Downloads

	add_settings_field(
		'somdn_email_new_download_enable',
		NULL,
		'somdn_email_settings_empty_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

	add_settings_field(
		'somdn_email_new_download_sendto',
		NULL,
		'somdn_email_settings_empty_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

	add_settings_field(
		'somdn_email_new_download_subject',
		NULL,
		'somdn_email_settings_empty_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

	add_settings_field(
		'somdn_email_new_download_heading',
		NULL,
		'somdn_email_settings_empty_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

	add_settings_field(
		'somdn_email_new_download_message',
		NULL,
		'somdn_email_settings_empty_render',
		'somdn_email_settings',
		'somdn_email_settings_section'
	);

}

function somdn_email_settings_section_callback() {
	echo '<p>Customise how this plugin sends emails to your customers.</p>';
	echo '<p class="description som-pad-bot-25"><strong>Note:</strong> Some default settings are taken from your WooCommerce email settings, and these can be overridden below.</p>';
}

function somdn_email_download_url_settings_section_callback() {
	echo 'Hello';
}

function somdn_email_settings_empty_render() {}

function somdn_email_settings_sender_name_render() {

	$email_defaults = somdn_get_site_email_defaults();
	$default_sender_name = $email_defaults['headers']['sender_name'];

	$options = get_option( 'somdn_email_settings' );
	$value = isset( $options['somdn_email_settings_sender_name'] ) ? $options['somdn_email_settings_sender_name'] : '' ; ?>

	<input type="text" id="somdn_email_settings[somdn_email_settings_sender_name]" name="somdn_email_settings[somdn_email_settings_sender_name]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;" placeholder="<?php echo $default_sender_name; ?>">
	<p>Customise the name to be shown as the email sender.</p>

	<?php

}

function somdn_email_settings_sender_address_render() {

	$email_defaults = somdn_get_site_email_defaults();
	$default_sender_address = $email_defaults['headers']['sender_address'];

	$options = get_option( 'somdn_email_settings' );
	$value = isset( $options['somdn_email_settings_sender_address'] ) ? $options['somdn_email_settings_sender_address'] : '' ; ?>

	<input type="email" id="somdn_email_settings[somdn_email_settings_sender_address]" name="somdn_email_settings[somdn_email_settings_sender_address]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;" placeholder="<?php echo $default_sender_address; ?>">
	<p>Customise the email address to send from.</p>

	<?php

}

function somdn_email_settings_content_type_render() {

	$options = get_option( 'somdn_email_settings' );
	$value = isset( $options['somdn_email_settings_content_type'] ) ? intval( $options['somdn_email_settings_content_type'] ) : 0 ; ?>

	<select class="som-mar-bot-15" name="somdn_email_settings[somdn_email_settings_content_type]" id="somdn_email_settings[somdn_email_settings_content_type]">
		<option value="0" <?php selected( $value, 0 ); ?>>HTML</option>
		<option value="1" <?php selected( $value, 1 ); ?>>Plain Text</option>
	</select>

	<p>By default emails are sent as html.</p>

	<?php

}

function somdn_do_email_global_settings_content() { ?>

	<div class="som-settings-setting-section-wrapper wrapper-small">

		<h2>Email Settings</h2>
		<p>Customise how this plugin sends emails.</p>
		<p class="description som-pad-bot-25"><strong>Note:</strong> Some default settings are taken from your WooCommerce email settings, and these can be overridden below.</p>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="somdn_email_settings[somdn_email_settings_sender_name]">From name</label></th>
					<td>
						<?php somdn_email_settings_sender_name_render(); ?>
					</td>
			</tr>
			<tr>
				<th scope="row"><label for="somdn_email_settings[somdn_email_settings_sender_address]">From address</label></th>
				<td>
					<?php somdn_email_settings_sender_address_render(); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="somdn_email_settings[somdn_email_settings_content_type]">Content type</label></th>
					<td>
						<?php somdn_email_settings_content_type_render(); ?>
					</td>
				</tr>
			</tbody>
		</table>

	</div>

<?php }

function somdn_do_email_settings_content() { ?>

	<?php $download_type_settings = somdn_get_plugin_link_full() . '&tab=settings&section=download_type'; ?>
	<?php $download_tracking_settings = somdn_get_plugin_link_full() . '&tab=settings&section=tracking'; ?>

	<div class="som-settings-setting-section-wrapper wrapper-xsmall">
		<h3>Emails</h3>
		<p>Customise the content for the emails.</p>
	</div>

	<div class="som-settings-email-option-wrap">

		<input class="som-setting-checkbox-hidden" type="checkbox" id="som-email-setting-1" name="som-email-setting-1">

		<div class="som-settings-email-option-heading">

			<label class="som-settings-email-option-heading-label" for="som-email-setting-1">
				<div class="som-settings-email-option-heading-title">
					<span>Download Links</span>
				</div>
				<div class="som-settings-email-option-heading-arrow">
					<span class="som-settings-arrow"></span>
				</div>
			</label>

		</div>

		<div class="som-settings-email-option-content">

			<div class="som-setting-gen-settings-wrap">
				<p class="description som-pad-bot-20">The email sent to users with their file download url when this option is selected in your <a href="<?php echo $download_type_settings; ?>">Download Delivery</a> settings.</p>
			</div>

			<?php

			// Grab the email default settings for download links (default $type for this function call)
			$email_defaults = somdn_get_site_email_defaults();
			$default_subject = $email_defaults['content']['subject'];
			$default_heading = $email_defaults['content']['heading'];
			$default_message = $email_defaults['content']['message'];

			$options = get_option( 'somdn_email_settings' );

			$email_subject = isset( $options['somdn_email_download_url_subject'] ) ? $options['somdn_email_download_url_subject'] : '' ;
			$email_heading = isset( $options['somdn_email_download_url_heading'] ) ? $options['somdn_email_download_url_heading'] : '' ;
			$email_message = isset( $options['somdn_email_download_url_message'] ) ? $options['somdn_email_download_url_message'] : '' ;
		
			?>

			<div class="som-setting-gen-settings-wrap">
				<h4>Email Subject</h4>
				<p><strong>Optional: </strong>Customise the email subject line. Use the following to replace certain elements:</p>
				<p class="som-no-margin"><strong>{site_name}</strong> - The site name</p>
				<p class="som-no-mar-top som-mar-bot-15"><strong>{product}</strong> - The product name</p>
				<input type="text" name="somdn_email_settings[somdn_email_download_url_subject]" value="<?php echo $email_subject; ?>" style="width: 100%;" placeholder="<?php echo $default_subject; ?>">
			</div>

			<hr class="som-setting-sep sep-300 top bottom">

			<div class="som-setting-gen-settings-wrap">
				<h4>Email Heading</h4>
				<p><strong>Optional: </strong>Customise the email heading text. Use the following to replace certain elements:</p>
				<p class="som-no-margin"><strong>{site_name}</strong> - The site name</p>
				<p class="som-no-mar-top som-mar-bot-15"><strong>{product}</strong> - The product name</p>
				<input type="text" name="somdn_email_settings[somdn_email_download_url_heading]" value="<?php echo $email_heading; ?>" style="width: 100%;" placeholder="<?php echo $default_heading; ?>">
			</div>

			<hr class="som-setting-sep sep-300 top bottom">

			<div class="som-setting-gen-settings-wrap">

				<h4>Email Body</h4>

				<div class="som-settings-pro-basic-limit-error-wrap" style="max-width: 100%;">

					<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the email body. Use the following to replace certain elements:</p>

					<p class="som-no-margin"><strong>{link}</strong> - The download URL</p>
					<p class="som-no-margin"><strong>{product}</strong> - The product name</p>
					<p class="som-no-margin"><strong>{username}</strong> - The user's login username. Default is "Customer" if none found</p>
					<p class="som-no-margin"><strong>{first_name}</strong> - The user's first name. Default is "Customer" if none found</p>
					<p class="som-no-margin"><strong>{site_name}</strong> - The site name</p>
					<p class="som-no-mar-top"><strong>{hours}</strong> - Number of hours until link expires (number)</p>

					<p class="som-mar-bot-15"><strong>Default:</strong></p>
					<div class="description som-mar-bot-20"><?php echo wpautop( wp_kses_post( $default_message ) ); ?></div>

					<?php

						$editor_id = 'somdn_download_type_email_message';
						$settings = array(
							'media_buttons' => false,
							'tinymce'=> false,
							'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
							'editor_class' => 'required',
							'editor_height' => 180,
							'textarea_name' => 'somdn_email_settings[somdn_email_download_url_message]'
						);
						$content = stripslashes( $email_message );

						wp_editor( $content, $editor_id, $settings );

					?>

				</div>

			</div>

		</div>

	</div>

	<div class="som-settings-email-option-wrap">

		<input class="som-setting-checkbox-hidden" type="checkbox" id="som-email-setting-2" name="som-email-setting-2">

		<div class="som-settings-email-option-heading">

			<label class="som-settings-email-option-heading-label" for="som-email-setting-2">
				<div class="som-settings-email-option-heading-title">
					<span>New Free Downloads</span>
				</div>
				<div class="som-settings-email-option-heading-arrow">
					<span class="som-settings-arrow"></span>
				</div>
			</label>

		</div>

		<div class="som-settings-email-option-content">

			<div class="som-setting-gen-settings-wrap">
				<p class="description">The email sent to site admins when a new free download has been actioned. Disabled by default.</p>
				<p class="description som-pad-bot-20"><strong>This feature also requires <a href="<?php echo $download_tracking_settings; ?>">Download Tracking</a> to be enabled.</strong></p>
			</div>

			<?php

			// Grab the email default settings for new free downloads (default $type for this function call)
			$email_defaults = somdn_get_site_email_defaults( 'new_free_download' );
			$default_sendto = $email_defaults['headers']['sender_address'];
			$default_subject = $email_defaults['content']['subject'];
			$default_heading = $email_defaults['content']['heading'];
			$default_message = $email_defaults['content']['message'];
			
			$options = get_option( 'somdn_email_settings' );

			$email_enable  = isset( $options['somdn_email_new_download_enable'] ) ? $options['somdn_email_new_download_enable'] : '' ;
			$email_sendto = isset( $options['somdn_email_new_download_sendto'] ) ? $options['somdn_email_new_download_sendto'] : '' ;
			$email_subject = isset( $options['somdn_email_new_download_subject'] ) ? $options['somdn_email_new_download_subject'] : '' ;
			$email_heading = isset( $options['somdn_email_new_download_heading'] ) ? $options['somdn_email_new_download_heading'] : '' ;
			$email_message = isset( $options['somdn_email_new_download_message'] ) ? $options['somdn_email_new_download_message'] : '' ;
		
			?>

			<div class="som-setting-gen-settings-wrap">
				<h4>Enable Email Notifications</h4>
				<p><label for="somdn_email_settings[somdn_email_new_download_enable]">
				<input type="checkbox" name="somdn_email_settings[somdn_email_new_download_enable]" id="somdn_email_settings[somdn_email_new_download_enable]" <?php $checked = ! empty( $email_enable ) ? checked( $email_enable, true ) : '' ; ?> value="1">Receive an email every time a user downloads a free product.</label></p>
			</div>

			<hr class="som-setting-sep sep-300 top bottom">

			<div class="som-setting-gen-settings-wrap">
				<h4>Email Recipients</h4>
				<p><strong>Optional: </strong>Customise who should receive this email.</p>
				<p>For multiple email address, separate each one by a comma and space. For example: a@b.com, c@d.com</p>
				<input type="text" name="somdn_email_settings[somdn_email_new_download_sendto]" value="<?php echo $email_sendto; ?>" style="width: 100%;" placeholder="<?php echo $default_sendto; ?>">
			</div>

			<hr class="som-setting-sep sep-300 top bottom">

			<div class="som-setting-gen-settings-wrap">
				<h4>Email Subject</h4>
				<p><strong>Optional: </strong>Customise the email subject line. Use the following to replace certain elements:</p>
				<p class="som-no-margin"><strong>{site_name}</strong> - The site name</p>
				<p class="som-no-mar-top som-mar-bot-15"><strong>{product}</strong> - The product name</p>
				<input type="text" name="somdn_email_settings[somdn_email_new_download_subject]" value="<?php echo $email_subject; ?>" style="width: 100%;" placeholder="<?php echo $default_subject; ?>">
			</div>

			<hr class="som-setting-sep sep-300 top bottom">

			<div class="som-setting-gen-settings-wrap">
				<h4>Email Heading</h4>
				<p><strong>Optional: </strong>Customise the email heading text.</p>
				<input type="text" name="somdn_email_settings[somdn_email_new_download_heading]" value="<?php echo $email_heading; ?>" style="width: 100%;" placeholder="<?php echo $default_heading; ?>">
			</div>

			<hr class="som-setting-sep sep-300 top bottom">

			<div class="som-setting-gen-settings-wrap">

				<h4>Email Body</h4>

				<div class="som-settings-pro-basic-limit-error-wrap" style="max-width: 100%;">

					<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the email body. Use the following to replace certain elements:</p>

					<p class="som-no-margin"><strong>{id}</strong> - The download ID</p>
					<p class="som-no-margin"><strong>{link}</strong> - Link to the download log</p>
					<p class="som-no-margin"><strong>{product}</strong> - The product name</p>
					<p class="som-no-margin"><strong>{username}</strong> - The user's login username. Default is "Customer" if none found</p>
					<p class="som-no-margin"><strong>{email}</strong> - The user's email address. Default is blank if none found</p>
					<p class="som-no-margin"><strong>{date}</strong> - Date & time of the download</p>
					<p class="som-no-margin"><strong>{site_name}</strong> - The site name</p>

					<p class="som-mar-bot-15"><strong>Default:</strong></p>
					<div class="description som-mar-bot-20"><?php echo wpautop( wp_kses_post( $default_message ) ); ?></div>

					<?php

						$editor_id = 'somdn_new_download_email_message';
						$settings = array(
							'media_buttons' => false,
							'tinymce'=> false,
							'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
							'editor_class' => 'required',
							'editor_height' => 180,
							'textarea_name' => 'somdn_email_settings[somdn_email_new_download_message]'
						);
						$content = stripslashes( $email_message );

						wp_editor( $content, $editor_id, $settings );

					?>

				</div>

			</div>

		</div>

	</div>

<?php }

/*
function somdn_download_type_settings_empty_render() {}



function somdn_do_download_type_settings_content() { ?>

<div class="som-settings-setting-section-wrapper wrapper-small">
	<h2>Download Delivery Settings</h2>
	<p>Choose how your free downloads are delivered to your users.</p>
	<p class="description">Note: This is not the same as your WooCommerce File Download Method.</p>
</div>

<?php somdn_download_type_option_render(); ?>

<div class="somdn-setting-download-type-redirect">
	<?php somdn_do_download_type_redirect_settings_content(); ?>
</div>

<div class="somdn-setting-download-type-email">
	<?php somdn_do_download_type_email_settings_content(); ?>
</div>

<?php }

/*

	$email_plain_text = isset( $options['somdn_download_type_email_plain_text'] ) ? intval( $options['somdn_download_type_email_plain_text'] ) : '' ;
	$email_subject = isset( $options['somdn_download_type_email_subject'] ) ? $options['somdn_download_type_email_subject'] : '' ;
	$email_message = isset( $options['somdn_download_type_email_message'] ) ? $options['somdn_download_type_email_message'] : '' ;

*/
