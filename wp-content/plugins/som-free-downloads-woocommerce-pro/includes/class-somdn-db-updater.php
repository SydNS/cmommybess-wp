<?php
/**
 * Free Downloads - Database Updater
 * 
 * @version	3.1.92
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Updater Class.
 *
 * @since 3.1.7
 */
final class SOMDN_DB_Updater {

	private $new_version;
	private $old_version;
	private $setting_name;
	private $fresh_install_setting = '';
	private $updates = array();

	private function clean_string( $string ) {
		return esc_html( trim( $string ) );
	}

	/**
	 * Class constructor.
	 *
	 * @param string  $version                The current/latest version of the plugin.
	 * @param string  $setting_name           The setting name for the version number stored in the database.
	 * @param string  $fresh_install_setting  The setting name to check for if this is a clean installation.
	 */
	public function __construct( $version = '', $setting_name = '', $fresh_install_setting = '' ) {
		//echo '$fresh_install_setting = ' . $fresh_install_setting;
		if ( empty( $version ) || empty( $setting_name ) ) {
			return;// bail
		}
		$this->new_version = $this->clean_string( $version );

		$this->setting_name = $this->clean_string( $setting_name );

		$this->old_version = ! empty( get_option( $this->setting_name ) ) ?
		$this->clean_string( get_option( $this->setting_name ) )
		: '0.0.1' ;

		//$this->old_version = '0.0.1';

		if ( ! empty( $fresh_install_setting ) ) {
			$this->fresh_install_setting = $this->clean_string( $fresh_install_setting );
		}

		//$this->old_version = '3.1.91';
		//$this->new_version = '3.1.91';
		//update_option( $this->setting_name, '3.1.91' );

		//$this->fresh_install_setting = 'somdn_some_setting_name';

		//echo '<p>$this->new_version = ' . $this->new_version . '</p>';
		//echo '<p>$this->old_version = ' . $this->old_version . '</p>';
		//echo '<p>$this->fresh_install_setting = ' . $this->fresh_install_setting . '</p>';

		if ( $this->is_clean_install() === true ) {
			//echo '<p>is_clean_install()</p>';
			update_option( $this->setting_name, $this->new_version );
			return;
		}

		if ( $this->is_current_version() === true ) {
			//echo '<p>is_current_version()</p>';
			return;
		}

		// Run the update procedures
		$this->init();
		$this->run();
		$this->update_complete();

	}

	private function update_complete() {
		$updated = update_option( $this->setting_name, $this->new_version );
		//echo '<p>$updated = '.$updated.'</p>';
		//exit;
		do_action( 'somdn_update_complete' );
	}

	private function is_current_version() {
		if ( version_compare( $this->new_version, $this->old_version, '=' ) ) {
			// Version numbers are the same
			//echo '<p>version the same</p>';
			return true;
		} else {
			//echo '<p>version different</p>';
			return false;
		}
	}

	private function is_clean_install() {
		$setting_to_check = $this->setting_name;
		if ( ! empty( $this->fresh_install_setting ) ) {
			$setting_to_check = $this->fresh_install_setting;
		}
		//echo '<p>$setting_to_check = ' .$setting_to_check . '</p>';
		if ( empty( get_option( $setting_to_check ) ) ) {
			//echo '<p>Is clean</p>';
			return true;
		} else {
			//echo '<p>Is existing</p>';
			return false;
		}
	}

	/**
	 * Set up filters to pull in any updates needed from other files.
	 *
	 * @uses apply_filters()
	 *
	 * @return void
	 */
	public function init() {
		$this->updates = apply_filters( 'somdn_plugin_updates_array', $this->get_updates_array() );
	}

	public function get_updates_array() {
		return array(
			'default_updates' => array(
				'2.1.0' => array(
					'somdn_plugin_update_to_2_1_0'
				),
				'3.1.5' => array(
					array( $this, 'somdn_plugin_update_to_3_1_5' )
				)
			)
		);
	}

	public function run() {
		$updates = $this->updates;
		//somdn_debug_array($updates);

		$cleaned_args = array(
			'db_setting' => $this->setting_name,
			'new_version' => $this->new_version
		);

		// Now we run through each update index which contains a stack of version numbers and callback functions
		// For each of these stacks we check the version number and if it's lower than the current one we run the callback
		// function which will perform the necessary tasks to update the plugin.

		// Split out each stack of updates, which starts with 'default_updates' and then include ones returned from the filter 'somdn_plugin_updates_array'
		foreach ( $updates as $stack ) {

			//somdn_debug_array($stack);

			foreach ( $stack as $version => $update_callbacks ) {
				//echo '<p>Version ' . $version . '.</p>';
				if ( version_compare( $this->new_version, $version, '<' ) ) {
					// If for some reason the version number inside the update is greater than the current
					// plugin version, just skip to the next one in the list.
					continue;
				}
				if ( version_compare( $this->old_version, $version, '<' ) ) {
					//echo '<p>Update needed for version ' . $version . '.</p>';
					foreach ( $update_callbacks as $update_callback ) {
						if ( is_callable( $update_callback ) ) {

							// Call the function to process the update for each version
							$update_result = call_user_func( $update_callback, $cleaned_args );

							if ( is_array( $update_result ) ) {
								$new_error = array();
								// Something went wrong with the update, an error has been returned (array of values) instead of a true boolean
								$update_title = $update_result['title'];
								$update_error = $update_result['error'];
								$new_error['title'] = $update_title;
								$new_error['error'] = $update_error;
								array_push( $_REQUEST['somdn_db_update_errors'], $new_error );
							}

						}
					}
				}
			}

		}
		do_action( 'somdn_update_run' );
	}

	public function somdn_plugin_update_to_3_1_5( $update_args ) {
		//echo '<p>class somdn_plugin_update_to_3_1_5</p>';

		$result = '';

		// Delete the old file download temp folder
		$upload_dir = wp_upload_dir();
		$old_zip_path = $upload_dir['basedir'] . '/download-now-uploads';
		if ( file_exists( $old_zip_path ) ) {
			array_map( 'unlink', glob( $upload_dir['basedir'] . '/download-now-uploads/*' ) );
			rmdir( $old_zip_path );
		}

		// No error to pass back for this update

		$result = true;

		return $result;
	}
}

function somdn_plugin_db_updater() {
	return SOMDN_DB_Updater::instance();
}

/*
 * Template for adding new updates to the updates array by filtering
 */
/*
add_filter( 'somdn_plugin_updates_array', 'somdn_plugin_updates_array_default', 10, 1);
function somdn_plugin_updates_array_default( $updates ) {
	$default_updates = array(
		'default_updates' => array(
			'2.1.0' => array(
				'somdn_plugin_update_to_2_1_0'
			),
			'3.1.5' => array(
				'somdn_plugin_update_to_3_1_5'
			)
		)
	);
	$new_updates_array = array_merge( $updates, $default_updates );
	return $new_updates_array;
}
*/
/*
Template for update errors:
	$result = array(
		'title' => 'Update {version number} Error',
		'error' => 'A description of the error.'
	);
*/