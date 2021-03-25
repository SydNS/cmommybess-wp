<?php
/**
 * Free Downloads - WooCommerce - Pro Functions
 * 
 * Various functions.
 * 
 * @version	3.1.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action('somdn_on_activate', 'somdn_pro_on_activate');
function somdn_pro_on_activate() {
	if ( function_exists( 'somdn_downloads_cpt' ) ) {
		somdn_downloads_cpt();// Load up the custom post types
		flush_rewrite_rules();// Clear the permalink rewrite rules after CPT created
	}
}

add_filter( 'woocommerce_is_purchasable', 'somdn_prevent_purchase_global', 9999, 2 );
function somdn_prevent_purchase_global( $purchasable, $product ) {
	$genoptions = get_option( 'somdn_gen_settings' );
	$global_site_disable = ( isset( $genoptions['somdn_pro_disable_ecommerce'] ) && $genoptions['somdn_pro_disable_ecommerce'] ) ? true : false ;
	if ( $global_site_disable === true ) {
		$purchasable = false;
	}
	return $purchasable;
}

function somdn_loop_add_to_cart_grouped( $args = array() ) {

	global $product;

	$genoptions = get_option( 'somdn_gen_settings' );
	$singleoptions = get_option( 'somdn_single_settings' );
	$hide_readmore = ( isset( $genoptions['somdn_hide_readmore_button_archive'] ) && $genoptions['somdn_hide_readmore_button_archive'] ) ? true : false ;
	$button_extra_text = '';
	if ( isset( $singleoptions['somdn_single_button_text'] ) && ! empty( $singleoptions['somdn_single_button_text'] ) ) 	{
		$button_extra_text = esc_html( $singleoptions['somdn_single_button_text'] );
	} else {
		$button_extra_text = __( 'Download Now', 'somdn-pro' );
	}

	$product_id = somdn_get_product_id( $product );

	$somdn_group_loop = $args['somdn_group_loop'];
	$somdn_valid_download = $args['somdn_valid_download'];
	$valid_download_no_login = $args['valid_download_no_login'];
	$somdn_logged_in = $args['somdn_logged_in'];

	$valid_default = somdn_is_product_valid( $product_id );

	if ( $valid_default ) {

		echo '<div>';
		if ( ! empty( $args ) ) {
			$download_page_args = apply_filters( 'somdn_loop_add_to_cart_grouped_args', array(
				'archive'=> true,
				'product' => $product,
				'archive_enabled' => true,
				'shortcode' => true,
				'shortcode_text' => $button_extra_text
			) );
			somdn_product_page( $download_page_args );
		} else {
			$download_page_args = apply_filters( 'somdn_loop_add_to_cart_grouped_args', array(
				'archive'=> true,
				'product' => $product,
				'echo' => true
			) );
			do_action( 'somdn_archive_product_page', $download_page_args );
		}
		echo '</div>';

		return;
/*
	} elseif ( somdn_is_product_valid( $product_id, false ) && $hide_readmore ) {
		// If the user would be able to download the product if they were logged in

		//echo '<span>free_no_download_login</span><br>';

		// Show nothing, custom action hook added
		do_action( 'somdn_archive_free_no_download_login' );
*/
	} else {
/*
		// An extra action check that plugins can hook into
		$extra_archive_action = somdn_extra_archive_action( $product_id, $hide_readmore );
		if ( $extra_archive_action && $hide_readmore ) {
			return;
		}
*/
		if ( $product ) {

			$defaults = array(
				'quantity'   => 1,
				'class'      => implode( ' ', array_filter( array(
					'button',
					'product_type_' . $product->get_type(),
					$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
					$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
				) ) ),
				'attributes' => array(
					'data-product_id'  => $product->get_id(),
					'data-product_sku' => $product->get_sku(),
					'aria-label'       => $product->add_to_cart_description(),
					'rel'              => 'nofollow',
				),
			);

			$args = apply_filters( 'woocommerce_loop_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

			if ( isset( $args['attributes']['aria-label'] ) ) {
				$args['attributes']['aria-label'] = strip_tags( $args['attributes']['aria-label'] );
			}

			wc_get_template( 'loop/add-to-cart.php', $args );

		}

	}

}

add_action( 'admin_enqueue_scripts', 'somdn_get_script_assets_pro' );
function somdn_get_script_assets_pro() {
	wp_enqueue_style('somdn-tinymce-style', plugins_url('/pro/assets/css/somdn_tinymce_style.css', dirname( dirname( __FILE__ ) ) ) );
	wp_enqueue_style('somdn-pro-admin-style', plugins_url('/pro/assets/css/somdn_pro_admin_css.css', dirname( dirname( __FILE__ ) ) ) );

	if ( ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'download_now_dashboard' ) ) {

		/**
		 * Select2
		 */

		// First check if Select2 is already enqueued before enqueuing it.
		//if ( ! wp_script_is( 'select2', 'enqueued' ) ) {
			wp_enqueue_style( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css', array(), '4.0.6' );
			wp_enqueue_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', array(), '4.0.6', true );
		//}

		wp_register_script( 'somdn-pro-admin-select2', plugins_url('/pro/assets/js/somdn_pro_admin_select2.js',  dirname( dirname( __FILE__ ) ) ), array( 'jquery', 'select2' ) );
		wp_localize_script( 'somdn-pro-admin-select2', 'somdn_select2_params', array(
			'somdn_search_products_nonce' => wp_create_nonce( 'somdn-search-products' ),
		) );
		wp_enqueue_script( 'somdn-pro-admin-select2' );

	}

	wp_register_script( 'somdn-pro-admin-script', plugins_url('/pro/assets/js/somdn_pro_admin_script.js',  dirname( dirname( __FILE__ ) ) ), array( 'jquery', 'wp-color-picker' ) );

	wp_enqueue_script( 'somdn-pro-admin-script' );
	wp_enqueue_style( 'wp-color-picker' );
}

/**
 * Load pro only scripts, remove basic edition script loader
 */
remove_action( 'wp_enqueue_scripts', 'somdn_load_scripts' );
add_action( 'wp_enqueue_scripts', 'somdn_pro_load_scripts' );
function somdn_pro_load_scripts() {

	wp_register_style( 'somdn-style', plugins_url('/assets/css/somdn-style.css', dirname( dirname( __FILE__ ) ) ) );
	wp_enqueue_style( 'somdn-style' );

	$track_options = get_option( 'somdn_pro_track_settings' );
	$newsletter_options = get_option( 'somdn_pro_newsletter_general_settings' );
	$download_type = get_option( 'somdn_download_type_settings' );

	$redirect_time = intval( isset( $download_type['somdn_download_type_redirect_time'] ) && $download_type['somdn_download_type_redirect_time']
	? $download_type['somdn_download_type_redirect_time']
	: 5 );
	if ( $redirect_time > 60 ) {
		$redirect_time = 60;
	} elseif ( $redirect_time <= 0 ) {
		$redirect_time = 5;
	}
	// Convert time in seconds to milliseconds for javascript
	$redirect_time = $redirect_time * 1000;

	$capture_emails_active = false;

	$email_fname_error = ( isset( $newsletter_options['somdn_newsletter_fname_error'] ) && $newsletter_options['somdn_newsletter_fname_error'] )
	? $newsletter_options['somdn_newsletter_fname_error']
	: __( 'Please enter your first name', 'somdn-pro' ) ;

	$email_lname_req = isset( $newsletter_options['somdn_newsletter_lname_required'] ) ? $newsletter_options['somdn_newsletter_lname_required'] : false ;
	$email_lname_error = ( isset( $newsletter_options['somdn_newsletter_lname_error'] ) && $newsletter_options['somdn_newsletter_lname_error'] )
	? $newsletter_options['somdn_newsletter_lname_error']
	: __( 'Please enter your last name', 'somdn-pro' ) ;

	$email_error_none = ( isset( $track_options['somdn_capture_email_error_none'] ) && $track_options['somdn_capture_email_error_none'] )
	? $track_options['somdn_capture_email_error_none']
	: __( 'Please enter your email address', 'somdn-pro' ) ;

	$email_error_invalid = ( isset( $track_options['somdn_capture_email_error_invalid'] ) && $track_options['somdn_capture_email_error_invalid'] )
	? $track_options['somdn_capture_email_error_invalid']
	: __( 'Please enter a valid email address', 'somdn-pro' ) ;

	$email_tel_req = isset( $newsletter_options['somdn_newsletter_tel_required'] ) ? $newsletter_options['somdn_newsletter_tel_required'] : false ;
	$email_tel_error = ( isset( $newsletter_options['somdn_newsletter_tel_error'] ) && $newsletter_options['somdn_newsletter_tel_error'] )
	? $newsletter_options['somdn_newsletter_tel_error']
	: __( 'Please enter your telephone number', 'somdn-pro' ) ;

	$email_company_req = isset( $newsletter_options['somdn_newsletter_company_required'] ) ? $newsletter_options['somdn_newsletter_company_required'] : false ;
	$email_company_error = ( isset( $newsletter_options['somdn_newsletter_company_error'] ) && $newsletter_options['somdn_newsletter_company_error'] )
	? $newsletter_options['somdn_newsletter_company_error']
	: __( 'Please enter your company name', 'somdn-pro' ) ;

	$email_website_req = isset( $newsletter_options['somdn_newsletter_website_required'] ) ? $newsletter_options['somdn_newsletter_website_required'] : false ;
	$email_website_error = ( isset( $newsletter_options['somdn_newsletter_website_error'] ) && $newsletter_options['somdn_newsletter_website_error'] )
	? $newsletter_options['somdn_newsletter_website_error']
	: __( 'Please enter your website', 'somdn-pro' ) ;

	$email_checkbox_error = ( isset( $newsletter_options['somdn_newsletter_checkbox_error'] ) && $newsletter_options['somdn_newsletter_checkbox_error'] )
	? $newsletter_options['somdn_newsletter_checkbox_error']
	: __( 'You must select the checkbox to download', 'somdn-pro' ) ;

	if ( somdn_is_email_capture_enabled() ) {
		$capture_emails_active = true;
	}

	$quickview_options = get_option( 'somdn_woo_quickview_settings' );
	$quickview_enabled = isset( $quickview_options['somdn_woo_quickview_enable'] ) ? $quickview_options['somdn_woo_quickview_enable'] : false ;

	wp_enqueue_style('somdn-pro-style', plugins_url('/pro/assets/css/somdn_pro_css.css', dirname( dirname( __FILE__ ) ) ) );

	wp_enqueue_script( 'somdn-pro-script', plugins_url('/pro/assets/js/somdn_pro_script.js',  dirname( dirname( __FILE__ ) ) ), 'jquery' , '1.0.0', true );

	wp_localize_script( 'somdn-pro-script', 'somdn_script_params', array(
		'somdn_ajax_url' => admin_url( 'admin-ajax.php' ),
		'somdn_ajax_nonce' => wp_create_nonce( 'somdn_ajax_nonce' ),
		'somdn_capture_emails_active' => $capture_emails_active,
		'somdn_capture_fname_req' => true,
		'somdn_capture_lname_req' => $email_lname_req,
		'somdn_capture_fname_empty' => apply_filters( 'somdn_capture_fname_empty', $email_fname_error ),
		'somdn_capture_lname_empty' => apply_filters( 'somdn_capture_lname_empty', $email_lname_error ),
		'somdn_capture_email_empty' => apply_filters( 'somdn_capture_email_empty', $email_error_none ),
		'somdn_capture_email_invalid' => apply_filters( 'somdn_capture_email_invalid', $email_error_invalid ),
		'somdn_capture_tel_req' => $email_tel_req,
		'somdn_capture_tel_empty' => apply_filters( 'somdn_capture_tel_empty', $email_tel_error ),
		'somdn_capture_company_req' => $email_company_req,
		'somdn_capture_company_empty' => apply_filters( 'somdn_capture_company_empty', $email_company_error ),
		'somdn_capture_website_req' => $email_website_req,
		'somdn_capture_website_empty' => apply_filters( 'somdn_capture_website_empty', $email_website_error ),
		'somdn_capture_checkbox_error' => $email_checkbox_error,
		'somdn_qview_active' => $quickview_enabled,
		'somdn_redirect_time' => $redirect_time
	) );

	wp_enqueue_style( 'dashicons' );

}

