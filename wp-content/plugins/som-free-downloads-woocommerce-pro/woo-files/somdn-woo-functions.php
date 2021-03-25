<?php
/**
 * Free Downloads - Woo Functions
 * 
 * WooCommerce specific functions/actions for downloads
 * 
 * @version 3.1.5
 * @author  Square One Media
 */

if ( ! defined( 'ABSPATH' ) ) exit;

//add_action( 'woocommerce_single_product_summary', 'somdn_product_page', 31 );
//add_action( 'woocommerce_simple_add_to_cart', 'somdn_product_page', 31 );

function somdn_load_product_page_content_woo() {
	$action = sanitize_text_field( apply_filters( 'somdn_product_page_content_woo', 'woocommerce_single_product_summary' ) );
	$priority = intval( apply_filters( 'somdn_product_page_content_woo_priority', 31 ) );
	add_action( $action, 'somdn_product_page', $priority );
}

add_filter( 'woocommerce_is_purchasable', 'somdn_prevent_purchase', 10, 2 );
function somdn_prevent_purchase( $purchasable, $product ) {

	$product_id = somdn_get_product_id( $product );

	if ( empty( $product_id ) ) {
		return $purchasable;
	}

	if ( ! somdn_is_product_valid( $product_id, false ) ) {
		return $purchasable;
	} else {
		$purchasable = false;
	}

	$purchasable = somdn_is_purchasable_compat( $purchasable );

	return $purchasable;

}
	
