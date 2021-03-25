<?php
/**
 * Free Downloads - Woo QuickView
 * 
 * Quick View addon loader
 * 
 * @version	3.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/*
 * Output the "Quick View" button, at the end of the shop elements just before the closing li tag
 */
add_action( 'woocommerce_after_shop_loop_item', 'somdn_quickview_link_basic', 30 );
function somdn_quickview_link_basic() {

	$quickview_options = get_option( 'somdn_woo_quickview_settings' );
	$quickview_enabled = isset( $quickview_options['somdn_woo_quickview_enable'] ) ? $quickview_options['somdn_woo_quickview_enable'] : false ;

	if ( $quickview_enabled ) {

		$quickview_bg = isset( $quickview_options['somdn_woo_quickview_button_colour'] ) ? esc_html( $quickview_options['somdn_woo_quickview_button_colour'] ) : '#2679ce' ;

		$quickview_text_colour = isset( $quickview_options['somdn_woo_quickview_button_text_colour'] ) ? esc_html( $quickview_options['somdn_woo_quickview_button_text_colour'] ) : '#fff' ;

		$quickview_text = isset( $quickview_options['somdn_woo_quickview_button_text'] ) ? $quickview_options['somdn_woo_quickview_button_text'] : '' ;
		if ( empty( $quickview_text ) ) {
			$quickview_text = __( 'Quick View', 'somdn-pro' );
		} else {
			$quickview_text = esc_html( $quickview_text );
		}

		$id = get_the_id();
		echo '<div class="somdn-qview-link-wrap"><span class="somdn-qview-link" data-somdn-qview-id="' . $id . '" id="somdn-qview-link-prod-' . $id . '" style="color: ' . $quickview_text_colour . '; background-color: ' . $quickview_bg . ';">' . $quickview_text . '</span></div>';

	}

}

/**
 * Output the "Quick View" modal window, before the product title so it can sit below any other modal we output in CSS
 */
add_action( 'woocommerce_after_shop_loop_item', 'somdn_quickview_modal_basic', 15 );
function somdn_quickview_modal_basic() {

	$quickview_options = get_option( 'somdn_woo_quickview_settings' );
	$quickview_enabled = isset( $quickview_options['somdn_woo_quickview_enable'] ) ? $quickview_options['somdn_woo_quickview_enable'] : false ;

	if ( $quickview_enabled ) {

		/**
		 * Output the capture email form
		 */
		do_action( 'somdn_before_quickview' );
		$default_quickview_template = SOMDN_PATH . 'woo-files/somdn-woo-quickview-template.php';
		$quickview_template = apply_filters( 'somdn_somdn_quickview_modal_template', $default_quickview_template );
		include( $quickview_template );
		do_action( 'somdn_after_quickview' );

	}

}

function somdn_quickview_add_to_cart( $product, $product_id ) {

	if ( empty( $product_id ) || empty( $product ) ) {
		return;
	}

	$valid = somdn_is_product_valid_quickview( $product, $product_id );
	if ( ! $valid ) {
		woocommerce_template_loop_add_to_cart();
		return;
	} else {
		echo somdn_quickview_add_to_cart_download( $product, $product_id );
	}

}

function somdn_quickview_add_to_cart_download( $product, $product_id ) {

	if ( empty( $product_id ) || empty( $product ) ) {
		return;
	}

	return somdn_quickview_download_form_simple( $product, $product_id );

}

function somdn_is_product_valid_quickview( $product, $product_id ) {
	return apply_filters( 'somdn_is_product_valid_quickview', false, $product, $product_id );
}

function somdn_is_product_valid_quickview_basic( $valid, $product, $product_id ) {
	$product_valid = somdn_is_product_valid( $product_id, false );
	if ( ! $product_valid ) {
		return false;
	}
	return $valid = true;
}