add_action( 'wp_ajax_somdn_ajax_validate_download', 'somdn_ajax_validate_download_callback' );
add_action( 'wp_ajax_nopriv_somdn_ajax_validate_download', 'somdn_ajax_validate_download_callback' );
function somdn_ajax_validate_download_callback() {

	check_ajax_referer( 'somdn_ajax_nonce', 'security' );

	$product_id = stripslashes( $_GET['product_id'] );

	//$valid = somdn_is_download_valid( $product_id );
	$valid = false;
	if ( $valid == false ) {
		echo 'download_invalid';
	} else {
		echo 'download_valid';
	}

	wp_die();

/*
	$product_id = stripslashes( $_GET['product_id'] );
	$return = array();

	foreach ( $products as $product_id ) {
		$product_id_int = (int)$product_id;
		$return[] = array( $product_id_int, get_the_title( $product_id ) . ' (#' . $product_id_int . ')' );
	}

	echo json_encode( $return );
	die;
*/
}

if ( ! function_exists( 'woocommerce_grouped_add_to_cart' ) ) {

	/**
	 * Output the grouped product add to cart area.
	 */
	function woocommerce_grouped_add_to_cart() {
		global $product;

		$products = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );

		if ( $products ) {

			$free_count = 0;
			$paid_count = 0;

			/* We want to check for any free products in the group, and if there aren't any load the default template. */
			$any_free = false;
			foreach ($products as $product_single) {
				$id = $product_single->get_id();
				if ( somdn_is_product_valid( $id, false ) ) {
					$any_free = true;
					$free_count ++;
				} else {
					$paid_count ++;
				}
			}

			//echo '<p>$paid_count = ' . $paid_count . '</p>';
			//echo '<p>$free_count = ' . $free_count . '</p>';

			$grouped_product = $product;
			$grouped_products = $products;

			if ( $any_free ) {
				include( SOMDN_PATH_PRO . 'templates/somdn-grouped.php' );
			} else {
				wc_get_template( 'single-product/add-to-cart/grouped.php', array(
					'grouped_product'    => $product,
					'grouped_products'   => $products,
					'quantites_required' => false,
				) );
			}

		}

	}
}

function somdn_show_pdf_variation( $file, $product_id, $variation_id ) {

	if ( somdn_is_local_file( $file ) ) {
		$duplicate_file = somdn_duplicate_pdf( $file, $product_id );
		if ( ! $duplicate_file ) {
			somdn_woo_download( $file, $product_id );
			return;			
		}
	} else {
		somdn_woo_download( $file, $product_id );
		return;
	}

	$google_path = 'https://docs.google.com/viewerng/viewer?url=' . $duplicate_file;
	//echo '<script>window.open("' . $google_path . '")</script>';

	do_action( 'somdn_count_download', $product_id );
	
	wp_redirect( $google_path );
	//wp_redirect( get_the_permalink( $product_id ) . '?somdn_pdf=' . $google_path );
	exit();

}

add_action('admin_head', 'somdn_add_free_download_button');
function somdn_add_free_download_button() {
	global $typenow;

	if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
		return;
	}

	if ( get_user_option('rich_editing') == 'true') {
		add_filter( 'mce_external_plugins', 'somdn_add_tinymce_plugin' );
		add_filter( 'mce_buttons', 'somdn_register_download_now_tinymce' );
	}
}

function somdn_add_tinymce_plugin($plugin_array) {
	$plugin_array[ 'somdn_download_now_tinymce' ] = plugins_url( '/pro/assets/js/somdn_tinymce_script.js', dirname(dirname(__FILE__)) );
	return $plugin_array;
}

function somdn_register_download_now_tinymce($buttons) {
	array_push( $buttons, 'somdn_download_now_tinymce' );
	return $buttons;
}

add_action( 'admin_head', 'somdn_product_id_head' );
function somdn_product_id_head() {

	$screen = get_current_screen();
	$productID = '';

	if ( $screen->base == 'post' && $screen->post_type == 'product' ) {

		if ( isset( $_GET[ 'post' ] ) ) {
			$productID = $_GET[ 'post' ];
			
echo '
<script type="text/javascript">
	somdn_product_id = "' . $productID . '";
</script>
';
		
		}
	}

}

function somdn_is_download_valid_variation( $product_id, $variation_id ) {
	return apply_filters( 'somdn_is_download_valid_variation', false, $product_id, $variation_id );
}

add_filter( 'somdn_is_download_valid_variation', 'somdn_is_variation_download_valid', 10, 3 );
function somdn_is_variation_download_valid( $valid, $product_id, $variation_id ) {

	$product = somdn_get_product( $product_id );
	if ( empty( $product ) ) {
		return false;
	}

	if ( ! somdn_is_variable_product_valid( $product_id, $variation_id ) ) {
		return $valid = false;
	}

	if ( somdn_download_limits_active() ) {
		$user_reached_limit = somdn_has_user_reached_limit( $product_id );
		if ( $user_reached_limit == true ) {
			return false;
		}
	}

	return true;

}

add_filter( 'somdn_is_download_valid', 'somdn_basic_download_has_user_reached_limit', 50, 2 );
function somdn_basic_download_has_user_reached_limit( $valid, $product_id ) {

	$user_reached_limit = false;

	if ( $valid == false ) {
		return $valid;
	}

	if ( somdn_download_limits_active() ) {
		$user_reached_limit = somdn_has_user_reached_limit( $product_id );
		if ( $user_reached_limit == true ) {
			$valid = false;
			//echo '$user_reached_limit = ' . $user_reached_limit;
			//exit;
		}
	}

	return $valid;

}

function somdn_is_variable_product_valid_type( $product, $product_id = '', $variation_id ) {
	return apply_filters( 'somdn_is_variable_product_valid_type', false, $product, $product_id, $variation_id );
}

add_filter( 'somdn_is_variable_product_valid_type', 'somdn_is_variable_product_valid_type_woo', 10, 4 );
function somdn_is_variable_product_valid_type_woo( $valid, $product, $product_id, $variation_id ) {
	if ( empty( $product ) ) {
		$product = somdn_get_product( $product_id );
	}
	if ( ! empty( $product ) ) {
		// Simple product check
		if ( $product->is_type( 'simple' ) ) {
			$valid = false;
		}
		if ( $product->is_type( 'variable' ) ) {
			$valid = true;
		}
		// Only include virtual downloadable products
		$variation_product = new WC_Product_Variation( $variation_id );
		$downloadable = $variation_product->is_downloadable();
		$virtual = true; //$variation_product->is_virtual();
		if ( ! $downloadable || ! $virtual ) {
			return false;
		}
	}
	return apply_filters( 'somdn_is_variable_product_valid_type_woo', $valid, $product, $product_id, $variation_id );
}

function somdn_is_variable_product_valid( $product_id, $variation_id, $check_login = true ) {
	return apply_filters( 'somdn_is_variable_product_valid', false, $product_id, $variation_id, $check_login );
}

add_filter( 'somdn_is_variable_product_valid', 'somdn_is_variable_product_valid_main', 10, 4 );
function somdn_is_variable_product_valid_main( $valid, $product_id, $variation_id, $check_login = true ) {

	$product = somdn_get_product( $product_id );
	if ( empty( $product ) ) {
		return false;
	}

	// Check if product is a valid product type for downloading free
	if ( ! somdn_is_variable_product_valid_type( $product, $product_id, $variation_id ) ) {
		return false;
	}

	$variation_parent = wp_get_post_parent_id( $variation_id );
	$variation_product = new WC_Product_Variation( $variation_id );

	if ( $variation_parent != $product_id ) {
		return false;
	}

	// Are products included individually and if so is this product included? If not return false
	if ( ! somdn_is_product_included( $product, $product_id ) ) {
		return false;
	}

	// If this product has no files for download, return false
	if ( ! somdn_product_has_downloads( $variation_product, $variation_id ) )  {
		return false;
	}

	// If the product is free and whether it is on sale and included in free downloads
	if ( ! somdn_is_product_free( $variation_product, $variation_id ) ) {
		return false;
	}

	// Check for product compatibility with other plugins
	if ( ! somdn_is_product_valid_compat( $product, $product_id ) ) {
		return false;
	}

	// Check if free downloads require login and if so and the user is not logged in, return false
	if ( $check_login ) {
		if ( ! somdn_is_required_login_check( $product, $product_id ) ) {
			return false;
		}
	}

	return true;

}