if ( ! function_exists( 'woocommerce_template_loop_add_to_cart' ) ) {

	$genoptions = get_option( 'somdn_gen_settings' );
	$archive_enabled = ( isset( $genoptions['somdn_include_archive_items'] ) && $genoptions['somdn_include_archive_items'] ) ? true : false ;

	if ( ! $archive_enabled ) {
		return;
	}

	function woocommerce_template_loop_add_to_cart( $args = array() ) {

		$genoptions = get_option( 'somdn_gen_settings' );
		$hide_readmore = ( isset( $genoptions['somdn_hide_readmore_button_archive'] ) && $genoptions['somdn_hide_readmore_button_archive'] ) ? true : false ;

		global $product;

		$product_id = somdn_get_product_id( $product );

		if ( somdn_is_product_valid( $product_id ) ) {
			// If the user is completely entitled to download this product

			//echo '<span>All ok</span><br>';

			echo '<div>';

			$download_page_args = apply_filters( 'somdn_template_loop_add_to_cart_args', array(
				'archive'=> true,
				'product' => $product,
				'echo' => true
			) );
			do_action( 'somdn_archive_product_page', $download_page_args );

			echo '</div>';

			return;

		} elseif ( somdn_is_product_valid( $product_id, false ) && $hide_readmore ) {
			// If the user would be able to download the product if they were logged in

			//echo '<span>free_no_download_login</span><br>';

			// Show nothing, custom action hook added
			do_action( 'somdn_archive_free_no_download_login' );

		} else {

			// An extra action check that plugins can hook into
			$extra_archive_action = somdn_extra_archive_action( $product_id, $hide_readmore );
			if ( $extra_archive_action && $hide_readmore ) {
				return;
			}

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
}

function somdn_get_product_woo( $product, $product_id = '' ) {
	if ( empty( $product_id ) ) {
		$product = somdn_get_global_product();
	} else {
		$product = wc_get_product( intval( $product_id ) );
	}
	return apply_filters( 'somdn_get_product_woo', $product, $product_id );
}

function somdn_get_global_product_woo( $global_product ) {
	global $product;
	$global_product = ( !empty( $product ) ) ? $product : '' ;
	return apply_filters( 'somdn_get_global_product_woo', $global_product );
}

function somdn_get_product_id_woo( $product_id = '', $product ) {
	if ( ! empty( $product ) ) {
		if ( method_exists( $product, 'get_id' ) ) {
			$product_id = $product->get_id();
		} else {
			$product_id = $product->id;
		}
	}
	return $product_id;
}

function somdn_is_product_woo( $is_product, $product = '' ) {
	if ( ! empty( $product ) ) {
		if ( wc_get_product( $product ) ) {
			$is_product = true;
		}
	}
	return apply_filters( 'somdn_is_product_woo', $is_product, $product );
}

function somdn_is_single_product_woo( $single_product ) {
	return apply_filters( 'somdn_is_single_product_woo', is_product() );
}

function somdn_get_files_woo( $downloads, $product, $product_id = '' ) {
	if ( ! empty( $product ) ) {
		if ( method_exists( $product, 'get_downloads' ) ) {
			$downloads = $product->get_downloads();
		} else {
			$downloads = $product->get_files();
		}
	}
	return apply_filters( 'somdn_get_files_woo', $downloads, $product, $product_id );
}

/**
 * Apply extra filters to the file url/path to download
 *
 * @param  string $file_path      The original unfiltered filepath
 * @param  object $product        The product object
 * @param  int    $key            The key in the array of products
 * @param  array  $download_array The array of file info ( id, name, file )
 * @param  int    $download_id    The ID of the download in the downloads array
 * @return string                 Filepath/URL for the file
 *
 *
 * MAY NEED TO CHECK amazon_s3 HAS_SHORTCODE
 *
 */
//add_filter( 'somdn_download_path', 'somdn_download_path_filtered', 10, 5 );
function somdn_download_path_filtered( $file_path, $product, $key, $download_array, $download_id ) {
	return $file_path;
}

function somdn_download_path_filtered_old( $file_path, $product, $key, $download_array, $download_id ) {
	if ( somdn_woocommerce_old_version_check() ) {
		$download_id = $key;
	}
	$file_path = $product->get_file_download_path( $download_id );
	return $file_path;
}

function somdn_get_price_woo( $price = '', $product, $product_id = '' ) {
	if ( empty( $product ) ) {
		$product = somdn_get_product( $product_id );
	}
	$price = $product->get_price();
	return apply_filters( 'somdn_get_price_woo', $price, $product );
}

function somdn_get_sale_price_woo( $sale_price = '', $product, $product_id = '' ) {
	if ( empty( $product ) ) {
		$product = somdn_get_product( $product_id );
	}
	$sale_price = get_post_meta( $product_id, '_sale_price', true);
	return apply_filters( 'somdn_get_sale_price_woo', $sale_price, $product_id );
}

function somdn_is_product_valid_type_woo_basic( $valid, $product, $product_id ) {

	if ( empty( $product ) ) {
		$product = somdn_get_product( $product_id );
	}
	if ( ! empty( $product ) ) {
		// Simple product check
		if ( $product->is_type( 'simple' ) ) {
			$valid = true;
		}
		// Exclude variations
		if ( $product->get_attribute( 'variation' ) != '' ) {	
			$valid = false;
		}
		// Only include virtual downloadable products
		$downloadable = $product->is_downloadable();
		$virtual = true; //$product->is_virtual();
		if ( ! $downloadable || ! $virtual ) {
			return false;
		}
	}

	return apply_filters( 'somdn_is_product_valid_type_woo_basic', $valid, $product, $product_id );
}

function somdn_change_read_more( $text ) {

	global $product;

	if ( ! $product )
		return $text;

	$product_id = somdn_get_product_id( $product );

	if ( ! somdn_is_product_valid( $product_id ) ) {
		return $text;
	}
	
	$options = get_option( 'somdn_gen_settings' );
	$newtext = ( isset( $options['somdn_read_more_text'] ) && $options['somdn_read_more_text'] ) ? $options['somdn_read_more_text']: false ;
	
	if ( $newtext ) {
		return $newtext;
	} else {
		return $text;
	}
	
}

function somdn_woo_download( $file_path, $product_id, $force = false ) {

	$filename = basename( $file_path );

	if ( strstr( $filename, '?' ) ) {
		$filename = current( explode( '?', $filename ) );
	}

	if ( $force ) {
		$file_download_method = apply_filters( 'woocommerce_file_download_method', 'force', $product_id );
	} else {
		$file_download_method = apply_filters( 'woocommerce_file_download_method', get_option( 'woocommerce_file_download_method', 'force' ), $product_id );
	}

	// Add 1 to the download count for this product
	do_action( 'somdn_count_download', $product_id );

	// Add action to prevent issues in IE
	add_action( 'nocache_headers', array( 'WC_Download_Handler', 'ie_nocache_headers_fix' ) );
	
	// Trigger download via one of the methods
	do_action( 'woocommerce_download_file_' . $file_download_method, $file_path, $filename );

}

function somdn_do_default_download_simple() {
	somdn_downloader_init();
}

function somdn_woocommerce_old_version_check( $version = '3.0' ) {
	if ( class_exists( 'WooCommerce' ) ) {
		if( version_compare( WC_VERSION, $version, '<' ) ) {
			return true;
		}
	}
	return false;
}

function somdn_get_button_classes_woo( $classes ) {
	return ' single_add_to_cart_button button';
}

function somdn_get_button_archive_classes_woo( $classes ) {
	return ' button product_type_simple add_to_cart_button';
}

function somdn_get_download_button_woo( $button, $text, $css, $archive, $product_id, $class, $all_zip ) {

	ob_start();

	if ( $class ) {
		$class = esc_attr( $class ) . ' ';
	}

	$owned_settings = get_option( 'somdn_owned_settings' );
	$include_owned = ( isset( $owned_settings['somdn_owned_enable'] ) && $owned_settings['somdn_owned_enable'] ) ? true : false ;

	if ( $include_owned ) {
		$owned = somdn_is_download_owned( false, '', $product_id );
		if ( $owned ) {
			$all_zip = false;
			if ( isset( $owned_settings['somdn_owned_button_text'] ) && ! empty( $owned_settings['somdn_owned_button_text'] ) ) 	{
				$text = esc_html( $owned_settings['somdn_owned_button_text'] );
			} else {
				$text = __( 'Download Again', 'somdn-pro' );
			}
		}
	}

	$zip_text = ' ';// small space to join
	$zip_text .= __( '(.zip)', 'somdn-pro' );

	$archive_class = esc_attr( somdn_get_button_archive_classes() );
	$button_class = esc_attr( somdn_get_button_classes() );

	if ( $archive ) { ?>

		<a rel="nofollow" href="<?php echo esc_url( get_the_permalink( $product_id ) ); ?>" class="<?php echo $class . 'somdn-download-archive' . $archive_class; ?>"><?php echo $text; ?></a>

	<?php } else { ?>

		<button style="<?php echo esc_attr( $css ); ?>" type="submit" id="somdn-form-submit-button" class="<?php echo $class . 'somdn-download-button' . $button_class; ?>"><?php echo $text; ?><?php if ( $all_zip ) echo $zip_text; ?></button>
	
	<?php }

	$button = ob_get_clean();

	return $button;

}

function somdn_get_single_download_link_woo( $link, $text, $css, $archive, $product_id, $class ) {

	ob_start();

	if ( $class ) {
		$class = esc_attr( $class ) . ' ';
	} ?>

	<a id="somdn-sdbutton" href="#" class="<?php echo $class; ?>somdn-download-link" style="<?php echo esc_attr( $css ); ?>"><?php echo esc_html( $text ); ?></a>

	<?php $link = ob_get_clean();

	return $link;

}

function somdn_get_multi_download_link_woo( $link, $count, $css, $shownumber, $name, $class ) {

	ob_start();

	if ( $class ) {
		$class = esc_attr( $class ) . ' ';
	} ?>

	<a id="somdn-md-link-<?php echo $count; ?>" href="#" class="<?php echo $class; ?>somdn-download-link" style="<?php echo esc_attr( $css ); ?>"><?php echo esc_html( $shownumber . $name ); ?></a>

	<?php $link = ob_get_clean();

	return $link;

}

function somdn_before_add_to_cart_form_woo() {
	do_action( 'woocommerce_before_add_to_cart_form' );
}

function somdn_before_add_to_cart_button_woo() {
	do_action( 'woocommerce_before_add_to_cart_button' );
}

function somdn_after_add_to_cart_form_woo() {
	do_action( 'woocommerce_after_add_to_cart_form' );
}

function somdn_after_add_to_cart_button_woo() {
	do_action( 'woocommerce_after_add_to_cart_button' );
}

function somdn_frontend_warning_class_woo() {
	return 'woocommerce-info somdn-download-error';
}

function somdn_frontend_error_class_woo() {
	return 'woocommerce-error somdn-download-error';
}

function somdn_is_download_owned( $free, $product, $product_id ) {

	$owned_settings = get_option( 'somdn_owned_settings' );
	$include_owned = ( isset( $owned_settings['somdn_owned_enable'] ) && $owned_settings['somdn_owned_enable'] ) ? true : false ;

	if ( is_admin() && ! wp_doing_ajax() ) {
		return $free;
	}

	if ( ! is_user_logged_in() || $include_owned == false || empty( $product_id ) ) {
		return $free;
	}

	$the_product_id = intval( $product_id );

	$current_user = wp_get_current_user();

	if ( empty( $current_user ) )
		return $free;

	$user_email = $current_user->user_email;

	/**
	 * Set up the global check lists for owned products, one list for checked products and the other for owned.
	 * If either are empty, declare them as empty arrays
	 */
	global $somdn_is_owned_products;
	global $somdn_checked_owned_products;

	if ( ! isset( $somdn_is_owned_products ) ) {
		$somdn_is_owned_products = array();
	}

	if ( ! isset( $somdn_checked_owned_products ) ) {
		$somdn_checked_owned_products = array();
	}

	/**
	 * Passed in $the_product_id is first checked against the $somdn_checked_owned_products array list.
	 * If it's in the list that means we've checked it before, and can simply return the owned status
	 * from the corresponding $the_product_id index in the $somdn_is_owned_products list.
	 */
	if ( is_array( $somdn_checked_owned_products ) ) {
		if ( array_key_exists( $the_product_id, $somdn_checked_owned_products ) ) {
			$checked_owned = $somdn_is_owned_products[ $the_product_id ];
			if ( $checked_owned == true ) {
				$free = true;
			}
			return $free;
		}
	}

	// If we've reached this point the product hasn't been checked for before

	// Product is first added to the checked list, so we don't check owned status again (performance improvement)
	$somdn_checked_owned_products[ $the_product_id ] = true;

	// Declare the product as not owned by default before any checks
	$somdn_is_owned_products[ $the_product_id ] = false;

	// $free will be set to true if the customer has bought the product and is currently eligible download it again
	//echo '<p>Owned Check</p>';
	if ( wc_customer_bought_product( $user_email, get_current_user_id(), $the_product_id ) ) {
		$downloads = WC()->customer->get_downloadable_products();
		$has_downloads = (bool) $downloads;
		if ( $has_downloads ) {
			foreach ( $downloads as $download ) :
				if ( $download['product_id'] == $the_product_id ) {
					$free = true;
					// Product is owned so we amend the status in the global product checked list to true
					$somdn_is_owned_products[ $the_product_id ] = true;
					break;
				}
			endforeach;
		}
	}

	// Return the free status of the product
	return $free;

}

function somdn_is_free_and_owned( $free_owned = false, $product_id ) {
	if ( ! empty( $product_id ) ) {
		$product = somdn_get_product( $product_id );
		if ( empty( $product ) ) {
			return false;
		}
		if ( somdn_is_product_valid( $product_id ) && somdn_is_download_owned( false, $product, $product_id ) ) {
			$free_owned = true;
		}
	}
	return $free_owned;
}

function somdn_is_download_owned_price( $sale_price, $product ) {

	$owned_settings = get_option( 'somdn_owned_settings' );
	$include_owned = ( isset( $owned_settings['somdn_owned_enable'] ) && $owned_settings['somdn_owned_enable'] ) ? true : false ;
	if ( ! $include_owned ) {
		return $sale_price;
	}

	$product_id = somdn_get_product_id( $product );
	if ( ! empty( $product_id ) ) {
		if ( somdn_is_free_and_owned( false, $product_id ) ) {
			$sale_price = 0.0;
		}
	}
	return $sale_price;
}

function somdn_download_owned_price_badge( $badge, $post, $product ) {

	$owned_settings = get_option( 'somdn_owned_settings' );
	$include_owned = ( isset( $owned_settings['somdn_owned_enable'] ) && $owned_settings['somdn_owned_enable'] ) ? true : false ;
	if ( ! $include_owned ) {
		return $badge;
	}

	$product_id = somdn_get_product_id( $product );
	if ( ! empty( $product_id ) ) {
		if ( somdn_is_free_and_owned( false, $product_id ) ) {
			$owned_settings = get_option( 'somdn_owned_settings' );
			$hide_badge = ( isset( $owned_settings['somdn_owned_badge_hide'] ) && $owned_settings['somdn_owned_badge_hide'] ) ? true : false ;
			if ( $hide_badge ) {
				return '';
			} else {
				if ( isset( $owned_settings['somdn_owned_badge_text'] ) && ! empty( $owned_settings['somdn_owned_badge_text'] ) ) 	{
					$text = esc_html( $owned_settings['somdn_owned_badge_text'] );
				} else {
					$text = __( 'OWNED!', 'somdn-pro' );
				}
				$badge = '<span class="onsale somdn-owned-badge">' . $text . '</span>';
			}
		}
	}
	return $badge;
}

function somdn_download_owned_price_html( $price_html, $product ) {

	$owned_settings = get_option( 'somdn_owned_settings' );
	$include_owned = ( isset( $owned_settings['somdn_owned_enable'] ) && $owned_settings['somdn_owned_enable'] ) ? true : false ;
	if ( ! $include_owned ) {
		return $price_html;
	}

	$product_id = somdn_get_product_id( $product );
	if ( ! empty( $product_id ) ) {
		if ( somdn_is_free_and_owned( false, $product_id ) ) {
			if( $product->is_type('variable') ) return $price_html;

			$price_html = wc_format_sale_price( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ), wc_get_price_to_display(  $product, array( 'price' => $product->get_sale_price() ) ) ) . $product->get_price_suffix();
		}
	}
	return $price_html;
}

/**
 * Custom add_to_cart shortcode, overrides default WooCommerce shortcode.
 * If product is a free download, output the Download Now button behaviour. Otherwise output Add To Basket.
 */
add_action( 'init', 'somdn_remove_woo_add_to_cart_shortcode', 99 );
function somdn_remove_woo_add_to_cart_shortcode() {
	remove_shortcode( 'add_to_cart' );// Remove the default shortcode action
	add_shortcode( 'add_to_cart', 'somdn_custom_woo_add_to_cart_shortcode' );// Add custom action
}
function somdn_custom_woo_add_to_cart_shortcode( $atts ) {

	/**
	 * Display a single product price + cart button.
	 *
	 * @version 1.0
	 * @since  3.2.0
	 * @param array $atts Attributes.
	 * @return string
	 */
	global $post;

	if ( empty( $atts ) ) {
		return '';
	}

	$atts = shortcode_atts( array(
		'id'         => '',
		'class'      => '',
		'quantity'   => '1',
		'sku'        => '',
		'style'      => 'border:4px solid #ccc; padding: 12px;',
		'show_price' => 'true',
	), $atts, 'product_add_to_cart' );

	if ( ! empty( $atts['id'] ) ) {
		$product_data = get_post( $atts['id'] );
	} elseif ( ! empty( $atts['sku'] ) ) {
		$product_id   = wc_get_product_id_by_sku( $atts['sku'] );
		$product_data = get_post( $product_id );
	} else {
		return '';
	}

	$product = is_object( $product_data ) && in_array( $product_data->post_type, array( 'product', 'product_variation' ), true ) ? wc_setup_product_data( $product_data ) : false;

	if ( ! $product ) {
		return '';
	}

	$genoptions = get_option( 'somdn_gen_settings' );
	$hide_readmore = ( isset( $genoptions['somdn_hide_readmore_button_archive'] ) && $genoptions['somdn_hide_readmore_button_archive'] ) ? true : false ;

	ob_start();

	$product_id = somdn_get_product_id( $product );

	if ( somdn_is_product_valid( $product_id ) ) {

		$download_page_args = apply_filters( 'somdn_custom_woo_add_to_cart_shortcode_args', array(
			'archive'=> true,
			'product' => $product,
			'echo' => true
		) );
		do_action( 'somdn_archive_product_page', $download_page_args );

	} else {

		echo '<p class="product woocommerce add_to_cart_inline ' . esc_attr( $atts['class'] ) . '" style="' . ( empty( $atts['style'] ) ? '' : esc_attr( $atts['style'] ) ) . '">';

		if ( wc_string_to_bool( $atts['show_price'] ) ) {
			// @codingStandardsIgnoreStart
			echo $product->get_price_html();
			// @codingStandardsIgnoreEnd
		}

		woocommerce_template_loop_add_to_cart( array(
			'quantity' => $atts['quantity'],
		) );

		echo '</p>';

	}

	// Restore Product global in case this is shown inside a product post.
	wc_setup_product_data( $post );

	return ob_get_clean();
}