<?php
/**
 * Free Downloads - WooCommerce - Download Redirect Functions
 * 
 * Various functions.
 * 
 * @version	3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'somdn_on_activate', 'somdn_on_activate_temp_downloads_cron', 40 );
function somdn_on_activate_temp_downloads_cron() {
	if ( ! wp_next_scheduled ( 'somdn_delete_temp_download_posts_event' ) ) {
		wp_schedule_event( time(), 'hourly', 'somdn_delete_temp_download_posts_event' );
	}
}

add_action( 'somdn_on_deactivate', 'somdn_on_deactivate_temp_downloads_cron', 40 );
function somdn_on_deactivate_temp_downloads_cron() {
	wp_clear_scheduled_hook( 'somdn_delete_temp_download_posts_event' );
}

add_action( 'somdn_delete_temp_download_posts_event', 'somdn_delete_temp_download_redirect_posts', 10 );
function somdn_delete_temp_download_redirect_posts() {

	// Delete temp redirect downloads older than 24 hours
	$temp_redirect_expire = intval( apply_filters( 'somdn_delete_temp_download_redirect_hours', 24 ) );
	$temp_redirect_before_date = date( 'Y-m-d H:i:s', strtotime( '-' . $temp_redirect_expire . ' hours' ) );

	$temp_redirect_args = array(
		'posts_per_page' => -1,
		'post_type' => 'somdn_temp_download',
		'fields' => 'ids',
		'meta_key' => 'download_type',
		'meta_value' => 'redirect',
		'date_query' => array(
			'before' => $temp_redirect_before_date
		)
	);

	$temp_redirect_posts = new WP_Query( $temp_redirect_args );
	$temp_redirect_posts_array = array();
	$temp_redirect_posts_count = $temp_redirect_posts->found_posts;

	$deleted_count = 0;

	if ( ! empty( $temp_redirect_posts_count ) ) {
		$temp_redirect_posts_array = $temp_redirect_posts->posts;
		foreach ( $temp_redirect_posts_array as $redirect_post ) {
			$deleted_count++;
			wp_delete_post( $redirect_post, true );
		}
	}

	if ( somdn_is_debug_on() && $deleted_count > 0 ) {
		somdn_write_log( '[DEBUG] Cleared temporary redirect downloads older than ' . $temp_redirect_expire . ' hours. Deleted: ' . $deleted_count . '.' );
	}

}

add_action( 'somdn_delete_temp_download_posts_event', 'somdn_delete_temp_download_email_posts', 20 );
function somdn_delete_temp_download_email_posts() {

	// Delete temp email downloads older than 30 days
	$temp_email_expire = intval( apply_filters( 'somdn_delete_temp_download_email_days', 30 ) );
	$temp_email_before_date = date( 'Y-m-d H:i:s', strtotime( '-' . $temp_email_expire . ' days' ) );

	$temp_email_args = array(
		'posts_per_page' => -1,
		'post_type' => 'somdn_temp_download',
		'fields' => 'ids',
		'meta_key' => 'download_type',
		'meta_value' => 'email',
		'date_query' => array(
			'before' => $temp_email_before_date
		)
	);

	$temp_email_posts = new WP_Query( $temp_email_args );
	$temp_email_posts_array = array();
	$temp_email_posts_count = $temp_email_posts->found_posts;

	$deleted_count = 0;

	if ( ! empty( $temp_email_posts_count ) ) {
		$temp_email_posts_array = $temp_email_posts->posts;
		foreach ( $temp_email_posts_array as $email_post ) {
			$deleted_count++;
			wp_delete_post( $email_post, true );
		}
	}

	if ( somdn_is_debug_on() && $deleted_count > 0 ) {
		somdn_write_log( '[DEBUG] Cleared temporary email downloads older than ' . $temp_email_expire . ' days.  Deleted: ' . $deleted_count . '.' );
	}

}

//add_action( 'somdn_before_form_inputs_simple', 'somdn_before_form_inputs_redirect' );
//add_action( 'somdn_before_form_inputs_variation', 'somdn_before_form_inputs_redirect' );
function somdn_before_form_inputs_redirect() {

	$download_type_options = get_option( 'somdn_download_type_settings' );
	$type = isset( $download_type_options['somdn_download_type_option'] ) ? intval( $download_type_options['somdn_download_type_option'] ) : 0 ;

	if ( empty( $type ) || $type == 0 ) {
		// Download type is standard instant downloads. Just bail.
		return;
	}

	if ( $type == 1 ) {
		// Redirect to confirmation page, output the hidden input with a redirect action
		echo '<input type="hidden" name="somdn_redirect_action" value="somdn_redirect_action">';
		return;
	}

	if ( $type == 2 ) {
		// Email URL then redirect to confirmation page, output the hidden input with a redirect action
		echo '<input type="hidden" name="somdn_redirect_action" value="somdn_redirect_action">';
		return;
	}

}

/**
 * This 'wp_loaded' (init) check determines whether or not a default download request has been made that needs to be
 * overridden using a redirect method instead. If true, a temporary download post is created, and the user is redirected
 * to the "Thank You" page, with special vars added to the URL for security checks.
 */