function somdn_has_user_reached_limit( $product_id, $user_id = '', $reached_limit = false ) {

	$errors = array();

	// Bail if there are no download limitations set
	if ( ! somdn_download_limits_active() ) {
		return $reached_limit;
	}

	if ( empty( $user_id ) ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		}
	}

	if (somdn_user_limits_excluded($user_id)) {
		return false;
	}

	$user_limits = somdn_get_user_limits( $user_id );
	if ( empty( $user_limits ) ) {
		return $reached_limit;
	}

	$limits_type     = $user_limits['type'];
	$limits_amount   = $user_limits['amount'];
	$limits_products = $user_limits['products'];
	$limits_freq     = $user_limits['freq'];
	$limits_error    = $user_limits['error'];
	$freq_name       = $user_limits['freq_name'];

	$gen_options = get_option( 'somdn_gen_settings' );
	$login_required = isset( $gen_options['somdn_require_login'] ) ? $gen_options['somdn_require_login'] : false ;

	$user_ip = somdn_current_user_ip();

	if ( $limits_type == 1 ) {
		// This limitation needs the user ID to work. If no user ID found, bail
		if ( empty( $user_id ) ) {
			return $reached_limit;
		}
		// This limitation needs the "Require Login" to work. If that option is not set, bail
		if ( $login_required == false ) {
			return $reached_limit;
		}
	}

	$amount_limited = ! empty( $limits_amount ) ? true : false ;
	$products_limited = ! empty( $limits_products ) ? true : false ;
/*
	if ( $amount_limited && $products_limited ) {
	
		// We have a total download limit with a set restriction on number of products
		$limits_error = 'You have reached your maximum free download limit for this product of ' . $limits_amount . ' per ' . $freq_name . '.';
	
	} elseif ( $amount_limited && ! $products_limited ) {

		// We have a total download limit but no restriction on number of products
		$limits_error = 'You have reached your maximum free download limit of ' . $limits_amount . ' per ' . $freq_name . '.';

	} elseif ( ! $amount_limited && $products_limited ) {

		// We have a restriction on the number of products, but no limit on how many downloads for that product
		$limits_error = 'You have reached your maximum free download limit of ' . $limits_products . ' products per ' . $freq_name . '.';

	} else {
		$limits_error = $limits_error;
	}

	echo '<pre>';
	print_r($user_limits);
	echo '</pre>';
	echo '<br><br>';
	echo 'New Error = ' . $limits_error;
	exit;
*/
	// Line up the default args for the post query
	$args = array(
		'fields' => 'ids',
		'post_type' => 'somdn_tracked',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(),
		'date_query' => array()
	);

	$meta_key = '';
	$meta_value = '';

	switch ( $limits_type ) {
		case 1: // Limit Downloads by User

			$meta_key = 'somdn_user_id';
			$meta_value = $user_id;
			break;
				

		case 2: // Limit Downloads by IP Address

			$meta_key = 'somdn_user_ip';
			$meta_value = $user_ip;
			break;

		default:
			// no recognised limit type
			return $reached_limit;
			break;
	}

	$current_time = current_time( 'mysql' );

	// Add the date range to the query args
	switch ( $limits_freq ) {
		case 1: // Current Day

/*
 * Current 3.1.8 solution
 */
			$timestamp = date( 'U', strtotime( $current_time ) );
			$today = getdate( $timestamp );

			$args['date_query'] = array(
				array(
					'year'  => $today['year'],
					'month' => $today['mon'],
					'day'   => $today['mday']
				),
			);

/*
 * Previous 3.0 solution
 *
			$current_time = current_time( 'mysql' );
			$today = getdate( $current_time );

			$args['date_query'] = array(
				array(
					'year'  => $today['year'],
					'month' => $today['mon'],
					'day'   => $today['mday']
				),
			);

/*
 * Previous 2.0 solution
 *

			$today = getdate();

			$args['date_query'] = array(
				array(
					'year'  => $today['year'],
					'month' => $today['mon'],
					'day'   => $today['mday']
				),
			);
*/
/*
 * Previous 1.0 solution
 *
			$args['date_query'] = array(
				array(
					'year'  => date( 'Y' ),
					'month' => date( 'n' ),
					'day'   => date( 'j' )
				)
			);
*/
			break;

		case 2: // Current Week

			//echo 'week';
			//exit;

			$weekstarting = get_weekstartend( $current_time );
			$after = date( 'Y-m-d', $weekstarting['start'] );
			$args['date_query'] = array(
				array(
					'after' => $after,
					'inclusive' => true
				)
			);

			//echo '$after = ' . $after;

		/*
			$args['date_query'] = array(
				array(
					'year' => date( 'Y' ),
					'week' => date( 'W' )
				)
			);
*/
			break;

		case 3: // Current Month

			$args['date_query'] = array(
				array(
					'year' => date( 'Y', strtotime($current_time) ),
					'month' => date( 'n', strtotime($current_time) )
				)
			);
			
			break;

		case 4: // Current Year

			$args['date_query'] = array(
				array(
					'year' => date( 'Y', strtotime($current_time) )
				)
			);
			
			break;
		
		default:
			// no recognised limit frequency
			return $reached_limit;
			break;
	}

	// If limit is set to limit global downloads only, run default check.
	if ( empty( $limits_products ) && ! empty( $limits_amount ) ) {

		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => $meta_key,
				'value' => $meta_value,
				'compare' => '=',
			)
		);

		$download_history = new WP_Query( $args );
		$download_history_count = $download_history->found_posts;

		if ( $download_history_count ) {
			if ( $download_history_count >= $limits_amount ) {
				$errors['download_limit_reached'] = $limits_error;
				array_push( $_REQUEST['somdn_errors'], $errors);
				$reached_limit = true;
			}
		}
		
		//somdn_debug_array($args);

		//echo '$download_history_count = ' . $download_history_count;
		//exit;

		return $reached_limit;

	}

	// If limit is set to restrict products with a limited number of downloads for each
	if ( ! empty( $limits_products ) && ! empty( $limits_amount ) ) {
	
		// First we check number of different products downloaded

		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => $meta_key,
				'value' => $meta_value,
				'compare' => '=',
			)
		);

		// Let's create an array of different product ID's previously downloaded
		$products_downloaded = array();

		$download_history = new WP_Query( $args );
		$download_history_count = $download_history->found_posts;

		if ( $download_history_count ) {

			$download_posts = $download_history->posts;

			foreach ( $download_posts as $download_post ) {

				$download_id = $download_post;// value is already a post id
				$downloaded_product = get_post_meta( $download_id, 'somdn_product_id', true );
				if ( ! empty( $downloaded_product ) ) {
					if ( ! in_array( $downloaded_product, $products_downloaded ) ) {
						array_push( $products_downloaded, $downloaded_product);
					}
				}

			}

			// Check if the current product isn't already in the download list
			$product_in_list = in_array( $product_id, $products_downloaded );

			// If not already downloaded and the max number of products has been downloaded, limit reached
			if ( ! $product_in_list && ( count( $products_downloaded ) >= $limits_products ) ) {
				//$limits_error = 'You have reached your maximum free download limit of ' . $limits_products . ' products per ' . $freq_name . '.';
				$errors['download_limit_reached'] = $limits_error;
				array_push( $_REQUEST['somdn_errors'], $errors);
				$reached_limit = true;
			}

		}
		
		if ( $reached_limit == true ) {
			return $reached_limit;
		}
		
		// Now that we know the number of products has not reached it's limit, let's check the downloads for this product

		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => $meta_key,
				'value' => $meta_value,
				'compare' => '=',
			),
			array(
				'key' => 'somdn_product_id',
				'value' => $product_id,
				'compare' => '=',
			)
		);

		$download_history = new WP_Query( $args );
		$download_history_count = $download_history->found_posts;

		if ( $download_history_count ) {
			if ( $download_history_count >= $limits_amount ) {
				$errors['download_limit_reached'] = $limits_error;
				array_push( $_REQUEST['somdn_errors'], $errors);
				$reached_limit = true;
			}
		}

		return $reached_limit;

	}

	// If limit is set to restrict products with unlimited downloads
	if ( ! empty( $limits_products ) && empty( $limits_amount ) ) {

		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key' => $meta_key,
				'value' => $meta_value,
				'compare' => '=',
			)
		);

		// Let's create an array of different product ID's previously downloaded
		$products_downloaded = array();

		$download_history = new WP_Query( $args );
		$download_history_count = $download_history->found_posts;

		if ( $download_history_count ) {

			$download_posts = $download_history->posts;

			foreach ( $download_posts as $download_post ) {

				$download_id = $download_post;// value is already a post id
				$downloaded_product = get_post_meta( $download_id, 'somdn_product_id', true );
				if ( ! empty( $downloaded_product ) ) {
					if ( ! in_array( $downloaded_product, $products_downloaded ) ) {
						array_push( $products_downloaded, $downloaded_product);
					}
				}

			}

			// Check if the current product isn't already in the download list
			$product_in_list = in_array( $product_id, $products_downloaded );

			// If not already downloaded and the max number of products has been downloaded, limit reached
			if ( ! $product_in_list && ( count( $products_downloaded ) >= $limits_products ) ) {
				$errors['download_limit_reached'] = $limits_error;
				 array_push( $_REQUEST['somdn_errors'], $errors);
				$reached_limit = true;
			}

		}

		return $reached_limit;

	}

	// Return for good measure
	return $reached_limit;

}

function somdn_get_user_downloads_count_total( $user_id ) {

	$args = array(
		'fields' => 'ids',
		'post_type' => 'somdn_tracked',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(),
		'date_query' => array()
	);

	$args['meta_query'] = array(
		'relation' => 'AND',
		array(
			'key' => 'somdn_user_id',
			'value' => $user_id,
			'compare' => '=',
		)
	);

	$download_history = new WP_Query( $args );
	$download_history_count = $download_history->found_posts;

	return $download_history_count;

}

