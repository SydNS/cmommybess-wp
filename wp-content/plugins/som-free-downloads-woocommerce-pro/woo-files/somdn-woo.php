<?php
/**
 * Free Downloads - Woo Loader
 * 
 * Load up the various woocommerce files
 * 
 * @version	3.0.92
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SOMDN_WOO_FILE', __FILE__ );
define( 'SOMDN_WOO_PATH', SOMDN_PATH . 'woo-files/' );

/**
 * WooCommerce specific filters
 */
add_filter( 'somdn_get_product', 'somdn_get_product_woo', 10, 2 );
add_filter( 'somdn_get_global_product', 'somdn_get_global_product_woo', 10, 1 );
add_filter( 'somdn_get_product_id', 'somdn_get_product_id_woo', 10, 2 );
add_filter( 'somdn_is_product', 'somdn_is_product_woo', 10, 2 );
add_filter( 'somdn_is_single_product', 'somdn_is_single_product_woo', 10, 1 );
add_filter( 'somdn_get_files', 'somdn_get_files_woo', 10, 3 );
add_filter( 'somdn_get_price', 'somdn_get_price_woo', 10, 3 );
add_filter( 'somdn_get_sale_price', 'somdn_get_sale_price_woo', 10, 3 );
add_filter( 'somdn_is_product_valid_type', 'somdn_is_product_valid_type_woo_basic', 10, 3 );
add_filter( 'woocommerce_product_add_to_cart_text' , 'somdn_change_read_more' );
add_filter( 'somdn_get_button_classes', 'somdn_get_button_classes_woo', 10, 1 );
add_filter( 'somdn_get_button_archive_classes', 'somdn_get_button_archive_classes_woo', 10, 1 );
add_filter( 'somdn_get_download_button', 'somdn_get_download_button_woo', 10, 7 );
add_filter( 'somdn_get_single_download_link', 'somdn_get_single_download_link_woo', 10, 6 );
add_filter( 'somdn_get_multi_download_link', 'somdn_get_multi_download_link_woo', 10, 6 );
add_filter( 'somdn_frontend_warning_class', 'somdn_frontend_warning_class_woo' );
add_filter( 'somdn_frontend_error_class', 'somdn_frontend_error_class_woo' );
add_filter( 'somdn_is_free', 'somdn_is_download_owned', 85, 3 );
add_filter( 'woocommerce_product_get_sale_price', 'somdn_is_download_owned_price', 99, 2 );
add_filter( 'woocommerce_get_price_html', 'somdn_download_owned_price_html', 99, 2 );
add_filter( 'somdn_is_product_valid_quickview', 'somdn_is_product_valid_quickview_basic', 10, 3 );

/**
 * WooCommerce compatibility filters
 */
add_filter( 'somdn_extra_archive_action', 'somdn_is_product_valid_wo_membership_basic', 10, 3 );
add_filter( 'somdn_is_product_valid_compat', 'somdn_product_valid_compat_woo_basic', 10, 3 );

/**
 * WooCommerce specific actions
 */
add_action( 'somdn_load_product_page_content', 'somdn_load_product_page_content_woo' );
add_action( 'somdn_do_download', 'somdn_woo_download', 10, 3 );
add_action( 'somdn_before_add_to_cart_form', 'somdn_before_add_to_cart_form_woo' );
add_action( 'somdn_before_add_to_cart_button', 'somdn_before_add_to_cart_button_woo' );
add_action( 'somdn_after_add_to_cart_form', 'somdn_after_add_to_cart_form_woo' );
add_action( 'somdn_after_add_to_cart_button', 'somdn_after_add_to_cart_button_woo' );
add_action( 'woocommerce_sale_flash' , 'somdn_download_owned_price_badge', 99, 3 );
add_action( 'somdn_before_quickview_title_wrap', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'somdn_do_default_download_type_simple', 'somdn_do_default_download_simple', 10 );

/**
 * Load up WooCommerce files
 */
require_once( SOMDN_PATH . 'woo-files/somdn-woo-functions.php' );
require_once( SOMDN_PATH . 'woo-files/somdn-woo-settings.php' );
require_once( SOMDN_PATH . 'woo-files/somdn-woo-meta.php' );
require_once( SOMDN_PATH . 'woo-files/somdn-woo-compatibility.php' );
require_once( SOMDN_PATH . 'woo-files/somdn-woo-quickview.php' );