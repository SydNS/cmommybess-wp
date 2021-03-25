<?php
/**
 * Free Downloads - WooCommerce - Pro Loader
 * 
 * Loads the pro files.
 * 
 * @version	3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'SOMDN_PATH_PRO', plugin_dir_path( dirname( __FILE__ ) ) . 'pro/' );
define( 'SOMDN_PRO', SOMDN_PATH_PRO . 'includes/somdn-pro-functions.php' );
define( 'SOM_SOMDN_STORE_URL', 'https://squareonemedia.co.uk' );
define( 'SOM_SOMDN_ITEM_NAME', 'Free Downloads - WooCommerce Pro' );
define( 'SOM_SOMDN_ITEM_ID', 73 );
define( 'SOM_SOMDN_LICENSE_PAGE', 'pluginname-license' );

// Load dependency files (functions etc)
require_once SOMDN_PATH_PRO . 'includes/somdn-pro-actions.php';
require_once SOMDN_PATH_PRO . 'includes/somdn-pro-functions.php';
require_once SOMDN_PATH_PRO . 'includes/somdn-pro-download-type.php';
require_once SOMDN_PATH_PRO . 'includes/somdn-pro-downloader.php';
require_once SOMDN_PATH_PRO . 'includes/somdn-pro-variation-download-page.php';
require_once SOMDN_PATH_PRO . 'includes/somdn-pro-meta.php';
require_once SOMDN_PATH_PRO . 'includes/somdn-pro-compatibility.php';
require_once SOMDN_PATH_PRO . 'includes/somdn-pro-settings.php';
require_once SOMDN_PATH_PRO . 'includes/somdn-pro-user-role-limits.php';
require_once SOMDN_PATH_PRO . 'includes/somdn-pro-user-limits.php';

/**
 * Plugin Updater
 *
 */
if( !class_exists( 'SOMDN_SL_Plugin_Updater' ) ) {
	// load our custom updater
	require_once SOMDN_PATH_PRO . 'updater/SOMDN_SL_Plugin_Updater.php';
}

// Plugin update checker
add_action( 'admin_init', 'somdn_plugin_updater', 0 );
function somdn_plugin_updater() {

	$license_key = trim( get_option( 'somdn_pro_license_key' ) ); 

	// setup the updater
	$somdn_updater = new SOMDN_SL_Plugin_Updater( SOM_SOMDN_STORE_URL, SOMDN_FILE,
		array(
			'version'	=> SOMDN_PLUGIN_VER,
			'license'	=> $license_key,
			'item_id'	=> SOM_SOMDN_ITEM_ID,
			'author'	=> 'Square One Media',
			'beta'		=> false
		)
	);

}

//add_filter( 'somdn_plugin_updates_array', 'somdn_plugin_updates_array_woo_pro', 50, 1 );
function somdn_plugin_updates_array_woo_pro( $updates ) {
	return $updates;
}