function somdn_get_user_downloads_count( $user_id = '', $products = false ) {

	if ( empty( $user_id ) ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		}
	}

	if (somdn_user_limits_excluded($user_id)) {
		return false;
	}

	$user_limits = somdn_get_user_limits( $user_id );

	$limits_type   = $user_limits['type'];
	$limits_amount = $user_limits['amount'];
	$limits_products = $user_limits['products'];
	$limits_freq   = $user_limits['freq'];
	$freq_name     = $user_limits['freq_name'];

	$gen_options = get_option( 'somdn_gen_settings' );
	$login_required = isset( $gen_options['somdn_require_login'] ) ? $gen_options['somdn_require_login'] : false ;

	$user_ip = somdn_current_user_ip();

	// Line up the default args for the post query
	$args = array(
		'fields' => 'ids',
		'post_type' => 'somdn_tracked',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => array(),
		'date_query' => array()
	);


	switch ( $limits_type ) {
		case 1: // Limit Downloads by User

			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => 'somdn_user_id',
					'value' => $user_id,
					'compare' => '=',
				)
			);

			break;

		case 2:
			// Limit Downloads by IP Address

			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => 'somdn_user_ip',
					'value' => $user_ip,
					'compare' => '=',
				)
			);

			break;

		default:
			// no recognised limit type
			break;
	}

	$current_time = current_time( 'mysql' );

	switch ( $limits_freq ) {
		case 1: // Current Day

/*
 * Current 3.1.8 solution
 */
			$timestamp = date( 'U', strtotime( $current_time ) );
			$today = getdate( $timestamp );

			$args['date_query'] = array(
				array(
					'year'  => $today['year'],
					'month' => $today['mon'],
					'day'   => $today['mday']
				),
			);

/*
 * Previous 3.0 solution
 *
			$current_time = current_time( 'mysql' );
			$today = getdate( $current_time );

			$args['date_query'] = array(
				array(
					'year'  => $today['year'],
					'month' => $today['mon'],
					'day'   => $today['mday']
				),
			);

/*
 * Previous 2.0 solution
 *

			$today = getdate();

			$args['date_query'] = array(
				array(
					'year'  => $today['year'],
					'month' => $today['mon'],
					'day'   => $today['mday']
				),
			);
*/
/*
 * Previous 1.0 solution
 *
			$args['date_query'] = array(
				array(
					'year'  => date( 'Y' ),
					'month' => date( 'n' ),
					'day'   => date( 'j' )
				)
			);
*/

			break;

		case 2: // Current Week

			$weekstarting = get_weekstartend( $current_time );
			$after = date( 'Y-m-d', $weekstarting['start'] );
			$args['date_query'] = array(
				array(
					'after' => $after,
					'inclusive' => true
				)
			);

		/*
			$args['date_query'] = array(
				array(
					'year' => date( 'Y' ),
					'week' => date( 'W' )
				)
			);
*/
			
			break;

		case 3: // Current Month

			$args['date_query'] = array(
				array(
					'year' => date( 'Y', strtotime($current_time) ),
					'month' => date( 'n', strtotime($current_time) )
				)
			);
			
			break;

		case 4: // Current Year

			$args['date_query'] = array(
				array(
					'year' => date( 'Y', strtotime($current_time) )
				)
			);
			
			break;
		
		default:
			// no recognised limit frequency
			break;
	}

	$download_history = new WP_Query( $args );
	$download_history_count = $download_history->found_posts;

	if ( $products == true && $download_history_count ) {

		$download_posts = $download_history->posts;
		$products_downloaded = array();

		foreach ( $download_posts as $download_post ) {

			$download_id = $download_post;// value is already a post id
			$downloaded_product = get_post_meta( $download_id, 'somdn_product_id', true );
			if ( ! empty( $downloaded_product ) ) {
				if ( ! in_array( $downloaded_product, $products_downloaded ) ) {
					array_push( $products_downloaded, $downloaded_product);
				}
			}

		}

		$download_history_count = count( $products_downloaded );

	}

	return $download_history_count;

}

function somdn_get_user_limits( $user_id = '' ) {

	$limits = [
		'type'      => '',
		'amount'    => '',
		'products'  => '',
		'freq'      => '',
		'freq_name' => '',
		'error'     => ''
	];

	// Bail if there are no download limitations set
	if ( ! somdn_download_limits_active() ) {
		return $limits;
	}

	if ( empty( $user_id ) ) {
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		}
	}

	if (somdn_user_limits_excluded($user_id)) {
		return $limits;
	}

	$limit_options = get_option( 'somdn_pro_basic_limit_settings' );

	$limits_type = isset( $limit_options['somdn_pro_basic_limit_type'] ) ? $limit_options['somdn_pro_basic_limit_type'] : 0 ;
	$limits_amount = isset( $limit_options['somdn_pro_basic_limit_amount'] ) ? $limit_options['somdn_pro_basic_limit_amount'] : '' ;
	$limits_products = isset( $limit_options['somdn_pro_basic_limit_products'] ) ? $limit_options['somdn_pro_basic_limit_products'] : '' ;
	$limits_freq = isset( $limit_options['somdn_pro_basic_limit_freq'] ) ? $limit_options['somdn_pro_basic_limit_freq'] : '' ;
	$limits_error = isset( $limit_options['somdn_pro_basic_limit_error'] ) ? $limit_options['somdn_pro_basic_limit_error'] : '' ;
	$freq_name = strtolower( somdn_get_download_frequency_name( $limits_freq ) );

	/**
	 * Check for memberships with custom limit settings
	 *
	 * @since 1.0.8
	 * @param array filled by return
	 * @param int $user_id
	 * @return array membership limit settings
	 */
	$custom_limit = somdn_get_user_custom_limits($user_id);

	if ( ! empty( $custom_limit ) ) {

		if ( ! empty( $custom_limit['limit_amount'] ) ) {
			$limits_amount = $custom_limit['limit_amount'];
		}
		if ( ! empty( $custom_limit['limit_products'] ) ) {
			$limits_products = $custom_limit['limit_products'];
		}
		if ( ! empty( $custom_limit['limit_freq'] ) ) {
			$limits_freq = $custom_limit['limit_freq'];
			$freq_name = strtolower( somdn_get_download_frequency_name( $limits_freq ) );
		}		
		if ( ! empty( $custom_limit['limit_error'] ) ) {
			$limits_error = $custom_limit['limit_error'];
		}
		
		if ( empty( $custom_limit['limit_amount'] ) && ! empty( $custom_limit['limit_products'] ) ) {
			// If set to unlimited downloads per number of products
			$limits_amount = '';
			$limits_products = $custom_limit['limit_products'];
		}

		if ( ! empty( $custom_limit['limit_amount'] ) && empty( $custom_limit['limit_products'] ) ) {
			// If set to limiteddownloads but unlimited products
			$limits_amount = $custom_limit['limit_amount'];
			$limits_products = '';
		}
		
	}

	if ( empty( $limits_error ) ) {

		$amount_limited = ! empty( $limits_amount ) ? true : false ;
		$products_limited = ! empty( $limits_products ) ? true : false ;
	
		if ( $amount_limited && $products_limited ) {

			$error_string = sprintf(
				__( 'You free download limit is %1$s downloads for %2$s products per %3$s.', 'somdn-pro' ),
				$limits_amount,
				$limits_products,
				$freq_name
			);

			// We have a total download limit with a set restriction on number of products
			//$limits_error = 'Your free download limit is ' . $limits_amount . ' downloads for ' . $limits_products . ' products per ' . $freq_name . '.';
			$limits_error = $error_string;

		} elseif ( $amount_limited && ! $products_limited ) {

			$error_string = sprintf(
				__( 'Your free download limit is %1$s downloads per %2$s.', 'somdn-pro' ),
				$limits_amount,
				$freq_name
			);

			// We have a total download limit but no restriction on number of products
			//$limits_error = 'Your free download limit is ' . $limits_amount . ' downloads per ' . $freq_name . '.';
			$limits_error = $error_string;

		} elseif ( ! $amount_limited && $products_limited ) {

			$error_string = sprintf(
				__( 'Your free download limit is %1$s products per %2$s.', 'somdn-pro' ),
				$limits_products,
				$freq_name
			);

			// We have a restriction on the number of products, but no limit on how many downloads for that product
			//$limits_error = 'Your free download limit is ' . $limits_products . ' products per ' . $freq_name . '.';
			$limits_error = $error_string;

		} else {

			$limits_error = $limits_error;

		}

	}

	$limits = array(
		'type'      => $limits_type,
		'amount'    => $limits_amount,
		'products'  => $limits_products,
		'freq'      => $limits_freq,
		'freq_name' => $freq_name,
		'error'     => $limits_error
	);
	
	return $limits;

}

function somdn_get_user_custom_limits($user_id): array
{
	$default = [];
	$user_id = intval($user_id);
	if (empty($user_id)) {
		return $default;
	}
	return apply_filters('somdn_user_custom_limits', $default, $user_id);
}

function somdn_user_limits_excluded($user_id): bool
{
	$default = false;
	$user_id = intval($user_id);
	if (empty($user_id)) {
		return $default;
	}
	return apply_filters('somdn_user_limits_excluded', $default, $user_id);
}

function somdn_get_download_frequency_name( $freq ) {
	switch ( $freq ) {
		case 1:
			return apply_filters( 'somdn_download_frequency_name', __( 'Day', 'somdn-pro' ), $freq );
			break;

		case 2:
			return apply_filters( 'somdn_download_frequency_name', __( 'Week', 'somdn-pro' ), $freq );
			break;

		case 3:
			return apply_filters( 'somdn_download_frequency_name', __( 'Month', 'somdn-pro' ), $freq );
			break;

		case 4:
			return apply_filters( 'somdn_download_frequency_name', __( 'Year', 'somdn-pro' ), $freq );
			break;

		default:
			break;
	}
}