function somdn_quickview_download_form_simple( $product, $product_id ) {

	$archive_enabled = false;
	$archive = false;
	$shortcode = false;
	$product_page_short = false;
	$echo = false;

	$genoptions = get_option( 'somdn_gen_settings' );
	$singleoptions = get_option( 'somdn_single_settings' );
	$multioptions = get_option( 'somdn_multi_settings' );
	$docoptions = get_option( 'somdn_docviewer_settings' );

	$requirelogin = isset( $genoptions['somdn_require_login'] ) ? true : false ;

	if ( ! is_user_logged_in() && $requirelogin ) {

		$allowed_tags = somdn_get_allowed_html_tags();

		$login_message = ( isset( $genoptions['somdn_require_login_message'] ) && $genoptions['somdn_require_login_message'] )
		? wpautop( wp_kses( $genoptions['somdn_require_login_message'], $allowed_tags ) )
		: __( 'Only registered users can download this free product.', 'somdn-pro' ); ;
		ob_start(); ?>

			<div class="<?php echo somdn_frontend_warning_class(); ?>"><?php echo $login_message; ?></div>

		<?php $message_content = ob_get_clean();

		if ( $echo ) {
			echo $message_content;
			return;
		} else {
			return $message_content;
		}

	}

	$downloads = somdn_get_files( $product );

	$downloads_count = count( $downloads );
	$is_single_download = ( 1 == $downloads_count ) ? true : false ;

	$shownumber = ( isset( $multioptions['somdn_show_numbers'] ) && $multioptions['somdn_show_numbers'] ) ? true : false ;
	 
	$buttoncss = ( isset( $genoptions['somdn_button_css'] ) && $genoptions['somdn_button_css'] ) ? esc_attr( $genoptions['somdn_button_css'] ) : '' ;
	$buttonclass = ( isset( $genoptions['somdn_button_class'] ) && $genoptions['somdn_button_class'] ) ? esc_attr( $genoptions['somdn_button_class'] ) : '' ;
	
	$linkcss = ( isset( $genoptions['somdn_link_css'] ) && $genoptions['somdn_link_css'] ) ? esc_attr( $genoptions['somdn_link_css'] ) : '' ;
	$linkclass = ( isset( $genoptions['somdn_link_class'] ) && $genoptions['somdn_link_class'] ) ? esc_attr( $genoptions['somdn_link_class'] ) : '' ;

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
	
	$single_type = ( isset( $singleoptions['somdn_single_type'] ) && 2 == $singleoptions['somdn_single_type'] ) ? 2 : 1 ;
	
	$pdf_default = __( 'Download PDF', 'somdn-pro' );
	$pdf_output = false;

	ob_start();

	if ( $is_single_download ) {

		do_action( 'somdn_before_simple_wrap', $product_id );
	
		/**
		 * Load the single file only template
		 */
		include( SOMDN_PATH . 'templates/download-forms/single-file.php' );

		do_action( 'somdn_after_simple_wrap', $product_id );

	} else {

		$multi_type = ( isset( $multioptions['somdn_display_type'] ) && $multioptions['somdn_display_type'] ) ? $multioptions['somdn_display_type'] : '1' ;

		/**
		 * 1. Links Only
		 */
		if ( 1 == $multi_type ) {

			do_action( 'somdn_before_simple_wrap', $product_id );

			/**
			 * Load the multi-file links only template
			 */
			include( SOMDN_PATH . 'templates/download-forms/multi-file-links.php' );

			do_action( 'somdn_after_simple_wrap', $product_id );


		/**
		 * 2. Button Only
		 */
		} elseif ( 2 == $multi_type ) {

			do_action( 'somdn_before_simple_wrap', $product_id );

			/**
			 * Load the multi-file button only template
			 */
			include( SOMDN_PATH . 'templates/download-forms/multi-file-button.php' );

			do_action( 'somdn_after_simple_wrap', $product_id );

		/**
		 * 3. Button + Checkboxes
		 */
		} elseif ( 3 == $multi_type ) {

			do_action( 'somdn_before_simple_wrap', $product_id );

			if ( empty( $buttonclass ) ) {
				$buttonclass = 'somdn-checkbox-submit';
			} else {
				$buttonclass .= 'somdn-checkbox-submit';
			}

			/**
			 * Load the multi-file button & checkboxes template
			 */
			include( SOMDN_PATH . 'templates/download-forms/multi-file-button-checkboxes.php' );

			do_action( 'somdn_after_simple_wrap', $product_id );

		/**
		 * 4. Button + Links
		 */
		} elseif ( 4 == $multi_type ) {

			do_action( 'somdn_before_simple_wrap', $product_id );

			/**
			 * Load the multi-file button & links template
			 */
			include( SOMDN_PATH . 'templates/download-forms/multi-file-button-links.php' );

			do_action( 'somdn_after_simple_wrap', $product_id );

		/**
		 * 5. Button & Filenames
		 */
		} elseif ( 5 == $multi_type ) {

			do_action( 'somdn_before_simple_wrap', $product_id );

			/**
			 * Load the multi-file button & filenames template
			 */
			include( SOMDN_PATH . 'templates/download-forms/multi-file-button-filenames.php' );

			do_action( 'somdn_after_simple_wrap', $product_id );

		}

	}

	$content = ob_get_clean();
		
	if ( $echo ) {
		echo $content;
		return;
	} else {
		return $content;
	}

}

/**
 * Compatibility for WooCommerce Quickview by IconicWP
 */
add_action( 'jckqv-after-addtocart', 'somdn_jckqv_after_addtocart' );
function somdn_jckqv_after_addtocart() {

	global $product;

	if ( empty( $product ) ) {
		return;
	}

	$product_id = somdn_get_product_id( $product );

	if ( empty( $product_id ) ) {
		return;
	}

	$valid = somdn_is_product_valid_quickview( $product, $product_id );
	if ( $valid ) {
		echo somdn_quickview_add_to_cart_download( $product, $product_id );
	}

	return;

	// Below for legacy testing only

	$valid = somdn_is_product_valid( $product_id );
	if ( $valid ) {

		if ( isset( $genoptions['somdn_read_more_text'] ) && ! empty( $genoptions['somdn_read_more_text'] ) ) {
			$buttontext = esc_html(  $genoptions['somdn_read_more_text'] );
		} else {
			$buttontext = __( 'Read More', 'somdn-pro' );
		}

		ob_start();

		$archive_class = esc_attr( somdn_get_button_archive_classes() ); ?>

		<div class="somdn-download-wrap">
			<a rel="nofollow" href="<?php echo esc_url( get_the_permalink( $product_id ) ); ?>" class="<?php echo 'somdn-download-archive' . $archive_class; ?>"><?php echo $buttontext; ?></a>
		</div>

		<?php $button = ob_get_clean();

		echo $button;

	} else {

		echo '<p>Not Valid</p>';

	}

}