<?php
/**
 * Free Downloads - WooCommerce - Pro Compatibility Functions
 * 
 * Functions for compatibility with other plugins.
 * 
 * @version	3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'somdn_memberships_meta_after_options_group', 'somdn_wc_membership_meta_pro', 10, 1 );
function somdn_wc_membership_meta_pro( $post_id ) {

	$membership_options = get_option( 'somdn_pro_membership_limit_settings' );
	$member_exclude = isset( $membership_options['somdn_pro_limit_member_exclude'] ) ? $membership_options['somdn_pro_limit_member_exclude'] : 0 ;

	if ( $member_exclude ) {

		$current_member_exclude = get_post_meta( $post_id, 'somdn_membership_exclude_limits', true ); ?>

		<div class="options_group">
			<p class="form-field post_name_field">
				<span class="somdn-woo-meta-span">
					<label for="somdn_membership_exclude_limits">Exclude Limitations</label>
					<label class="label-checkbox">
					<input style="margin-right: 10px;" type="checkbox" id="somdn_membership_exclude_limits" name="somdn_membership_exclude_limits" value="1" <?php checked( 1, $current_member_exclude ); ?>>Exclude membership from any free download limitations</label>
					<span class="woocommerce-help-tip" data-tip="Only applies if limit restrictions are enabled in Free Downloads. For example: 5 per day."></span>
					<br>
					<span class="description">Only applies if limit restrictions are enabled in Free Downloads.</span>
				</span>
			</p>
		</div>

	<?php } ?>

	<div class="options_group">
		<fieldset class="form-field post_name_field">
			<span class="somdn-woo-meta-span">

				<label for="">Custom Limitations</label>

				<span class="somdn-woo-meta-span" style="font-size: 13px;">Override global free download limitations <span class="woocommerce-help-tip" data-tip="Only applies if limit restrictions are enabled in Free Downloads."></span></span>

				<?php

					$amount_value = get_post_meta( $post_id, 'somdn_membership_limit_amount', true );
					if ( empty( $amount_value ) ) $amount_value = '';

					$prod_value = get_post_meta( $post_id, 'somdn_membership_limit_products', true );
					if ( empty( $prod_value ) ) $prod_value = '';

					$freq_value = get_post_meta( $post_id, 'somdn_membership_limit_freq', true );
					if ( empty( $freq_value) ) $freq_value = '';

					$limit_error = get_post_meta( $post_id, 'somdn_membership_limit_error', true );
					if ( empty( $limit_error ) ) $limit_error = '';

				?>

				<span class="somdn-woo-meta-span pad-25">
					<span class="somdn-setting-left-col">Download limit period</span>
					<select name="somdn_membership_limit_freq" id="somdn_membership_limit_freq" style="float: none;">
						<option value="" <?php selected( $freq_value, '' ); ?> class="somdn_invalid_select">Please choose...</option>
						<option value="1" <?php selected( $freq_value, 1 ); ?>>Day</option>
						<option value="2" <?php selected( $freq_value, 2 ); ?>>Week</option>
						<option value="3" <?php selected( $freq_value, 3 ); ?>>Month</option>
						<option value="4" <?php selected( $freq_value, 4 ); ?>>Year</option>
					</select>
				</span>

				<span class="somdn-woo-meta-span pad-25">
					<span class="somdn-setting-left-col">Number of downloads</span>
					<input type="number" min="0" max="1000" value="<?php echo $amount_value; ?>" name="somdn_membership_limit_amount" id="somdn_membership_limit_amount" placeholder="0 - 1000" class="somdn-number-input">
					<div>Leave blank for unlimited.</div>
					<span class="description">Limit the number of times a download can be requested.</span>
				</span>

				<span class="somdn-woo-meta-span pad-25">
					<span class="somdn-setting-left-col">Number of products</span>
					<input type="number" min="0" max="1000" value="<?php echo $prod_value; ?>" name="somdn_membership_limit_products" id="somdn_membership_limit_products" placeholder="0 - 1000" class="somdn-number-input">
					<div>Leave blank for unlimited.</div>

					<span class="description">Limit the number of different products that can be downloaded.</span>
					<span class="description">If a product limit is set, the number of downloads will apply per product.</span>
				</span>

				<span class="description">See <a href="<?php echo somdn_get_plugin_link_full_admin(); ?>&tab=settings&section=limit">Limit settings</a> for more info.</span>

			</span>

			<br>

			<span class="somdn-woo-meta-span">

				<span class="somdn-woo-meta-span" style="font-size: 13px;">Customise the limit reached message for this membership plan.</span>

					<?php

						$editor_id = 'somdn_membership_limit_error';
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
							'textarea_name' => 'somdn_membership_limit_error'
						);
						$content = stripslashes( $limit_error );

						wp_editor( $content, $editor_id, $settings );

					?>

			</span>

		</fieldset>
	</div>

<?php }

add_action( 'somdn_memberships_save', 'somdn_memberships_save_pro', 10, 4 );
function somdn_memberships_save_pro( $post_data, $this_id, $post_id, $post ) {

	$options = get_option( 'somdn_pro_membership_limit_settings' );
	$value = isset( $options['somdn_pro_limit_member_exclude'] ) ? $options['somdn_pro_limit_member_exclude'] : 0 ;

	if ( ! $value ) {
		return;
	}

	if ( isset( $post_data['somdn_membership_exclude_limits'] ) ) {
		update_post_meta( $post_id, 'somdn_membership_exclude_limits', esc_attr( $post_data['somdn_membership_exclude_limits'] ) );
	} else {
		update_post_meta( $post_id, 'somdn_membership_exclude_limits', null );
	}
}

add_action( 'somdn_memberships_save', 'somdn_memberships_save_pro_limit', 20, 4 );
function somdn_memberships_save_pro_limit( $post_data, $this_id, $post_id, $post ) {
	if ( isset( $post_data['somdn_membership_limit_amount'] ) ) {
		update_post_meta( $post_id, 'somdn_membership_limit_amount', esc_attr( $post_data['somdn_membership_limit_amount'] ) );
	} else {
		update_post_meta( $post_id, 'somdn_membership_limit_amount', null );
	}
	if ( isset( $post_data['somdn_membership_limit_products'] ) ) {
		update_post_meta( $post_id, 'somdn_membership_limit_products', esc_attr( $post_data['somdn_membership_limit_products'] ) );
	} else {
		update_post_meta( $post_id, 'somdn_membership_limit_products', null );
	}
	if ( isset( $post_data['somdn_membership_limit_freq'] ) ) {
		update_post_meta( $post_id, 'somdn_membership_limit_freq', esc_attr( $post_data['somdn_membership_limit_freq'] ) );
	} else {
		update_post_meta( $post_id, 'somdn_membership_limit_freq', null );
	}
	if ( isset( $post_data['somdn_membership_limit_error'] ) ) {
		update_post_meta( $post_id, 'somdn_membership_limit_error', $post_data['somdn_membership_limit_error'] );
	} else {
		update_post_meta( $post_id, 'somdn_membership_limit_error', null );
	}
}

add_filter('somdn_user_limits_excluded', 'somdn_user_has_limit_excluded_membership', 10, 2);
function somdn_user_has_limit_excluded_membership($has_excluded, $user_id = '') {

	if ( ! is_user_logged_in() ) return false;

	if ( ! somdn_memberships() ) return false;

	// If $excluded is already populated then return it.
	if ($has_excluded == true) {
		return $has_excluded;
	}

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$options = get_option( 'somdn_pro_membership_limit_settings' );
	$exclusions_enabled = isset( $options['somdn_pro_limit_member_exclude'] ) ? $options['somdn_pro_limit_member_exclude'] : 0 ;

	if ( empty( $exclusions_enabled ) ) {
		return false;
	}

	$plans = wc_memberships_get_membership_plans();
	$plan_ids = array();
	$included_plans = array();

	if ( ! empty( $plans ) ) {
		foreach ( $plans as $plan ) {
			$plan_id = $plan->get_id();
			$plan_ids[] = $plan_id;
			$included = get_post_meta( $plan_id, 'somdn_membership_exclude_limits', true );
			if ( ! empty( $included ) ) {
				$included_plans[] = $plan_id;
				if ( wc_memberships_is_user_active_member( $user_id, $plan_id ) ) {
					return true;
				}
			}
		}
	}

	return false;

}

/**
 * Check for memberships with custom limit settings
 *
 * @since 1.0.8
 * @param array filled by return
 * @param int $user_id
 * @return array membership limit settings
 */
