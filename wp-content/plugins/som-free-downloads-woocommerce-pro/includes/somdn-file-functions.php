<?php
/**
 * Free Downloads - File Functions
 * 
 * Managing files and filepath functions
 * 
 * @version 3.0.9
 * @author  Square One Media
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function somdn_get_upload_folder_parent_path( $slash = false ) {
	$upload_dir = wp_upload_dir();
	$path = $upload_dir['basedir'] . '/free-downloads-files';
	return $slash == true ? trailingslashit( $path ) : $path ;
}

function somdn_get_upload_folder_zip_path( $slash = false ) {
	$upload_dir = wp_upload_dir();
	$path = $upload_dir['basedir'] . '/free-downloads-files/temp-files';
	return $slash == true ? trailingslashit( $path ) : $path ;
}

function somdn_create_temp_uploads_folders() {
	$parent = somdn_get_upload_folder_parent_path();
	$zip_path = somdn_get_upload_folder_zip_path();
	if ( ! file_exists( $parent ) ) {
		mkdir( $parent, 0777, true );
	}
	if ( ! file_exists( $zip_path ) ) {
		mkdir( $zip_path, 0777, true );
	}
}

function somdn_create_empty_index_files( $parent = '', $zip_path = '' ) {

	if ( empty( $parent ) || empty( $zip_path ) ) {
		$parent = somdn_get_upload_folder_parent_path();
		$zip_path = somdn_get_upload_folder_zip_path();
	}

	// Top level blank index.php
	if ( ! file_exists( $parent . '/index.php' ) && wp_is_writable( $parent ) ) {
		@file_put_contents( $parent . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
	}
	// Top level blank index.php
	if ( ! file_exists( $zip_path . '/index.php' ) && wp_is_writable( $zip_path ) ) {
		@file_put_contents( $zip_path . '/index.php', '<?php' . PHP_EOL . '// Silence is golden.' );
	}

}

add_action( 'somdn_on_activate', 'somdn_on_activate_file_cron', 20 );
function somdn_on_activate_file_cron() {
	if ( ! wp_next_scheduled ( 'somdn_delete_download_files_event' ) ) {
		wp_schedule_event( time(), 'hourly', 'somdn_delete_download_files_event' );
	}
	somdn_create_temp_uploads_folders();
	somdn_create_empty_index_files();
}

add_action( 'somdn_on_deactivate', 'somdn_on_deactivate_file_cron', 20 );
function somdn_on_deactivate_file_cron() {

	wp_clear_scheduled_hook( 'somdn_delete_download_files_event' );

	$parent = somdn_get_upload_folder_parent_path( true );
	$zip_path = somdn_get_upload_folder_zip_path( true );

	// First we empty the folder, then delete it
	if ( is_dir( $zip_path ) ) {
		array_map( 'unlink', glob( $zip_path . '*' ) );
		rmdir( $zip_path );
	}

	// First we empty the folder, then delete it
	if ( is_dir( $zip_path ) ) {
		array_map( 'unlink', glob( $parent . '*' ) );
		rmdir( $parent );
	}

}

/*
add_action( 'init', 'som_test_glob_function' );
function somdn_test_glob_function() {
	$parent = somdn_get_upload_folder_parent_path();
	$zip_path = somdn_get_upload_folder_zip_path();
	$deleted_count = 0;
	// Loop through files in the temp zip folder
	foreach ( glob( $zip_path. '/*' ) as $file ) {
		$path_parts = pathinfo( $file );
		$ext = $path_parts['extension'];
		$basename = $path_parts['basename'];
		echo '<p>$ext = ' . $ext . '</p>';
	}
	exit;
}
*/

