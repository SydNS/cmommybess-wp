<?php
/**
 * Free Downloads - WooCommerce - MailChimp Newsletter Addon
 * 
 * @version 1.0.9
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$track_options = get_option( 'somdn_pro_track_settings' );
$subscribe_option = isset( $track_options['somdn_capture_email_subscribe'] ) ? $track_options['somdn_capture_email_subscribe'] : 0 ;

if ( $subscribe_option != 'mailchimp' ) return;

add_action( 'somdn_count_download_post_success', 'somdn_user_subbed_newsletter_mailchimp', 30, 1 );
function somdn_user_subbed_newsletter_mailchimp( $post_id ) {

	$user_subbed = get_post_meta( $post_id, 'somdn_user_subbed', true );

	$track_options = get_option( 'somdn_pro_track_settings' );
	$subscribe_option = isset( $track_options['somdn_capture_email_subscribe'] ) ? $track_options['somdn_capture_email_subscribe'] : 0 ;

	if ( ! empty( $user_subbed ) && $subscribe_option == 'mailchimp' ) {

		$user_email = get_post_meta( $post_id, 'somdn_user_email', true );
		$user_fname = get_post_meta( $post_id, 'somdn_user_fname', true );
		$user_lname = get_post_meta( $post_id, 'somdn_user_lname', true );

		if ( ! empty( $user_email ) ) {

			$mailchimp_options = get_option( 'somdn_pro_newsletter_mailchimp_settings' );

			$api_key = isset( $mailchimp_options['somdn_newsletter_mailchimp_api_key'] ) ? sanitize_text_field( $mailchimp_options['somdn_newsletter_mailchimp_api_key'] ) : '' ;
			$list_id = isset( $mailchimp_options['somdn_newsletter_mailchimp_list_id'] ) ? sanitize_text_field( $mailchimp_options['somdn_newsletter_mailchimp_list_id'] ) : '' ;

			$doubleoptin = isset( $mailchimp_options['somdn_newsletter_mailchimp_doubleoptin'] )
			? $mailchimp_options['somdn_newsletter_mailchimp_doubleoptin']
			: false ;

			if ( empty( $api_key ) || empty( $list_id ) ) {
				return;
			}

			// First let's check if the email address is already on the list
			$subbed_already = '';
			$subbed_already = json_decode( somdn_mailchimp_subscriber_status( $user_email, '', $list_id, $api_key, array( 'FNAME' => $user_fname, 'LNAME' => $user_lname ), true ) );

			//echo '<pre>';
			//print_r($subbed_already);
			//echo '</pre>';
			//exit;

			// If $subbed_already is empty then the email address isn't in the list, so we can skip to $result check.
			if ( ! empty( $subbed_already ) && is_object( $subbed_already ) ) {
				if ( ( $subbed_already->status == 'subscribed' ) || ( $subbed_already->status == 'pending' ) ) {
					// Email address already on the list, do nothing
					return;
				}
			}

			$sub_status = 'subscribed';

			if ( ! empty( $doubleoptin ) ) {
				$sub_status = 'pending';
			}

			// Email not already on list, let's put it there
			$result = json_decode( somdn_mailchimp_subscriber_status( $user_email, $sub_status, $list_id, $api_key, array( 'FNAME' => $user_fname, 'LNAME' => $user_lname ) ) );

			//echo '<pre>';
			//print_r($result);
			//echo '</pre>';
			//exit;

			$data = array(
				'first_name' => $user_fname,
				'last_name' => $user_lname,
				'email' => $user_email,
				'api_key' => $api_key,
				'list_id' => $list_id,
				'sub_status' => $sub_status
			);

			if ( ! empty( $result ) && is_object( $result ) ) {
				if ( $result->status == $sub_status ) {
					// MailChimp sub was successful
					do_action( 'somdn_newsletter_mailchimp_success', $post_id, $data );
				} else {
					// Error reporting
					somdn_email_site_admin_mailchimp_error( $result, $post_id, $data );
				}
			} else {
				somdn_email_site_admin_mailchimp_error( $result, $post_id, $data );
			}

		}

	}

}

add_action( 'somdn_newsletter_mailchimp_success', 'somdn_email_site_admin_subbed', 10, 1 );
function somdn_mailchimp_subscriber_status( $email, $status = '', $list_id, $api_key, $merge_fields = array('FNAME' => '','LNAME' => ''), $check = false ){
	$data = array(
		'apikey'        => $api_key,
		'email_address' => $email,
		'status'        => $status,
		'merge_fields'  => $merge_fields,
		'timestamp_opt' => date( 'Y-m-d H:i:s' )
	);
	$mch_api = curl_init(); // initialize cURL connection

	$action = ( $check == true ) ? 'GET' : 'PUT';

	curl_setopt($mch_api, CURLOPT_URL, 'https://' . substr($api_key,strpos($api_key,'-')+1) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5(strtolower($data['email_address'])));
	curl_setopt($mch_api, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.base64_encode( 'user:'.$api_key )));
	curl_setopt($mch_api, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
	curl_setopt($mch_api, CURLOPT_RETURNTRANSFER, true); // return the API response
	curl_setopt($mch_api, CURLOPT_CUSTOMREQUEST, $action); // method GET - check if subbed
	curl_setopt($mch_api, CURLOPT_TIMEOUT, 10);
	curl_setopt($mch_api, CURLOPT_POST, true);
	curl_setopt($mch_api, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($mch_api, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json

	$result = curl_exec($mch_api);
	return $result;
}

function somdn_email_site_admin_mailchimp_error( $result = '', $post_id, $data = array() ) {

/*
$data = array(
	'first_name' => $user_fname,
	'last_name' => $user_lname,
	'email' => $user_email,
	'api_key' => $api_key,
	'list_id' => $list_id,
	'sub_status' => $sub_status
);
*/

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$confirm_errors = isset( $options['somdn_newsletter_confirmations_errors'] ) ? $options['somdn_newsletter_confirmations_errors'] : '' ;

	// Bail if no error confirmation email needed
	if ( empty( $confirm_errors ) ) return;

	$admin_email = get_option( 'admin_email' );
	$site_name = get_option( 'blogname' );
	$subject = __( 'Mailchimp Site Subscription Error', 'somdn-pro' );

	$headers = array(
		'Content-Type: text/html; charset=UTF-8'
	);

	add_filter( 'wp_mail_content_type', 'somdn_html_emails' );

	ob_start(); ?>

	<p><?php _e( 'Hello there', 'somdn-pro' ); ?></p>
	<p><?php _e( 'A free download user attempted to subscribe to your MailChimp newsletter list but had an error. See below.', 'somdn-pro' ); ?></p>

	<?php

	// Set up a new error log
	$new_error_log = 'MailChimp subscription error during free download, tracked download ID # ' . $post_id . '. ' ;
	//somdn_write_log();

	?>
	
	<?php
	
	if ( empty( $result ) ) { ?>

		<p><?php _e( 'No response came back. Please check your API Key is correct in the plugin settings.', 'somdn-pro' ); ?></p>
		<?php $new_error_log .= 'Message: No response came back. Please check your API Key is correct in the plugin settings.'; ?>

	<?php } else {

		$encoded_result = json_encode( $result, JSON_UNESCAPED_SLASHES ) . '.';
		$detail = '';

		if ( is_object( $result ) ) {
			if ( isset( $result->detail ) ) {
				$detail = $result->detail;
			}
		}

		if ( $result->status == 400 ) {

			echo '<p>Error 400</p><p>' . $encoded_result . '</p>';
			$new_error_log .= '- Error 400: ' . $detail;

		} elseif ( $result->status == 404 ) {

			echo '<p>Invalid MailChimp List ID in plugin settings. Error 404: ' . $detail . '</p>';
			$new_error_log .= '- Invalid MailChimp List ID in plugin settings.';

		} elseif ( $result->status == 401 ) {

			echo '<p>Invalid MailChimp API Key in plugin settings. Error 401: ' . $detail . '</p>';
			$new_error_log .= '- Invalid MailChimp API Key in plugin settings.';

		} elseif ( is_object( $result ) ) {

			echo '<p>Error object</p><p>' . $encoded_result . '</p>';
			$new_error_log .= '- Error object: ' . $encoded_result;

		} elseif ( is_array( $result ) ) {

			echo '<p>Error Array</p><p>' . $encoded_result . '</p>';
			$new_error_log .= '- Error Array: ' . $encoded_result;

		} else {

			// Unknown generic error, include contact request to find cause.
			echo '<p>Error: ' . $encoded_result . '</p><p>Please contact <a href="https://squareonemedia.co.uk/community/forums/forum/premium-support/free-downloads-woocommerce/">Square One Media</a> support with the above details.</p>';
			$new_error_log .= '- Error: ' . $encoded_result;

		}

	} ?>

	<?php somdn_write_log( $new_error_log ); ?>

	<p>Data:</p>
	<p>
		First Name: <?php echo $data['first_name']; ?><br>
		Last Name: <?php echo $data['last_name']; ?><br>
		Email: <?php echo $data['email']; ?><br>
		API Key: <?php echo $data['api_key']; ?><br>
		List ID: <?php echo $data['list_id']; ?><br>
		Sub status: <?php echo $data['sub_status']; ?>
	</p>

	<br>
	<p><?php _e( 'Note: This error was not shown to the user.', 'somdn-pro' ); ?></p>

	<?php $message = ob_get_clean();

	$email_success = wp_mail( $admin_email, $subject, $message, $headers );

	if ( $email_success == false ) {
		somdn_write_log( "wp_mail() function failed during MailChimp subscription error checking. If using a LOCALHOST ensure you've set up SMTP locally."  );
	}

	remove_filter( 'wp_mail_content_type', 'somdn_html_emails' );

}