add_action( 'wp_loaded', 'somdn_downloader_download_redirect_override', 500 );
function somdn_downloader_download_redirect_override() {

	// Bail if no 'somdn_redirect_action' found in $_POST
	//if ( empty( $_POST['somdn_redirect_action'] ) )
	//	return;

	// Bail if no 'action' found in $_POST. Without the 'action' we don't know what download data to save in the post
	if ( empty( $_POST['action'] ) )
		return;

	$download_type_options = get_option( 'somdn_download_type_settings' );
	$download_type = isset( $download_type_options['somdn_download_type_option'] ) ? intval( $download_type_options['somdn_download_type_option'] ) : 0 ;

	if ( empty( $download_type ) || $download_type == 0 ) {
		// Download type is standard instant downloads.
		return;
	}

	$tracked = somdn_are_downloads_tracked();
	$capture_emails_active = somdn_is_email_capture_enabled();

	$delivery_method = '';
	$redirect_page = '';

	if ( $download_type == 1 ) {
		$delivery_method = 'redirect';
		$redirect_page = isset( $download_type_options['somdn_download_type_redirect_page'] ) ? intval( $download_type_options['somdn_download_type_redirect_page'] ) : '' ;
	} elseif ( $download_type == 2 ) {
		$delivery_method = 'email';
		$redirect_page = isset( $download_type_options['somdn_download_type_email_page'] ) ? intval( $download_type_options['somdn_download_type_email_page'] ) : '' ;
	} else {
		// Not a proper redirect type, bail
		somdn_write_log( $download_type . ' is not a valid download delivery method. Please check your settings. Defaulted to Instant Download.' );
		return;
	}

	// If there's no redirect page set up just bail, defaulting to instant download
	if ( empty( $redirect_page ) ) {
		// Let's write to our log file for debugging purposes
		somdn_write_log( 'Redirect download attempt failed. No page set up in settings. Defaulted to Instant Download.' );
		return;
	}

	if ( ! get_post_type( $redirect_page ) ) {
		// Let's write to our log file for debugging purposes
		somdn_write_log( 'Redirect download attempt failed. The selected Redirect Page in the settings does not exist.' );
		// Because there isn't a valid redirect page set up, fall back on default instant download.
		return;
	}

	$redirect_page_url = get_the_permalink( $redirect_page );

	if ( empty( $redirect_page_url ) ) {
		// Let's write to our log file for debugging purposes
		somdn_write_log( 'Redirect download attempt failed. Could not find permalink for Redirect Page in settings.' );
		// Because there isn't a valid redirect page set up, fall back on default instant download.
		return;
	}

	// We have a value in our redirect page setting
	$redirect_page_set = true;

	// Because this is a non-standard download delivery method we remove the default download actions.
	remove_action( 'wp_loaded', 'somdn_downloader_init', 999 );
	remove_action( 'wp_loaded', 'somdn_downloader_variations_init', 999 );

	// Now let's start grabbing the download data from $_POST and use it to verify the redirect request is valid
	$action = sanitize_key( $_POST['action'] ); // The free download action (single file, mutli-variation etc)
	$actions = somdn_get_download_actions();
	$variation_actions = somdn_get_download_variation_actions();

	// We check the 'action' to ensure it's a valid download action, and bail if not
	if ( ! in_array( $action, $actions ) && ! in_array( $action, $variation_actions ) )
		return;

	// We build our standard errors variable for default download key checks, and product download validation
	$_REQUEST['somdn_errors'] = array();

	// Bail if the download key was invalid, meaning the request may have been old.
	// Because download forms submit to the product page, the error will display on there.
	if ( ! somdn_verify_download_request_key() ) {
		return false;
	}

	$free_download_type = 'simple';
	if ( in_array( $action, $actions ) ) {
		// Download request is standard
	} elseif ( in_array( $action, $variation_actions ) ) {
		// Download request is variation
		$free_download_type = 'variation';
	} else {
		// No download action, bail
		return false;
	}

	// Get the Product and Variation ID from $_POST. Variation may not be present.
	$product_id = intval( isset( $_POST['somdn_product'] ) ? $_POST['somdn_product'] : 0 );
	$variation_id = intval( isset( $_POST['somdn_variation'] ) ? $_POST['somdn_variation'] : 0 );

	// Bail if there's no Product ID
	if ( empty( $product_id ) )
		return false;

	// Check if the 'somdn_variation' is set in $_POST but it has no ID attached to it, meaning no variation selected
	if ( ( array_key_exists( 'somdn_variation', $_POST ) ) && empty( $variation_id ) ) {
		return false;
	}

	switch ( $free_download_type ) {
		case 'simple':
			if ( ! somdn_is_download_valid( $product_id ) ) {
				return false;
			}
			break;
		case 'variation':
			if ( ! somdn_is_download_valid_variation( $product_id, $variation_id ) ) {
				return false;
			}
			break;
		default:
			if ( ! somdn_is_download_valid( $product_id ) ) {
				return false;
			}
			break;
	}

	//somdn_debug_array($_POST);
	//exit;

	// Let's grab the $_POST variables relating to the file and download type.
	$files_list = array();
	$total_files = intval( isset( $_POST['somdn_totalfiles'] ) ? $_POST['somdn_totalfiles'] : 0 );
	$download_all = isset( $_POST['somdn_download_files_all'] ) ? trim( $_POST['somdn_download_files_all'] ) : '' ;
	$pdf_output = isset( $_POST['pdf'] ) ? trim( $_POST['pdf'] ) : '' ;
	$productfile = intval( isset( $_POST['somdn_productfile'] ) ? $_POST['somdn_productfile'] : 0 );

	if ( ! empty( $total_files ) ) {
		// Because the $total_files variable exists, we know that a multi-file checkbox or links form was used.
		// We want to loop through possible available files and build an array of files that the downloader can use
		$for_count = 0;
		while ( $total_files > $for_count ) {
			$for_count++;
			$current_file = 'somdn_download_file_' . $for_count;
			if ( ! empty( $_POST[$current_file] ) ) {
				// This file has been requested, so add it to our list
				$files_list[] = $current_file;
			}
		}
	}

	$user_id = get_current_user_id();

	// Create a timestamp secret key to store in the temp download
	$secret_key = time();
	// Create a special key using the product id as an int
	$product_key = somdn_get_temp_download_key_from_int( $product_id );

	// Build the redirect data associative array
	// All of keys match what the HTML inputs on a download from would be called, to match possible $_POST submissions
	$redirect_data = array(
		'action' => $action,
		'somdn_product' => $product_id
	);

	if ( ! empty( $variation_id ) ) {
		// This is a variation download
		$redirect_data['somdn_variation'] = $variation_id;
	}

	// Now let's grab the user details from $_POST, which would be from the email capture form. 
	// If nothing found it also checks for user data for the logged in user
	$details = somdn_get_customer_tracking_from_post_data( $_POST );

	$user_fname     = isset( $details['somdn_user_fname'] ) ? sanitize_text_field( $details['somdn_user_fname'] ) : '' ;
	$user_lname     = isset( $details['somdn_user_lname'] ) ? sanitize_text_field( $details['somdn_user_lname'] ) : '' ;
	$user_tel       = isset( $details['somdn_user_tel'] ) ? sanitize_text_field( $details['somdn_user_tel'] ) : '' ;
	$user_company   = isset( $details['somdn_user_company'] ) ? sanitize_text_field( $details['somdn_user_company'] ) : '' ;
	$user_website   = isset( $details['somdn_user_website'] ) ? sanitize_text_field( $details['somdn_user_website'] ) : '' ;
	$user_email     = isset( $details['somdn_user_email'] ) ? sanitize_email( $details['somdn_user_email'] ) : '' ;
	$user_subscribe = isset( $details['somdn_capture_email_subscribe'] ) ? esc_attr( $details['somdn_capture_email_subscribe'] ) : '' ;

	$redirect_data['somdn_download_user_name']      = $user_fname;
	$redirect_data['somdn_download_user_lname']     = $user_lname;
	$redirect_data['somdn_download_user_tel']       = $user_tel;
	$redirect_data['somdn_download_user_company']   = $user_company;
	$redirect_data['somdn_download_user_website']   = $user_website;
	$redirect_data['somdn_download_user_email']     = $user_email;
	$redirect_data['somdn_capture_email_subscribe'] = $user_subscribe;

	// If we're going to email a download link to the user, we need to make sure we have an email address.
	if ( $delivery_method == 'email' && empty( $user_email ) ) {
		// No email address was collected by somdn_get_customer_tracking_from_post_data(). Let's check if the user is logged in
		$current_user_id = get_current_user_id();
		if ( empty( $current_user_id ) && $capture_emails_active == false ) {
			// We can't email the user if they're not logged in and email capture is off
			somdn_write_log( 'Redirect for email download attempt failed. User not logged in and email capture is not enabled. Defaulted to Instant Download.' );
			// Fall back on default instant download.
			somdn_do_default_download( $free_download_type );
			return;
		}
		// No email address was found, do default download and write an error
		somdn_write_log( 'Redirect for email download attempt failed. No email address found for user ID# ' . $current_user_id . '. Defaulted to Instant Download.' );
		// Because saving the url to the new post failed, fall back on default instant download.
		somdn_do_default_download( $free_download_type );
		return;
	}

		//somdn_debug_array($redirect_data);
		//exit;
/*
	// If downloads are being tracked (which is not required for redirect) and emails captured we add all of the user contact data
	if ( $tracked == true && $capture_emails_active == true ) {
		// Now let's grab the user details from $_POST, which would be from the email capture form
		$details = somdn_get_customer_tracking_from_post_data( $_POST );

		$user_fname = isset( $details['somdn_user_fname'] ) ? sanitize_text_field( $details['somdn_user_fname'] ) : '' ;
		$user_lname = isset( $details['somdn_user_lname'] ) ? sanitize_text_field( $details['somdn_user_lname'] ) : '' ;
		$user_tel = isset( $details['somdn_user_tel'] ) ? sanitize_text_field( $details['somdn_user_tel'] ) : '' ;
		$user_company = isset( $details['somdn_user_company'] ) ? sanitize_text_field( $details['somdn_user_company'] ) : '' ;
		$user_website = isset( $details['somdn_user_website'] ) ? sanitize_text_field( $details['somdn_user_website'] ) : '' ;
		$user_email = isset( $details['somdn_user_email'] ) ? sanitize_email( $details['somdn_user_email'] ) : '' ;
		$user_subscribe = isset( $details['somdn_capture_email_subscribe'] ) ? esc_attr( $details['somdn_capture_email_subscribe'] ) : '' ;

		$redirect_data['somdn_download_user_name']      = $user_fname;
		$redirect_data['somdn_download_user_lname']     = $user_lname;
		$redirect_data['somdn_download_user_tel']       = $user_tel;
		$redirect_data['somdn_download_user_company']   = $user_company;
		$redirect_data['somdn_download_user_website']   = $user_website;
		$redirect_data['somdn_download_user_email']     = $user_email;
		$redirect_data['somdn_capture_email_subscribe'] = $user_subscribe;

	}
*/
	$user_download_data = array(
		'somdn_user_id' => intval( $user_id ),
		'somdn_user_ip' => somdn_current_user_ip(),
		'somdn_secret_key' => $secret_key,
		'somdn_product_key' => $product_key
	);

	if ( ! empty( $total_files ) ) {
		// We have a multiple file form, add the total file count to the data
		$redirect_data['somdn_totalfiles'] = $total_files;
	}

	if ( ! empty( $download_all ) ) {
		// We have a multiple file form, add the Download All option is selected or set
		$redirect_data['somdn_download_files_all'] = $download_all;
	}

	if ( ! empty( $files_list ) ) {
		// We have a checkbox form with selected files, add each selected file to the data
		foreach ( $files_list as $file ) {
			$redirect_data[$file] = true;
		}
	}

	if ( ! empty( $productfile ) ) {
		// We have a specific file from a multi-link download form
		$redirect_data['somdn_productfile'] = $productfile;
	}

	if ( ! empty( $pdf_output ) ) {
		// PDF viewer is being used
		$redirect_data['pdf'] = $pdf_output;
	}

	// Create a temporary post to store the download data
	$temp_download_information = array(
		'post_type' => 'somdn_temp_download',
		'post_title' => 'Temp Download (' . $delivery_method . '): ' . get_the_title( $product_id ),
		'post_content' => 'Temp Download',
		'post_status' => 'publish',
		'meta_input' => array(
			'user_data' => $user_download_data,
			'download_data' => $redirect_data,
			'download_type' => $delivery_method
		)
	);

	$temp_download_id = wp_insert_post( $temp_download_information );
	//$temp_download_id = 0;

	if ( ! $temp_download_id ) {
		// Post wasn't recored.
		somdn_write_log( 'Redirect download attempt failed. Temp download wp_insert_post function. Defaulted to Instant Download.' );
		// Because the temporary download creation failed, fall back on default instant download.
		somdn_do_default_download( $free_download_type );
		return;
	}

	// We want to add the temp download ID to the title, for debugging purposes.
	$new_title = array(
		'ID' => $temp_download_id,
		'post_title' => 'Temp Download #' . $temp_download_id . ' (' . $delivery_method . '): ' . get_the_title( $product_id )
	);
	wp_update_post( $new_title );

	// Create a special encoded key using the temp download ID number
	$download_key = somdn_get_temp_download_key_from_int( $temp_download_id );

	// Create a special encoded key using the $secret_key variable
	$secret_key_encoded = somdn_get_temp_download_key_from_int( $secret_key );

	// Create a special encoded key using the $delivery_method variable
	$type_encoded = somdn_get_temp_download_key_from_string( $delivery_method );

	// Create a special encoded key using the user id as an int
	// base64_encode called directly in case $user_id = 0
	$somdn_rrukey = base64_encode( $user_id );

	// Create an array of query args to add to the redirect url
	$redirect_url_args = array(
		'somdn_rrpage' => 'somdn_rrpage',
		'somdn_rrtdid' => $temp_download_id,
		'somdn_rrdkey' => $download_key,
		'somdn_rrskey' => $secret_key_encoded,
		'somdn_rrpkey' => $product_key,
		'somdn_rrukey' => $somdn_rrukey,
		'somdn_rrtype' => $type_encoded
	);

	$redirect_url = add_query_arg( $redirect_url_args, $redirect_page_url );

	if ( $delivery_method == 'redirect' ) {

		//echo '<p>$redirect_url = ' . $redirect_url . '</p>';
		wp_safe_redirect( $redirect_url );
		exit;

	} else {

		//$user_email = '';

		// We need to build a url the user can click to download the file, and email it to them.
		$email_url_args = array(
			'somdn_rremdl' => 'somdn_rremdl',
			'somdn_rrtdid' => $temp_download_id,
			'somdn_rrdkey' => $download_key,
			'somdn_rrskey' => $secret_key_encoded,
			'somdn_rrpkey' => $product_key,
			'somdn_rrukey' => $somdn_rrukey,
			'somdn_rrtype' => $type_encoded
		);

		$product_page_url = get_the_permalink( $product_id );
		// Pass the url through esc_url_raw() so we can save it to a post meta field
		$email_url_esc = esc_url( add_query_arg( $email_url_args, $product_page_url ) );
		$email_url = esc_url_raw( add_query_arg( $email_url_args, $product_page_url ) );
		$email_url_save_success = update_post_meta( $temp_download_id, 'email_url', $email_url );

		if ( ! $email_url_save_success ) {
			// Post wasn't recored.
			somdn_write_log( 'Redirect for email download attempt failed. URL could not be saved to post meta. Defaulted to Instant Download.' );
			// Fall back on default instant download.
			somdn_do_default_download( $free_download_type );
			return;
		}

		//echo '<p>get_the_title( $product_id ) = ' . html_entity_decode( wp_specialchars_decode( get_the_title( $product_id ) ) ) . '</p>';
		//exit;

		$product_name = html_entity_decode( wp_specialchars_decode( esc_html( get_the_title( $product_id ) ) ) );

		$email_defaults = somdn_get_site_email_defaults();

		//somdn_debug_array($email_defaults);

		$email_options = get_option( 'somdn_email_settings' );

		$email_sender_address = somdn_get_email_sender_address();

		$email_sender_address_setting = isset( $email_options['somdn_email_settings_sender_address'] ) ? $email_options['somdn_email_settings_sender_address'] : '' ;

		// Check if we have a custom email address set up and that it's a valid email address
		if ( ! empty( $email_sender_address_setting ) ) {
			if ( ! filter_var( $email_sender_address_setting, FILTER_VALIDATE_EMAIL ) ) {
				// We have a value for the sender address in the plugin settings, but it's not a valid email address
				somdn_write_log( 'Redirect for email download attempt failed. Email address for sender is not a valid email address. Defaulted to Instant Download.' );
				somdn_do_default_download( $free_download_type );
				return;
			}
		}

		// Let's check to make sure we actually have an email address to send from
		if ( empty( $email_sender_address ) ) {
			somdn_write_log( 'Redirect for email download attempt failed. Email address for sender not found. Defaulted to Instant Download.' );
			somdn_do_default_download( $free_download_type );
			return;
		}

		// This final email address validation check is just for good measure, the above 2 checks should catch any problems
		if ( ! filter_var( $email_sender_address, FILTER_VALIDATE_EMAIL ) ) {
			somdn_write_log( 'Redirect for email download attempt failed. Email address for sender is not a valid email address. Defaulted to Instant Download.' );
			somdn_do_default_download( $free_download_type );
			return;
		}

		$email_message = isset( $email_options['somdn_email_download_url_message'] ) ? $email_options['somdn_email_download_url_message'] : '' ;
		if ( empty( $email_message ) ) {
			$email_message = $email_defaults['content']['message'];
		}

		$email_subject = isset( $email_options['somdn_email_download_url_subject'] ) ? $email_options['somdn_email_download_url_subject'] : '' ;
		if ( empty( $email_subject ) ) {
			$email_subject = $email_defaults['content']['subject'];
		}

		$email_heading = isset( $email_options['somdn_email_download_url_heading'] ) ? $email_options['somdn_email_download_url_heading'] : '' ;
		if ( empty( $email_subject ) ) {
			$email_heading = $email_defaults['content']['heading'];
		}

		$email_message_link_text = apply_filters( 'somdn_email_message_link_text', $product_name, $temp_download_id, $email_url_esc );
		$email_message_link = '<a href="' . $email_url_esc . '">' . $email_message_link_text . '</a>';

		// First name to use in the email defaults to the email capture form
		$user_first_name = '';
		$user_first_name = $user_fname;
		$default_user_name = __( 'Customer', 'somdn-pro' );

		$site_user_data = get_user_by( 'ID', $user_id );
		if ( empty( $site_user_data ) ) {
			$username = $default_user_name;
		} else {
			$username = $site_user_data->user_login;
		}

		if ( empty( $user_first_name ) ) {
			$user_first_name = $username;
		}

		// Just default to Customer is nothing at all found
		if ( empty( $username ) ) {
			$user_first_name = $default_user_name;
		}

		$sitename = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

		$content_type = somdn_get_email_content_type();
		$email_expiry = isset( $download_type_options['somdn_download_type_email_expire_time'] ) ? intval( $download_type_options['somdn_download_type_email_expire_time'] ) : 24 ;
		if ( $email_expiry == 0 ) {
			$email_expiry = 24;
		}
		if ( $email_expiry > 168 ) {
			// Maximum expiration time is 168 hours (7 days)
			$email_expiry = 168;
		}

		/**
		 * Strippable content
		 * {link} - The download URL
		 * {product} - The product name
		 * {username} - The username (default is "Customer") if none found
		 * {site_name} - The site name
		 * {hours} - The number of hours until expiry
		 */

		$replacements = array(
			'subject' => array(
				'{product}' => $product_name,
				'{site_name}' => $sitename
			),
			'message' => array(
				'{product}' => $product_name,
				'{site_name}' => $sitename,
				'{first_name}' => $user_first_name,
				'{username}' => $username,
				'{hours}' => (string) $email_expiry
			),
			'heading' => array(
				'{product}' => $product_name,
				'{site_name}' => $sitename
			)
		);

		//echo '<p>$product_name = ' . $product_name . '</p>';
		//echo '<p>$email_subject before = ' . $email_subject . '</p>';

		// Let's strip/replace placeholders and then clean the subject
		$email_subject = str_replace( "{product}", $product_name, $email_subject );
		$email_subject = str_replace( "{site_name}", $sitename, $email_subject );
		$email_subject = wp_specialchars_decode( $email_subject );
		//echo '<p>$email_subject = ' . $email_subject . '</p>';
		//exit;

		// Let's strip/replace placeholders and then clean the message
		$email_message = str_replace( "{product}", $product_name, $email_message );
		$email_message = str_replace( "{first_name}", $user_first_name, $email_message );
		$email_message = str_replace( "{username}", $username, $email_message );
		$email_message = str_replace( "{site_name}", $sitename, $email_message );
		$email_message = str_replace( "{hours}", $email_expiry, $email_message );

		if ( $content_type == 'text/plain' ) {
			$email_message = str_replace( "{link}", $email_url, $email_message );
			$email_message = wp_specialchars_decode( esc_html( $email_message ) );
		} else {
			$email_message = str_replace( "{link}", $email_message_link, $email_message );
			$email_message = wp_kses_post( $email_message );
		}

		// Let's strip/replace placeholders and then clean the heading
		$email_heading = str_replace( "{product}", $product_name, $email_heading );
		$email_heading = str_replace( "{site_name}", $sitename, $email_heading );
		$email_heading = wp_specialchars_decode( esc_html( $email_heading ) );

		//echo $email_message;
		//exit;

		// Added in 3.1.91
		do_action( 'somdn_download_type_pre_send_email', $product_id );

		// Now let's attempt to actually send the email
		$email_sent = somdn_send_email( $type = 'free_download_url', $user_email, $email_subject, $email_message, $email_heading );

		if ( $email_sent == false ) {
			// The email didn't send for some reason. Default back to instant downloads.
			somdn_write_log( 'Redirect for email download attempt failed. Email could not be sent. Defaulted to Instant Download.', 1 );
			somdn_do_default_download( $free_download_type );
			return;
		}

		// Added in 3.1.91
		do_action( 'somdn_download_type_post_send_email', $product_id );

		//wp_safe_redirect( $product_page_url );
		wp_safe_redirect( $redirect_url );
		exit;

	}

}

