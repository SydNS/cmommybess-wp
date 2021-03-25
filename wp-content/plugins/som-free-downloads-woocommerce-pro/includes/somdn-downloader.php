<?php
/**
 * Free Downloads - Downloader
 * 
 * Functions to action the file download.
 * 
 * @version	3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function somdn_do_download( $file_path, $product_id, $force = false ) {
	do_action( 'somdn_do_download', $file_path, $product_id, $force );
}

function somdn_get_download_actions() {
	$actions = array(
		'somdn_download_single',
		'somdn_download_all_files',
		'somdn_download_multi_single',
		'somdn_download_multi_checked'
	);
	return apply_filters( 'somdn_download_actions', $actions );
}

add_action( 'wp_loaded', 'somdn_downloader_init', 999 );
function somdn_downloader_init( $ajax = false ) {

	if ( empty( $_POST['action'] ) )
		return false;

	$action = sanitize_key( $_POST['action'] );
	$actions = somdn_get_download_actions();

	if ( ! in_array( $action, $actions ) )
		return false;

	$_REQUEST['somdn_errors'] = array();

	if ( ! somdn_verify_download_request_key() ) {
		return false;
	}

	$product_id = intval( $_POST['somdn_product'] );
	if ( empty( $product_id ) ) {
		return false;
	}

	if ( ! somdn_is_download_valid( $product_id ) ) {
		return false;
	}

	//echo '<p>Simple POST Data</p>';
	//echo '<pre>';
	//print_r($_POST);
	//echo '</pre>';
	//exit;

	global $somdn_init_download_type;

	$custom_action = false;

	$singleoptions = get_option( 'somdn_single_settings' );
	$force_zip = ( isset( $singleoptions['somdn_single_force_zip'] ) && $singleoptions['somdn_single_force_zip'] ) ? true : false ;

	if ( $action === 'somdn_download_single' ) {
		if ( $force_zip ) {
			somdn_download_all_files( $product_id );
		} else {
			$somdn_init_download_type = 'single_file_default';
			somdn_download_single( $product_id );
		}
	} elseif ( $action === 'somdn_download_all_files' ) {
		somdn_download_all_files( $product_id );
	} elseif ( $action === 'somdn_download_multi_single' ) {
		$somdn_init_download_type = 'single_file_default';
		somdn_download_multi_single( $product_id );
	} elseif ( $action === 'somdn_download_multi_checked' ) {
		somdn_download_multi_checked( $product_id );
	} elseif ( apply_filters( 'somdn_custom_actions', $custom_action, $action ) ) {
		// Do custom action stuff
	}

}

function somdn_download_single( $product_id = '' ) {

	$product = somdn_get_product( $product_id );
	$title = preg_replace( '/[^a-z\d]+/i', '-', get_the_title( $product_id ) );

	$downloads = somdn_get_files( $product );
	$downloads_count = count( $downloads );

	foreach ( $downloads as $key => $each_download )  {
		$file_path = somdn_get_download_filepath( $product, $key, $each_download, $each_download['id'] );
	}
	
	$pdf = isset( $_POST['pdf'] ) ? true : false ;

	//echo '<pre>';
	//print_r($downloads);
	//echo '</pre>';
	//echo '$file_path = ' . $file_path;
	//exit;

	if ( $pdf ) {
		//echo '<p>Show PDF</p>';
		somdn_show_pdf( $file_path, $product_id );
	} else {
		somdn_do_download( $file_path, $product_id );
	}

	//echo 'here';
	//exit;

}

function somdn_download_multi_single( $product_id = '' ) {

	$product_file = intval( $_POST['somdn_productfile'] );
	if ( empty( $product_file ) ) {
		return;
	}

	$product = somdn_get_product( $product_id );
	$title = preg_replace( '/[^a-z\d]+/i', '-', get_the_title( $product_id ) );

	$downloads = somdn_get_files( $product );
	$downloads_count = count( $downloads );

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
		somdn_show_pdf( $file_path, $product_id );
	} else {
		somdn_do_download( $file_path, $product_id );
	}

}

function somdn_download_multi_checked( $product_id ) {

	//somdn_debug_array($_POST);
	//exit;

	$product = somdn_get_product( $product_id );
	$title = preg_replace( '/[^a-z\d]+/i', '-', get_the_title( $product_id ) );

	$downloads = somdn_get_files( $product );
	$downloads_count = count( $downloads );
	$is_single_download = ( 1 == $downloads_count ) ? true : false ;
	
	$download_files = somdn_get_file_paths( $product, $downloads );

	//echo '<p>$download_files</p>';
	//echo '<pre>';
	//print_r($download_files);
	//echo '</pre>';
	//exit;
	
	$checked_downloads = somdn_get_selected_product_files( $_POST );

	if ( empty( $checked_downloads ) ) {
		$checked_error = somdn_get_checkbox_error_text();
		$errors['empty_checkboxes'] = $checked_error;
		array_push( $_REQUEST['somdn_errors'], $errors);
		return;
	}

	$download_all = ( !empty( $_POST['somdn_download_files_all'] ) ) ? true : false ;

	if ( $download_all ) {
		somdn_download_all_files( $product_id );
		return;
	}

	$selected_file_paths = array();

	foreach ( $download_files as $file => $each_file )  {
		if ( in_array( $file, $checked_downloads ) ) {
			array_push( $selected_file_paths, $each_file );
		}
	}

	$single = ( count($selected_file_paths) === 1 ) ? true : false;
	if ( $single === true ) {
		somdn_do_download( $selected_file_paths[0], $product_id );
		return;
	}

	$file_path = somdn_zip_all_download_files( $selected_file_paths, $title, $product_id );
	
	somdn_do_download( $file_path, $product_id );
	
}

function somdn_download_multi_checked_original( $product_id ) {

	$product = somdn_get_product( $product_id );
	$title = preg_replace( '/[^a-z\d]+/i', '-', get_the_title( $product_id ) );

	$downloads = somdn_get_files( $product );
	$downloads_count = count( $downloads );
	
	$download_files = somdn_get_file_paths( $product, $downloads );
	
	$checked_downloads = array();
	
	$checked_count = 0;
	$checked_total = 0;
	$all_checked = false;
	
	while ( $checked_count < $downloads_count ) {
	
		$checkbox_number = $checked_count + 1;
	
		$checkbox_id = 'somdn_download_file_' . strval( $checkbox_number );
		
		$checkbox = ( isset( $_POST[$checkbox_id] ) && $_POST[$checkbox_id] ) ? intval( $_POST[$checkbox_id] ) : false ;
		
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

	//echo '<pre>';
	//print_r($checked_downloads);
	//echo '</pre>';
	//exit;

	if ( $download_all ) {
		somdn_download_all_files( $product_id );
		return;
	}

	if ( $checked_total <= 0 ) {
		$checked_error = somdn_get_checkbox_error_text();
		$errors['empty_checkboxes'] = $checked_error;
		array_push( $_REQUEST['somdn_errors'], $errors);
		return;	
	}

	$file_path = somdn_zip_all_download_files( $checked_downloads, $title, $product_id );
	
	somdn_do_download( $file_path, $product_id );
	
}

function somdn_download_all_files( $product_id = '' ) {

	if ( ! $product_id ) {
		$product_id = intval( $_POST['somdn_product'] );
	}

	$product = somdn_get_product( $product_id );
	$title = preg_replace( '/[^a-z\d]+/i', '-', get_the_title( $product_id ) );

	$downloads = somdn_get_files( $product );
	$downloads_count = count( $downloads );
	
	$download_files = somdn_get_file_paths( $product, $downloads );
		
	$file_path = somdn_zip_all_download_files( $download_files, $title, $product_id );

	somdn_do_download( $file_path, $product_id );

}

function somdn_zip_all_download_files( $downloads, $title, $product_id = '' ) {

	somdn_create_temp_uploads_folders();
	$parent = somdn_get_upload_folder_parent_path();
	$zip_path = somdn_get_upload_folder_zip_path();

	$upload_dir = wp_upload_dir();

	$now = DateTime::createFromFormat( 'U.u', microtime( true ) );
	$code1 = $now->format( "ms" );
	$code2 = $now->format( "u" );
	$downloadID = get_current_user_id() . $code1 . $code2;
	
	$zip_path = $zip_path . '/' . $title . '-' . $downloadID . '.zip';

	$files = array();

	foreach ( $downloads as $download )  {

		$path = parse_url( $download, PHP_URL_PATH );
		$abs_filepath = $_SERVER['DOCUMENT_ROOT'] . $path;

		//$is_local = file_exists( $abs_filepath );
		if ( file_exists( $abs_filepath ) ) {
			array_push( $files, preg_replace('/(\/+)/','/', $abs_filepath ) );
		} elseif ( file_exists( $path ) ) {
			array_push( $files, $path );
		}

	}

	if ( empty( $files ) ) {
		$log_entry = 'No files were zipped for download. Check the files are stored locally and you are not using external URLs.';
		if ( ! empty( $product_id ) ) {
			$log_entry .= ' Product ID ' . $product_id . '.';
		}
		somdn_write_log( $log_entry );
		somdn_wp_error( '<strong>ERROR</strong>: no files were found to download' );
	}

	$files_to_zip = $files;

	$result = somdn_create_zip( $files_to_zip, $zip_path );
	if ( $result == false ) {
		$log_entry = 'Unable to create ZIP file for download. To use this feature your server needs ZipArchive to be installed.';
		if ( ! empty( $product_id ) ) {
			$log_entry .= ' Product ID ' . $product_id . '.';
		}
		somdn_write_log( $log_entry );
	}

	$fileurl = $upload_dir['baseurl'] . '/free-downloads-files/temp-files/' . $title . '-' . $downloadID . '.zip';

	$file_path = $fileurl;
	
	return $file_path;

}