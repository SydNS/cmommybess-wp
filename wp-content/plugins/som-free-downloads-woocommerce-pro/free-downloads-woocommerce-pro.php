<?php
/**
 * Plugin Name: Free Downloads WooCommerce Pro
 * Plugin URI: https://squareonemedia.co.uk
 * Description: Allow users to instantly download your free digital products without going through the checkout.
 * Version: 3.2.1
 * Author: Square One Media
 * Author URI: https://squareonemedia.co.uk
 * Requires at least: 4.4
 * Tested up to: 5.5.1
 * Requires PHP: 7.0.0
 *
 * Text Domain: somdn-pro
 * Domain Path: /i18n/languages
 *
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 4.4.1
 */

defined( 'ABSPATH' ) || exit;

if ( defined( 'SOMDN_FILE' ) ) {
	if ( SOMDN_FILE != __FILE__ ) {
		require_once( 'somdn-uninstall-existing.php' );
		// Return after deactivating the existing Free Downloads plugin.
		// Once the page has finished loading the new plugin will be active.
		return;
	}
}

final class SOM_Free_Downloads {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	private $version = '3.2.1';

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	private $name = 'Free Downloads WooCommerce Pro';

	/**
	 * Base version (woocommerce or edd).
	 *
	 * @var string
	 */
	private $base = 'woocommerce';

	/**
	 * Plugin database version setting.
	 *
	 * @var string
	 */
	private $db_setting = 'somdn_woo_pro_plugin_db_version';

	/**
	 * Plugin file.
	 *
	 * @var string
	 */
	private $file = __FILE__;

	/**
	 * Plugin setting that would not be set if this was a clean install.
	 *
	 * @var string
	 */
	private $fresh_install_setting = 'somdn_gen_settings';

	/**
	 * The array of requirement data for this plugin.
	 *
	 * @var array
	 * @since 1.0
	 */
	private $requirements_data;

	/**
	 * The array of requirements for this plugin.
	 *
	 * @var array
	 * @since 1.0
	 */
	private $requirements;

	/**
	 * Whether the plugin requirements have been met. Default = True
	 *
	 * @var bool
	 * @since 1.0
	 */
	private $requirements_met = true;

	/**
	 * The single instance of the class.
	 *
	 * @var SOM_Free_Downloads
	 * @since 3.1.7
	 */
	private static $instance = null;

	/**
	 * Empty Constructor
	 */
	private function __construct() {}

	/**
	 * Main Class Instance.
	 *
	 * Ensures only one instance of this class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @return Class instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			if ( self::$instance->check_requirements() == false ) {
				return NULL;
			}
			self::$instance->define_constants();
			self::$instance->includes();
			self::$instance->init_hooks();
			self::$instance->load_modules();
		}
		return self::$instance;
	}

	/**
	 * Define Plugin Constants.
	 */
	private function define_constants() {
		$this->define( 'SOMDN_FILE', $this->file );
		$this->define( 'SOMDN_PLUGIN_VER', $this->version );
		$this->define( 'SOMDN_BASE', $this->base );
		$this->define( 'SOMDN_PLUGIN_NAME_FULL', $this->name );
		$this->define( 'SOMDN_PATH', plugin_dir_path( SOMDN_FILE ) );
		$this->define( 'SOMDN_PLUGIN_PATH', plugin_basename( dirname( SOMDN_FILE ) ) );
		$this->define( 'SOMDN_PLUGIN_BASENAME', plugin_basename( SOMDN_FILE ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		require_once SOMDN_PATH . 'includes/somdn-functions.php';
		require_once SOMDN_PATH . 'includes/somdn-file-functions.php';
		require_once SOMDN_PATH . 'includes/somdn-downloader.php';
		require_once SOMDN_PATH . 'includes/somdn-download-page.php';
		require_once SOMDN_PATH . 'includes/somdn-plugin-settings.php';
		require_once SOMDN_PATH . 'includes/somdn-compatibility.php';
		require_once SOMDN_PATH . 'includes/somdn-meta.php';
		require_once SOMDN_PATH . 'includes/somdn-doc-viewer-functions.php';
		require_once SOMDN_PATH . 'includes/somdn-shortcodes.php';

		require_once SOMDN_PATH . 'somdn-base-loader.php';

		$pro_loader = SOMDN_PATH . 'pro/somdn-pro-loader.php';
		if ( file_exists( $pro_loader ) ) require_once $pro_loader;

		// Load the update file to update the database where needed
		require_once SOMDN_PATH . 'includes/class-somdn-db-updater.php';

		do_action( 'somdn_after_file_loader' );
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook( $this->file, array( $this, 'somdn_activated' ) );
		register_deactivation_hook( $this->file, array( $this, 'somdn_deactivated' ) );
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), -1 );
	}