add_filter('somdn_user_custom_limits', 'somdn_custom_membership_limit_check', 10, 2);
function somdn_custom_membership_limit_check( $limits, $user_id = '' ) {

	if ( ! is_user_logged_in() ) return $limits;

	if ( ! somdn_memberships() ) return $limits;

	// If $limits is already populated then return it.
	if (!empty($limits)) {
		return $limits;
	}

	if ( empty( $user_id ) ) {
		$user_id = get_current_user_id();
	}

	$plans = wc_memberships_get_membership_plans();
	$plan_ids = array();
	$included_plans = array();

	$plan_limits = array(
		'limit_amount' => '',
		'limit_products' => '',
		'limit_freq' => '',
		'limit_error' => ''
	);

	if ( ! empty( $plans ) ) {
		foreach ( $plans as $plan ) {
			$plan_id = $plan->get_id();
			$plan_ids[] = $plan_id;
			if ( wc_memberships_is_user_active_member( $user_id, $plan_id ) ) {
				$amount_value = get_post_meta( $plan_id, 'somdn_membership_limit_amount', true );
				$products_value = get_post_meta( $plan_id, 'somdn_membership_limit_products', true );
				$freq_value = get_post_meta( $plan_id, 'somdn_membership_limit_freq', true );
				$limit_error = get_post_meta( $plan_id, 'somdn_membership_limit_error', true );
				if ( ! empty( $amount_value ) || ! empty( $freq_value ) || ! empty( $products_value ) ) {
					$plan_limits['limit_amount'] = $amount_value;
					$plan_limits['limit_products'] = $products_value;
					$plan_limits['limit_freq'] = $freq_value;
					if ( ! empty( $limit_error ) ) {
						$plan_limits['limit_error'] = $limit_error;
					}
					return $plan_limits;
				}
			}
		}
	}

	return $limits;

}

