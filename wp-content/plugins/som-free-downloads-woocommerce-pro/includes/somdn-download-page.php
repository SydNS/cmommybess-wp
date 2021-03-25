<?php
/**
 * Free Downloads - Download Page
 * 
 * The function to display download links.
 * 
 * @version	2.4.6
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'somdn_download_button', 'somdn_get_download_button', 10, 6 );
add_action( 'somdn_single_download_link', 'somdn_get_single_download_link', 10, 5 );
add_action( 'somdn_multi_download_link', 'somdn_get_multi_download_link', 10, 5 );
add_action( 'somdn_archive_product_page', 'somdn_product_page', 10, 1 );

function somdn_product_page( $args = array() ) {

	$defaults = array(
		'archive' => false,
		'product' => '',
		'echo' => true,
		'archive_enabled' => false,
		'shortcode' => false,
		'shortcode_text' => '',
		'product_page_short' => false
	);

	$somdn_args = apply_filters( 'somdn_product_page_args', wp_parse_args( $args, $defaults ), $args, $defaults );

	$archive = $somdn_args['archive'];
	$product = $somdn_args['product'];
	$echo = $somdn_args['echo'];
	$archive_enabled = $somdn_args['archive_enabled'];
	$shortcode = $somdn_args['shortcode'];
	$shortcode_text = $somdn_args['shortcode_text'];
	$product_page_short = $somdn_args['product_page_short'];

	if ( ! $product ) {
		$product = somdn_get_product();
	}
 
	if ( ! $product ) {
		$product = somdn_get_global_product();
	}

	if ( ! $product ) {
		return false;
	}

	if ( ! $archive && ! somdn_is_single_product() && ! is_page() && ! $archive_enabled ) {
		return false;
	}

	$product_id = intval( somdn_get_product_id( $product ) );

	if ( $shortcode == true || $archive == true ) {
		$valid_default = somdn_is_product_valid( $product_id );
		if ( ! $valid_default ) {
			return;
		}
	} else {
		$valid_no_login = somdn_is_product_valid( $product_id, false );
		if ( ! $valid_no_login ) {
			return;
		}
	}

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

		if ( isset( $singleoptions['somdn_single_button_text'] ) && ! empty( $singleoptions['somdn_single_button_text'] ) ) {
			$buttontext = esc_html( $singleoptions['somdn_single_button_text'] );
		} else {
			$buttontext = __( 'Download Now', 'somdn-pro' );
		}

	} else {

		if ( isset( $multioptions['somdn_multi_button_text'] ) && ! empty( $multioptions['somdn_multi_button_text'] ) ) {
			$buttontext = esc_html( $multioptions['somdn_multi_button_text'] );
		} else {
			$buttontext = __( 'Download All', 'somdn-pro' );
		}
	
	}
	
	$single_type = ( isset( $singleoptions['somdn_single_type'] ) && 2 == $singleoptions['somdn_single_type'] ) ? 2 : 1 ;
	
	$pdf_default = __( 'Download PDF', 'somdn-pro' );
	$pdf_output = false;

	if ( ! $archive_enabled ) {
		$archive_enabled = ( isset( $genoptions['somdn_include_archive_items'] ) && $genoptions['somdn_include_archive_items'] ) ? true : false ;
	}

	if ( $archive_enabled && $archive && ! $shortcode ) {

		if ( isset( $genoptions['somdn_read_more_text'] ) && ! empty( $genoptions['somdn_read_more_text'] ) ) {
			$buttontext = esc_html(  $genoptions['somdn_read_more_text'] );
		} else {
			$buttontext = __( 'Download', 'somdn-pro' );
		}

		$single_type = 1;
		
	}
	
	if ( $shortcode ) {
		$single_type = 1;
	}
	
	if ( $shortcode_text ) {
		$buttontext = $shortcode_text;
	}
	
	ob_start();
	
	if ( ( is_page() || somdn_is_single_product() ) && ! $archive ) {
		echo somdn_hide_cart_style();
	}

	if ( $is_single_download ) {

		if ( empty( $shortcode ) && ! $archive ) {
			do_action( 'somdn_before_simple_wrap', $product_id );
		}
	
		/**
		 * Load the single file only template
		 */
		$template = somdn_get_template( 'single-file' );
		if ( ! empty( $template ) ) {
			include( $template );
		}

		if ( empty( $shortcode ) && ! $archive ) {
			do_action( 'somdn_after_simple_wrap', $product_id );
		}

		if ( empty( $shortcode ) && ! $archive ) {
			do_action( 'somdn_single_errors' );
		}

	} else {

		$multi_type = ( isset( $multioptions['somdn_display_type'] ) && $multioptions['somdn_display_type'] ) ? $multioptions['somdn_display_type'] : '1' ;

		if ( $archive_enabled && $archive ) {
			$multi_type = 2;
		}

		if ( $shortcode ) {
			$multi_type = 2;
		}

		/**
		 * 1. Links Only
		 */
		if ( 1 == $multi_type ) {

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_before_simple_wrap', $product_id );
			}

			/**
			 * Load the multi-file links only template
			 */
			$template = somdn_get_template( 'multi-file-links' );
			if ( ! empty( $template ) ) {
				include( $template );
			}

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_after_simple_wrap', $product_id );
			}

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_single_errors' );
			}

		/**
		 * 2. Button Only
		 */
		} elseif ( 2 == $multi_type ) {

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_before_simple_wrap', $product_id );
			}

			/**
			 * Load the multi-file button only template
			 */
			$template = somdn_get_template( 'multi-file-button' );
			if ( ! empty( $template ) ) {
				include( $template );
			}

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_after_simple_wrap', $product_id );
			}

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_single_errors' );
			}

		/**
		 * 3. Button + Checkboxes
		 */
		} elseif ( 3 == $multi_type ) {

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_before_simple_wrap', $product_id );
			}

			if ( empty( $buttonclass) ) {
				$buttonclass = 'somdn-checkbox-submit';
			} else {
				$buttonclass .= 'somdn-checkbox-submit';
			}

			/**
			 * Load the multi-file button & checkboxes template
			 */
			$template = somdn_get_template( 'multi-file-button-checkboxes' );
			if ( ! empty( $template ) ) {
				include( $template );
			}

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_after_simple_wrap', $product_id );
			}

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_single_errors' );
			}

		/**
		 * 4. Button + Links
		 */
		} elseif ( 4 == $multi_type ) {

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_before_simple_wrap', $product_id );
			}

			/**
			 * Load the multi-file button & links template
			 */
			$template = somdn_get_template( 'multi-file-button-links' );
			if ( ! empty( $template ) ) {
				include( $template );
			}

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_after_simple_wrap', $product_id );
			}

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_single_errors' );
			}

		/**
		 * 5. Button & Filenames
		 */
		} elseif ( 5 == $multi_type ) {

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_before_simple_wrap', $product_id );
			}

			/**
			 * Load the multi-file button & filenames template
			 */
			$template = somdn_get_template( 'multi-file-button-filenames' );
			if ( ! empty( $template ) ) {
				include( $template );
			}

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_after_simple_wrap', $product_id );
			}

			if ( empty( $shortcode ) && ! $archive ) {
				do_action( 'somdn_single_errors' );
			}

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

function somdn_get_download_button( $text, $css, $archive = false, $product_id = '', $class, $all_zip = false ) {
	echo apply_filters( 'somdn_get_download_button', $button = '', $text, $css, $archive, $product_id, $class, $all_zip );
}

function somdn_get_single_download_link( $text, $css, $archive = false, $product_id = '', $class ) {
	echo apply_filters( 'somdn_get_single_download_link', $link = '', $text, $css, $archive, $product_id, $class );
}

function somdn_get_multi_download_link( $count, $css, $shownumber, $name, $class ) {
	echo apply_filters( 'somdn_get_multi_download_link', $link = '', $count, $css, $shownumber, $name, $class );
}