add_action( 'somdn_count_download' , 'somdn_count_download_post', 20, 1 );
function somdn_count_download_post( $product_id, $variation_id = '' ) {

	$tracked = somdn_are_downloads_tracked();

	// Don't track downloads if not needed
	if ( $tracked == false ) {
		return;
	}

	/**
	 * Check if owned downloads are enabled and if true and product is owned, don't count download.
	 */
	$owned_settings = get_option( 'somdn_owned_settings' );
	$include_owned = ( isset( $owned_settings['somdn_owned_enable'] ) && $owned_settings['somdn_owned_enable'] ) ? true : false ;
	if ( $include_owned ) {
		$owned = somdn_is_download_owned( false, '', $product_id );
		if ( $owned ) {
			return;
		}
	}

	//somdn_debug_array($_POST);
	//exit;

	$variation_id = isset( $_POST['somdn_variation'] ) ? sanitize_text_field( $_POST['somdn_variation'] ) : '' ;
	if ( empty( $variation_id ) ) {
		$variation_id = '';
	}

	$action = esc_html( $_POST['action'] );

	$download_all = ( ( isset( $_POST['somdn_download_files_all'] ) && $_POST['somdn_download_files_all'] ) ) ? true : false ;
	$total_files = intval( isset( $_POST['somdn_totalfiles'] ) ? $_POST['somdn_totalfiles'] : 0 );
	$productfile = intval( isset( $_POST['somdn_productfile'] ) ? $_POST['somdn_productfile'] : 0 );
	$single = ( $action === 'somdn_download_single' || $action === 'somdn_download_single_variation' ) ? true : false ;

	$product = '';

	if ( $variation_id ) {
		$product = new WC_Product_Variation( $variation_id );
	} else {
		$product = somdn_get_product( $product_id );
	}

	if ( empty( $product ) )
		return;

	$downloads = somdn_get_files( $product );

	//somdn_debug_array($downloads);

	$download_files = array();

	if ( $single ) {
		//echo '<p>Single</p>';
		$count = 1;
		foreach ( $downloads as $key => $each_download ) {
			$download_name = $count . ': ' . $each_download['name'];
			array_push( $download_files, $download_name );
			$count++;
		}
	}

	// From a multi-file forms, when all files have been checked, or when "Download All" button has been clicked
	if ( $download_all || $action === 'somdn_download_all_files' || $action === 'somdn_download_all_files_variation' ) {
		$count = 1;
		foreach ( $downloads as $key => $each_download ) {
			$download_name = $count . ': ' . $each_download['name'];
			array_push( $download_files, $download_name );
			$count++;
		}
	}

	if ( ! $download_all && ! empty( $total_files ) ) {
		// Because the $total_files variable exists, we know that a multi-file checkbox or links form was used.
		// We want to loop through possible available files and build an array of files that the downloader can use
		$count = 1;	
		foreach ( $downloads as $key => $each_download ) {
			$checkbox_id = 'somdn_download_file_' . strval( $count );
			if ( isset( $_POST[$checkbox_id] ) && $_POST[$checkbox_id] ) {
				$download_name = $count . ': ' . $each_download['name'];
				array_push( $download_files, $download_name );
			}
			$count++;
		}
	}

	// For multi-file links, where a single product file is clicked
	if ( ! empty( $productfile ) ) {
		// Loop through the product download files and see which one was selected
		$count = 1;
		foreach ( $downloads as $key => $each_download )  {
			if ( $count == $productfile ) {
				$download_name = $count . ': ' . $each_download['name'];
				array_push( $download_files, $download_name );
				break;
			}
			$count++;
		}
	}

	//somdn_debug_array($download_files);
	//somdn_debug_array($_POST);
	//exit;

	$details = somdn_get_customer_tracking_from_post_data();

	$user_fname = isset( $details['somdn_user_fname'] ) ? sanitize_text_field( $details['somdn_user_fname'] ) : '' ;
	$user_lname = isset( $details['somdn_user_lname'] ) ? sanitize_text_field( $details['somdn_user_lname'] ) : '' ;
	$user_tel = isset( $details['somdn_user_tel'] ) ? sanitize_text_field( $details['somdn_user_tel'] ) : '' ;
	$user_company = isset( $details['somdn_user_company'] ) ? sanitize_text_field( $details['somdn_user_company'] ) : '' ;
	$user_website = isset( $details['somdn_user_website'] ) ? sanitize_text_field( $details['somdn_user_website'] ) : '' ;
	$user_email = isset( $details['somdn_user_email'] ) ? sanitize_email( $details['somdn_user_email'] ) : '' ;

	$user_subscribe = isset( $details['somdn_capture_email_subscribe'] ) ? esc_attr( $details['somdn_capture_email_subscribe'] ) : '' ;

	$current_user_id = get_current_user_id();

	// Downloads are tracked, let's create a tracked download post
	$post_information = array(
		'post_type' => 'somdn_tracked',
		'post_title' => 'Free Download: ' . get_the_title( $product_id ),
		'post_content' => 'Download',
		'post_status' => 'publish',
		'meta_input' => array(
			'somdn_user_id' => $current_user_id,
			'somdn_user_email' => $user_email,
			'somdn_user_subbed' => $user_subscribe,
			'somdn_user_fname' => $user_fname,
			'somdn_user_lname' => $user_lname,
			'somdn_user_tel' => $user_tel,
			'somdn_user_company' => $user_company,
			'somdn_user_website' => $user_website,
			'somdn_user_ip' => somdn_current_user_ip(),
			'somdn_product_id' => $product_id,
			'somdn_variation_id' => $variation_id,
			'somdn_download_files' => $download_files
		)
	);

	$post_id = wp_insert_post( $post_information );

	if ( ! $post_id ) {
		// Post wasn't recored. Do something here at some point.
	} else {
		do_action( 'somdn_count_download_post_success', $post_id, $post_information );
	}

}

function somdn_get_customer_tracking_from_post_data( $data = array() ) {

	if ( empty( $data ) ) {
		$data = $_POST;
	}

	$details = array();

	if ( empty( $data ) )
		return $details;

	//echo '<pre>';
	//print_r($data);
	//echo '</pre>';
	//exit;

	$user_fname = isset( $data['somdn_download_user_name'] ) ? sanitize_text_field( $data['somdn_download_user_name'] ) : '' ;
	$user_lname = isset( $data['somdn_download_user_lname'] ) ? sanitize_text_field( $data['somdn_download_user_lname'] ) : '' ;
	$user_tel = isset( $data['somdn_download_user_tel'] ) ? sanitize_text_field( $data['somdn_download_user_tel'] ) : '' ;
	$user_company = isset( $data['somdn_download_user_company'] ) ? sanitize_text_field( $data['somdn_download_user_company'] ) : '' ;
	$user_website = isset( $data['somdn_download_user_website'] ) ? sanitize_text_field( $data['somdn_download_user_website'] ) : '' ;
	$user_email = isset( $data['somdn_download_user_email'] ) ? sanitize_email( $data['somdn_download_user_email'] ) : '' ;

	$user_subscribe = isset( $data['somdn_capture_email_subscribe'] ) ? esc_attr( $data['somdn_capture_email_subscribe'] ) : '' ;

	$current_user_id = get_current_user_id();
	$user = get_user_by( 'ID', $current_user_id );

	if ( empty( $user_fname ) ) {
		if ( $current_user_id ) {
			$user_fname = $user->first_name;
		} else {
			$user_fname = '';
		}
	}

	if ( empty( $user_lname ) ) {
		if ( $current_user_id ) {
			$user_lname = $user->last_name;
		} else {
			$user_lname = '';
		}
	}

	if ( empty( $user_website ) ) {
		if ( $current_user_id ) {
			$user_website = $user->user_url;
		} else {
			$user_website = '';
		}
	}

	if ( empty( $user_email ) ) {
		if ( $current_user_id ) {
			$user_email = $user->user_email;
		} else {
			$user_email = '';
		}
	}

	$details = array(
		'somdn_user_id' => $current_user_id,
		'somdn_user_email' => $user_email,
		'somdn_capture_email_subscribe' => $user_subscribe,
		'somdn_user_fname' => $user_fname,
		'somdn_user_lname' => $user_lname,
		'somdn_user_tel' => $user_tel,
		'somdn_user_company' => $user_company,
		'somdn_user_website' => $user_website,
		'somdn_user_ip' => somdn_current_user_ip()
	);

	return $details;

}

function somdn_email_site_admin_subbed( $post_id ) {

	$options = get_option( 'somdn_pro_newsletter_general_settings' );
	$confirm = isset( $options['somdn_newsletter_confirmations'] ) ? $options['somdn_newsletter_confirmations'] : '' ;

	// Bail if no confirmation email needed
	if ( empty( $confirm ) ) return;

	$user_subbed = get_post_meta( $post_id, 'somdn_user_subbed', true );

	if ( ! empty( $user_subbed ) ) {

		$admin_email = get_option( 'admin_email' );
		$site_name = get_option( 'blogname' );
		$post_link = get_edit_post_link( $post_id );
		$user_email = get_post_meta( $post_id, 'somdn_user_email', true );
		$subject = __( 'Free Download Newsletter Subscription', 'somdn-pro' );

		$headers = array(
			'Content-Type: text/html; charset=UTF-8'
		);

		add_filter( 'wp_mail_content_type', 'somdn_html_emails' );

		ob_start(); ?>

		<p><?php _e( 'Hello there', 'somdn-pro' ); ?></p>
		<p><?php _e( 'A user has subscribed to your newsletter when downloading your free product', 'somdn-pro' ); ?>.</p>
		<p><?php printf( __( 'Email address: <strong>%s</strong>', 'somdn-pro' ), $user_email ); ?></p>
		<?php
		/*<p><a href="<?php echo $post_link; ?>"><?php _e( 'View More Details', 'somdn-pro' ); ?></a></p>
		*/
		?>

		<?php $message = ob_get_clean();

		$email_success = wp_mail( $admin_email, $subject, $message, $headers );

		if ( $email_success == false ) {
			somdn_write_log( "wp_mail() function failed while sending download newsletter sign up notification to site admin. If using a LOCALHOST ensure you've set up SMTP locally." );
		}

		remove_filter( 'wp_mail_content_type', 'somdn_html_emails' );

	}

}

/**
 * $message will be run through wptexturize() then wpautop(), but not wp_kses_post, this happens elsewhere.
 *
 */