/**
 * This 'wp_loaded' (init) check determines whether or not a download redirect page request is currently loading.
 * Typical scenario for the check being true is after the "Download Now" button is clicked, and the user has been
 * redirected to the "Thank You" page.
 */
add_action( 'wp_loaded', 'somdn_init_check_for_get_redirect', 800 );
function somdn_init_check_for_get_redirect() {

	$redirect_action = isset( $_GET['somdn_rrpage'] ) ? sanitize_text_field( $_GET['somdn_rrpage'] ) : '' ;
	$redirect_download_action = isset( $_POST['somdn_rrpost'] ) ? sanitize_text_field( $_POST['somdn_rrpost'] ) : '' ;
	if ( $redirect_action !== 'somdn_rrpage' ) {
		// This is not a redirect temp download request, bail.
		return;
	}

	if ( $redirect_download_action ) {
		// This is an actual download request, bail because another action will take care of it.
		return;
	}

	// If $valid_request fails here it's because some or all of the data in the url is invalid, possibly manually entered.
	// If the request is invalid the action is to simply redirect to the default shop page
	$valid_request = somdn_validate_redirect_load_request();
	if ( empty( $valid_request ) || $valid_request == false ) {
		$redirect_url = '';
		$shop_id = get_option( 'woocommerce_shop_page_id' ) ? intval( get_option( 'woocommerce_shop_page_id' ) ) : 0 ;
		if ( $shop_id > 0 ) {
			$shop_url = get_the_permalink( $shop_id );
			$redirect_url = esc_url( $shop_url );
		} else {
			$redirect_url = esc_url( home_url() );
		}
		wp_redirect( $redirect_url );
		exit;
	}

	// true

}

