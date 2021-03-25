<?php
/**
 * Free Downloads - Main plugin class Loader
 * 
 * @version	3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add a notice to the WP dashboard advising of any missing requirements
 */
//add_action( 'admin_notices', 'somdn_plugin_no_req_plugins_notice' );
function somdn_plugin_no_req_plugins_notice(){
 
	if( ! somdn_plugin_is_required_active() ) {
		?>
		<div class="notice notice-error">
			<p><strong><?php echo SOMDN_PLUGIN_NAME_FULL; ?></strong> will not work without all of the following plugins:</p>
			
			<?php
			
			$requirements = somdn_plugin_requirements();

			if ( ! empty( $requirements ) ) {
				foreach ( $requirements as $requirement ) {

					printf( '<p><a rel="nofollow" href="%2$s" target="_blank">%1$s</a></p>', $requirement['name'], $requirement['url']);

				}
			}
			
			?>		

		</div>
		<?php
	}
}

/**
 * If plugin dependencies are active, load in the required plugin files
 */
//if ( somdn_plugin_is_required_active() ) {

	require_once( SOMDN_PATH . 'includes/somdn-functions.php' );
	require_once( SOMDN_PATH . 'includes/somdn-file-functions.php' );
	require_once( SOMDN_PATH . 'includes/somdn-downloader.php' );
	require_once( SOMDN_PATH . 'includes/somdn-download-page.php' );
	require_once( SOMDN_PATH . 'includes/somdn-plugin-settings.php' );
	require_once( SOMDN_PATH . 'includes/somdn-compatibility.php' );
	require_once( SOMDN_PATH . 'includes/somdn-meta.php' );
	require_once( SOMDN_PATH . 'includes/somdn-doc-viewer-functions.php' );
	require_once( SOMDN_PATH . 'includes/somdn-shortcodes.php' );

	require_once( SOMDN_PATH . 'somdn-base-loader.php' );

	$pro_loader = SOMDN_PATH . 'pro/somdn-pro-loader.php';
	if ( file_exists( $pro_loader ) ) require_once( $pro_loader );

	// Load the update file to update the database where needed
	require_once( SOMDN_PATH . 'includes/somdn-updates-master.php' );

	do_action( 'somdn_after_file_loader' );

//}