function somdn_send_email( $type = 'free_download_url', $to = '', $subject = '', $message = '', $heading = '' ) {

	$email_sent = false;
	$base = constant( 'SOMDN_BASE' );

	if ( empty( $to ) || empty( $subject ) || empty( $message ) ) {
		somdn_write_log( 'Sending email failed. Empty values for To, Subject, or Message.' );
		return $email_sent;
	}

	if ( empty( $heading ) ) {
		$heading = somdn_get_email_heading();
	}

	$content_type = somdn_get_email_content_type();

	if ( $content_type == 'text/plain' ) {

		$heading = apply_filters( 'somdn_mail_content_heading_plaint', wptexturize( $heading .= "\n\n" ) );
		$message = apply_filters( 'somdn_mail_content', $message );
		$message = $heading . $message;
		//echo $message;
		//exit;

	} else {

		if ( $base == 'woocommerce' ) {

			if ( class_exists( 'WC_Emails' ) && class_exists( 'WC_Email' ) ) {
				/**
				 * Set up the WooCommerce mailer functions for styling purposes
				 */
				$mailer = WC()->mailer();
				$email = new WC_Email();
				$message = apply_filters( 'woocommerce_mail_content', $email->style_inline( $mailer->wrap_message( $heading, $message ) ) );
			} else {
				// No Woo mail functions can be used, so just format using standard functions
				$heading = apply_filters( 'somdn_mail_content_heading', wpautop( wptexturize( '<h3>' . $heading . '</h3>' ) ) );
				$message = apply_filters( 'somdn_mail_content', wpautop( wptexturize( $message ) ) );
				$message = $heading . $message;
			}

		} else {

			$heading = apply_filters( 'somdn_mail_content_heading', wpautop( wptexturize( '<h3>' . $heading . '</h3>' ) ) );
			$message = apply_filters( 'somdn_mail_content', wpautop( wptexturize( $message ) ) );
			$message = $heading . $message;

		}

	}

	add_filter( 'wp_mail_from', 'somdn_get_email_sender_address' );
	add_filter( 'wp_mail_from_name', 'somdn_get_email_sender_name' );
	add_filter( 'wp_mail_content_type', 'somdn_get_email_content_type' );

	$email_sent = wp_mail( $to, $subject, $message );

	remove_filter( 'wp_mail_from', 'somdn_get_email_sender_address' );
	remove_filter( 'wp_mail_from_name', 'somdn_get_email_sender_name' );
	remove_filter( 'wp_mail_content_type', 'somdn_get_email_content_type' );

	return $email_sent;

}
function somdn_get_email_sender_address( $email = '' ) {
	$options = get_option( 'somdn_email_settings' );
	$email_sender_address = isset( $options['somdn_email_settings_sender_address'] ) ? $options['somdn_email_settings_sender_address'] : '' ;
	if ( empty( $email_sender_address ) ) {
		$email_defaults = somdn_get_site_email_defaults();
		$email = sanitize_email( $email_defaults['headers']['sender_address'] );
	} else {
		$email = sanitize_email( $email_sender_address );
	}
	return $email;
}
function somdn_get_email_sender_name( $name ) {
	$options = get_option( 'somdn_email_settings' );
	$email_sender_name = isset( $options['somdn_email_settings_sender_name'] ) ? $options['somdn_email_settings_sender_name'] : '' ;
	if ( empty( $email_sender_name ) ) {
		$email_defaults = somdn_get_site_email_defaults();
		$name = wp_specialchars_decode( $email_defaults['headers']['sender_name'], ENT_QUOTES );
	} else {
		$name = wp_specialchars_decode( esc_html( $email_sender_name ), ENT_QUOTES );
	}
	return $name;
}
function somdn_get_email_content_type( $type = '' ) {
	$options = get_option( 'somdn_email_settings' );
	$content_type = isset( $options['somdn_email_settings_content_type'] ) ? intval( $options['somdn_email_settings_content_type'] ) : 0 ;
	if ( empty( $content_type ) ) {
		$type = 'text/html';
	} elseif ( $content_type == 1 ) {
		$type = 'text/plain';
	} else {
		$type = 'text/html';
	}
	return $type;
}
function somdn_get_email_heading( $type = 'free_download_url' ) {
	$heading = '';
	$email_defaults = somdn_get_site_email_defaults( $type );
	if ( $type == 'free_download_url' ) {
		$options = get_option( 'somdn_email_settings' );
		$email_heading = isset( $options['somdn_email_download_url_heading'] ) ? $options['somdn_email_download_url_heading'] : '' ;
		if ( empty( $email_heading ) ) {
			$heading = $email_defaults['content']['heading'];
		} else {
			$heading = wp_specialchars_decode( esc_html( $email_heading ), ENT_QUOTES );
		}	
	}
	return $heading;
}
function somdn_get_site_email_defaults( $type = 'free_download_url' ) {

	$types = array();

	$defaults = array();
	$content = array();

	$headers = somdn_get_site_email_headers();

	if ( $type == 'free_download_url' ) {
		// The emails sent to customers with their download links

		ob_start();
		echo __( 'Dear {first_name},', 'somdn-pro' );
		echo "\n\n";
		echo __( 'Please click the below link for your {product} free download.', 'somdn-pro' );
		echo "\n\n";
		echo '{link}';
		echo "\n\n";
		echo __( 'This download link will expire in {hours} hours.', 'somdn-pro' );
		$free_download_message = ob_get_clean();

		$content = array(
			'subject' => __( 'Your {site_name} free download for {product}', 'somdn-pro' ),
			'heading' => __( 'Free Download', 'somdn-pro' ),
			'message' => $free_download_message
		);

	} elseif ( $type == 'new_free_download' ) {
		// The emails sent when a new free download has happened

		ob_start();
		echo __( 'A new free download has been made on your site.', 'somdn-pro' );
		echo "\n\n";
		echo __( 'Product: {product}', 'somdn-pro' );
		echo "\n";
		echo __( 'Date & Time: {date}', 'somdn-pro' );
		echo "\n";
		echo __( 'Username: {username}', 'somdn-pro' );
		echo "\n";
		echo __( 'Download ID: {id}', 'somdn-pro' );
		echo "\n\n";
		echo __( 'View download log: {link}', 'somdn-pro' );
		$free_download_message = ob_get_clean();

		$content = array(
			'subject' => __( '{site_name} New Free Download: {product}', 'somdn-pro' ),
			'heading' => __( 'New Free Download', 'somdn-pro' ),
			'message' => $free_download_message
		);

	}

	$defaults['headers'] = $headers;
	$defaults['content'] = $content;

	return $defaults;

}

add_action( 'somdn_count_download_post_success', 'somdn_count_download_notify_emails', 10, 2 );
function somdn_count_download_notify_emails( $post_id, $post_info ) {

	$email_options = get_option( 'somdn_email_settings' );
	$track_options = get_option( 'somdn_pro_track_settings' );
	$email_enable  = isset( $email_options['somdn_email_new_download_enable'] ) ? $email_options['somdn_email_new_download_enable'] : false ;
	$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : false ;

	// Only send these emails if the setting and download tracking are both enabled
	if ( ! $email_enable || ! $track_enabled )
		return;

	$email_defaults = somdn_get_site_email_defaults( 'new_free_download' );

	$post_meta = isset( $post_info['meta_input'] ) ? $post_info['meta_input'] : '';
	if ( empty( $post_meta ) )
		return;

	$product_id = intval( isset( $post_meta['somdn_product_id'] ) ? $post_meta['somdn_product_id'] : 0 );
	if ( empty( $product_id ) )
		return;

	$product_name = html_entity_decode( wp_specialchars_decode( esc_html( get_the_title( $product_id ) ) ) );

	$default_email = isset( $email_defaults['headers']['sender_address'] ) ? sanitize_email( $email_defaults['headers']['sender_address'] ) : '';
	$email_addresses = isset( $email_options['somdn_email_new_download_sendto'] ) ? $email_options['somdn_email_new_download_sendto'] : '' ;
	if ( empty( $email_addresses ) ) {
		// No email in settings, set to default
		$email_addresses = $default_email;
	} else {
		// The setting isn't blank, let's check for comma separated string and ensure they're email address
		$emails = explode( ', ', $email_addresses );
		$filtered_emails = array();
		foreach ( $emails as $email ) {
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ){
				//Maybe do some error catching here
			} else {
				$filtered_emails[] = $email;
			}
		}
		if ( empty( $filtered_emails ) ) {
			// After filtering the email addresses in the settings, none of them were valid
			somdn_write_log( 'Could not send free download notification to custom email address, invalid string in setting: ' . $email_addresses, 1 );
			$email_addresses = $default_email;
		} else {
			// Valid email addresses are found. Variable is now an array.
			$email_addresses = $filtered_emails;
		}
	}

	if ( empty( $email_addresses ) ) {
		somdn_write_log( 'Could not send free download notification as no valid email address was found.', 1 );
		return;
	}

	// Filter added in 3.1.91
	$email_addresses = apply_filters( 'somdn_count_download_notify_email_addresses', $email_addresses, $post_id );

/*
	if ( is_array( $email_addresses ) ) {
		echo '$email_addresses = array:';
		somdn_debug_array( $email_addresses );
	} else {
		echo '$email_addresses = string: ' . $email_addresses . '<br>';
	}

	if ( is_array( $email_addresses ) ) {
		// Clean the array of email addresses of any duplicates
		$email_addresses = somdn_clean_array( $email_addresses );
	}	else {
		$email_addresses = trim( $email_addresses );
	}

	if ( is_array( $email_addresses ) ) {
		echo '$email_addresses = array:';
		somdn_debug_array( $email_addresses );
	} else {
		echo '$email_addresses = string: ' . $email_addresses . '<br>';
	}

	exit;

*/

	$email_message = isset( $email_options['somdn_email_new_download_message'] ) ? $email_options['somdn_email_new_download_message'] : '' ;
	if ( empty( $email_message ) ) {
		$email_message = $email_defaults['content']['message'];
	}

	$email_subject = isset( $email_options['somdn_email_new_download_subject'] ) ? $email_options['somdn_email_new_download_subject'] : '' ;
	if ( empty( $email_subject ) ) {
		$email_subject = $email_defaults['content']['subject'];
	}

	$email_heading = isset( $email_options['somdn_email_new_download_heading'] ) ? $email_options['somdn_email_new_download_heading'] : '' ;
	if ( empty( $email_heading ) ) {
		$email_heading = $email_defaults['content']['heading'];
	}

	$email_url_esc = esc_url( get_edit_post_link( $post_id ) );
	$email_url = esc_url_raw( get_edit_post_link( $post_id ) );
	$email_message_link_text = $product_name;
	$email_message_link = '<a href="' . $email_url_esc . '">' . $email_message_link_text . '</a>';

	//echo '$post_id = ' . $post_id;
	//exit;

	//echo '$email_url_esc = ' . $email_url_esc;
	//exit;

	$user_id = intval( isset( $post_meta['somdn_user_id'] ) ? $post_meta['somdn_user_id'] : 0 );
	$username = '';
	$user_email = isset( $post_meta['somdn_user_email'] ) ? sanitize_email( $post_meta['somdn_user_email'] ) : '';
	$default_user_name = __( 'Customer', 'somdn-pro' );

	$site_user_data = get_user_by( 'ID', $user_id );
	//print_r($site_user_data);
	//exit;
	if ( empty( $site_user_data ) ) {
		$username = $default_user_name;
	} else {
		$username = $site_user_data->user_login;
	}

	// Gets the date and time of the download, starting with the day of the week, in the format of the website
	$post_time = (string) get_the_date( 'l', $post_id ) . ' ' . get_the_date( '', $post_id ) . ' - ' . get_the_time( '', $post_id );
	//$post_time = get_the_time( 'l, F jS, Y \a\t g:ia', $post_id );

	$sitename = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

	$content_type = somdn_get_email_content_type();

	$replacements = array(
		'subject' => array(
			'{product}' => $product_name,
			'{site_name}' => $sitename
		),
		'message' => array(
			'{id}' => $post_id,
			'{link}' => $post_id,
			'{product}' => $product_name,
			'{username}' => $username,
			'{email}' => $user_email,
			'{date}' => $post_time,
			'{site_name}' => $sitename
		)
	);

	// Let's strip/replace placeholders and then clean the subject
	$email_subject = str_replace( "{product}", $product_name, $email_subject );
	$email_subject = str_replace( "{site_name}", $sitename, $email_subject );
	$email_subject = wp_specialchars_decode( $email_subject );

	// Let's strip/replace placeholders and then clean the message
	$email_message = str_replace( "{id}", $post_id, $email_message );
	$email_message = str_replace( "{product}", $product_name, $email_message );
	$email_message = str_replace( "{username}", $username, $email_message );
	$email_message = str_replace( "{email}", $user_email, $email_message );
	$email_message = str_replace( "{date}", $post_time, $email_message );
	$email_message = str_replace( "{site_name}", $sitename, $email_message );

	if ( $content_type == 'text/plain' ) {
		$email_message = str_replace( "{link}", $email_url, $email_message );
		$email_message = wp_specialchars_decode( esc_html( $email_message ) );
	} else {
		$email_message = str_replace( "{link}", $email_message_link, $email_message );
		$email_message = wp_kses_post( $email_message );
	}

	// Now let's attempt to actually send the email
	$email_sent = somdn_send_email( $type = 'new_free_download', $email_addresses, $email_subject, $email_message, $email_heading );

	//echo '$product_name = ' . $product_name;
	//exit;

	if ( $email_sent == false ) {
		// The email didn't send for some reason. Default back to instant downloads.
		somdn_write_log( 'Free download email notification failed to send.' );
	}

}