/**
 * This 'wp_loaded' (init) check determines whether or not a temp download is currently being requested.
 * Typical scenario for the check being true is when the user is already on the "Thank You" page.
 */
add_action( 'wp_loaded', 'somdn_init_check_for_redirect_download_request', 850 );
function somdn_init_check_for_redirect_download_request() {

	$redirect_download_action = isset( $_POST['somdn_rrpost'] ) ? sanitize_text_field( $_POST['somdn_rrpost'] ) : '' ;
	if ( $redirect_download_action !== 'somdn_rrpost' ) {
		// This is not a redirect temp download request, bail.
		return;
	}

	$_REQUEST['somdn_redirect_errors'] = array();

	// First we validate the request variables
	$download_valid = somdn_validate_redirect_download_request();
	if ( empty( $download_valid ) || $download_valid == false ) {
		// The download request was invalid, errors may output on the product page
		return false;
	}

	// Redirected download request is valid, default downloader behaviour will manage the rest now.
	// We just need to add each of the standard download form values to the $_POST variable.
	if ( isset( $download_valid['download_vars'] ) ) {
		$download_request_data = $download_valid['download_vars'];
		foreach ( $download_request_data as $key => $value ) {
			$_POST[$key] = $value;
			//echo '<p>$key = ' . $key . ' : $value = ' . $value . '</p>';
		}
	}

	//exit;

}