add_action( 'add_meta_boxes', 'somdn_user_membership_download_info', 99 );
function somdn_user_membership_download_info() {
	$screen = get_current_screen();
	if ( 'add' != $screen->action ) {
		add_meta_box(
			'something-something',
			__( 'Free Download Details', 'something' ),
			'somdn_user_membership_download_info_html',
			'wc_user_membership',
			'normal',
			'high'
		);
	}
}

function somdn_user_membership_download_info_html( $post ) {

	//$download_limits = somdn_get_user_limits( get_current_user_id() );

	$limits_enabled = somdn_download_limits_active();

	$membership_user = $post->post_author;

	$download_limits = somdn_get_user_limits( $membership_user );
	$limits_type   = $download_limits['type'];
	$limits_amount = $download_limits['amount'];
	$limits_freq   = $download_limits['freq'];
	$limits_error  = $download_limits['error'];
	$freq_name     = $download_limits['freq_name'];

	$total_downloads = somdn_get_user_downloads_count_total($membership_user);
	if ( empty( $total_downloads) ) {
		$total_downloads = 0;
	}

	$has_excluded_membership = somdn_user_has_limit_excluded_membership($membership_user);

	?>

	<div class="somdn-membership-free-downloads-wrap">
		<p>All time user downloads: <strong><?php echo $total_downloads; ?></strong></p>
	</div>

		<div class="somdn-membership-free-downloads-limits-heading">
			<h4>Download Limit Details</h4>
		</div>

	<?php if ( ! $limits_enabled ) { ?>
		<div class="somdn-membership-free-downloads-wrap">
			<p>No download limits are active.</p>
		</div>
	<?php } ?>

	<?php if ( $limits_enabled && ( $limits_type == 2 || $limits_type == 4 ) ) { ?>
		<div class="somdn-membership-free-downloads-wrap">
			<p>You cannot view limit details here when set to IP Address.</p>
		</div>
	<?php } ?>

	<?php if ( $limits_enabled && $has_excluded_membership ) { ?>
		<div class="somdn-membership-free-downloads-wrap">
			<p>This user has no download limits due to their membership plan.</p>
		</div>
	<?php } ?>

	<?php if ( $limits_enabled && ! $has_excluded_membership ) { ?>

		<?php if ( $limits_type == 1 ) { ?>

			<div class="somdn-membership-free-downloads-wrap">
				<div class="somdn-membership-free-downloads-wrap-current">
					<p><span><?php printf( _x( 'Current %1s: <strong>%2s</strong>', 'Show the user current download count', 'somdn-pro' ), ucfirst( $freq_name ), somdn_get_user_downloads_count( $membership_user ) ); ?></span></p>
				</div>
				<div class="somdn-membership-free-downloads-wrap-remain">
					<p><span><?php printf( __( 'Allowance: <strong>%s</strong>', 'somdn-pro' ), $limits_amount ); ?></span></p>
				</div>
			</div>

		<?php } elseif ( $limits_type == 3 ) { ?>

			<div class="somdn-membership-free-downloads-wrap">
				<div class="somdn-membership-free-downloads-wrap-remain">
					<p><span><?php printf( __( 'Allowance: <strong>%1s</strong> per product per <strong>%2s</strong>.', 'somdn-pro' ), $limits_amount, $freq_name ); ?></span></p>
				</div>
			</div>

		<?php } ?>

	<?php } ?>

	<?php

}

/**
 * Compatibility for Woocommerce Products List by NitroWeb
 * 
 * Outputs a download button, replacing the add to cart button
 */