add_action( 'somdn_delete_download_files_event', 'somdn_delete_download_files' );
function somdn_delete_download_files() {
	$parent = somdn_get_upload_folder_parent_path();
	$zip_path = somdn_get_upload_folder_zip_path();
	$deleted_count = 0;
	// Loop through files in the temp zip folder
	foreach ( glob( $zip_path. '/*' ) as $file ) {
		// Only delete files that are older than 30 minutes (1800 seconds)
		if ( time() - filectime( $file ) > 1800 ) {
			$path_parts = pathinfo( $file );
			$ext = $path_parts['extension'];
			$basename = $path_parts['basename'];
			if ( $basename != 'index.php' ) {
				// the empty index.php file is not counted towards to deletion count
				$deleted_count++;
			}
			unlink( $file );
		}
	}
	if ( $deleted_count > 0 ) {
		if ( somdn_is_debug_on() ) {
		 somdn_write_log( '[DEBUG] Temporary download folder cleared. Deleted: ' . $deleted_count . '.' );
		}
	}
	// Delete all files
	// array_map( 'unlink', glob( $zip_path . '/*' ) );
	// Top level blank index.php
	somdn_create_empty_index_files( $parent, $zip_path );
}

/**
 * Return an array of product files from download or post data
 *
 * @param $download_data The download file array
 * @return array Just the files selected as numbers
 */
function somdn_get_selected_product_files( $download_data ) {

	$selected_files = array();

	// No $download_data passed through
	if ( empty( $download_data ) )
		return $selected_files;

	// No action in $download_data
	if ( empty( $download_data['action'] ) )
		return false;

	// No product id in $download_data
	if ( empty( $download_data['somdn_product'] ) )
		return false;

	$product = somdn_get_product( intval( $download_data['somdn_product'] ) );

	// Return for good measure if product not found
	if ( empty( $product ) )
		return $selected_files;

	return apply_filters( 'somdn_get_selected_product_files', $selected_files, $download_data );

}

add_filter( 'somdn_get_selected_product_files', 'somdn_get_selected_product_files_standard', 15, 2 );
function somdn_get_selected_product_files_standard( $selected_files, $download_data ) {

	$action = sanitize_key( $_POST['action'] );
	$actions = somdn_get_download_actions();

	if ( in_array( $action, $actions ) ) {

		// This is a standard product download submitted by a frontend download form
		$product = somdn_get_product( $download_data['somdn_product'] );
		$downloads = somdn_get_files( $product );
		$downloads_count = count( $downloads );

		$checked_count = 0;
		
		while ( $checked_count < $downloads_count ) {
		
			$checkbox_number = $checked_count + 1;
			$checkbox_id = 'somdn_download_file_' . strval( $checkbox_number );

			if ( isset( $_POST[$checkbox_id] ) && $_POST[$checkbox_id] ) {
				array_push( $selected_files, ( $checked_count ) );
			}
			
			$checked_count++;
			
		}

	}

	return $selected_files;

}

/**
 * Return an array of all file paths to download
 *
 * @param $product The WooCommerce product object
 * @param $downloads The download file array
 * @return array Just the file paths
 */
function somdn_get_file_paths( $product, $downloads ) {
	$download_files = array();
	foreach ( $downloads as $key => $each_download )  {
		$file_path = somdn_get_download_filepath( $product, $key, $each_download, $each_download['id'] );
		array_push( $download_files, $file_path );
	}
	return $download_files;
}

/**
 * Return the filtered file url/path to download
 *
 * @param  object $product        The product object
 * @param  int    $key            The key in the array of products
 * @param  array  $download_array The array of file info ( id, name, file )
 * @param  int    $download_id    The ID of the download in the downloads array
 * @return string                 Filepath/URL for the file (filtered)
 */
function somdn_get_download_filepath( $product, $key, $download_array, $download_id ) {
	$file_path = $download_array['file'];
	return apply_filters( 'somdn_download_path', $file_path, $product, $key, $download_array, $download_id );
}

function somdn_get_download_filepath_raw( $product, $key, $download_array, $download_id ) {
	$file_path = $download_array['file'];
	return $file_path;
}

/*
 * Check whether the file we're downloading is stored locally or on an external server.
 * First check if the file is duplicated into the temporary folder, if not then check by using the url headers
 */