/**
 * This 'wp_loaded' (init) check determines whether or not a temp download is currently being requested.
 * Typical scenario for the check being true is when the user is already on the "Thank You" page.
 */
add_action( 'wp_loaded', 'somdn_init_check_for_email_download_request', 860 );
function somdn_init_check_for_email_download_request() {

	$email_download_action = isset( $_REQUEST['somdn_rremdl'] ) ? sanitize_text_field( $_REQUEST['somdn_rremdl'] ) : '' ;
	if ( $email_download_action !== 'somdn_rremdl' ) {
		// This is not a redirect temp download request, bail.
		return;
	}

	$_REQUEST['somdn_redirect_errors'] = array();

	//$validated_vars = somdn_validate_redirect_load_request();
	//somdn_debug_array($validated_vars);
	//exit;

	//echo '<p>Here we go</p>';
	//exit;

	// First we validate the request variables
	$download_valid = somdn_validate_redirect_download_request();
	if ( empty( $download_valid ) || $download_valid == false ) {
		// The download request was invalid, errors may output on the product page
		return false;
	}

	//echo (string) $download_valid['request_vars']['secret_key_decoded'];

	//somdn_debug_array($download_valid);
	//exit;

	// Redirected download request is valid, default downloader behaviour will manage the rest now.

	// Before we do anything we mark the temporary download post for deletion, so it is "expired".
	// Once an email download link has been used once it can't be used again

	$request_vars = $download_valid['request_vars'];
	$download_id = intval( $request_vars['download_id'] );
	$temp_download_deleted = update_post_meta( $download_id, 'link_expired', true );
	if ( empty( $temp_download_deleted ) ) {
		// Something went wrong with the deletion. It's not a big deal as all temp posts are removed periodically, but worth writing to log
		somdn_write_log( 'Email download link error. Could not mark temporary download post for deletion. ID# ' . $download_id . '.' );
	}

	// We just need to add each of the standard download form values to the $_POST variable.
	if ( isset( $download_valid['download_vars'] ) ) {
		$download_request_data = $download_valid['download_vars'];
		foreach ( $download_request_data as $key => $value ) {
			$_POST[$key] = $value;
			//echo '<p>$key = ' . $key . ' : $value = ' . $value . '</p>';
		}
		$download_key = (string) $download_valid['request_vars']['secret_key'];
		// $download_key has to be added to the global $_REQUEST variable. It won't work in $_POST
		$_REQUEST['somdn_download_key'] = $download_key;
	}

}

function somdn_is_redirect_secret_key_valid( $key ) {

	if ( empty( $key ) )
		return false;

	// Key is a timestamp and $minutes is the difference between the current time and the timestamp of the download request
	$now = time(); // current time
	$diff = $now - $key;
	$minutes = floor( ( $diff / 60 ) );

	// Default to 60 minutes for redirect requests, so they can't continue to be used, requiring a fresh one to be generated
	// This is overriden for email downloads by using the filter 'somdn_redirect_download_key_expire', changing the default to 24 hours
	$expiration_time = intval( apply_filters( 'somdn_redirect_download_key_expire', 60 ) );

	//echo '<p>$minutes = ' . $minutes . '</p>';
	//echo '<p>$key = ' . $key . '</p>';
	//echo '<p>$expiration_time = ' . $expiration_time . '</p>';


	// Key is valid for $expiration_time minutes
	$valid = ( $minutes < $expiration_time ) ? true : false ;

	//echo '<p>$valid = ' . $valid . '</p>';

	//exit;
	return $valid;

}

add_filter( 'somdn_redirect_download_key_expire', 'somdn_email_download_key_expire', 10 );
function somdn_email_download_key_expire( $minutes ) {

	$expire_mins = $minutes;
	$options = get_option( 'somdn_download_type_settings' );
	// Which method are we using?
	$download_type = isset( $options['somdn_download_type_option'] ) ? intval( $options['somdn_download_type_option'] ) : 0 ;
	if ( $download_type == 2 ) {
		// Email downloads remain valid for 24 hours by default, or set by the user.
		$email_expire_hours = isset( $options['somdn_download_type_email_expire_time'] ) ? intval( $options['somdn_download_type_email_expire_time'] ) : 24 ;
		if ( $email_expire_hours == 0 ) {
			$email_expire_hours = 24;
		}
		if ( $email_expire_hours > 168 ) {
			// Maximum expiration time is 168 hours (7 days)
			$email_expire_hours = 168;
		}
		$email_expire_mins = floor( $email_expire_hours * 60 );
		$expire_mins = $email_expire_mins;
	}

	//echo '$expire_mins = ' . $expire_mins;

	return $expire_mins;
}

