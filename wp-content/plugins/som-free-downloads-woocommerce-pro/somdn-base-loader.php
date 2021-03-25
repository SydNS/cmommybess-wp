<?php
/**
 * Free Downloads - Base File Loader
 * Check for the base (WooCommerce, EDD etc) and load the necessary files
 * 
 * @version 3.0.9
 * @package Free Downloads
 * @author  Square One Media
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( defined( 'SOMDN_BASE' ) ) {

	$base = constant( 'SOMDN_BASE' );

	/**
	 * Check for WooCommerce
	 */
	if ( $base === 'woocommerce' ) :
		require_once( SOMDN_PATH . 'woo-files/somdn-woo.php' );
	endif;

	/**
	 * Check for Easy Digital Downloads
	 */
	if ( $base === 'edd' ) :
		require_once( SOMDN_PATH . 'edd-files/somdn-edd.php' );
	endif;

	do_action( 'somdn_load_bases', $base );

}

do_action( 'somdn_after_load_bases' );