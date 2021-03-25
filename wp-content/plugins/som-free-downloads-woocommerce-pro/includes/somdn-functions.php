<?php
/**
 * Free Downloads - Functions
 * 
 * Various functions.
 * 
 * @version	3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', 'somdn_load_plugin_textdomain' );
function somdn_load_plugin_textdomain() {

	$lang_dir = SOMDN_PLUGIN_PATH . '/i18n/languages';

	$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
	$locale = apply_filters( 'plugin_locale', $locale, 'somdn-pro' );

	// Filename of the .mo translation file for the user locale
	// Example: {text-domain}-es_CO.mo, Colombian Spanish
	$mofile = 'somdn-pro' . '-' . $locale . '.mo';

	// Clear this plugin textdomain if it's loaded
	unload_textdomain( 'somdn-pro' );

	// Let's see if we have one included in the plugin custom languages directory added by users. If one exists it will take priority
	// Full path is 'wp-content/languages/free-downloads/'
	load_textdomain( 'somdn-pro', WP_LANG_DIR . '/free-downloads/' . $mofile );

	// Try loading a translation from the default languages locations (starting with this plugin's languages folder $lang_dir)
	load_plugin_textdomain( 'somdn-pro', false, $lang_dir );

}

add_action( 'admin_enqueue_scripts', 'somdn_get_script_assets' );
function somdn_get_script_assets() {

	if ( ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'download_now_dashboard' ) ) {
		/**
		 * If the current admin page is this plugin's settings page
		 */
		wp_enqueue_script( 'somdn-settings-script', plugins_url( '/assets/js/somdn-settings-script.js', dirname(__FILE__) ), array( 'jquery', 'wp-color-picker' ) , '1.0.0', true );
		wp_enqueue_style( 'wp-color-picker' );		 
		wp_register_style( 'somdn-settings-style', plugins_url( '/assets/css/somdn-settings-style.css', dirname(__FILE__) ), '', 1.1 );
		wp_enqueue_style( 'somdn-settings-style' );
	}

	/**
	 * CSS changes for admin area, including post columns
	 */
	wp_register_style( 'somdn-admin-style', plugins_url( '/assets/css/somdn-admin-style.css', dirname(__FILE__) ), '', 1.1 );
	wp_enqueue_style( 'somdn-admin-style' );

}

add_action( 'wp_enqueue_scripts', 'somdn_load_scripts' );
function somdn_load_scripts() {
	wp_enqueue_script( 'somdn-script', plugins_url( '/assets/js/somdn_script.js', dirname(__FILE__) ), array( 'jquery' ), '1.0.0', true );
	wp_register_style( 'somdn-style', plugins_url( '/assets/css/somdn-style.css', dirname(__FILE__) ) );
	wp_enqueue_style( 'somdn-style' );
	$quickview_options = get_option( 'somdn_woo_quickview_settings' );
	$quickview_enabled = isset( $quickview_options['somdn_woo_quickview_enable'] ) ? $quickview_options['somdn_woo_quickview_enable'] : false ;
	wp_localize_script( 'somdn-script', 'somdn_script_params', array(
		'somdn_qview_active' => $quickview_enabled
	) );
	do_action( 'somdn_frontend_scripts_enqueued', 'somdn-script', 'somdn-style' );
}

add_action( 'somdn_frontend_scripts_enqueued' , 'somdn_frontend_custom_css', 10, 2 );
function somdn_frontend_custom_css( $script, $style ) {

	$genoptions = get_option( 'somdn_gen_settings' );
	$buttoncss = ( isset( $genoptions['somdn_button_css'] ) && $genoptions['somdn_button_css'] ) ? $genoptions['somdn_button_css'] : '' ;
	$linkcss = ( isset( $genoptions['somdn_link_css'] ) && $genoptions['somdn_link_css'] ) ? $genoptions['somdn_link_css'] : '' ;

	if ( ! empty( $buttoncss ) ) {
		$custom_button_css = '.somdn-download-wrap .somdn-download-button, .somdn-download-wrap a.somdn-download-archive {' . esc_attr( $buttoncss ) . '}';
		wp_add_inline_style( $style, $custom_button_css );
	}

	if ( ! empty( $linkcss ) ) {
		$custom_link_css = '.somdn-download-wrap .somdn-download-link {' . esc_attr( $linkcss ) . '}';
		wp_add_inline_style( $style, $custom_link_css );
	}

}