function somdn_validate_redirect_download_request() {

	$show_debug = somdn_is_debug_on();

	// First we validate the request variables
	$validated_vars = somdn_validate_redirect_load_request();

	if ( empty( $validated_vars ) || $validated_vars == false ) {
		// Initial validation failed, usually due to invalid data being supplied.
		if ( $show_debug  ) {
			somdn_write_log( '[DEBUG] Redirect download failed. Initial validation failed, usually due to invalid data being supplied.' );
		}
		return false;
	}

	// All checked, sanitised, and decoded (where needed) variables returned in $validated_vars.
	$download_id          = $validated_vars['download_id'];
	$download_key         = $validated_vars['download_key'];
	$secret_key           = $validated_vars['secret_key'];
	$product_key          = $validated_vars['product_key'];
	$user_id_var          = $validated_vars['user_id_var'];
	$download_key_decoded = $validated_vars['download_key_decoded'];
	$secret_key_decoded   = $validated_vars['secret_key_decoded'];
	$product_key_decoded  = $validated_vars['product_key_decoded'];
	$user_id_var_decoded  = $validated_vars['user_id_var_decoded'];

	$temp_download_type = get_post_meta( $download_id, 'download_type', true );

	$download_options = get_option( 'somdn_download_type_settings' );
	$email_expire_message = isset( $download_options['somdn_download_type_email_expire_message'] ) ? $download_options['somdn_download_type_email_expire_message'] : '' ;
	$email_used_message = isset( $download_options['somdn_download_type_email_used_message'] ) ? $download_options['somdn_download_type_email_used_message'] : '' ;

	if ( empty( $email_expire_message ) ) {
		$email_expire_message = __( 'Your download key has now expired, please try again.', 'somdn-pro' );
	} else {
		$email_expire_message = wp_specialchars_decode( esc_html( $email_expire_message ) );
	}

	if ( empty( $email_used_message ) ) {
		$email_used_message = __( 'Your download key has already been used, please try again.', 'somdn-pro' );
	} else {
		$email_used_message = wp_specialchars_decode( esc_html( $email_used_message ) );
	}

	// Verify the $secret_key variable created just for redirect is still valid
	if ( ! somdn_is_redirect_secret_key_valid( $secret_key_decoded ) ) {
		// The key has expired, these only last a short time. 24 hours for email downloads, 1 hour for page redirects
		if ( $temp_download_type == 'redirect' ) {
			// For redirect downloads the key is valid for 1 hour. We don't need to let the user know it's expired
			if ( $show_debug  ) {
				somdn_write_log( '[DEBUG] Redirect download request expired. User not informed. Redirected to product page. Temp download ID# ' . $download_id . '.' );
			}
		} else {
			// For email downloads we want to show the warning to the user on the product page
			$expired_redirect_key = $email_expire_message;
			$errors['expired_redirect_key'] = $expired_redirect_key;
			array_push( $_REQUEST['somdn_redirect_errors'], $errors);
		}
		return false;
	}

	// For email downloads, verify the download ID post has not already been used
	if ( $temp_download_type == 'email' ) {
		$temp_download_deleted = get_post_meta( $download_id, 'link_expired', true );
		if ( ! empty( $temp_download_deleted ) ) {
			$used_key = $email_used_message;
			$errors['used_key'] = $used_key;
			array_push( $_REQUEST['somdn_redirect_errors'], $errors);
			return false;
		}
	}

	$user_data = get_post_meta( $download_id, 'user_data', true ); // Get the temp download user data array
	if ( empty( $user_data ) || ! is_array( $user_data ) ) {
		return false;
	}
	$temp_download_data = get_post_meta( $download_id, 'download_data', true ); // Get the temp download data array
	if ( empty( $temp_download_data ) || ! is_array( $temp_download_data ) ) {
		return false;
	}

	// 'somdn_user_id' is saved raw and does not need to be decoded
	$download_user_id     = isset( $user_data['somdn_user_id'] ) ? intval( $user_data['somdn_user_id'] ) : 0 ;
	// 'somdn_secret_key' is saved raw and does not need to be decoded
	$download_secret_key  = isset( $user_data['somdn_secret_key'] ) ? intval( $user_data['somdn_secret_key'] ) : 0 ;
	// 'somdn_product_key' is saved encoded and does need to be decoded
	$download_product_key = isset( $user_data['somdn_product_key'] ) ? intval( somdn_decode_download_key( $user_data['somdn_product_key'] ) ) : 0 ;

	// Does the User ID of the request and temp download match
	if ( $download_user_id != $user_id_var_decoded ) {
		return false;
	}

	// Does the decoded secret key of the request and temp download match
	if ( $download_secret_key != $secret_key_decoded ) {
		return false;
	}

	// Does the decoded product key of the request and temp download match
	if ( $download_product_key != $temp_download_data['somdn_product'] ) {
		return false;
	}

	// Finally we check that the original requester is the same as the one seeing the form
	// Note: If both $user_id_var_decoded and $user_id are ZERO this is ok, it means it is a guest download
	$current_user_id = get_current_user_id();
	$verify_user = apply_filters('somdn_verify_user_if_redirected', true);
	if ( $verify_user === true ) {
		if ( $current_user_id != $user_id_var_decoded ) {
			// The user IDs do not match
			somdn_write_log( 'A user attempted a redirect download using a different user ID than the one used to raise the link. User ID# ' . $user_id_var_decoded . ' in the download data, User ID# ' . $current_user_id . ' currently in session. Temp download ID# ' . $download_id . '.' );
			return false;
		}
	}

	// Everything is validated, return all checked variables

	$validated_vars_all = array(
		'request_vars' => $validated_vars,
		'download_vars' => $temp_download_data,
		'user_vars' => $user_data
	);

	return $validated_vars_all;

}