function somdn_get_site_email_headers() {
	$headers = array(
		'sender_address' => apply_filters( 'somdn_default_email_sender_address', get_option( 'admin_email' ) ),
		'sender_name' => apply_filters( 'somdn_default_email_sender_name', get_option( 'blogname' ) )
	);
	return apply_filters( 'somdn_get_site_email_headers', $headers );
}

add_filter( 'somdn_default_email_sender_address', 'somdn_default_email_sender_address_woo', 20, 1 );
function somdn_default_email_sender_address_woo( $email_address ) {
	if ( class_exists( 'WooCommerce' ) ) {
		// WooCommerce loaded, is the mailer class loaded as well?
		if ( class_exists( 'WC_Emails' ) ) {
			// Mail class loaded
			$woocommerce_from_address = WC()->mailer()->get_from_address();
		}
	}
	if ( ! empty( $woocommerce_from_address ) ) {
		$email_address = $woocommerce_from_address;
	}
	return $email_address;
}

add_filter( 'somdn_default_email_sender_name', 'somdn_default_email_sender_name_woo', 20, 1 );
function somdn_default_email_sender_name_woo( $email_name ) {
	if ( class_exists( 'WooCommerce' ) ) {
		// WooCommerce loaded, is the mailer class loaded as well?
		if ( class_exists( 'WC_Emails' ) ) {
			// Mail class loaded
			$woocommerce_from_name = WC()->mailer()->get_from_name();
		}
	}
	if ( ! empty( $woocommerce_from_name ) ) {
		$email_name = $woocommerce_from_name;
	}
	return $email_name;
}

function somdn_html_emails() {
	return 'text/html';
}

//add_filter( 'somdn_restrict', 'somdn_is_restricted', 10, 2 );
function somdn_is_restricted( $restrict, $productID ) {
	return false;
}

function somdn_are_downloads_tracked() {
	$tracked = false;
	return apply_filters( 'somdn_are_downloads_tracked', $tracked );
}

add_filter( 'somdn_are_downloads_tracked', 'somdn_has_download_limits', 10, 1 );
function somdn_has_download_limits( $tracked ) {
	if ( somdn_download_limits_active() ) {
		$tracked = true;
	}
	return $tracked;
}

add_filter( 'somdn_are_downloads_tracked', 'somdn_downloads_tracked_enabled', 99, 1 );
function somdn_downloads_tracked_enabled( $tracked ) {
	$track_options = get_option( 'somdn_pro_track_settings' );
	$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : 0 ;
	if ( ! empty( $track_enabled ) ) {
		$tracked = true;
	}
	return $tracked;
}

function somdn_download_limits_active() {

	$active = false;

	$options = get_option( 'somdn_pro_basic_limit_settings' );

	$limits_enabled = isset( $options['somdn_pro_basic_limit_enable'] ) ? $options['somdn_pro_basic_limit_enable'] : 0 ;
	$limits_type = isset( $options['somdn_pro_basic_limit_type'] ) ? $options['somdn_pro_basic_limit_type'] : 0 ;
	$amount_value = isset( $options['somdn_pro_basic_limit_amount'] ) ? $options['somdn_pro_basic_limit_amount'] : '' ;
	$products_value = isset( $options['somdn_pro_basic_limit_products'] ) ? $options['somdn_pro_basic_limit_products'] : '' ;
	$freq_value = isset( $options['somdn_pro_basic_limit_freq'] ) ? $options['somdn_pro_basic_limit_freq'] : '' ;

	$amount_limited = ! empty( $amount_value ) ? true : false ;
	$products_limited = ! empty( $products_value ) ? true : false ;

	if ( ! empty( $limits_enabled ) ) {

		$limits_array = array(
			$limits_type,
			$freq_value
		);

		foreach ( $limits_array as $limit_value ) {
			if ( empty( $limit_value ) ) {
				return $active = false;
			}
		}

		
		if ( ! $amount_limited && ! $products_limited ) {
			return $active = false;
		}

	} else {

		return $active = false;

	}

	return $active = true;

}

function somdn_valid_download_check_limits_setup() {
	$limit_options = get_option( 'somdn_pro_basic_limit_settings' );
	$limits_enabled = isset( $limit_options['somdn_pro_basic_limit_enable'] ) ? $limit_options['somdn_pro_basic_limit_enable'] : 0 ;
	if ( $limits_enabled && ! somdn_download_limits_active() ) {
		// Let's write to our log file for debugging purposes
		somdn_write_log( 'A free download was made but download limit settings are not complete, so no limits applied.', 1 );
	}
}

function somdn_is_email_capture_enabled( $product_id = '' ) {

	$track_options = get_option( 'somdn_pro_track_settings' );
	$capture_emails = isset( $track_options['somdn_capture_email_enable'] ) ? $track_options['somdn_capture_email_enable'] : false ;
	$capture_emails_users = isset( $track_options['somdn_capture_email_users_enable'] ) ? $track_options['somdn_capture_email_users_enable'] : false ;
	$capture_emails_active = false;
	$email_capture_excluded = false;

	if ( $capture_emails ) {

		if ( $capture_emails_users ) {
			$capture_emails_active = true;
		} elseif ( ! is_user_logged_in() ) {
			$capture_emails_active = true;
		}

		if ( ! empty( $product_id ) ) {
			// Check if product is ticked to be excluded from email capture
			$email_capture_excluded = get_post_meta( $product_id, 'somdn_exclude_email_capture', true );
			//echo '<p>$included = ' . $included . '</p>';
			if ( isset( $email_capture_excluded ) && ! empty( $email_capture_excluded ) ) {
				$email_capture_excluded = true;
				$capture_emails_active = false;
			}
		}

	}

	return apply_filters( 'capture_emails_active', $capture_emails_active, $track_options, $product_id, $email_capture_excluded );

}

function somdn_current_user_ip() {
	// Check for user remote address
	$remote_address = ! empty( $_SERVER['REMOTE_ADDR'] )
		? $_SERVER['REMOTE_ADDR']
		: '127.0.0.1';
	// Remove any junk
	$retval = preg_replace( '/[^0-9a-fA-F:., ]/', '', $remote_address );
	// Filter then return
	return apply_filters( 'somdn_current_user_ip', $retval, $remote_address );
}

add_action( 'somdn_before_variation_wrap', 'somdn_output_download_count_output_above', 10, 1 );
add_action( 'somdn_after_variation_wrap', 'somdn_output_download_count_output_below', 10, 1 );

add_action( 'somdn_before_form_inputs_simple', 'somdn_capture_email_form', 10, 1 );
add_action( 'somdn_before_form_inputs_variation', 'somdn_capture_email_form', 10, 2 );
function somdn_capture_email_form( $product_id, $variation_id = '' ) {

	if ( somdn_is_email_capture_enabled( $product_id ) ) {

		/**
		 * Output the capture email form
		 */
		do_action( 'somdn_before_capture_email' );

		$template = somdn_get_template( 'capture-email' );
		if ( ! empty( $template ) ) {
			include( $template );
		}

		do_action( 'somdn_after_capture_email' );

	}

}

function somdn_get_templates_pro( $templates ) {
	$theme_path = get_template_directory();
	$pro_templates = array(
		'variation-single-file' => array(
			'path' => SOMDN_PATH_PRO . 'templates/download-forms/variation-single-file.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/variation-single-file.php'
			),
		'variation-multi-file-links' => array(
			'path' => SOMDN_PATH_PRO . 'templates/download-forms/variation-multi-file-links.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/variation-multi-file-links.php'
			),
		'variation-multi-file-button' => array(
			'path' => SOMDN_PATH_PRO . 'templates/download-forms/variation-multi-file-button.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/variation-multi-file-button.php'
			),
		'variation-multi-file-button-checkboxes' => array(
			'path' => SOMDN_PATH_PRO . 'templates/download-forms/variation-multi-file-button-checkboxes.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/variation-multi-file-button-checkboxes.php'
			),
		'variation-multi-file-button-links' => array(
			'path' => SOMDN_PATH_PRO . 'templates/download-forms/variation-multi-file-button-links.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/variation-multi-file-button-links.php'
			),
		'variation-multi-file-button-filenames' => array(
			'path' => SOMDN_PATH_PRO . 'templates/download-forms/variation-multi-file-button-filenames.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/variation-multi-file-button-filenames.php'
			),
		'capture-email' => array(
			'path' => SOMDN_PATH_PRO . 'templates/download-forms/capture-email-form.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/capture-email-form.php'
			),
		'free-downloads' => array(
			'path' => SOMDN_PATH_PRO . 'templates/account/free-downloads.php',
			'custom_path' => $theme_path . '/somdn-templates/account/free-downloads.php'
			),
		'free-download-limits' => array(
			'path' => SOMDN_PATH_PRO . 'templates/account/free-download-limits.php',
			'custom_path' => $theme_path . '/somdn-templates/account/free-download-limits.php'
			)
	);
	$templates = $templates + $pro_templates;
	return apply_filters( 'somdn_get_templates_pro', $templates, $pro_templates );
}