/*
 * Previous method, included for dev reference
 */
/*
include( SOMDN_PATH_PRO . 'addons/mailchimp/MailChimp.php' );

// Then call/use the class
use \DrewM\SOMDN_MailChimp\SOMDN_MailChimp;

add_action( 'somdn_count_download_post_success', 'somdn_user_subbed_newsletter_mailchimp', 30, 1 );
function somdn_user_subbed_newsletter_mailchimp( $post_id ) {

	$user_subbed = get_post_meta( $post_id, 'somdn_user_subbed', true );

	$track_options = get_option( 'somdn_pro_track_settings' );
	$subscribe_option = isset( $track_options['somdn_capture_email_subscribe'] ) ? $track_options['somdn_capture_email_subscribe'] : 0 ;

	if ( ! empty( $user_subbed ) && $subscribe_option == 'mailchimp' ) {

		$user_email = get_post_meta( $post_id, 'somdn_user_email', true );
		$user_fname = get_post_meta( $post_id, 'somdn_user_fname', true );
		$user_lname = get_post_meta( $post_id, 'somdn_user_lname', true );

		if ( ! empty( $user_email ) ) {

			$mailchimp_options = get_option( 'somdn_pro_newsletter_mailchimp_settings' );

			$api_key = isset( $mailchimp_options['somdn_newsletter_mailchimp_api_key'] ) ? $mailchimp_options['somdn_newsletter_mailchimp_api_key'] : '' ;
			$list_id = isset( $mailchimp_options['somdn_newsletter_mailchimp_list_id'] ) ? $mailchimp_options['somdn_newsletter_mailchimp_list_id'] : '' ;

			if ( empty( $api_key ) || empty( $list_id ) ) {
				return;
			}

			$MailChimp = new SOMDN_MailChimp( $api_key );

			// Submit subscriber data to MailChimp
			// For parameters doc, refer to: http://developer.mailchimp.com/documentation/mailchimp/reference/lists/members/
			// For wrapper's doc, visit: https://github.com/drewm/mailchimp-api
			$result = $MailChimp->post("lists/$list_id/members", [
				'email_address' => $user_email,
				'merge_fields'  => ['FNAME'=>$user_fname, 'LNAME'=>$user_lname],
				'status'        => 'subscribed',
			]);

			if ( $MailChimp->success() ) {
				somdn_email_site_admin_subbed( $post_id );
			// Success message
				//echo '<h4>Thank you, you have been added to our mailing list.</h4>';
				//exit;
			} else {
			// Display error
				//echo $MailChimp->getLastError();
				//echo'<br>';
				//echo '<pre>';
				//print_r( $MailChimp->getLastResponse() );
				//echo '</pre>';
				//exit;
			// Alternatively you can use a generic error message like:
			// echo "<h4>Please try again.</h4>";
			}

		}

	}

}
*/