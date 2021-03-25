<?php
/**
 * Free Downloads - Doc Viewer Functions
 * 
 * Functions for the document viewer feature.
 * 
 * @version	2.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function somdn_get_ext_from_path( $path ) {

	$filepath = strtok( $path, '?' );
	$ext = pathinfo( $filepath , PATHINFO_EXTENSION );
	$ext = strtolower( $ext );
	
	return $ext;
	
}

function somdn_is_supported_doc( $ext, $supported_docs ) {

	if ( in_array( $ext, $supported_docs ) ) {
		return true;
	}

}

function somdn_get_supported_docs() {

	$file_types = array(
		'pdf'
	);

	return $file_types;

	$file_types = array(
		'pdf',
		'ps',
		'eps',
		'doc',
		'docx',
		'ppt',
		'pptx',
		'xlsx',
		'xls',
		'gdoc',
		'gslides',
		'gsheet'
	);

	return $file_types;
}