add_filter( 'wcplpro_add_to_cart_td', 'somdn_wcplpro_add_to_cart_td', 70, 2 );
function somdn_wcplpro_add_to_cart_td( $wcplpro_cart, $product ) {

	$genoptions = get_option( 'somdn_gen_settings' );
	$archive_enabled = ( isset( $genoptions['somdn_include_archive_items'] ) && $genoptions['somdn_include_archive_items'] ) ? true : false ;

	if ( $archive_enabled ) {

		$somdn_download = get_the_id( $product );

		if ( somdn_is_product_valid( $somdn_download ) ) {

			$buttontext = '';

			if ( isset( $genoptions['somdn_read_more_text'] ) && ! empty( $genoptions['somdn_read_more_text'] ) ) 	{
				$buttontext = esc_html( $genoptions['somdn_read_more_text'] );
			} else {
				$buttontext = __( 'Download', 'somdn-pro' );
			}

			$wcplpro_cart = '<td class="cartcol"><div class="somdn-wcplpro-wrap">' . do_shortcode( '[download_now id="' . $somdn_download . '" text="' . $buttontext . '"]' ) . '</div></td>';

		}
		
	}

	return $wcplpro_cart;

}

/**
 * Compatibility for Woocommerce Products List by NitroWeb
 * 
 * Removes the quantity input for free downloads
 */
add_filter( 'wcplpro_allcolumns', 'somdn_wcplpro_allcolumns_qty', 70, 2 );
function somdn_wcplpro_allcolumns_qty( $allcolumns, $product ) {

	if ( array_key_exists( 'wcplpro_qty', $allcolumns ) ) {

		$genoptions = get_option( 'somdn_gen_settings' );
		$archive_enabled = ( isset( $genoptions['somdn_include_archive_items'] ) && $genoptions['somdn_include_archive_items'] ) ? true : false ;

		if ( $archive_enabled ) {

			$somdn_download = get_the_id( $product );

			if ( somdn_is_product_valid( $somdn_download ) ) {

				$free_qty = '<td class="qtycol" data-title="Quantity"></td>';

				$allcolumns['wcplpro_qty'] = $free_qty;	

			}
			
		}

	}

	return $allcolumns;

}

/**
 * Compatibility for WooCommerce PDF Watermark
 */
add_filter( 'somdn_download_path', 'somdn_download_path_woo_pdf_marker', 50, 5 );
function somdn_download_path_woo_pdf_marker( $file_path, $product, $key, $download_array, $download_id ) {

	$ext = somdn_get_ext_from_path( $file_path );
	if ( $ext === 'pdf' ) {
		if ( class_exists( 'WC_PDF_Watermark' ) ) {
			$new_file_path = somdn_woo_watermark_pdf_file( $file_path, $product );
			//echo '$new_file_path = ' . $new_file_path;
			//exit;
		}
		if ( ! empty( $new_file_path ) ) {
			return $new_file_path;
		}
	}

	return $file_path;

}

function somdn_wc_pdf_watermark_use_uploads_dir() {
	return true;
}

function somdn_woo_watermark_pdf_file( $file_path, $product, $product_id = '' ) {

	// Check if we should skip watermark based on product specific settings
	// Check variation level first
	if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
		if ( isset( $product->variation_id ) && 'yes' == get_post_meta( $product->variation_id , '_pdf_watermark_disable', true ) ) {
			return $file_path;
		}
	} else {
		if ( $product->is_type( 'variation' ) && 'yes' == get_post_meta( $product->get_id() , '_pdf_watermark_disable', true ) ) {
			return $file_path;
		}
	}

	// Check single product
	if ( 'yes' == get_post_meta( $product->get_id() , '_pdf_watermark_disable', true ) ) {
		return $file_path;
	}

	// Setup a new file to process
	$original_file = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $file_path );

	// Make sure we have a pdf file, if not return original file
	if ( ! somdn_is_file_pdf( $original_file ) ) {
		return $file_path;
	}

	// If we made it till here it means the file should be watermarked.

	// Get the product ID to do variation & single level overrides.
	if ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $product->variation_id ) ) {
		$product_id = $product->variation_id;
	} else {
		$product_id = $product->get_id();
	}

	add_filter( 'wc_pdf_watermark_use_uploads_dir', 'somdn_wc_pdf_watermark_use_uploads_dir' );
	$new_file = somdn_woo_watermark_do_pdf( $original_file, $product_id, 0 );
	if ( ! empty( $new_file ) ) {
		return $new_file;
	} else {
		return $file_path;
	}

}

