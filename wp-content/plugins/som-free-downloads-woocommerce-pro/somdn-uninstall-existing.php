<?php
/**
 * Existing Plugin Uninstaller
 * 
 * Loaded if the plugin detects an existing version is already installed.
 * For example if the user has Free Downloads WooCommerce already active,
 * but has activated the Pro Edition. Since these plugins can't be used together,
 * the existing plugin gets deactivated.
 *
 * Since we don't want to conflict with the existing install, everything is run
 * in the global scope.
 * 
 * @author Square One Media
 * @version	1.0.1
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'SOMDN_PLUGIN_BASENAME' ) ) {
	return;
}

// Bring in the plugin.php so we can call functions in it
require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
if ( is_plugin_active( SOMDN_PLUGIN_BASENAME ) ) {
	deactivate_plugins( SOMDN_PLUGIN_BASENAME );
}