remove_action( 'somdn_get_forum_link', 'somdn_get_forum_link_basic' );
add_action( 'somdn_get_forum_link', 'somdn_get_forum_link_pro' );
function somdn_get_forum_link_pro() {
	$text = __( 'If you need further support please visit the premium support forum for this plugin over at', 'somdn-pro' );
	$url = ' <a href="https://squareonemedia.co.uk/community/forums/" target="_blank">Square One Media</a>';
	echo $text . $url . '.';
}

function somdn_get_account_downloads_columns() {
	$columns = apply_filters( 'somdn_account_downloads_columns', array(
		'download-product'   => __( 'Product', 'somdn-pro' ),
		'download-file'      => __( 'Download', 'somdn-pro' )
	) );
	return $columns;
}

add_action( 'woocommerce_after_account_downloads', 'somdn_downloads_table', 60 );
function somdn_downloads_table() {

	$options = get_option( 'somdn_pro_track_settings' );
	$value = isset( $options['somdn_download_history_enable'] ) ? $options['somdn_download_history_enable'] : 0 ;
	if ( ! empty( $value ) ) {
		do_action( 'somdn_before_account_downloads' );
		$template = somdn_get_template( 'free-downloads' );
		if ( ! empty( $template ) ) {
			include( $template );
		}
		do_action( 'somdn_after_account_downloads' );
	}
}

add_action( 'woocommerce_after_account_downloads', 'somdn_download_limits_table', 75 );
function somdn_download_limits_table() {
	$options = get_option( 'somdn_pro_basic_limit_settings' );
	$value = isset( $options['somdn_pro_limit_acc_page'] ) ? $options['somdn_pro_limit_acc_page'] : 0 ;
	if ( ! empty( $value ) && somdn_download_limits_active() ) {
		do_action( 'somdn_before_account_limits_table' );
		$template = somdn_get_template( 'free-download-limits' );
		if ( ! empty( $template ) ) {
			include( $template );
		}
		do_action( 'somdn_after_account_limits_table' );
	}
}

function somdn_do_default_download_variation() {
	somdn_downloader_variations_init();
}

function somdn_clean_array( $array ) {
	$unique = array_unique( $array );
	$clean_array = array();
	foreach ( $unique as $unique_value ) {
		if ( ! empty( $unique_value ) ) {
			$clean_array[] = $unique_value;
		}
	}
	return $clean_array;
}

add_action( 'somdn_variation_errors', 'somdn_variation_errors_output', 50 );
function somdn_variation_errors_output() {

	if ( empty( $_REQUEST ) ) {
		return;
	}

	ob_start();

	$somdn_errors = isset( $_REQUEST['somdn_errors'] ) ? $_REQUEST['somdn_errors'] : '' ;

	$somdn_errors_used = array();

	if ( ! empty( $somdn_errors ) && is_array( $somdn_errors ) ) :

		$allowed_tags = somdn_get_allowed_html_tags();

		foreach ( $somdn_errors as $somdn_error ) :
			if ( ! empty( $somdn_error ) && is_array( $somdn_error ) ) :
				foreach ( $somdn_error as $error ) :

					$cleaned_error = wpautop( wp_kses( $error, $allowed_tags ) );

					if ( ! in_array( $cleaned_error, $somdn_errors_used ) ) :

						array_push( $somdn_errors_used, $cleaned_error ); ?>

						<div class="<?php echo somdn_frontend_warning_class(); ?>">
							<?php echo $cleaned_error; ?>
						</div>

					<?php endif;

				endforeach;
			endif;
		endforeach;

	endif;

	$error_content = ob_get_clean();
	echo $error_content;

}

//add_filter( 'wp_handle_upload', 'somdn_pro_wp_handle_upload', 10, 2 );
function somdn_pro_wp_handle_upload( $data, $action ) {

/*
array(
	'file' => $new_file,
	'url'  => $url,
	'type' => $type,
),
'wp_handle_sideload' === $action ? 'sideload' : 'upload'
*/

//var_dump($data);
$path_parts = pathinfo($data['file'], PATHINFO_EXTENSION);
$extension = strtolower( $path_parts );
$pdf = ( $extension == 'pdf' ) ? true : false ;
//echo '$pdf = ' . $pdf . ' ';

}

//add_shortcode( 'download_limit_error', 'somdn_download_limit_error_message' );
function somdn_download_limit_error_message() {
	return somdn_single_errors_output();
}

add_shortcode( 'download_limits', 'somdn_download_limits_shortcode' );
function somdn_download_limits_shortcode() {

	if ( ! is_user_logged_in() )
		return;

	ob_start();

	if ( somdn_download_limits_active() ) {
		$template = somdn_get_template( 'free-download-limits' );
		if ( ! empty( $template ) ) {
			include( $template );
		}
		do_action( 'somdn_after_account_limits_table' );
	}

	return ob_get_clean();

}

function somdn_single_shortcode_pro( $atts ) {

	// Attributes
	$atts = shortcode_atts(
		array(
			'id' => '',
			'align' => 'left',
			'text' => '',
			'variation' => ''
		),
		$atts,
		'download_now'
	);
	
	$product_id = $atts['id'];
	$variation_id = $atts['variation'];
	$align = $atts['align'];
	$shortcode_text = $atts['text'];

	// Bail if no product ID
	if ( empty( $product_id ) )
		return;

	$product = somdn_get_product( $product_id );

	// Bail if no product matches the productID
	if ( ! $product )
		return;

	$download_button = somdn_get_shortcode_product_content_pro( $product_id, $variation_id, $shortcode_text );

	// Bail if no download button returned
	if ( ! $download_button ) {
		return;
	}

	$content = '<div class="somdn-shortcode-wrap ' . esc_attr( $align ) . '">' . $download_button . '</div>';
	return $content;

}

function somdn_get_shortcode_product_content_pro( $product_id, $variation_id, $shortcode_text ) {

	$archive = true;
	$archive_enabled = true;

	$product = somdn_get_product( $product_id );
	if ( ! $product ) {
		return;
	}

	$product_id = intval( $product_id );
	$variation_id = intval( $variation_id );

	$download_type = '';

	if ( ! empty( $variation_id ) ) {
		if ( ! somdn_is_variable_product_valid( $product_id, $variation_id ) ) {
			return;
		} else {
			$download_type = 'variation';
		}
	} else {
		if ( ! somdn_is_product_valid( $product_id )  ) {
			return;
		} else {
			$download_type = 'normal';
		}
	}

	if ( $download_type === 'variation' ) {
		$variation_product = wc_get_product( $variation_id );
		$downloads = somdn_get_files( $variation_product );
		$downloads_count = count( $downloads );
	} else {
		$downloads = somdn_get_files( $product );
		$downloads_count = count( $downloads );
	}

	$is_single_download = ( 1 == $downloads_count ) ? true : false ;

	$genoptions = get_option( 'somdn_gen_settings' );
	$singleoptions = get_option( 'somdn_single_settings' );
	$multioptions = get_option( 'somdn_multi_settings' );
	$docoptions = get_option( 'somdn_docviewer_settings' );
	 
	$shownumber = ( isset( $multioptions['somdn_show_numbers'] ) && $multioptions['somdn_show_numbers'] ) ? true : false ;
	 
	$buttoncss = ( isset( $genoptions['somdn_button_css'] ) && $genoptions['somdn_button_css'] ) ? $genoptions['somdn_button_css'] : '' ;
	$buttonclass = ( isset( $genoptions['somdn_button_class'] ) && $genoptions['somdn_button_class'] ) ? $genoptions['somdn_button_class'] : '' ;
	
	$linkcss = ( isset( $genoptions['somdn_link_css'] ) && $genoptions['somdn_link_css'] ) ? $genoptions['somdn_link_css'] : '' ;
	$linkclass = ( isset( $genoptions['somdn_link_class'] ) && $genoptions['somdn_link_class'] ) ? $genoptions['somdn_link_class'] : '' ;

	$pdfenabled = ( isset( $docoptions['somdn_docviewer_enable'] ) && $docoptions['somdn_docviewer_enable'] ) ? true : false ;

	if ( $is_single_download ) {

		if ( isset( $singleoptions['somdn_single_button_text'] ) && ! empty( $singleoptions['somdn_single_button_text'] ) ) 	{
			$buttontext = esc_html( $singleoptions['somdn_single_button_text'] );
		} else {
			$buttontext = __( 'Download Now', 'somdn-pro' );
		}

	} else {

		if ( isset( $multioptions['somdn_multi_button_text'] ) && ! empty( $multioptions['somdn_multi_button_text'] ) ) 	{
			$buttontext = esc_html( $multioptions['somdn_multi_button_text'] );
		} else {
			$buttontext = __( 'Download All', 'somdn-pro' );
		}

	}

	$pdf_default = __( 'Download PDF', 'somdn-pro' );
	$pdf_output = false;

	if ( $shortcode_text ) {
		$buttontext = $shortcode_text;
	}
	
	ob_start();
	
	if ( ( is_page() || somdn_is_single_product() ) ) {
		echo somdn_hide_cart_style();
	}

	$template = '';
	$single_type = '';

	if ( $is_single_download ) {

		$single_type = 1;

		if ( $download_type === 'variation' ) {
			$template = somdn_get_template( 'variation-single-file' );
		} else {
			$template = somdn_get_template( 'single-file' );
		}

	} else {

		if ( $download_type === 'variation' ) {
			$template = somdn_get_template( 'variation-multi-file-button' );
		} else {
			$template = somdn_get_template( 'multi-file-button' );
		}

	}

	if ( ! empty( $template ) ) {
		include( $template );
	}

	$content = ob_get_clean();
		
	return $content;

}