function somdn_woo_watermark_do_pdf( $original_file, $product_id ) {

	if ( ! class_exists( 'WC_PDF_Watermarker' ) ) {
		//require_once 'class-wc-pdf-watermarker.php';
		require_once ABSPATH . '/wp-content/plugins/woocommerce-pdf-watermark/includes/class-wc-pdf-watermarker.php';
		require_once( SOMDN_PATH_PRO . 'includes/somdn-pro-compatibility-pdf-watermarker.php' );
	}

	//$pdf = new WC_PDF_Watermarker();
	$pdf = new SOMDN_WC_PDF_Watermarker();

	$upload_dir = wp_upload_dir();

	somdn_create_temp_uploads_folders();

	$parent = somdn_get_upload_folder_parent_path();
	$zip_path = somdn_get_upload_folder_zip_path( true );

	//$temp_folder = $upload_dir['basedir'] . "/free-downloads-files/temp-files/";
	//$temp_folder = $upload_dir['basedir'] . "/free-downloads-files/temp-files/";

	// Add fallback to use a custom dir in wp-content/uploads should host not have tmp folder
	$new_file_path = $zip_path;
	$file_name = somdn_woo_watermark_pdf_get_temporary_file_name( $original_file );

	// Make provision for local and remote hosted files
	if ( somdn_woo_watermark_pdf_is_local_file( $original_file ) ) {
		// We have a local file
		$pdf->watermark( $_POST, $product_id, $original_file, $new_file_path . $file_name );
	} else {
		// We have a remote file
		$remote_name = $file_name . '.remote';
		somdn_woo_watermark_pdf_fetch_remote_file( $original_file, $new_file_path . $remote_name );
		$pdf->watermark( $_POST, $product_id, $new_file_path . $remote_name, $new_file_path . $file_name );
		// Delete downloaded file
		unlink( $new_file_path . $remote_name );
	}

	$new_file = $new_file_path . $file_name;

	// Set the stamped file so we can delete it via shutdown hook.
	//woocommerce_pdf_watermark()->stamped_file[] = $new_file;

	return $new_file;
}

function somdn_woo_watermark_pdf_is_local_file( $file_path ) {
	$upload_dir = wp_upload_dir();
	return ( false !== stripos( $file_path, $upload_dir['basedir'] ) );
}

function somdn_woo_watermark_pdf_fetch_remote_file( $original_file, $local_path_and_file ) {

	if ( ! file_exists( $local_path_and_file ) ) {
		$response = wp_remote_get( $original_file );
		$remote_file = wp_remote_retrieve_body( $response );
		@file_put_contents( $local_path_and_file, $remote_file );
	}

}

function somdn_woo_watermark_pdf_get_temporary_file_name( $original_file ) {

	$now = DateTime::createFromFormat( 'U.u', microtime( true ) );
	$code1 = $now->format( "ms" );
	$code2 = $now->format( "u" );

	$file_info = pathinfo( $original_file );
	$base_filename = $file_info['filename'];
	$base_fileext = $file_info['extension'];

	$file_name = $base_filename . '_' . $code1 . $code2 . '.' . $base_fileext;

	// Remove any query from the remote file name to avoid exposing query parameters and
	// a generally ugly file name to the user (e.g. for S3 served files)
	$file_name = preg_replace( '/[?].*$/', '', $file_name );

	// In the unlikely event that file_name is now empty, use a generic filename
	if ( empty( $file_name ) ) {
		$file_name = 'untitled.pdf';
	}

	return $file_name;

}

function somdn_is_file_pdf( $file_path ) {

	$file_info = pathinfo( $file_path );
	$file_ext = $file_info['extension'];
	$file_ext = strtolower( substr( $file_ext, 0, 3 ) );
	return ( 'pdf' === $file_ext );

}

function somdn_pro_woo_pdf_watermark_settings_section_callback() { ?>
	<p><?php _e( 'Custom settings to handle watermarked PDF files.', 'somdn-pro' ); ?></p>
<?php }

add_action( 'somdn_after_docviewer_settings', 'somdn_pro_pdf_watermark_settings' );
function somdn_pro_pdf_watermark_settings() {

	if ( class_exists( 'WC_PDF_Watermark' ) ) :

		register_setting( 'somdn_docviewer_settings', 'somdn_pro_woo_pdf_watermark_settings' );

		add_settings_section(
			'somdn_pro_woo_pdf_watermark_settings_section', 
			__( 'WooCommerce PDF Watermark', 'somdn-pro' ), 
			'somdn_pro_woo_pdf_watermark_settings_section_callback', 
			'somdn_docviewer_settings'
		);

		add_settings_field( 
			'somdn_pro_woo_pdf_watermark_text', 
			__( 'Watermark Text', 'somdn-pro' ), 
			'somdn_pro_woo_pdf_watermark_text_render', 
			'somdn_docviewer_settings', 
			'somdn_pro_woo_pdf_watermark_settings_section' 
		);

	endif;

}