	public function somdn_activated() {
		do_action( 'somdn_on_activate' );
		do_action( 'somdn_pro_activated' );
	}

	public function somdn_deactivated() {
		do_action( 'somdn_on_deactivate' );
		do_action( 'somdn_pro_deactivated' );
	}

	/**
	 * Plugins that add new modules to Free Downloads WooCommerce can
	 * use the 'somdn_load_modules' action to hook into this plugin.
	 */
	private function load_modules() {
		do_action( 'somdn_load_modules' );
	}

	/**
	 * When WP has loaded all plugins, trigger the `somdn_loaded` hook.
	 *
	 * This ensures `somdn_loaded` is called only after all other plugins
	 * are loaded, to avoid issues caused by plugin directory naming changing
	 * the load order.
	 *
	 * @since 3.1.7
	 */
	public function on_plugins_loaded() {
		do_action( 'somdn_loaded' );
		$this->update_plugin();
	}

	private function update_plugin() {
		// setup the updater
		$updater = new SOMDN_DB_Updater(
			$this->version,
			$this->db_setting,
			$this->fresh_install_setting
		);
	}

	public function get_version() {
		return $this->version;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_base() {
		return $this->base;
	}

	public function get_db_setting() {
		return $this->db_setting;
	}

	public function get_main_file() {
		return $this->file;
	}

	private function check_requirements() {
		// Check if plugin requirements are met before loading anything else
		$this->set_requirements_data();
		// Load up the dependency checker class
		$can_do_check = include_once( 'class-som-plugin-requirements.php' );
		if ( $can_do_check == true ) {
			$requirements_check = new SOMDN_WOO_PRO_Requirements( $this->requirements_data );
			if ( $requirements_check->requirements_met == false ) {
				$this->requirements_met = false;
				$requirements_check->missing_requirements();
			}
		}
		return $this->requirements_met;
	}

	private function set_requirements_data() {
		// Build the plugin requirements array
		$this->requirements = array(
			$woocommerce_plugin = array(
				'type_id' => 'wp_plugin',
				'type_name' => __( 'WordPress Plugin', 'somdn-pro' ),
				'name' => 'WooCommerce',
				'url'  => 'https://en-gb.wordpress.org/plugins/woocommerce/',
				'path' => 'woocommerce/woocommerce.php'
			),
			$php_version_check = array(
				'type_id' => 'php_min_ver',
				'type_name' => __( 'Minimum PHP Version', 'somdn-pro' ),
				'name' => 'Version 7.0.0',
				'url' => 'https://www.php.net/manual/en/migration56.php',
				'min_ver' => '7.0.0'
			)
		);
		// Build the requirements data array
		$this->requirements_data = array(
			'plugin_name' => $this->name,
			'plugin_file' => $this->file,
			'requirements'  => $this->requirements,
			'error_title' => __( 'Missing Requirements', 'somdn-pro' ),
			'error_message' => _x( '{plugin_name} will not work without the following requirements:', 'This message is explaining that this plugin, called {plugin_name}, will not work without a list of requirements or dependencies.', 'somdn-pro' )
		);
	}

}

/**
 * Load the plugin
 */
require_once( 'somdn.php' );
somdn();