function somdn_is_local_file( $url ) {

	$url_array = explode( '/', $url );
	$filename = end( $url_array );

	$upload_dir = wp_upload_dir();
	$local_path = $upload_dir['basedir'] . '/free-downloads-files/temp-files/' . $filename;
	$url_path = $upload_dir['baseurl'] . '/free-downloads-files/temp-files/' . $filename;

	if ( file_exists( $local_path ) ) {
		//echo '<p>Exists</p>';
		return true;
	} else {
		//echo '<p>Not Local</p>';
		$headers = get_headers( $url );
		return stripos( $headers[0], '200 OK' ) ? false : true ;
	}

}

function somdn_is_file_local( $file_path ) {
	$upload_dir = wp_upload_dir();
	return ( false !== stripos( $file_path, $upload_dir['basedir'] ) );
}

function somdn_file_get_temporary_file_name( $original_file ) {

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
		$file_name = 'somefile.txt';
	}

	return $file_name;

}

/**
 * Check if file is stored locally and if not fetch external file
 * @version 3.1.5
 */
add_filter( 'somdn_download_path', 'somdn_download_path_external_file', 25, 5 );
function somdn_download_path_external_file( $file_path, $product, $key, $download_array, $download_id ) {

	$product_id = intval( somdn_get_product_id( $product ) );
	if ( empty( $product_id ) ) {
		return $file_path;
	}

	global $somdn_init_download_type;

	if ( ! empty( $somdn_init_download_type ) ) {
		if ( $somdn_init_download_type == 'single_file_default' ) {
			// No need to fetch the file, single file downloads work for external files already
			return $file_path;
		}
	}

	$original_file = str_replace( WP_CONTENT_URL, WP_CONTENT_DIR, $file_path );
	//echo '<p>$original_file = [' . $original_file . ']</p>';

	$local = somdn_is_file_local( $original_file );
	//echo '<p>$local = [' . $local . ']</p>';

	if ( $local ) {
		// Don't do anything, file exists locally already
		return $file_path;
	}

	// File is external, so let's fetch it and store a temporary copy on the server

	//echo '<p>$local = [' . $local . ']</p>';
	//echo '<p>somdn_download_path_external_file</p>';
	//echo '<p>$file_path = [' . $file_path . ']</p>';
	//exit;

	somdn_create_temp_uploads_folders();

	$parent = somdn_get_upload_folder_parent_path();
	$zip_path = somdn_get_upload_folder_zip_path( true );

	$new_file_path = $zip_path;
	$file_name = somdn_file_get_temporary_file_name( $original_file );

	$fetched_file = somdn_do_fetch_remote_file( $original_file, $new_file_path . $file_name );
	//unlink( $new_file_path . $remote_name );

	if ( $fetched_file == false ) {
		// Something went wrong with the file fetch, return default path
		$log_entry = 'There was a problem fetching an external file for download.';
		if ( ! empty( $product_id ) ) {
			$log_entry .= ' Product ID ' . $product_id . '.';
		}
		somdn_write_log( $log_entry );
		return $file_path;
	}

	$new_file = $new_file_path . $file_name;
	//echo '<p>$new_file = [' . $new_file . ']</p>';
	//exit;

	if ( ! empty( $new_file ) ) {
		return $new_file;
	}

	return $file_path;

}

/**
 * Fetch external files
 * @version 3.1.5
 */
function somdn_do_fetch_remote_file( $original_file, $local_path_and_file ) {

	if ( ! file_exists( $local_path_and_file ) ) {
		//echo "<p>1st check - File doesn't exist</p>";
		$response = wp_remote_get( $original_file );
		$remote_file = wp_remote_retrieve_body( $response );
		@file_put_contents( $local_path_and_file, $remote_file );
	}

	if ( ! file_exists( $local_path_and_file ) ) {
		//The fetch command didn't work
		//echo "<p>2nd check - File doesn't exist</p>";
		return false;
	}

	return true;

}

/*
 * Determines whether the file is locally stored or not.
 * If local then open unsing Google Dosc Viewer, otherwise download as normal.
 */