function somdn_pro_woo_pdf_watermark_text_render() {

	$options = get_option( 'somdn_pro_woo_pdf_watermark_settings' );
	$value = isset( $options['somdn_pro_woo_pdf_watermark_text'] ) ? $options['somdn_pro_woo_pdf_watermark_text'] : '' ; ?>

	<div class="som-settings-pro-basic-limit-error-wrap">

		<p><strong>Override the default Watermark Text.</strong></p>
		<p class="som-mar-bot-15">Recommended if you have custom text set globally or on a product that includes order details.</p>
		<p class="som-mar-bot-15">You can use {first_name}, {last_name}, {email}, {site_name}, {site_url} tags to insert customer specific data in the watermark. Bear in mind that not all of this information may be available, depending on how your site and free downloads are set up.</p>

		<textarea rows="4" style="width: 500px; max-width: 100%;" name="somdn_pro_woo_pdf_watermark_settings[somdn_pro_woo_pdf_watermark_text]" id="somdn_pro_woo_pdf_watermark_settings[somdn_pro_woo_pdf_watermark_text]"><?php echo esc_textarea( $value ); ?></textarea>

	</div>

	<hr class="som-setting-sep sep-300">

	<?php

}

/**
 * Compatibility for Paid Member Subscriptions
 */

 function somdn_memberships_pms() {
	if ( class_exists( 'Paid_Member_Subscriptions' ) ) {
		return true;
	} else {
		return false;
	}
}

/*
 * Only runs during the download check, as price pulls through on the frontend
*/
function somdn_is_product_member_free_pms( $free, $product, $product_id ) {

	//if ( $free == true ) {
	//	return $free;
	//}

	if ( ! is_user_logged_in() )
		return $free;

	if ( empty( $product ) ) {
		return $free;
	}

	//if ( $product->is_type( 'variable' ) ) {
	//	return $free;
	//}

	if ( somdn_memberships_pms() ) {

		$product_has_member_discounts = somdn_pms_product_has_member_discounts( $product_id );

		if ( $product_has_member_discounts == false ) {
			return $free;
		}

		$is_discount_free = somdn_pms_member_discount_is_free( $product, $product_id );
		if ( $is_discount_free ) {
			$free = true;
		}

	}

	return $free;

}

function somdn_pms_member_discount_is_free( $product, $product_id ) {

	somdn_pms_get_member_discounts();

	global $somdn_pms_woo_member_discounts, $somdn_pms_woo_member_subscriptions;

	$base_price = $product->get_regular_price();
	$discount_price = somdn_pms_get_discounted_price( $base_price, $product );
	//echo '$discount_price = ' . $discount_price . '<br>';
	if ( isset( $discount_price ) && $discount_price <= 0.0 ) {
		return true;
	} else {
		return false;
	}

}

function somdn_pms_get_member_discounts() {

  // here we store the global subscription plan product discounts for the current (logged in) member
  global $somdn_pms_woo_member_discounts, $somdn_pms_woo_member_subscriptions;

  // make sure the global variables were not set already on a different hook
  if ( !isset($somdn_pms_woo_member_discounts) || !isset($somdn_pms_woo_member_subscriptions) ) {

      $member = pms_get_member( get_current_user_id() );

      if ( !empty($member) ) {

          $somdn_pms_woo_member_discounts = array();
          $somdn_pms_woo_member_subscriptions = array();

          foreach ( $member->subscriptions as $subscription ) {

              if ( $subscription['status'] == 'active' ){

                  $somdn_pms_woo_member_subscriptions[] = $subscription['subscription_plan_id'];
                  $discounts = get_post_meta( (int)$subscription['subscription_plan_id'], 'pms-woo-subscription-plan-product-discounts', true );

                  if ( !empty($discounts) ) {

                      foreach ($discounts as $key => $discount) {
                          if ( $discount['status'] == 'inactive' ) unset($discounts[$key]);
                      }

                      $somdn_pms_woo_member_discounts = array_merge($somdn_pms_woo_member_discounts, $discounts);
                  }
              }
          }

      }

  }
}

/**
 * Get product discounted price for member.
 *
 * @param float $base_price Original price.
 * @param int|\WC_Product $product Product ID or product object.
 * @param int|null $member_id Optional, defaults to current user id.
 * @return float|null The discounted price or null if no discount applies.
 */
