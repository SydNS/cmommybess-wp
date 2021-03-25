<?php
/**
 * Free Downloads - Compatibility Functions
 * 
 * Functions for compatibility with other plugins.
 * 
 * @version	3.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function somdn_is_product_valid_compat( $product, $product_id ) {
	return apply_filters( 'somdn_is_product_valid_compat', false, $product, $product_id );
}