function somdn_validate_redirect_load_request() {

	$valid_request = false;
	$show_debug = somdn_is_debug_on();

	// We use the $_REQUEST global so we can capture either $_GET or $_POST variables

	// INT: ID of the temp download, left raw for comparison
	$download_id = isset( $_REQUEST['somdn_rrtdid'] ) ? intval( $_REQUEST['somdn_rrtdid'] ) : '' ;
	// INT: ID of the temp download, encoded for decode
	$download_key = isset( $_REQUEST['somdn_rrdkey'] ) ? sanitize_text_field( $_REQUEST['somdn_rrdkey'] ) : '' ;
	// Special key saved in the temp download to check, encoded in url
	$secret_key   = isset( $_REQUEST['somdn_rrskey'] ) ? sanitize_text_field( $_REQUEST['somdn_rrskey'] ) : '' ;
	// INT: ID of the product being downloaded, encoded for decode
	$product_key  = isset( $_REQUEST['somdn_rrpkey'] ) ? sanitize_text_field( $_REQUEST['somdn_rrpkey'] ) : '' ;
	// INT: ID of the user who requested the download originally, encoded for decode
	$user_id_var  = isset( $_REQUEST['somdn_rrukey'] ) ? sanitize_text_field( $_REQUEST['somdn_rrukey'] ) : '' ;
	// INT: Value of the requested temp download type, encoded for decode
	$request_var  = isset( $_REQUEST['somdn_rrtype'] ) ? sanitize_text_field( $_REQUEST['somdn_rrtype'] ) : '' ;

	//somdn_debug_array($_REQUEST);
	//echo '<p>$download_id = ' . $download_id . '</p>';
	//echo '<p>$download_key = ' . $download_key . '</p>';
	//echo '<p>$secret_key = ' . $secret_key . '</p>';
	//echo '<p>$product_key = ' . $product_key . '</p>';
	//echo '<p>$user_id_var = ' . $user_id_var . '</p>';
	//echo '<p>$request_var = ' . $request_var . '</p>';
	//exit;

	if ( empty( $download_id ) || empty( $download_key ) || empty( $secret_key ) || empty( $product_key ) || empty( $user_id_var ) || empty( $request_var ) ) {
		// If any of the required query vars are empty, return false
		if ( $show_debug  ) {
			somdn_write_log( '[DEBUG] Redirect download failed. Empty vars in somdn_validate_redirect_load_request().' );
		}
		return false;
	}

	// First let's start by decoding the encoded keys to check against the temporary download being requested
	$download_key_decoded = intval( somdn_decode_download_key( $download_key ) );
	$secret_key_decoded   = (string) somdn_decode_download_key( $secret_key );
	$product_key_decoded  = intval( somdn_decode_download_key( $product_key ) );
	$user_id_var_decoded  = intval( somdn_decode_download_key( $user_id_var ) );
	$request_var_decoded  = (string) somdn_decode_download_key( $request_var );

	// First we verify the download ID and the decoded download key match. If not, return false
	if ( $download_id !== $download_key_decoded ) {
		// The numbers don't match
		if ( $show_debug  ) {
			somdn_write_log( '[DEBUG] Redirect download failed. $download_id and key did not match.' );
		}
		return false;
	}

	// Verify the download ID post itself exists. If not, return false
	if ( false === get_post_status( $download_id ) ) {
		if ( $show_debug  ) {
			somdn_write_log( '[DEBUG] Email download request made but the post did not exist. Likely an old link. Temp download ID# ' . $download_id . '.' );
		}
		// No error shows to the user
		return false;
	}

	// Verify the post type for $download_id is 'somdn_temp_download'
	if ( get_post_type( $download_id ) != 'somdn_temp_download' ) {
		return false;
	}

	// Next we verify the product ID itself exists. If not, return false
	if ( false === get_post_status( $product_key_decoded ) ) {
		// This ID number is not for a post
		if ( $show_debug  ) {
			somdn_write_log( '[DEBUG] Redirect download failed. Invalid Product ID.' );
		}
		// No error shows to the user
		return false;
	}
	$product = somdn_get_product( $product_key_decoded );
	if ( empty( $product ) ) {
		// This ID number is a post but not a product
		if ( $show_debug  ) {
			somdn_write_log( '[DEBUG] Redirect download failed. ID is not a product.' );
		}
		// No error shows to the user
		return false;
	}

	// Check that the decoded secret key is a timestamp
	if ( ! somdn_is_timestamp( $secret_key_decoded ) ) {
		// This number is not a valid timestamp
		if ( $show_debug  ) {
			somdn_write_log( '[DEBUG] Redirect download failed. Secret key failed.' );
		}
		// No error shows to the user
		return false;
	}

	// Make sure the download request type queried matches the temporary download post
	$download_type = get_post_meta( $download_id, 'download_type', true );
	if ( empty( $download_type ) ) {
		// No download_type for download_id
		if ( $show_debug  ) {
			somdn_write_log( '[DEBUG] Redirect download failed. No type found in $download_id.' );
		}
		return false;
	}

	$valid_types = somdn_get_valid_download_types();

	if ( ! in_array( $request_var_decoded, $valid_types ) ) {
		// The request type is not valid
		if ( $show_debug  ) {
			somdn_write_log( '[DEBUG] Redirect download failed. Request type not valid.' );
		}
		return false;
	}

	// We got this far, the request vars have been validated
	// Now let's build an array of checked vars so we don't have to repeat the code

	$validated_vars = array(
		'download_id' => $download_id,
		'download_key' => $download_key,
		'secret_key' => $secret_key,
		'product_key' => $product_key,
		'user_id_var' => $user_id_var,
		'request_var' => $request_var,
		'download_key_decoded' => $download_key_decoded,
		'secret_key_decoded' => $secret_key_decoded,
		'product_key_decoded' => $product_key_decoded,
		'user_id_var_decoded' => $user_id_var_decoded,
		'request_var_decoded' => $request_var_decoded
	);

	return $validated_vars;

}

