<?php
/**
 * Free Downloads - WooCommerce - Pro Actions
 * 
 * Fire up the pro actions and filters
 * 
 * @version	1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WooCommerce specific filters
 */
add_action( 'somdn_product_meta_box_html', 'somdn_product_meta_box_html_pro', 10, 2 );
add_action( 'somdn_save_meta_product_meta', 'somdn_save_meta_product_meta_pro', 10 );
add_action( 'somdn_before_form_inputs_variation', 'somdn_output_timestamp_form', 50 );
add_action( 'somdn_load_product_page_content', 'somdn_load_product_page_content_woo_variations', 20 );
add_action( 'somdn_do_default_download_type_variation', 'somdn_do_default_download_variation', 10 );
add_action( 'somdn_count_download', 'somdn_valid_download_check_limits_setup' );

remove_filter( 'somdn_is_product_included', 'somdn_is_product_included_individual', 50 );
add_filter( 'somdn_is_product_included', 'somdn_is_product_included_advanced', 25, 3 );

add_action( 'wp_ajax_somdn_search_products', 'somdn_search_products_ajax_callback' );

remove_shortcode( 'download_now' );
add_shortcode( 'download_now', 'somdn_single_shortcode_pro' );

/**
 * WooCommerce compatibility filters
 */
add_filter( 'somdn_is_free', 'somdn_is_product_member_free', 10, 3 );
add_filter( 'somdn_is_free', 'somdn_is_product_member_free_pms', 35, 3 );

/**
 * Template file features
 */
add_filter( 'somdn_get_templates', 'somdn_get_templates_pro', 20, 1 );

//add_action( 'init', 'somdn_ajax_search_actions_filters' );
function somdn_ajax_search_actions_filters() {

	/**
	 * Actions
	 *
	 */
	

	/**
	 * Filters
	 *
	 */

}