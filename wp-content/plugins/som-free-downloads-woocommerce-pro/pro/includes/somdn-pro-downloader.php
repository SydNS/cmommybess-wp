<?php
/**
 * Free Downloads - WooCommerce - Pro Downloader
 * 
 * Various functions.
 *
 * @version	1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

remove_action( 'somdn_do_download', 'somdn_woo_download', 10 );
add_action( 'somdn_do_download', 'somdn_woo_download_pro', 10, 3 );
function somdn_woo_download_pro( $file_path, $product_id, $force = false ) {

	$filename = basename( $file_path );

	if ( strstr( $filename, '?' ) ) {
		$filename = current( explode( '?', $filename ) );
	}

	$ext = somdn_get_ext_from_path( $file_path );
	if ( $ext === 'pdf' ) {
		if ( class_exists( 'WC_PDF_Watermark' ) ) {
			$force = true;
		}
	}

	if ( $force ) {
		$file_download_method = apply_filters( 'woocommerce_file_download_method', 'force', $product_id );
	} else {
		$file_download_method = apply_filters( 'woocommerce_file_download_method', get_option( 'woocommerce_file_download_method', 'force' ), $product_id );
	}

	// Add 1 to the download count for this product
	do_action( 'somdn_count_download', $product_id );

	// Add action to prevent issues in IE
	add_action( 'nocache_headers', array( 'WC_Download_Handler', 'ie_nocache_headers_fix' ) );
	
	// Trigger download via one of the methods
	do_action( 'woocommerce_download_file_' . $file_download_method, $file_path, $filename );

}

function somdn_woo_download_variation( $file_path, $product_id, $variation_id, $force = false ) {

	$filename = basename( $file_path );

	if ( strstr( $filename, '?' ) ) {
		$filename = current( explode( '?', $filename ) );
	}
	
	if ( $force ) {
		$file_download_method = apply_filters( 'woocommerce_file_download_method', 'force', $product_id );
	} else {
		$file_download_method = apply_filters( 'woocommerce_file_download_method', get_option( 'woocommerce_file_download_method', 'force' ), $product_id );
	}

	// Add 1 to the download count for this product
	do_action( 'somdn_count_download', $product_id );

	do_action( 'somdn_count_variation_download', $variation_id, $product_id );

	// Add action to prevent issues in IE
	add_action( 'nocache_headers', array( 'WC_Download_Handler', 'ie_nocache_headers_fix' ) );
	
	// Trigger download via one of the methods
	do_action( 'woocommerce_download_file_' . $file_download_method, $file_path, $filename );

}

function somdn_get_download_variation_actions() {
	$variation_actions = array(
		'somdn_download_single_variation',
		'somdn_download_all_files_variation',
		'somdn_download_multi_single_variation',
		'somdn_download_multi_checked_variation'
	);
	return apply_filters( 'somdn_download_variation_actions', $variation_actions );
}

add_action( 'wp_loaded', 'somdn_downloader_variations_init', 999 );
function somdn_downloader_variations_init() {

	if ( empty( $_POST['action'] ) )
		return;

	$action = $_POST['action'];
	$actions = somdn_get_download_variation_actions();

	if ( ! in_array( $action, $actions ) )
		return;

	$_REQUEST['somdn_errors'] = array();

	if ( ! somdn_verify_download_request_key() ) {
		return false;
	}

	$product_id = intval( $_POST['somdn_product'] );
	$variation_id = intval( $_POST['somdn_variation'] );

	// Bail if there's no product id or variation id passed
	if ( empty( $product_id ) || empty( $variation_id ) )
		return;

	if ( ! somdn_is_download_valid_variation( $product_id, $variation_id ) ) {
		return;
	}

	global $somdn_init_download_type;

	$singleoptions = get_option( 'somdn_single_settings' );
	$force_zip = ( isset( $singleoptions['somdn_single_force_zip'] ) && $singleoptions['somdn_single_force_zip'] ) ? true : false ;

	if ( $action === 'somdn_download_single_variation' ) {
		if ( $force_zip ) {
			somdn_download_all_files_variation();
		} else {
			$somdn_init_download_type = 'single_file_default';
			somdn_download_single_variation();
		}
	} elseif ( $action === 'somdn_download_all_files_variation' ) {
		somdn_download_all_files_variation();
	} elseif ( $action === 'somdn_download_multi_single_variation' ) {
		$somdn_init_download_type = 'single_file_default';
		somdn_download_multi_single_variation();
	} elseif ( $action === 'somdn_download_multi_checked_variation' ) {
		somdn_download_multi_checked_variation();
	}

}

function somdn_download_single_variation() {

	$product_id = sanitize_text_field( $_POST['somdn_product'] );
	$variation_id = sanitize_text_field( $_POST['somdn_variation'] );

	$product = wc_get_product( $product_id );
	$variation_product = new WC_Product_Variation( $variation_id );

	$title = preg_replace('/[^a-z\d]+/i', '-', get_the_title( $product_id ) );

	$downloads = somdn_get_files( $variation_product );
	$downloads_count = count( $downloads );
	$is_single_download = ( 1 == $downloads_count ) ? true : false ;

	foreach ( $downloads as $key => $each_download )  {
		$file_path = somdn_get_download_filepath( $product, $key, $each_download, $each_download['id'] );
	}
	
	$pdf = isset( $_POST['pdf'] ) ? true : false ;

	if ( $pdf ) {
		somdn_show_pdf_variation( $file_path, $product_id, $variation_id );
	} else {
		somdn_woo_download_variation( $file_path, $product_id, $variation_id );
	}

}

function somdn_download_multi_single_variation() {

	$product_id = sanitize_text_field( $_POST['somdn_product'] );
	$variation_id = sanitize_text_field( $_POST['somdn_variation'] );

	$download_valid = apply_filters( 'somdn_is_download_valid_variation', false, $product_id, $variation_id );
	if ( ! $download_valid ) {
		return;
	}

	$product = wc_get_product( $product_id );
	$variation_product = new WC_Product_Variation( $variation_id );

	$title = preg_replace('/[^a-z\d]+/i', '-', get_the_title( $product_id ) );

	$downloads = somdn_get_files( $variation_product );
	$downloads_count = count( $downloads );
	$product_file = $_POST['somdn_productfile'];

	$product_file = $product_file - 1;

	$for_count = 0;

	foreach ( $downloads as $key => $each_download )  {

		if ( $for_count == $product_file ) {

			$file_path = somdn_get_download_filepath( $product, $key, $each_download, $each_download['id'] );
			break;

		}

		$for_count++;

	}

	$pdf = isset( $_POST['pdf'] ) ? true : false ;

	if ( $pdf ) {
		somdn_show_pdf_variation( $file_path, $product_id, $variation_id );
	} else {
		somdn_woo_download_variation( $file_path, $product_id, $variation_id );
	}

}

function somdn_download_multi_checked_variation() {

	$product_id = sanitize_text_field( $_POST['somdn_product'] );
	$variation_id = sanitize_text_field( $_POST['somdn_variation'] );

	$download_valid = apply_filters( 'somdn_is_download_valid_variation', false, $product_id, $variation_id );
	if ( ! $download_valid ) {
		return;
	}

	$product = wc_get_product( $product_id );
	$variation_product = new WC_Product_Variation( $variation_id );
	$title = preg_replace('/[^a-z\d]+/i', '-', get_the_title( $product_id ) );

	$downloads = somdn_get_files( $variation_product );
	$downloads_count = count( $downloads );
	
	$download_files = somdn_get_file_paths( $variation_product, $downloads );
	
	$checked_downloads = array();
	
	$checked_count = 0;
	$checked_total = 0;
	$all_checked = false;
	
	while ( $checked_count < $downloads_count ) {
	
		$checkbox_number = $checked_count + 1;
	
		$checkbox_id = 'somdn_download_file_' . strval( $checkbox_number );
		
		$checkbox = ( isset( $_POST[$checkbox_id] ) && $_POST[$checkbox_id] ) ? $_POST[$checkbox_id] : false ;
		
		if ( $checkbox ) {

			array_push( $checked_downloads, $download_files[$checked_count] );
	
			$checked_total++;
		}
		
		$checked_count++;
		
	}

	if ( $checked_total == $downloads_count ) {
		$all_checked = true;
	}
	
	$download_all = ( ( isset( $_POST['somdn_download_files_all'] ) && $_POST['somdn_download_files_all'] ) || $all_checked ) ? true : false ;

	if ( $download_all ) {
		somdn_download_all_files_variation( $variation_id, $product_id );
		return;
	}

	if ( $checked_total <= 0 ) {
		echo somdn_checkbox_download_error();
		return;	
	}

	$single = ( count($checked_downloads) === 1 ) ? true : false;
	if ( $single === true ) {
		somdn_woo_download_variation( $checked_downloads[0], $product_id, $variation_id );
		return;
	}

	$file_path = somdn_zip_all_download_files( $checked_downloads, $title, $product_id );
	
	somdn_woo_download_variation( $file_path, $product_id, $variation_id );

}

function somdn_download_all_files_variation( $variation_id = '', $product_id = '' ) {

	if ( ! $variation_id ) {
		$variation_id = sanitize_text_field( $_POST['somdn_variation'] );
	}

	if ( ! $product_id ) {
		$product_id = sanitize_text_field( $_POST['somdn_product'] );
	}

	$download_valid = apply_filters( 'somdn_is_download_valid_variation', false, $product_id, $variation_id );
	if ( ! $download_valid ) {
		return;
	}

	$product = wc_get_product( $product_id );
	$variation_product = new WC_Product_Variation( $variation_id );
	$title = preg_replace('/[^a-z\d]+/i', '-', get_the_title( $product_id ) );

	$downloads = somdn_get_files( $variation_product );
	$downloads_count = count( $downloads );

	$download_files = somdn_get_file_paths( $variation_product, $downloads );

	$file_path = somdn_zip_all_download_files( $download_files, $title, $product_id );

	somdn_woo_download_variation( $file_path, $product_id, $variation_id );

}

add_filter( 'somdn_get_selected_product_files', 'somdn_get_selected_product_files_variation', 25, 2 );
function somdn_get_selected_product_files_variation( $selected_files, $download_data ) {

	// Get the download action type and variation actions
	$action = sanitize_key( $download_data['action'] );
	$actions = somdn_get_download_variation_actions();

	if ( in_array( $action, $actions ) ) {
		// This is a variation product download submitted by a frontend download form
	}

	return $selected_files;

}