function somdn_get_download_redirect_shortcode_content( $shortcode_text = '' ) {

	$content = '';

	// First we validate the redirect download request.
	$validated_vars = somdn_validate_redirect_load_request();
	if ( empty( $validated_vars ) || $validated_vars == false ) {
		// Initial validation failed, usually due to invalid data being supplied in the url
		// Because this shouldn't happen for valid requests, the page still loads but no information shows
		return false;
	}

	$user_id              = get_current_user_id();
	$request_var_decoded  = $validated_vars['request_var_decoded'];
	$download_id          = $validated_vars['download_id'];
	$download_key         = $validated_vars['download_key'];
	$secret_key           = $validated_vars['secret_key'];
	$product_key          = $validated_vars['product_key'];
	$user_id_var          = $validated_vars['user_id_var'];
	$user_id_var_decoded  = $validated_vars['user_id_var_decoded'];
	$request_var          = $validated_vars['request_var'];
	$product_id           = $validated_vars['product_key_decoded'];

	// Final user ID check for security
	if ( $user_id_var_decoded != $user_id ) {
		somdn_write_log( 'Redirect download shortcode failed. User IDs did not match. Output nothing.' );
		return false;
	}

	$text = 'click here';

	if ( ! empty( $shortcode_text ) ) {
		$text = $shortcode_text;
	}

	// Let's grab the Download Delivery Settings
	$options = get_option( 'somdn_download_type_settings' );

	// Which method are we using?
	$download_type = isset( $options['somdn_download_type_option'] ) ? intval( $options['somdn_download_type_option'] ) : 0 ;

	if ( empty( $download_type ) || $download_type > 2 ) {
		// We're not redirecting or emailing any download links
		somdn_write_log( 'Redirect download shortcode failed. Download method selected is invalid.' );
		return false;
	}

	$content = '';
	ob_start();

	if ( $download_type == 1 && $request_var_decoded == 'redirect' ) {
		// Redirect download delivery - $request_type = $request_var_decoded;

		$redirect_message = isset( $options['somdn_download_type_redirect_message'] ) ? $options['somdn_download_type_redirect_message'] : '' ;
		$redirect_text = isset( $options['somdn_download_type_redirect_text'] ) ? $options['somdn_download_type_redirect_text'] : '' ;
		$click_here_default = __( 'click here', 'somdn-pro' );
		$redirect_message_output = '';

		if ( empty( $redirect_text ) ) {
			$redirect_text = $click_here_default;
		} else {
			$redirect_text = esc_html( $redirect_text );
		}

		$redirect_link = sprintf( '<a class="somdn-redirect-form-link" href="#" rel="nofollow">%s</a>', $redirect_text );

		$redirect_time = intval( isset( $options['somdn_download_type_redirect_time'] ) && $options['somdn_download_type_redirect_time']
		? $options['somdn_download_type_redirect_time']
		: 5 );
		if ( $redirect_time > 60 ) {
			$redirect_time = 60;
		} elseif ( $redirect_time <= 0 ) {
			$redirect_time = 5;
		}

		$default_redirect_text = sprintf(
			wpautop(
				__( 'Your download should start automatically in %1$s seconds. If it doesn\'t please %2$s. Do not refresh this page.', 'somdn-pro' )
			),
			intval( $redirect_time ),
			$redirect_link
		);

		if ( empty( $redirect_message ) ) {
			// Just output the default, translatable text
			$redirect_message = $default_redirect_text;
		} else {
			$redirect_message_output = wpautop( wp_kses_post( $redirect_message ) );
			$redirect_message_output = str_replace( "{click_here}", $redirect_link, $redirect_message_output );
			$redirect_message = $redirect_message_output;
		}

		do_action( 'somdn_before_redirect_page_message', $redirect_message, $product_id, $user_id_var_decoded, $validated_vars );

		echo $redirect_message;

		do_action( 'somdn_after_redirect_page_message', $redirect_message, $product_id, $user_id_var_decoded, $validated_vars );

		?>

		<?php do_action( 'somdn_before_download_redirect_form', $validated_vars ); ?>
		<form id="somdn_download_redirect_form" method="POST" action="<?php echo get_the_permalink( $product_id ); ?>">
			<?php do_action( 'somdn_before_download_redirect_form_inputs', $validated_vars ); ?>
			<input type="hidden" name="somdn_rrtdid" value="<?php echo $download_id; ?>">
			<input type="hidden" name="somdn_rrdkey" value="<?php echo $download_key; ?>">
			<input type="hidden" name="somdn_rrskey" value="<?php echo $secret_key; ?>">
			<input type="hidden" name="somdn_rrpkey" value="<?php echo $product_key; ?>">
			<input type="hidden" name="somdn_rrukey" value="<?php echo $user_id_var; ?>">
			<input type="hidden" name="somdn_rrtype" value="<?php echo $request_var; ?>">
			<input type="hidden" name="somdn_rrpost" value="somdn_rrpost">
			<?php $key = somdn_get_download_key(); ?>
			<input type="hidden" name="somdn_download_key" value="<?php echo $key; ?>">
			<?php do_action( 'somdn_after_download_redirect_form_inputs', $validated_vars ); ?>
		</form>
		<?php do_action( 'somdn_after_download_redirect_form', $validated_vars ); ?>

		<?php

	} elseif ( $download_type == 2 && $request_var_decoded == 'email' ) {
		// Email download delivery

		$download_data = get_post_meta( $download_id, 'download_data', true ); // Get the temp download user data array
		$download_email_address = isset( $download_data['somdn_download_user_email'] ) ? trim( esc_html( $download_data['somdn_download_user_email'] ) ) : '' ;
		//somdn_debug_array($download_data);

		$email_page_message = isset( $options['somdn_download_type_email_page_message'] ) ? $options['somdn_download_type_email_page_message'] : '' ;
		$email_page_default = __( 'A link to download the file has been emailed to you, please check your inbox.', 'somdn-pro' );
		$email_page_output = '';

		if ( empty( $email_page_message ) ) {
			$email_page_message = $email_page_default;
		} else {
			$email_page_output = wpautop( wp_kses_post( $email_page_message ) );
			$email_page_output = str_replace( "{email}", $download_email_address, $email_page_output );
			$email_page_message = $email_page_output;
		}

		do_action( 'somdn_before_email_page_message', $email_page_message, $product_id, $user_id_var_decoded, $validated_vars );

		echo $email_page_message;

		do_action( 'somdn_after_email_page_message', $email_page_message, $product_id, $user_id_var_decoded, $validated_vars );

	}

	$content = ob_get_clean();
	return $content;

}

add_shortcode( 'download_redirect', 'somdn_download_redirect_shortcode' );
function somdn_download_redirect_shortcode( $atts ) {

	// We use a global to ensure only a single form is output
	global $somdn_download_redirect_shortcode;

	// Return with no content if a form is already output on the page somewhere
	if ( ! empty( $somdn_download_redirect_shortcode ) ) {
		return;
	}

	// Global set to true, we are running a new download form
	$somdn_download_redirect_shortcode = true;

	// No download form has been output yet. Let's check this request is valid.
	$redirect_action = isset( $_GET['somdn_rrpage'] ) ? sanitize_text_field( $_GET['somdn_rrpage'] ) : '' ;
	if ( $redirect_action !== 'somdn_rrpage' ) {
		//This isn't a correct download request, output nothing.
		return;
	}

	// Attributes
	$atts = shortcode_atts(
		array(
			'text' => ''
		),
		$atts,
		'download_redirect'
	);

	$shortcode_text = $atts['text'];


	$download_content = somdn_get_download_redirect_shortcode_content( $shortcode_text );

	// Bail if no download button returned
	if ( ! $download_content ) {
		return;
	}

	$content = $download_content;
	return $content;

}

function somdn_get_valid_download_types() {
	$valid_types = array(
		'redirect',
		'email'
	);
	return $valid_types;
}

add_action( 'somdn_variation_errors', 'somdn_single_errors_redirect_output', 70 );
add_action( 'somdn_single_errors', 'somdn_single_errors_redirect_output', 70 );
function somdn_single_errors_redirect_output() {

	if ( empty( $_REQUEST ) ) {
		return;
	}

	ob_start();

	$somdn_errors = isset( $_REQUEST['somdn_redirect_errors'] ) ? $_REQUEST['somdn_redirect_errors'] : '' ;

	$somdn_errors_used = array();

	if ( ! empty( $somdn_errors ) && is_array( $somdn_errors ) ) :

		$allowed_tags = somdn_get_allowed_html_tags();

		foreach ( $somdn_errors as $somdn_error ) :
			if ( ! empty( $somdn_error ) && is_array( $somdn_error ) ) :
				foreach ( $somdn_error as $error ) :

					$cleaned_error = wpautop( wp_kses( $error, $allowed_tags ) );

					if ( ! in_array( $cleaned_error, $somdn_errors_used ) ) :

						array_push( $somdn_errors_used, $cleaned_error ); ?>

						<div class="<?php echo somdn_frontend_warning_class(); ?>">
							<?php echo $cleaned_error; ?>
						</div>

					<?php endif;

				endforeach;
			endif;
		endforeach;

	endif;

	$error_content = ob_get_clean();
	echo $error_content;

}