function somdn_pms_get_discounted_price( $base_price, $product, $member_id = null ) {

    if ( empty( $member_id ) ) {
        $member_id = get_current_user_id();
    }

    if ( is_numeric( $product ) ) {
        $product = wc_get_product( (int) $product );
    }

    $price          = null;
    $product_id     = null;
    $member_discounts = array();

    // We need a product and a user to get a member discounted price.
    if ( $product instanceof WC_Product && $member_id > 0 ) {

        // if is variation, use the id of the parent product
        if ($product->is_type( 'variation' )) {
            $product_id = $product->get_parent_id();
        } else {
            $product_id = $product->get_id();
        }

        $member_discounts = somdn_pms_get_user_membership_discounts($product, $member_id);
    }


    if ( $product_id && !empty($member_discounts) ) {
        // We have membership discounts that need to be applied to the product price

        $price = (float)$base_price;
        $discounted_price = $price;
        $prices = array();

        $discounts_behaviour = get_post_meta((int)$product_id, 'pms-woo-product-membership-discounts-behaviour', true);
        //for products added in Woocommerce before activating PMS and running the save_post hook added in PMS
        if ( empty($discounts_behaviour) )
            $discounts_behaviour = 'default';
        
        $discount_location = array();
        $discount_type = array('fixed', 'percent');

        switch ($discounts_behaviour) {
            case 'default':  // best price
                $discount_location = array('subscription_plan', 'product');
                break;
            case 'ignore':  //apply only discounts set per product, ignore the rest
                $discount_location = array('product');
        }


        // Apply discounts and store both lowest individual price (after applying just one discount -> $prices) and lowest cumulative price (after applying all discounts -> $discounted_price)
        foreach ($discount_location as $location) {

            foreach ($discount_type as $type) {

                if (!empty($member_discounts[$location])) {

                    if (!empty($member_discounts[$location][$type])) {

                        foreach ($member_discounts[$location][$type] as $discount_amount) {

                            switch ($type) {
                                case 'fixed' :
                                    $discounted_price = max($discounted_price - (float)$discount_amount, 0);
                                    $prices[] = max($price - (float)$discount_amount, 0);
                                    break;

                                case 'percent' :
                                    $discounted_price = max($discounted_price * (100 - (float)$discount_amount) / 100, 0);
                                    $prices[] = max($price * (100 - (float)$discount_amount) / 100, 0);
                                    break;
                            }

                        }
                    }
                }
            }
        }

				if (!empty($prices)) {
            $price = min($prices);
        }

        // Sanity check.
        if ($price >= $base_price) {
            $price = null;
        }

    }// end if ( $product_id && !empty($member_discounts) )

    /**
     * Filter discounted membership price of a product.
     *
     * @param null|float $price The discounted price or null if no discount applies.
     * @param float $base_price The original price (not discounted by PMS).
     * @param int $product_id The id of the product (or variation) the price is for.
     * @param int $member_id The id of the logged in member (it's zero for non logged in users).
     * @param \WC_Product $product The product object for the price being discounted.
     */
    return apply_filters( 'somdn_pms_woo_get_discounted_price', $price, $base_price, $product_id, $member_id, $product );
}

/**
 * Check if the user has any membership discounts for the product
 *
 * @param int|\WC_Product $product Product ID or object.
 * @param null|int $user_id Optional, defaults to current user id.
 * @return array()| $member_discount containing an array of all user discounts for the product
 *         empty array if no membership discounts found for this product
 */