add_action( 'init', 'somdn_load_product_page', 10 );
function somdn_load_product_page() {
	do_action( 'somdn_load_product_page_content' );
}

add_action( 'somdn_count_download' , 'somdn_count_download_meta', 10, 1 );
function somdn_count_download_meta( $product_id ) {

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

	$download_count = get_post_meta( $product_id, 'somdn_dlcount', true );
	$download_count++;
	update_post_meta( $product_id, 'somdn_dlcount', intval( $download_count ) );

}

/**
 * Functions for returning product data
 *
 */
function somdn_get_product( $product_id = '' ) {
	return apply_filters( 'somdn_get_product', '', $product_id );
}
function somdn_get_global_product() {
	return apply_filters( 'somdn_get_global_product', '' );
}
function somdn_get_product_id( $product = '' ) {
	return apply_filters( 'somdn_get_product_id', '', $product );
}
/**
 * Functions for returning product data
 * @param int|object $product the object or id of the post
 * @return bool               If post is a product object
 */
function somdn_is_product( $product ) {
	return apply_filters( 'somdn_is_product', false, $product );
}
function somdn_is_single_product() {
	return apply_filters( 'somdn_is_single_product', false );
}
function somdn_get_files( $product = '', $product_id = '' ) {
	return apply_filters( 'somdn_get_files', array(), $product, $product_id );
}
function somdn_is_product_valid_type( $product, $product_id = '' ) {
	return apply_filters( 'somdn_is_product_valid_type', false, $product, $product_id );
}
function somdn_get_price( $product, $product_id ) {
	return apply_filters( 'somdn_get_price', '', $product, $product_id );
}
function somdn_get_sale_price( $product, $product_id ) {
	return apply_filters( 'somdn_get_sale_price', '', $product, $product_id );
}

/**
 * Check if the passed in product is valid for free download.
 *
 * @param $product_id The ID of the product
 * @param $check_login whether to check for login required. Default true
 * @return bool filtered, is product valid. Default false
 */
function somdn_is_product_valid( $product_id, $check_login = true ) {

	if ( empty( $product_id ) ) {
		return false;
	}

	$the_product_id = intval( $product_id );

	$debug = false;
	if ( $debug ) {
		$backtrace = debug_backtrace();
		$debug_data = $backtrace[1];
		if ( ! empty( $debug_data ) ) {
			$file = isset( $debug_data['file'] ) ? $debug_data['file'] : '' ;
			$function = isset( $debug_data['function'] ) ? $debug_data['function'] : '' ;
			$line = isset( $debug_data['line'] ) ? $debug_data['line'] : '' ;
			$debug_output = array(
				'file' => $file,
				'function' => $function,
				'line' => $line
			);
			echo '<pre class="somdn-debug-wrap">';
			print_r( $debug_output );
			echo '</pre>';
		}
	}

	/**
	 * Set up the global check lists for free products, one list for checked products and the other for checked without login.
	 * If either are empty, declare them as empty arrays
	 */
	global $somdn_checked_products;
	global $somdn_checked_products_guest;

	if ( ! isset( $somdn_checked_products ) ) {
		$somdn_checked_products = array();
	}

	if ( ! isset( $somdn_checked_products_guest ) ) {
		$somdn_checked_products_guest = array();
	}

	/**
	 * Passed in $the_product_id is first checked against the $somdn_checked_products array list.
	 * If it's in the list that means we've checked it before, and can simply return the free status
	 * (value) from the array. (performance improvement)
	 *
	 * The value of $check_login determines which list we check against
	 */

	if ( $check_login == true ) {

		if ( is_array( $somdn_checked_products ) ) {
			if ( array_key_exists( $the_product_id, $somdn_checked_products ) ) {
				$checked_valid = $somdn_checked_products[ $the_product_id ];
				return $checked_valid;
			} else {
				$valid = apply_filters( 'somdn_is_product_valid', false, $the_product_id, $check_login );
				$somdn_checked_products[ $the_product_id ] = $valid;
				return $valid;
			}
		}

	} else {

		if ( is_array( $somdn_checked_products_guest ) ) {
			if ( array_key_exists( $the_product_id, $somdn_checked_products_guest ) ) {
				$checked_valid = $somdn_checked_products_guest[ $the_product_id ];
				return $checked_valid;
			} else {
				$valid = apply_filters( 'somdn_is_product_valid', false, $the_product_id, $check_login );
				$somdn_checked_products_guest[ $the_product_id ] = $valid;
				return $valid;
			}
		}

	}

	// For good measure
	return false;

}

