<?php
/**
 * Free Downloads - WooCommerce - Pro Settings - Download Delivery (Download Delivery)
 * 
 * @version 3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'somdn_settings_subtabs_after_multiple' , 'somdn_settings_subtabs_download_delivery', 10, 1 );
function somdn_settings_subtabs_download_delivery( $active_section ) {
	$nav_active = ( $active_section == 'download_type' ) ? 'current' : '' ;
	echo '<li><a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=download_type" class="' . $nav_active . '">Download Delivery</a> | </li>';
}

add_action( 'somdn_settings_page_content' , 'somdn_settings_content_download_type', 10 );
function somdn_settings_content_download_type( $active_section ) {
	if ( 'download_type' == $active_section ) {
		somdn_download_type_settings_content();
	}
}

function somdn_download_type_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7">

				<form action="options.php" class="som-settings-settings-form" method="post">

					<div class="som-settings-gen-settings-form-wrap">

						<?php

							settings_fields( 'somdn_download_type_settings' );
							somdn_do_download_type_settings_content();
							//somdn_do_custom_settings_sections( 'somdn_download_type_settings', true, false );
							submit_button();

						?>

					</div>

				</form>

			</div>

			<div class="som-settings-col-5 som-settings-guide som-settings-multi-guide">

				<?php $img_location = plugins_url( '/assets/images/', SOMDN_FILE ); ?>
			
				<div class="som-settings-guide-img">
					<h2>Redirect to confirmation page</h2>
				</div>

				<div class="som-settings-guide-img">
					<img src="<?php echo $img_location . 'redirect-example.png'; ?>" style="width: 400px;">
				</div>

			</div>

		</div>
	</div>

<?php

}

add_action( 'admin_init', 'somdn_settings_download_type' );
function somdn_settings_download_type() {

	register_setting( 'somdn_download_type_settings', 'somdn_download_type_settings' );

	add_settings_section(
		'somdn_download_type_settings_section',
		__( 'Download Delivery Settings', 'somdn-pro' ),
		'somdn_download_type_settings_section_callback',
		'somdn_download_type_settings'
	);

	$options = get_option( 'somdn_download_type_settings' );

	$download_type_settings = array(
		'somdn_download_type_option',
		'somdn_download_type_redirect_page',
		'somdn_download_type_redirect_time',
		'somdn_download_type_redirect_message',
		'somdn_download_type_redirect_text',
		'somdn_download_type_redirect_expire_time',
		'somdn_download_type_email_page',
		'somdn_download_type_email_page_message',
		'somdn_download_type_email_expire_time',
		'somdn_download_type_email_expire_message',
		'somdn_download_type_email_used_message'
	);

	foreach ( $download_type_settings as $setting ) {
		add_settings_field(
			$setting,
			NULL,
			'somdn_download_type_settings_empty_render',
			'somdn_download_type_settings',
			'somdn_download_type_settings_section'
		);
	}

}

function somdn_download_type_settings_empty_render() {}

function somdn_download_type_settings_section_callback() {}

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

function somdn_download_type_option_render() {

	$options = get_option( 'somdn_download_type_settings' );
	$type = isset( $options['somdn_download_type_option'] ) ? intval( $options['somdn_download_type_option'] ) : 0 ; ?>

	<input class="som-setting-settings-input-radio" type="radio" id="somdn_download_type_type_0" name="somdn_download_type_settings[somdn_download_type_option]" value="0" <?php checked( 0, $type, true ); ?>><label class="som-setting-settings-input-label" for="somdn_download_type_type_0"><strong> Instant download <span class="description">(Default)</span></strong></label>
	<p>Files will be downloaded as soon as the download button is clicked, or when the email capture form is submitted.</p>

	<div class="som-settings-settings-spacer-md"></div>
	
	<input class="som-setting-settings-input-radio" type="radio" id="somdn_download_type_type_1" name="somdn_download_type_settings[somdn_download_type_option]" value="1" <?php checked( 1, $type, true ); ?>><label class="som-setting-settings-input-label" for="somdn_download_type_type_1"><strong> Redirect to confirmation page</strong></label>
	<p>Users will be redirected to a page of your choice and their download will start a few seconds later. A link to start the download will also show just in case.</p>

	<div class="som-settings-settings-spacer-md"></div>

	<?php $email_url = somdn_get_plugin_link_full() . '&tab=settings&section=emails'; ?>
	<?php $tracking_url = somdn_get_plugin_link_full() . '&tab=settings&section=tracking'; ?>

	<input class="som-setting-settings-input-radio" type="radio" id="somdn_download_type_type_2" name="somdn_download_type_settings[somdn_download_type_option]" value="2" <?php checked( 2, $type, true ); ?>><label class="som-setting-settings-input-label" for="somdn_download_type_type_2"><strong> Email link then redirect to confirmation page</strong></label>
	<p>Users will be redirected to a page of your choice, and their download URL will be emailed to them. Customise the email in the <a href="<?php echo $email_url; ?>">Emails</a> settings.</p>
	<p class="description"><strong>Note:</strong> Email Capture must be enabled in <a href="<?php echo $tracking_url; ?>">Tracking & Email Capture</a> if you allow guest downloads.</p>
	<p class="description"><strong>Note:</strong> Some details are taken from your WooCommerce email settings, and those can be overridden below.</p>

	<?php

}

function somdn_do_download_type_redirect_settings_content() {

	$options = get_option( 'somdn_download_type_settings' );
	$redirect_page = isset( $options['somdn_download_type_redirect_page'] ) ? $options['somdn_download_type_redirect_page'] : '' ;
	$redirect_time = isset( $options['somdn_download_type_redirect_time'] ) ? $options['somdn_download_type_redirect_time'] : '' ;
	$redirect_text = isset( $options['somdn_download_type_redirect_text'] ) ? $options['somdn_download_type_redirect_text'] : '' ;
	$message = isset( $options['somdn_download_type_redirect_message'] ) ? $options['somdn_download_type_redirect_message'] : '' ;

	if ( $redirect_time > 60 ) {
		$redirect_time = 60;
	}

	$args = array(
		'selected' => $redirect_page,
		'show_option_none' => 'Please choose...',
		'name' => 'somdn_download_type_settings[somdn_download_type_redirect_page]',
		'id' => 'somdn_download_type_settings[somdn_download_type_redirect_page]'
	); ?>

	<hr class="som-setting-sep sep-300 top bottom">

	<div class="som-settings-setting-section-wrapper wrapper-xsmall">
		<h3>Redirect Page Settings</h3>
	</div>

	<div class="som-setting-gen-settings-wrap">
		<h4>Redirect page</h4>
		<p>This is the page users will be redirected to after submitting a download form.</p>
		<p class="som-mar-bot-15">Select which page your <code>[download_redirect]</code> shortcode is on.</p>
		<div><?php wp_dropdown_pages( $args ); ?></div>
	</div>

	<hr class="som-setting-sep sep-300 top bottom">

	<div class="som-setting-gen-settings-wrap">
		<h4>Redirect time</h4>
		<p>The time in seconds before the download starts automatically on the redirect page.</p>
		<p class="description">Default: <strong>5 seconds</strong></p>
		<p class="description som-mar-bot-15">Max: <strong>60 seconds</strong></p>
		<input type="number" class="short" style="" name="somdn_download_type_settings[somdn_download_type_redirect_time]" id="somdn_download_type_settings[somdn_download_type_redirect_time]" value="<?php echo $redirect_time; ?>" placeholder="5" step="1" min="1" max="60" style="width: 50px; max-width: 100%;">
	</div>

	<hr class="som-setting-sep sep-300 top bottom">

	<div class="som-setting-gen-settings-wrap">

		<h4>Page text</h4>

		<div class="som-settings-pro-basic-limit-error-wrap">

			<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the message displayed on the confirmation page. Use <strong>{click_here}</strong> to show the download link. Use <strong>{time}</strong> to show the redirect time in seconds.</p>

			<p class="description som-mar-bot-15">Default: Your download should start automatically in <strong>{time}</strong> seconds. If it doesn't please <strong>click here</strong>. Do not refresh this page.</p>

			<?php

				$editor_id = 'somdn_download_type_redirect_message';
				$settings = array(
					'media_buttons' => false,
					'tinymce'=> false,
					'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
					'editor_class' => 'required',
					'editor_height' => 180,
					'textarea_name' => 'somdn_download_type_settings[somdn_download_type_redirect_message]'
				);
				$content = stripslashes( $message );

				wp_editor( $content, $editor_id, $settings );

			?>

		</div>
	</div>

	<hr class="som-setting-sep sep-300 top bottom">

	<div class="som-setting-gen-settings-wrap">
		<h4>Page "click here" text</h4>
		<p><strong>Optional: </strong>Customise the download link text.</p>
		<p class="description som-mar-bot-15">Default: <strong>click here</strong></p>
		<input type="text" name="somdn_download_type_settings[somdn_download_type_redirect_text]" value="<?php echo $redirect_text; ?>" style="width: 300px; max-width: 100%;">
	</div>

	<hr class="som-setting-sep sep-300">

<?php }

function somdn_do_download_type_email_settings_content() {

	$options = get_option( 'somdn_download_type_settings' );
	$email_page = isset( $options['somdn_download_type_email_page'] ) ? $options['somdn_download_type_email_page']: '' ;
	$email_page_message = isset( $options['somdn_download_type_email_page_message'] ) ? $options['somdn_download_type_email_page_message'] : '' ;
	$email_expire_time = isset( $options['somdn_download_type_email_expire_time'] ) ? intval( $options['somdn_download_type_email_expire_time'] ) : '' ;
	$email_expire_message = isset( $options['somdn_download_type_email_expire_message'] ) ? $options['somdn_download_type_email_expire_message'] : '' ;
	$email_used_message = isset( $options['somdn_download_type_email_used_message'] ) ? $options['somdn_download_type_email_used_message'] : '' ;

	$args = array(
		'selected' => $email_page,
		'show_option_none' => 'Please choose...',
		'name' => 'somdn_download_type_settings[somdn_download_type_email_page]',
		'id' => 'somdn_download_type_settings[somdn_download_type_email_page]'
	); ?>

	<hr class="som-setting-sep sep-300 top bottom">

	<div class="som-settings-setting-section-wrapper wrapper-xsmall">
		<h3>Confirmation Page Settings</h3>
	</div>

	<div class="som-setting-gen-settings-wrap">
		<h4>Email confirmation page</h4>
		<p>This is the page users will be redirected to after submitting a download form.</p>
		<p class="som-mar-bot-15">Select which page your <code>[download_redirect]</code> shortcode is on.</p>
		<div><?php wp_dropdown_pages( $args ); ?></div>
	</div>

	<hr class="som-setting-sep sep-300 top bottom">

	<div class="som-setting-gen-settings-wrap">

		<h4>Page Text</h4>

		<div class="som-settings-pro-basic-limit-error-wrap">

			<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the message displayed on the confirmation page. Use <strong>{email}</strong> if you want to show the email address they used.</p>

			<p class="description som-mar-bot-15">Default: A link to download the file has been emailed to you, please check your inbox.</p>

			<?php

				$editor_id = 'somdn_download_type_email_page_message';
				$settings = array(
					'media_buttons' => false,
					'tinymce'=> false,
					'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
					'editor_class' => 'required',
					'editor_height' => 180,
					'textarea_name' => 'somdn_download_type_settings[somdn_download_type_email_page_message]'
				);
				$content = stripslashes( $email_page_message );

				wp_editor( $content, $editor_id, $settings );

			?>

		</div>

	</div>

	<hr class="som-setting-sep sep-300 top bottom">

	<div class="som-settings-setting-section-wrapper wrapper-xsmall">
		<h3>Email Link Settings</h3>
	</div>

	<div class="som-setting-gen-settings-wrap">
		<h4>Link expiry time</h4>
		<p class="som-no-margin"><strong>Optional: </strong>Set how long (hours) the download url remains valid. Values between 1 and 168 (168 hours = 7 days).</p>
		<p class="description"><strong>Note:</strong> This is just the url link expiry itself. Normal download checks are always made regardless.</p>
		<p class="description som-mar-bot-15"><strong>Note:</strong> Download links always expire once they've been used.</p>
		<p class="description som-mar-bot-15">Default: <strong>24</strong></p>
		<?php if ( ! empty( $email_expire_time ) ) { ?>
			<?php if ( $email_expire_time > 168 ) {
				$email_expire_time = 168;
			} ?>
			<input type="number" class="short" style="" name="somdn_download_type_settings[somdn_download_type_email_expire_time]" id="somdn_download_type_settings[somdn_download_type_email_expire_time]" value="<?php echo $email_expire_time; ?>" placeholder="24" step="1" min="1" max="168" style="width: 50px; max-width: 100%;">
		<?php } else { ?>
			<input type="number" class="short" style="" name="somdn_download_type_settings[somdn_download_type_email_expire_time]" id="somdn_download_type_settings[somdn_download_type_email_expire_time]" value="" placeholder="24" step="1" min="1" max="168" style="width: 50px; max-width: 100%;">
		<?php } ?>
	</div>

	<hr class="som-setting-sep sep-300 top bottom">

	<div class="som-setting-gen-settings-wrap">

		<h4>Expired link message</h4>

		<div class="som-settings-pro-basic-limit-error-wrap">
			<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the message displayed on a product page when a download link has expired.</p>
			<p class="description som-mar-bot-15">Default: Your download key has now expired, please try again.</p>
			<input type="text" name="somdn_download_type_settings[somdn_download_type_email_expire_message]" value="<?php echo $email_expire_message; ?>" style="width: 100%;">
		</div>

	</div>

	<hr class="som-setting-sep sep-300 top bottom">

	<div class="som-setting-gen-settings-wrap">

		<h4>Used link message</h4>

		<div class="som-settings-pro-basic-limit-error-wrap">
			<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the message displayed on a product page when a download link has been used.</p>
			<p class="description som-mar-bot-15">Default: Your download key has already been used, please try again.</p>
			<input type="text" name="somdn_download_type_settings[somdn_download_type_email_used_message]" value="<?php echo $email_used_message; ?>" style="width: 100%;">
		</div>

	</div>

	<hr class="som-setting-sep sep-300">

	<?php

}

/*

		// Global
		'somdn_download_type_email_sender_address',
		'somdn_download_type_email_sender_name',

		// Email Specific
		'somdn_download_type_email_subject',
		'somdn_download_type_email_heading',
		'somdn_download_type_email_message',
		'somdn_download_type_email_plain_text'

	$email_plain_text = isset( $options['somdn_download_type_email_plain_text'] ) ? intval( $options['somdn_download_type_email_plain_text'] ) : '' ;
	$email_sender_name = isset( $options['somdn_download_type_email_sender_name'] ) ? $options['somdn_download_type_email_sender_name'] : '' ;
	$email_sender_address = isset( $options['somdn_download_type_email_sender_address'] ) ? $options['somdn_download_type_email_sender_address'] : '' ;
	$email_subject = isset( $options['somdn_download_type_email_subject'] ) ? $options['somdn_download_type_email_subject'] : '' ;
	$email_message = isset( $options['somdn_download_type_email_message'] ) ? $options['somdn_download_type_email_message'] : '' ;

*/