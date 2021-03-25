<?php
/**
 * WC_NP class.
 */
class WC_NP {

	/**
	 * Theme init.
	 */
	public static function init() {
		// Remove default wrappers.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );

		// Add custom wrappers.
		add_action( 'woocommerce_before_main_content', array( __CLASS__, 'output_content_wrapper' ) );
		add_action( 'woocommerce_after_main_content', array( __CLASS__, 'output_content_wrapper_end' ) );

		// Declare theme support for features.
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		add_theme_support( 'woocommerce', array(
			'thumbnail_image_width' => 400,
			'single_image_width'    => 800,
		) );
	}

	/**
	 * Open wrappers.
	 */
	public static function output_content_wrapper() {
		echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
	}

	/**
	 * Close wrappers.
	 */
	public static function output_content_wrapper_end() {
		echo '</main></div>';
	}
}

WC_NP::init();

$productsON = false;

if ($productsON) {
    //Woocommerce theme functions

    // add our woocommerce scripts and styles
    add_action('wp_enqueue_scripts', 'add_woocommerce_script', 1003);
    function add_woocommerce_script() {
        if (is_product() || is_shop() || is_cart()) {
            wp_register_script('woocommerce-theme-scripts', get_template_directory_uri() . '/woocommerce/js/woocommerce-theme-scripts.js', array('wc-add-to-cart-variation'), false, true);
            wp_enqueue_script('woocommerce-theme-scripts');
            wp_enqueue_style('woocommerce-theme-styles', get_template_directory_uri() . '/woocommerce/css/woocommerce-theme-styles.css', array());
        }
    }

    // add html for variation price/old price
    add_filter( 'woocommerce_available_variation', 'variations_product_price_html' );
    function variations_product_price_html( $variations ) {
        $variations['display_price_html'] = wc_price($variations['display_price']);
        $variations['display_regular_price_html'] = $variations['display_regular_price'] === $variations['display_price'] ? '' : wc_price($variations['display_regular_price']);
        return $variations;
    }

    // add our class to button "view cart"
    add_filter( 'wc_add_to_cart_message_html', 'filter_message_add_to_cart', 10, 3 );
    function filter_message_add_to_cart( $message ) {
        return $message = preg_replace('/class="/', 'class="u-btn ', $message);
    }

    // add our class to button "confirm order"
    add_filter('woocommerce_order_button_html', 'filter_class_payment_button', 10, 3);
    function filter_class_payment_button( $html ) {
        $button_text = __('Place order', 'woocommerce');
        $button_with_our_class = '‌<a onclick="jQuery(\'#place_order\').click();" style="float: right;" class="u-btn">' . $button_text . '</a>';
        $hidden_original_button = str_replace('button ', 'button u-form-control-hidden ', $html);
        return $html = $button_with_our_class . $hidden_original_button;
    }

    if (!function_exists('np_review_ratings_enabled')) {
        /**
         * @return bool
         */
        function np_review_ratings_enabled() {
            return 'yes' === get_option('woocommerce_enable_reviews') && 'yes' === get_option('woocommerce_enable_review_rating');
        }
    }
    if (!function_exists('np_review_ratings_required')) {
        /**
         * @return bool
         */
        function np_review_ratings_required() {
            return 'yes' === get_option('woocommerce_review_rating_required');
        }
    }
    /**
     * WooCommerce: Change the number of products displayed per page @ shop page
     */
    add_filter('loop_shop_per_page', 'product_count_shop_per_page', 99);
    function product_count_shop_per_page($default_products_count) {
        $products_count = '{count_products}';
        if (!is_numeric($products_count)) {
            return $default_products_count;
        }
        return $products_count;
    }
}