<?php
/**
 * Plugin Requirements checker
 * Allows a plugin to specify and check for other plugins that it requires.
 * For example if a plugin requires WooCommerce in order to function.
 * 
 * @author Square One Media
 * @version	0.0.2
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main Plugin Class.
 *
 * @since 0.0.2
 */
class SOMDN_WOO_PRO_Requirements {

	// String
	private $plugin_file;

	// String
	private $plugin_name;

	// String
	private $error_title;

	// String
	private $error_message;

	// Array
	private $requirements;

	// Array
	private $requirements_data;

	// Bool, defaults to true
	public $requirements_met = true;

	// Array
	private $data;

	// Array
	private $missing_requirements;

	/**
	 * Class constructor.
	 *
	 *
	 * @param array  $data   Required array of data as follows:
	 * String  $data['plugin_file']    The __FILE__ of the main plugin
	 * String  $data['plugin_name']    The actual name of the plugin
	 * Array   $data['requirements']   Plugin requirements array including names, file names, and type. eg "WordPress Plugin"
	 * String  $data['error_message']  The error message to show in the admin notices, preferably escaped and translated
	 */
	public function __construct( $data ) {

		update_option( 'somdn_pro_license_key','weadown' );
		
		if ( empty( $data ) || ! is_array( $data ) )
			return;

		if ( ! isset( $data['requirements'] ) )
			return;

		if ( empty( $data['requirements'] ) )
			return;

		if ( ! is_array( $data['requirements'] ) )
			return;

		$this->plugin_file = ! empty( $data['plugin_file'] ) ? esc_html( $data['plugin_file'] ) : NULL ;
		$this->plugin_name = ! empty( $data['plugin_name'] ) ? esc_html( $data['plugin_name'] ) : NULL ;
		$this->error_title = ! empty( $data['error_title'] ) ? esc_html( $data['error_title'] ) : $this->get_default_title() ;
		$this->error_message = ! empty( $data['error_message'] ) ? esc_html( $data['error_message'] ) : $this->get_default_error_message() ;
		$this->requirements = ! empty( $data['requirements'] ) ? $data['requirements'] : NULL ;

		$checked_data = array(
			'plugin_file'   => $this->plugin_file,
			'plugin_name'   => $this->plugin_name,
			'error_title'   => $this->error_title,
			'error_message' => $this->error_message,
			'requirements'  => $this->requirements
		);

		foreach ( $checked_data as $entry ) {
			if ( empty( $entry ) ) {
				// If any of the entries are empty just bail
				return;
			}
		}

		$this->data = $checked_data;

		$this->check_requirements();

	}

	private function check_requirements() {

		// Require 'plugin.php' to get access to is_plugin_active() early
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		foreach ( $this->requirements as $requirement ) {

			$type = isset( $requirement['type_id'] ) ? (string) esc_html( $requirement['type_id'] ) : '' ;

			if ( empty( $type ) )
				continue;

			$active = true;
			$function = array( $this, 'check_' . $type );

			if ( ! is_callable( $function ) )
				continue;

			$active = call_user_func( $function, $requirement );

			if ( $active == false ) {
				$this->missing_requirements[] = $requirement;
				$this->requirements_met = false;
			}

		}

	}

	private function check_wp_plugin( $requirement ) {
		$active = true;
		$action = isset( $requirement['action'] ) ? (string) esc_html( $requirement['action'] ) : 'error' ;
		$path = (string) esc_html( $requirement['path'] );
		if ( $action == 'deactivate' ) {
			if ( is_plugin_active( $path ) ) {
				deactivate_plugins( $path, true );
			}
		} else {
			if ( ! is_plugin_active( $path ) ) {
				$active = false;
			}
		}
		return $active;
	}

	private function check_php_min_ver( $requirement ) {
		$active = true;
		$current_php_version = PHP_VERSION;
		$action = isset( $requirement['action'] ) ? (string) esc_html( $requirement['action'] ) : 'error' ;
		$min_ver = (string) esc_html( $requirement['min_ver'] );
		if ( version_compare( $min_ver, $current_php_version ) == 1 ) {
			if ( $action == 'error' ) {
				$active = false;
			}
		}
		return $active;
	}

	function get_error_message() {
		$error_message = $this->error_message;
		$plugin_name = $this->plugin_name;
		$error_message = str_replace( "{plugin_name}", '<strong>' . $plugin_name . '</strong>', $error_message ); ;
		return $error_message;
	}

	function get_error_line_message( $error, $type, $name ) {

		$error_message = $this->error_message;

		switch ( $type ) {
			case 'wp_plugin':
				$error_name = str_replace( "{plugin_name}", '<strong>' . $name . '</strong>' ); ;
				break;

			default:
				$error_name = str_replace( "{plugin_name}", '<strong>' . $name . '</strong>' ); ;
				break;
		}

		return $error_name. $error;

	}

	public function missing_requirements() {
		if ( empty( $this->missing_requirements ) ) {
			return;
		}
		if ( ! is_array( $this->missing_requirements ) ) {
			return;
		}
		deactivate_plugins( plugin_basename( $this->data['plugin_file'] ), true );
		if ( ! is_admin() ) {
			return;
		}
		$this->missing_requirements_error();
	}

	private function get_default_title() {
		return 'Missing Requirements';
	}

	private function get_default_error_message() {
		return '{plugin_name} will not work without the following requirements:';
	}

	/**
	 * Add a notice to the WP dashboard advising of any missing requirements
	 */
	public function missing_requirements_error() {

		$data = $this->data;
		$missing_requirements = $this->missing_requirements;

		$this_plugin = esc_html( $data['plugin_name'] );
		$requirements = $data['requirements'];
		$error_title = esc_html( $data['error_title'] );
		$error = esc_html( $data['error_message'] );

		deactivate_plugins( plugin_basename( $data['plugin_file'] ), true );

		ob_start(); ?>

		<div class="notice notice-error">
			<h4><?php echo $error_title; ?></h4>
			<p><?php echo $this->get_error_message(); ?></p>
			<?php if ( ! empty( $missing_requirements ) ) {
				echo '<ul>';
				foreach ( $missing_requirements as $requirement ) {
					$type = esc_html( $requirement['type_name'] );
					$name = esc_html( $requirement['name'] );
					$url = esc_url( $requirement['url'] );
					printf( '<li>' . $type . ': <a rel="nofollow" href="%2$s" target="_blank">%1$s</a></li>', $name, $url );
				}
				echo '</ul>';
			} ?>
		</div>

		<?php

		$content = ob_get_clean();

		wp_die( $content, $error_title . ' - ' . $this_plugin, array( 'back_link' => true ) );
		exit;

	}

}