function somdn_pms_get_user_membership_discounts( $the_product, $the_user = null ) {

    global $somdn_pms_woo_member_discounts, $somdn_pms_woo_member_subscriptions;

    // initialize the $member_discount array
    $member_discounts = array();

    // Get the product.
    if ( is_numeric( $the_product ) ) {
        $the_product = wc_get_product( (int) $the_product );
    } elseif ( null === $the_product ) {
        global $product;

        if ( $product instanceof WC_Product ) {
            $the_product = $product;
        }
    }

    // bail out if no product
    if ( ! $the_product instanceof WC_Product ) {
        return $member_discounts;
    }


    // get the user id
    if ( null === $the_user ) {
        $member_id = get_current_user_id();
    } elseif ( is_numeric( $the_user ) ) {
        $member_id = (int) $the_user;
    } elseif ( isset( $the_user->ID ) ) {
        $member_id = (int) $the_user->ID;
    } else {
        return $member_discounts;
    }

    // bail out if user is not logged in
    if ( 0 === $member_id ) {
        return $member_discounts;
    }

    // if is variation, use the id of the parent product
    if ($the_product->is_type( 'variation' )) {
        $product_id = $the_product->get_parent_id();
    } else {
        $product_id = $the_product->get_id();
    }

    // get discounts behaviour for this product
    $discounts_behaviour = get_post_meta((int)$product_id, 'pms-woo-product-membership-discounts-behaviour', true);
    //for products added in Woocommerce before activating PMS and running the save_post hook added in PMS
    if ( empty($discounts_behaviour) )
        $discounts_behaviour = 'default';

    // check if there are any global subscription discounts that apply to this product
    if ( !empty($somdn_pms_woo_member_discounts) && ($discounts_behaviour == 'default') ) {

        foreach ($somdn_pms_woo_member_discounts as $discount) {

            // don't save inactive discounts
            if ( $discount['status'] == 'inactive' )
                continue;

            if ( ($discount['discount-for'] == 'products') &&
                ( !isset($discount['name']) || ( isset($discount['name']) && in_array($product_id, $discount['name'])) ) ){

                $member_discounts['subscription_plan'][$discount['type']][] = $discount['amount'];
            }

            if ( ($discount['discount-for'] == 'product-categories') &&
                ( ( !isset($discount['name']) && has_term('', 'product_cat', (int)$product_id) ) || ( isset($discount['name']) && has_term( $discount['name'], 'product_cat', (int)$product_id ) ) ) ) {

                $member_discounts['subscription_plan'][$discount['type']][] = $discount['amount'];
            }

        }
    }

    // check if there are any discounts set per product that apply to logged in member
    $product_discounts = get_post_meta( (int)$product_id, 'pms-woo-product-membership-discounts', true );
    //for products added in Woocommerce before activating PMS and running the save_post hook added in PMS
    if( $product_discounts === '' )
        $product_discounts = array();

    if ( !empty($product_discounts) ){

        foreach ( $product_discounts as $product_discount ){

            if ( $product_discount['status'] == 'inactive' )
                continue;

            if ( !empty($pms_woo_member_subscriptions) && in_array( $product_discount['subscription-plan'], $pms_woo_member_subscriptions ) )
                $member_discounts['product'][$product_discount['type']][] = $product_discount['amount'];
        }

    }

    return $member_discounts;
}

/**
 * Function that checks if there are any active membership discounts that apply to this product
 *
 * @param null|int|\WC_Product $product Product ID or object.
 * @return bool
 */
function somdn_pms_product_has_member_discounts( $the_product = null ){

    // Get the product.
    if ( is_numeric( $the_product ) ) {
        $the_product = wc_get_product( (int) $the_product );
    } elseif ( null === $the_product ) {
        global $product;

        if ( $product instanceof WC_Product ) {
            $the_product = $product;
        }
    }

    // bail out if no product
    if ( ! $the_product instanceof WC_Product ) {
        return false;
    }

    // if is variation, use the id of the parent product
    if ($the_product->is_type( 'variation' )) {
        $product_id = $the_product->get_parent_id();
    } else {
        $product_id = $the_product->get_id();
    }


    // first check if there are any discounts set per product
    $product_discounts = get_post_meta( (int)$product_id, 'pms-woo-product-membership-discounts', true );
    //for products added in Woocommerce before activating PMS and running the save_post hook added in PMS
    if( $product_discounts === '' )
        $product_discounts = array();

    if ( !empty($product_discounts) ) {

        foreach ($product_discounts as $product_discount) {

            if ($product_discount['status'] == 'active' && !empty($product_discount['subscription-plan'])) {

                // product has membership discounts
                return true;
            }
        }
    }


    // see if product is not excluded from global membership discounts (per subscription plan)
    $discounts_behaviour = get_post_meta( (int)$product_id, 'pms-woo-product-membership-discounts-behaviour', true);
    //for products added in Woocommerce before activating PMS and running the save_post hook added in PMS
    if ( empty($discounts_behaviour) )
        $discounts_behaviour = 'default';

    if ( $discounts_behaviour == 'ignore')
        return false;


    // check if there are global membership discounts set per subscription plan that apply to this product
    $subscription_plans = pms_get_subscription_plans();

    if ( !empty($subscription_plans) ) {

        foreach ( $subscription_plans as $subscription_plan ) {

            $subscription_plan_discounts = get_post_meta( (int)$subscription_plan->id, 'pms-woo-subscription-plan-product-discounts', true );

            if ( !empty($subscription_plan_discounts) ){

                foreach ( $subscription_plan_discounts as $discount ) {

                    // don't save inactive discounts
                    if ( $discount['status'] == 'active' ) {

                        if (($discount['discount-for'] == 'products') &&
                            (!isset($discount['name']) || (isset($discount['name']) && in_array($product_id, $discount['name'])))) {
                            // product has membership discounts
                            return true;
                        }

                        if (($discount['discount-for'] == 'product-categories') &&
                            ((!isset($discount['name']) && has_term('', 'product_cat', $product_id)) || (isset($discount['name']) && has_term($discount['name'], 'product_cat', $product_id)))) {
                            // product has membership discounts
                            return true;
                        }
                    }
                }

            }
        }
    }

    return false;
}