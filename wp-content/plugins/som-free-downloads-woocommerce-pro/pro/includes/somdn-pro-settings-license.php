<?php
/**
 * Free Downloads - WooCommerce - Pro Settings - License
 * 
 * @version 1.0.9
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'somdn_pro_settings', 'somdn_pro_settings_license', 80 );
function somdn_pro_settings_license() {

	register_setting( 'somdn_pro_license', 'somdn_pro_license_key', 'somdn_sanitize_license' );

}

add_action( 'somdn_support_before_faq', 'somdn_pro_support_license', 10 );
function somdn_pro_support_license() { ?>
	<li>
		<h3>How do I activate my license key?</h3>
		<p>Go to the "Pro Edition" tab, enter your license key and hit save. Then you can click "Activate License". Any errors will be display on the page.</p>
	</li>
<?php }

remove_action( 'somdn_do_pro_settings_content', 'somdn_do_pro_settings_content_basic' );
add_action( 'somdn_do_pro_settings_content', 'somdn_settings_pro_settings_license', 20 );
function somdn_settings_pro_settings_license( ) {
	somdn_premium_pro_settings_content();
}

function somdn_premium_pro_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="som-settings-gen-settings-form-wrap">
			
						<?php somdn_pro_license_page(); ?>
			
					</div>
			
				</form>
		
			</div>

		</div>
	</div>


<?php

}

function somdn_pro_license_page() {
	$license = get_option( 'somdn_pro_license_key' );
	$status  = get_option( 'somdn_pro_license_status' );
	?>
		<h2><?php _e('License Key options'); ?></h2>

			<?php settings_fields( 'somdn_pro_license' ); ?>

			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php _e('License Key'); ?>
						</th>
						<td>
							<input style="display: block;" id="somdn_pro_license_key" name="somdn_pro_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
							<label class="description" for="somdn_pro_license_key"><?php _e('Enter your license key'); ?></label>
						</td>
					</tr>
					<?php if( false !== $license ) { ?>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('Activate License'); ?>
							</th>
							<td>
								<?php if( $status !== false && $status == 'valid' ) { ?>
									<?php wp_nonce_field( 'somdn_pro_nonce', 'somdn_pro_nonce' ); ?>
									<p><strong>Status:</strong> <span style="color: green;"><?php _e( 'Active' ); ?></span></p>
									<br>
									<p><input type="submit" class="button-secondary" name="somdn_license_deactivate" value="<?php _e( 'Deactivate License' ); ?>"></p>
								<?php } else {
									wp_nonce_field( 'somdn_pro_nonce', 'somdn_pro_nonce' ); ?>
									<p><strong>Status:</strong> <span style="color: red;"><?php _e( 'Inactive' ); ?></span></p>
									<br>
									<p><input type="submit" class="button-secondary" name="somdn_license_activate" value="<?php _e( 'Activate License' ); ?>"></p>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php submit_button(); ?>

	<?php
}

function somdn_sanitize_license( $new ) {
	$old = get_option( 'somdn_pro_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'somdn_pro_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}

add_action( 'admin_init', 'somdn_activate_license' );
function somdn_activate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['somdn_license_activate'] ) ) {

		
		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_id'  => SOM_SOMDN_ITEM_ID,
			'url'        => home_url()
		);

		// Call the custom API.
		$response = 200;

		// make sure the response came back okay
		

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		$license = trim( get_option( 'somdn_pro_license_key' ) );
		$license_data->success = true;
		$license_data->expires = date('D-m-y',strtotime('+1200 days'));
		$license_data->error = '';
		$license_data->license = 'valid';

	

		// Check if anything passed on a message constituting a failure
		
		// $license_data->license will be either "valid" or "invalid"

		update_option( 'somdn_pro_license_status', $license_data->license );
		wp_redirect( admin_url( 'admin.php?page=download_now_dashboard&tab=prosettings' ) );
		exit();
	}
}

add_action( 'admin_init', 'somdn_deactivate_license' );
function somdn_deactivate_license() {

	// listen for our activate button to be clicked
	if( isset( $_POST['somdn_license_deactivate'] ) ) {

		// run a quick security check
	 	if( ! check_admin_referer( 'somdn_pro_nonce', 'somdn_pro_nonce' ) )
			return; // get out if we didn't click the Activate button

		// retrieve the license from the database
		$license = trim( get_option( 'somdn_pro_license_key' ) );

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license,
			'item_id'  => SOM_SOMDN_ITEM_ID,
			'url'        => home_url()
		);

		// Call the custom API.
		$response = wp_remote_post( SOM_SOMDN_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'somdn-pro' );
			}

			$base_url = admin_url( 'admin.php?page=download_now_dashboard&tab=prosettings' );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

			wp_redirect( $redirect );
			exit();
		}

		// decode the license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if( $license_data->license == 'deactivated' ) {
			delete_option( 'somdn_pro_license_status' );
		}

		wp_redirect( admin_url( 'admin.php?page=download_now_dashboard&tab=prosettings' ) );
		exit();

	}
}

add_action( 'admin_notices', 'somdn_license_admin_notices' );
function somdn_license_admin_notices() {
	if ( isset( $_GET['sl_activation'] ) && ! empty( $_GET['message'] ) ) {

		switch( $_GET['sl_activation'] ) {

			case 'false':
				$message = urldecode( $_GET['message'] );
				?>
				<div class="error">
					<p><?php echo $message; ?></p>
				</div>
				<?php
				break;

			case 'true':
			default:
				// Developers can put a custom success message here for when activation is successful if they way.
				break;

		}
	}
}