add_filter( 'somdn_is_product_valid', 'somdn_is_product_valid_basic', 10, 3 );
function somdn_is_product_valid_basic( $valid, $product_id, $check_login ) {

	if ( empty( $product_id ) ) {
		return false;
	}

	// Get the product/download object
	$product = somdn_get_product( $product_id );

	if ( empty( $product ) ) {
		return false;
	}

	// Check if product is a valid product type for downloading free
	if ( ! somdn_is_product_valid_type( $product, $product_id ) ) {
		return false;
	}

	// If the product is free and whether it is on sale and included in free downloads
	if ( ! somdn_is_product_free( $product, $product_id ) ) {
		return false;
	}

	// Are products included individually and if so is this product included? If not return false
	if ( ! somdn_is_product_included( $product, $product_id ) ) {
		return false;
	}

	// If this product has no files for download, return false
	if ( ! somdn_product_has_downloads( $product, $product_id ) )  {
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

/**
 * Check if the passed in product is included or excluded
 *
 * @param $product The product object of the product
 * @param $product_id The ID of the product
 * @return bool filtered, is product included. Default true
 */
function somdn_is_product_included( $product, $product_id ) {
	// Default to true from the start
	$included = true;
	return apply_filters( 'somdn_is_product_included', $included, $product, $product_id );
}

/**
 * Check if the passed in product is valid for the actual download action
 *
 * @param $product_id The ID of the product
 * @return bool filtered, is download action valid. Default false
 */
function somdn_is_download_valid( $product_id ) {
	return apply_filters( 'somdn_is_download_valid', false, $product_id );
}

add_filter( 'somdn_is_download_valid', 'somdn_is_download_valid_basic', 10, 2 );
function somdn_is_download_valid_basic( $valid, $product_id ) {

	$product = somdn_get_product( $product_id );
	if ( ! $product ) {
		return $valid = false;
	}

	$download_valid = somdn_is_product_valid( $product_id );
	if ( ! $download_valid ) {
		return $valid = false;
	}

	return $valid = true;

}

function somdn_extra_archive_action( $product_id, $hide_readmore ) {
	return apply_filters( 'somdn_extra_archive_action', false, $product_id, $hide_readmore );
}

add_filter( 'somdn_is_product_included', 'somdn_is_product_included_individual', 50, 3 );
function somdn_is_product_included_individual( $included, $product, $product_id ) {

	// Included usually comes through as true by default unless changed elsewhere.

	// Get the plugin general settings
	$genoptions = get_option( 'somdn_gen_settings' );
	$somdn_indy = isset( $genoptions['somdn_indy_items'] ) ? $genoptions['somdn_indy_items'] : false ;
	$somdn_indy_excl = isset( $genoptions['somdn_indy_exclude_items'] ) ? $genoptions['somdn_indy_exclude_items'] : false ;
	
	// Check if individually include or exclude products is set globally
	if ( ! $somdn_indy && ! $somdn_indy_excl ) {
		// Nothing set, return $included with filter
		return apply_filters( 'somdn_is_product_included_individual', $included, $product, $product_id );
	}

	if ( $included == true ) {

		// Product has been included, which is the default action

		if ( $somdn_indy ) {
			// We have globally set only individual products to be included

			// Check if this product is ticked to be included
			$is_included = get_post_meta( $product_id, 'somdn_included', true );
			if ( empty( $is_included ) || ! $is_included ) {
				// Although product came through as globally included in free downloads, it is not set to be individually included
				$included = false;
			}

		} elseif ( $somdn_indy_excl ) {
			// We have globally set only individual products to be excluded

			// Check if product is ticked to be excluded
			$is_included = get_post_meta( $product_id, 'somdn_included', true );
			if ( isset( $is_included ) && ! empty( $is_included ) ) {
				// Although product came through as included in free downloads, it is individually set to be excluded
				$included = false;
			}

		}

	} else {

		// Product has been excluded somewhere, possibly through an external action

		// Check if we have globally set individual products to be included
		if ( $somdn_indy ) {
			// Check if this product is ticked to be included
			$is_included = get_post_meta( $product_id, 'somdn_included', true );
			if ( ! empty( $is_included ) && $is_included ) {
				// Although product came through as excluded from free downloads, it is individually set to be included
				$included = true;
			}
		}

	}

	return apply_filters( 'somdn_is_product_included_individual', $included, $product, $product_id );

}

function somdn_product_has_downloads( $product, $product_id ) {
	$has_downloads = false;
	if ( empty( $product ) ) {
		$product = somdn_get_product( $product_id );
	}
	$downloads = somdn_get_files( $product );
	$downloads_count = count( $downloads );
	if ( $downloads_count > 0 )  {
		$has_downloads = true;
	}
	return apply_filters( 'somdn_product_has_downloads', $has_downloads, $product_id );
}

function somdn_is_required_login_check( $product, $product_id ) {
	$required_login_check = true;
	// Get the plugin general settings
	$genoptions = get_option( 'somdn_gen_settings' );
	$require_login_setting = isset( $genoptions['somdn_require_login'] ) ? true : false ;
	if ( ! is_user_logged_in() && $require_login_setting ) {
		$required_login_check = false;
	}
	return apply_filters( 'somdn_is_required_login_check', $required_login_check, $require_login_setting, $product_id );
}

function somdn_is_product_free( $product, $product_id ) {
	return apply_filters( 'somdn_is_free', false, $product, $product_id );
}

add_filter( 'somdn_is_free', 'somdn_is_product_free_for_user', 10, 3 );
function somdn_is_product_free_for_user( $free, $product, $product_id ) {

	if ( empty( $product ) ) {
		$product = somdn_get_product( $product_id );
	}

	if ( ! empty( $product ) ) {

		// Get the plugin general settings
		$genoptions = get_option( 'somdn_gen_settings' );
	
		$price = somdn_get_price( $product, $product_id );
		$sale = somdn_get_sale_price( $product, $product_id );
		$onsaleticked = isset( $genoptions['somdn_include_sale_items'] ) && ! empty( $genoptions['somdn_include_sale_items'] ) ? true : false ;
	
		if ( ( $price <= 0.0 ) || ( $onsaleticked == true && ( $sale != NULL && $sale <= 0.0 ) ) ) {
			$free = true;
		}

		if ( $onsaleticked == false && ( $sale != NULL && $sale <= 0.0 ) ) {
			$free = false;
		}

	}

	return apply_filters( 'somdn_is_product_free_for_user', $free, $product, $product_id );

}

function somdn_get_available_downloads_text() {

	$multioptions = get_option( 'somdn_multi_settings' );

	if ( ! isset( $multioptions['somdn_available_downloads_text'] ) || ! $multioptions['somdn_available_downloads_text'] ) {
		$available_downloads_text = __( 'Available Downloads', 'somdn-pro' );
	} else {
		$available_downloads_text = isset( $multioptions['somdn_available_downloads_text'] ) ? $multioptions['somdn_available_downloads_text'] : __( 'Available Downloads:', 'somdn-pro' ) ;
	} ?>
	
	<div class="somdn-available-downloads">
		<span><?php echo $available_downloads_text; ?>:</span>
	</div>
	
<?php }

function somdn_get_checkbox_error_text() {

	$multi_options = get_option( 'somdn_multi_settings' );
	$checkbox_error = isset( $multi_options['somdn_checkbox_error_text'] ) ? $multi_options['somdn_checkbox_error_text'] : '' ;

	if ( empty( $checkbox_error ) ) {
		$checkbox_error = __( 'Please select at least 1 checkbox', 'somdn-pro' );
	} else {
		$checkbox_error = esc_html( $checkbox_error );
	}

	return apply_filters( 'somdn_get_checkbox_error_text', $checkbox_error );
	
}

function somdn_get_button_classes() {
	return apply_filters( 'somdn_get_button_classes', $classes = '' );
}

function somdn_get_button_archive_classes() {
	return apply_filters( 'somdn_get_button_classes', $classes = '' );
}

function somdn_frontend_warning_class() {
	return apply_filters( 'somdn_frontend_warning_class', $class = '' );
}

function somdn_frontend_error_class() {
	return apply_filters( 'somdn_frontend_error_class', $class = '' );
}

function somdn_get_plugin_link_full() {
	return apply_filters( 'somdn_get_plugin_link_full', '?page=download_now_dashboard' );
}
function somdn_get_plugin_link_full_admin() {
	$url = get_admin_url() . 'admin.php' . somdn_get_plugin_link_full();
	return apply_filters( 'somdn_get_plugin_link_full_admin', $url );
}

function somdn_is_pro() {
	$pro = false;
	if ( defined( 'SOMDN_PRO' ) && file_exists( SOMDN_PRO ) ) {
		$pro = true;
	}
	return $pro;
}

/**
 * Get an array of template files filtered
 *
 * @return array filtered $templates An array of templates with ID, default path and override path
 */
function somdn_get_templates() {
	$theme_path = get_template_directory();
	$templates = array(
		'single-file' => array(
			'path' => SOMDN_PATH . 'templates/download-forms/single-file.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/single-file.php'
			),
		'multi-file-links' => array(
			'path' => SOMDN_PATH . 'templates/download-forms/multi-file-links.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/multi-file-links.php'
			),
		'multi-file-button' => array(
			'path' => SOMDN_PATH . 'templates/download-forms/multi-file-button.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/multi-file-button.php'
			),
		'multi-file-button-checkboxes' => array(
			'path' => SOMDN_PATH . 'templates/download-forms/multi-file-button-checkboxes.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/multi-file-button-checkboxes.php'
			),
		'multi-file-button-links' => array(
			'path' => SOMDN_PATH . 'templates/download-forms/multi-file-button-links.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/multi-file-button-links.php'
			),
		'multi-file-button-filenames' => array(
			'path' => SOMDN_PATH . 'templates/download-forms/multi-file-button-filenames.php',
			'custom_path' => $theme_path . '/somdn-templates/download-forms/multi-file-button-filenames.php'
			)
	);
	//$all_templates = apply_filters( 'somdn_get_templates', $templates );
	//somdn_debug_array($all_templates);
	//exit;
	return apply_filters( 'somdn_get_templates', $templates );
}

/**
 * Return the template based on $template_name
 *
 *
 * @param string $template_name The string name of the template to retrieve
 * @return string $template_path The path to the template
 */
function somdn_get_template( $template_name ) {

	$template_path = '';

	if ( empty( $template_name ) )
		return $template_path;

	$templates = somdn_get_templates();
	if ( empty( $templates ) )
		return $template_path;

	if ( ! array_key_exists( $template_name, $templates ) ) {
		return $template_path;
	}

	$template = $templates[ $template_name ];

	if ( isset( $template['path'] ) && ! empty( $template['path'] ) ) {
		if ( file_exists( $template['path'] ) ) {
			$template_path = $template['path'];
		}
	}

	if ( isset( $template['custom_path'] ) && ! empty( $template['custom_path'] ) ) {
		if ( file_exists( $template['custom_path'] ) ) {
			$template_path = $template['custom_path'];
		}
	}

	return apply_filters( 'somdn_get_template', $template_path, $template_name, $templates );

}

function somdn_get_valid_download_queries() {
	$queries = array(
		'somdn_download_key'
	);
	return apply_filters( 'somdn_get_valid_download_queries', $queries );
}

add_action( 'somdn_before_form_inputs_simple', 'somdn_output_timestamp_form', 50 );
function somdn_output_timestamp_form( $product_id ) {
	$key = somdn_get_download_key();
	echo '<input type="hidden" name="somdn_download_key" value="' . $key . '">';
}

function somdn_check_key_for_download() {
	$genoptions = get_option( 'somdn_gen_settings' );
	$settings_check = isset( $genoptions['somdn_disable_security_key_check'] ) ? false : true ;
	$check = $settings_check;
	return apply_filters( 'somdn_check_key_for_download', $check );
}

function somdn_verify_download_request_key( $query_arg = 'somdn_download_key' ) {

	// If we aren't checking download key validity, just return true
	if ( somdn_check_key_for_download() == false ) {
		return true;
	}

	$query = $query_arg;
	$valid_queries = somdn_get_valid_download_queries();

	if ( ! in_array( $query, $valid_queries) ) {
		return false;
	}

	$key = isset( $_REQUEST[$query_arg] ) ? $_REQUEST[$query_arg] : '' ;

	if ( empty( $key ) ) {
		$key_error_none = __( 'Error 1: Invalid download key. Please try again.', 'somdn-pro' );
		$errors['key_error_none'] = $key_error_none;
		array_push( $_REQUEST['somdn_errors'], $errors);
		return false;
	}

	$result = isset( $key ) ? somdn_verify_download_key( $key, $query_arg ) : false ;
	// Download key check failed
	if ( empty( $result ) || $result == false ) {
		$result = false;
	}
	// Do extra things
	do_action( 'somdn_verify_download_request_key', $key, $query_arg, $result );

	return $result;

}

function somdn_verify_download_key( $key = '', $query_arg ) {

	$valid = false;

	$key = (string) $key;

	if ( empty( $key ) ) {
		$key_error_none = __( 'Error 1: Invalid download key. Please try again.', 'somdn-pro' );
		$errors['key_error_none'] = $key_error_none;
		array_push( $_REQUEST['somdn_errors'], $errors);
		return $valid;
	}

	$somdn_download_key = somdn_decode_download_key( $key );

	if ( ! somdn_is_timestamp( $somdn_download_key ) ) {
		$key_error_timestamp = __( 'Error 2: Invalid download key. Please try again.', 'somdn-pro' );
		$errors['key_error_timestamp'] = $key_error_timestamp;
		array_push( $_REQUEST['somdn_errors'], $errors);
		return $valid;
	}

	if ( ! somdn_is_key_valid( $somdn_download_key ) ) {
		$key_error_time = __( 'Your download key has expired. Please try again.', 'somdn-pro' );
		$errors['key_error_time'] = $key_error_time;
		array_push( $_REQUEST['somdn_errors'], $errors);
	} else {
		$valid = true;
	}

	return $valid;

}

function somdn_is_key_valid( $key ) {

	if ( empty( $key ) )
		return false;

	$now = time(); // current time
	$diff = $now - $key;
	$hours = floor( ( $diff / 60 ) / 60 );

	// Default to 24 hours
	$expiration_time = apply_filters( 'somdn_download_key_expire', 24 );

	// Key is valid for $expiration_time hours
	$valid = ( $hours < $expiration_time ) ? true : false ;

	return $valid;

}

/**
 * Return a unix timestamp encoded with base64
 *
 * @return string $key The timestamp to use as a download key
 */
function somdn_get_download_key() {
	$key = base64_encode( time() );
	return $key;
}

/**
 * Returns a number encoded with base64
 *
 * @return string $key The key converted from an number to use as a download key
 */
function somdn_get_temp_download_key_from_int( $default_key = '' ) {

	if ( empty( $default_key ) )
		return $default_key;

	$id_for_key = intval( $default_key );

	$key = base64_encode( $id_for_key );
	return $key;

}

/**
 * Returns a string encoded with base64
 *
 * @return string $key The key converted from an number to use as a download key
 */
function somdn_get_temp_download_key_from_string( $default_key = '' ) {

	if ( empty( $default_key ) )
		return $default_key;

	$string_key = (string) $default_key;

	$key = base64_encode( $string_key );
	return $key;

}

/**
 * Return a string decoded with base64
 * Expected return is a unix timestamp
 *
 * @return string $decoded_key The converted string
 */
function somdn_decode_download_key( $key, $strict = false ) {
	$decoded_key = base64_decode( $key, $strict );
	return $decoded_key;
}

/**
 * Validate that a string is a unix timestamp
 *
 * @return bool True or False
 */
function somdn_is_timestamp( $timestamp ) {
	return ( (string) (int) $timestamp === $timestamp ) 
		&& ( $timestamp <= PHP_INT_MAX )
		&& ( $timestamp >= ~PHP_INT_MAX );
}

add_action( 'somdn_single_errors', 'somdn_single_errors_output', 50 );
function somdn_single_errors_output() {

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

add_action( 'somdn_before_simple_wrap', 'somdn_output_download_count_output_above', 10, 1 );
add_action( 'somdn_after_simple_wrap', 'somdn_output_download_count_output_below', 10, 1 );
function somdn_output_download_count_output_above( $product_id ) {

	$options = get_option( 'somdn_gen_settings' );
	$is_output = ( isset( $options['somdn_download_counts_output'] ) && $options['somdn_download_counts_output'] ) ? intval( $options['somdn_download_counts_output'] ) : 0 ;
	$output_text = ( isset( $options['somdn_download_counts_output_text'] ) && $options['somdn_download_counts_output_text'] ) ? esc_html( $options['somdn_download_counts_output_text'] ) : '' ;

	$download_count = get_post_meta( $product_id, 'somdn_dlcount', true ) ? intval( get_post_meta( $product_id, 'somdn_dlcount', true ) ) : 0 ;

	if ( ! empty( $is_output ) && $is_output == 1 ) {

		$class = ' above';

		if ( empty( $output_text ) ) {
			$output_text = sprintf( __( '(Downloads - %s)', 'somdn-pro' ), '{count}' ) ;
			$ouput_content = str_replace( '{count}', $download_count, $output_text );
		} else {
			$ouput_content = str_replace( '{count}', $download_count, $output_text );
		}

		$start = '<div class="somdn-download-count-wrap' . esc_attr( $class ) .'"><p class="somdn-download-count"><strong>';
		$end = '</strong></p></div>';
		$ouput_complete = $start . $ouput_content . $end;

		echo apply_filters( 'somdn_output_download_count_output_above', $ouput_complete, $start, $end, $ouput_content, $download_count, $output_text, $class );

	}

}

function somdn_output_download_count_output_below( $product_id ) {

	$options = get_option( 'somdn_gen_settings' );
	$is_output = ( isset( $options['somdn_download_counts_output'] ) && $options['somdn_download_counts_output'] ) ? intval( $options['somdn_download_counts_output'] ) : 0 ;
	$output_text = ( isset( $options['somdn_download_counts_output_text'] ) && $options['somdn_download_counts_output_text'] ) ? esc_html( $options['somdn_download_counts_output_text'] ) : '' ;

	$download_count = get_post_meta( $product_id, 'somdn_dlcount', true ) ? intval( get_post_meta( $product_id, 'somdn_dlcount', true ) ) : 0 ;

	if ( ! empty( $is_output ) && $is_output == 2 ) {

		$class = ' below';

		if ( empty( $output_text ) ) {
			$output_text = sprintf( __( '(Downloads - %s)', 'somdn-pro' ), '{count}' ) ;
			$ouput_content = str_replace( '{count}', $download_count, $output_text );
		} else {
			$ouput_content = str_replace( '{count}', $download_count, $output_text );
		}

		$start = '<div class="somdn-download-count-wrap' . esc_attr( $class ) .'"><p class="somdn-download-count"><strong>';
		$end = '</strong></p></div>';
		$ouput_complete = $start . $ouput_content . $end;

		echo apply_filters( 'somdn_output_download_count_output_below', $ouput_complete, $start, $end, $ouput_content, $download_count, $output_text, $class );

	}

}

add_action( 'somdn_get_forum_link', 'somdn_get_forum_link_basic' );
function somdn_get_forum_link_basic() {
	$text = __( 'If you need further support please visit the support forum for this plugin over at', 'somdn-pro' );
	$url = ' <a href="https://wordpress.org/support/plugin/download-now-for-woocommerce/" target="_blank">WordPress.org</a>';
	echo $text . $url . '.';
}

function somdn_get_allowed_html_tags() {
	$allowed_tags = array(
		'a' => array(
			'href' => array(),
			'title' => array(),
			'target' => array()
		),
		'p' => array(
			'style' => array()
		),
		'br' => array(),
		'em' => array(),
		'strong' => array(),
	);
	return apply_filters( 'somdn_get_allowed_html_tags', $allowed_tags );
}

function somdn_do_default_download( $type = 'simple' ) {
	do_action( 'somdn_do_default_download_type_' . $type, $type );
}

function somdn_wp_error( $message, $args = array( 'back_link' => true ) ) {
	$error = new WP_Error( 'somdn_error', $message );
	$site_title = get_bloginfo( 'name', 'display' );
	wp_die( $error, $site_title . ' - Error', $args );
}

function somdn_write_log( $log = '', $log_level = -1 ) {

	if ( empty( $log ) ) {
		return;
	}

	$log_type = '';

	switch ( $log_level ) {
		case 0:
			$log_type = '[DEBUG]';
			break;
		case 1:
			$log_type = '[WARNING]';
			break;
		case 2:
			$log_type = '[ERROR]';
			break;
		case 3:
			$log_type = '[CRITICAL]';
			break;
		default:
			$log_type = '';
			break;
	}

	somdn_create_temp_uploads_folders();
	$parent = somdn_get_upload_folder_parent_path();

	$log_timezone = '';
	$log_timezone_string = get_option( 'timezone_string' );
	$log_time = current_time( '[d-M-Y H:i:s' );

	if ( empty( $log_timezone_string ) ) {
		$log_timezone = ']';
	} else {
		$log_timezone = ' ' . esc_html( $log_timezone_string ) . ']';
	}

	$log_filename = $parent . '/free_downloads_log.txt';
	$new_enty = $log_time . $log_timezone . ' ' . $log_type . ' ' . $log;
	$new_enty = sanitize_text_field($new_enty);

	// Standard insert new log entry after existing entires
	//file_put_contents( $log_filename, $new_enty . "\n", FILE_APPEND );
	//return;

	/**
	 * The below essentially prepends a new log entry to the existing file, meaning new entries go to the top.
	 */
	if ( file_exists( $log_filename ) ) {
		// If we already have a log file created, grab the existing logs
		$file_content = file_get_contents( $log_filename );
		// Insert the new log entry at the start of a new file, and put previous ones below
		file_put_contents( $log_filename, $new_enty . "\n\n" . $file_content );
	} else {
		// No logs exist, start a new file
		file_put_contents( $log_filename, $new_enty . "\n", FILE_APPEND );
	}

}

//add_action( 'somdn_debug_is_product_valid', 'somdn_debug_is_product_valid_output', 10, 1 );
function somdn_debug_is_product_valid_output( $args ) {
	$debug = true;
	if ( $debug ) {
		$backtrace = debug_backtrace();
		$debug_data = $backtrace[1];
		if ( ! empty( $debug_data ) ) {
			$file = isset( $debug_data['file'] ) ? $debug_data['file'] : '' ;
			$function = isset( $debug_data['function'] ) ? $debug_data['function'] : '' ;
			$line = isset( $debug_data['line'] ) ? $debug_data['line'] : '' ;
			$debug_output = array(
				'file' => $file,
				'function' => $function,
				'line' => $line
			);
			echo '<pre class="somdn-debug-wrap">';
			print_r( $backtrace[1] );
			echo '</pre>';			
		}
	}
}

function somdn_is_debug_on() {
	$debug_options = get_option( 'somdn_debug_settings' );
	$debugging = isset( $debug_options['somdn_debug_logging_enable'] ) ? $debug_options['somdn_debug_logging_enable'] : false ;
	return ( $debugging );
}

function somdn_debug_line( $text = array(), $echo = true ) {
	$ob_start;
	$current_line = 1;
	foreach ( $text as $line ) {
		echo '<p><strong>Line ' . $current_line . ':</strong> ' . $line . '</p>';
		$current_line++;
	}
	$debug = ob_get_clean();
	if ( $echo ) {
		echo $debug;
	} else {
		return $debug;
	}
}

function somdn_debug_array( $array = array(), $echo = true ) {
	$ob_start;
	echo '<pre class="somdn-debug-wrap">';
	print_r( $array );
	echo '</pre>';	
	$debug = ob_get_clean();
	if ( $echo ) {
		echo $debug;
	} else {
		return $debug;
	}
}