function somdn_show_pdf( $file, $product_id ) {

	if ( somdn_is_local_file( $file ) ) {

		// File is stored locally. Now to check if the file has already been duplicated (security) for viewing into the temporary folder.
		// If it has then $duplicate_file variable is set to $url_path, otherwise run the normal duplicate procedure.

		$url_array = explode( '/', $file );
		$filename = end( $url_array );

		$upload_dir = wp_upload_dir();

		somdn_create_temp_uploads_folders();

		$parent = somdn_get_upload_folder_parent_path();
		$zip_path = somdn_get_upload_folder_zip_path( true );

		$local_path = $zip_path . $filename;
		$url_path = $upload_dir['baseurl'] . '/free-downloads-files/temp-files/' . $filename;

		if ( ! file_exists( $local_path ) ) {

			// File not already duplicated

			$duplicate_file = somdn_duplicate_pdf( $file );
			if ( ! $duplicate_file ) {
				somdn_do_download( $file, $product_id );
				return;
			}

		} else {

			// File has already been duplicated
			$duplicate_file = $url_path;

		}

	} else {
		// File is hosted externally, download as normal
		somdn_do_download( $file, $product_id );
		return;
	}

	$google_path = 'https://docs.google.com/viewerng/viewer?url=' . $duplicate_file;
	//echo '<script>window.open("' . $google_path . '")</script>';

	do_action( 'somdn_count_download', $product_id );
	
	wp_redirect( $google_path );
	//wp_redirect( get_the_permalink( $product_id ) . '?somdn_pdf=' . $google_path );
	exit();

}

/**
 * Duplicate a local PDF file for viewing externally
 *
 * @param  string $file_path  The file path of the original PDF
 * @return string $newfileurl Filepath/URL for the file
 */
function somdn_duplicate_pdf( $file_path ) {

	$upload_dir = wp_upload_dir();

	somdn_create_temp_uploads_folders();

	$parent = somdn_get_upload_folder_parent_path();
	$zip_path = somdn_get_upload_folder_zip_path();

	$path         = parse_url( $file_path, PHP_URL_PATH );
	$abs_filepath = $_SERVER['DOCUMENT_ROOT'] . $path;
	$uri          = ltrim( $path, '/' );

	$now        = DateTime::createFromFormat( 'U.u', microtime( true ) );
	$code1      = $now->format( "ms" );
	$code2      = $now->format( "u" );
	$downloadID = get_current_user_id() . $code1 . $code2;

	$newfileurl = $upload_dir['baseurl'] . '/free-downloads-files/temp-files/' . $downloadID . '.pdf';
	$newfile    = $upload_dir['basedir'] . '/free-downloads-files/temp-files/' . $downloadID . '.pdf';

	if ( ! copy( $abs_filepath, $newfile ) ) {
		return false;
	} else {
		return $newfileurl;
	}

}

/**
 * Create a ZIP archive from passed in filepaths
 *
 * @param array  $files The file paths
 * @param string $destination The location to save the ZIP archive
 * @param bool   $overwrite whether to overwrite an existing archive if found
 * @param bool   $distill_subdirectories remove subdirectories
 * @return string Filepath/URL for the file
 */
function somdn_create_zip( $files = array(), $destination = '', $overwrite = false, $distill_subdirectories = true ) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
		    if ($distill_subdirectories) {
		        $zip->addFile($file, basename($file) );
		    } else {
		        $zip->addFile($file, $file);
		    }
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		//print_r($zip);
		
		//$newfilepath = $zip['filename'];
		$newfilepath = $zip->filename;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		if ( file_exists($destination) ) {
			return $newfilepath;
		} else {
			return false;
		}
	}
	else
	{
		return false;
	}
}

/**
 * Get filesize function. In testing.
 * 
 */
//foreach( $downloads as $key => $each_download )  {
//		$path = parse_url( $each_download['file'], PHP_URL_PATH );
//		$abs_filepath = $_SERVER['DOCUMENT_ROOT'] . $path;
//		$size = filesize($abs_filepath);
//		echo formatBytes($size, 1);
//}
//function formatBytes($size, $precision = 2) {
//	$base = log($size, 1024);
//	$suffixes = array( '', ' KB', ' MB', ' G', ' T' );   
//	return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
//}