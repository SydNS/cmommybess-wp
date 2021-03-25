<?php
/**
 * Free Downloads - WooCommerce - Pro Variation Download Page
 * 
 * Various functions.
 * 
 * @version	1.1.12
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//add_action( 'woocommerce_single_product_summary', 'somdn_variable_product_page', 35 );
//add_action( 'woocommerce_variable_add_to_cart', 'somdn_variable_product_page', 35 );

function somdn_load_product_page_content_woo_variations() {
	$action = sanitize_text_field( apply_filters( 'somdn_product_page_content_woo_variations', 'woocommerce_variable_add_to_cart' ) );
	$priority = intval( apply_filters( 'somdn_product_page_content_woo_variations_priority', 35 ) );
	add_action( $action, 'somdn_variable_product_page', $priority );
}

function somdn_variable_product_page( $args = array() ) {

	$defaults = array(
		'archive' => false,
		'product' => '',
		'echo' => true,
		'archive_enabled' => false,
		'shortcode' => false,
		'shortcode_text' => '',
		'product_page_short' => false
	);

	$somdn_args = apply_filters( 'somdn_variable_product_page_args', wp_parse_args( $args, $defaults ), $args, $defaults );

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

	if ( $product->is_type( 'simple' ) ) {
		return false;
	}

	if ( ! $product->is_type( 'variable' ) ) {
		return false;
	}

	$genoptions = get_option( 'somdn_gen_settings' );
	$available_variations = $product->get_available_variations();

	do_action( 'somdn_variation_errors' );

	foreach ( $available_variations as $key => $variation ) {

		$variation_id = $variation['variation_id'];
		$product_id = intval( somdn_get_product_id( $product ) );

		if ( somdn_is_variable_product_valid( $product_id, $variation_id, false ) ) {

			$genoptions = get_option( 'somdn_gen_settings' );
			$requirelogin = isset( $genoptions['somdn_require_login'] ) ? true : false ;

			if ( ! is_user_logged_in() && $requirelogin ) {

				$allowed_tags = somdn_get_allowed_html_tags();

				$login_message = ( isset( $genoptions['somdn_require_login_message'] ) && $genoptions['somdn_require_login_message'] )
				? wpautop( wp_kses( $genoptions['somdn_require_login_message'], $allowed_tags ) )
				: __( 'Only registered users can download this free product.', 'somdn-pro' ); ;
				ob_start(); ?>

				<?php echo '<div class="somdn-variable-anchor" data-somdn-anchor-var-id="' . $variation_id . '"></div>'; ?>
				<?php echo '<div style="padding-top: 1em;" class="somdn-download-wrap-variable" data-somdn-var-id="' . $variation_id . '" id="somdn-download-variable-' . $variation_id . '">'; ?>
					<div class="somdn-download-wrap">
						<div class="<?php echo somdn_frontend_warning_class(); ?>"><?php echo $login_message; ?></div>
					</div>
				</div>

				<?php $message_content = ob_get_clean();

				if ( $echo ) {
					echo $message_content;
					return;
				} else {
					return $message_content;
				}

			}

			$variation_product = wc_get_product( $variation_id );
			$downloads = somdn_get_files( $variation_product );
			$downloads_count = count( $downloads );

			//echo 'product_id = ' . $product_id . '<br>';
			//echo 'variation_id = ' . $variation_id . '<br>';

			$is_single_download = ( 1 == $downloads_count ) ? true : false ;

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
			
			$single_type = ( isset( $singleoptions['somdn_single_type'] ) && 2 == $singleoptions['somdn_single_type'] ) ? 2 : 1 ;
			
			$pdf_default = __( 'Download PDF', 'somdn-pro' );
			$pdf_output = false;

			if ( ! $archive_enabled ) {
				$archive_enabled = ( isset( $genoptions['somdn_include_archive_items'] ) && $genoptions['somdn_include_archive_items'] ) ? true : false ;
			}

			if ( $archive_enabled && $archive && ! $shortcode ) {

				if ( isset( $genoptions['somdn_read_more_text'] ) && ! empty( $genoptions['somdn_read_more_text'] ) ) 	{
					$buttontext = esc_html( $genoptions['somdn_read_more_text'] );
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
			
			if ( ( is_page() || is_product() ) && ! $archive ) {
				//echo somdn_hide_cart_style();
			}

			//do_action( 'somdn_single_errors' );

			echo '<div class="somdn-variable-anchor" data-somdn-anchor-var-id="' . $variation_id . '"></div>';
			echo '<div style="padding-top: 1em;" class="somdn-download-wrap-variable" data-somdn-var-id="' . $variation_id . '" id="somdn-download-variable-' . $variation_id . '">';

			if ( $is_single_download ) {

				do_action( 'somdn_before_variation_wrap', $product_id, $variation_id );
			
				/**
				 * Load the single file only template
				 */
				$template = somdn_get_template( 'variation-single-file' );
				if ( ! empty( $template ) ) {
					include( $template );
				}

				do_action( 'somdn_after_variation_wrap', $product_id, $variation_id );

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

					do_action( 'somdn_before_variation_wrap', $product_id, $variation_id );

					/**
					 * Load the multi-file links only template
					 */
					$template = somdn_get_template( 'variation-multi-file-links' );
					if ( ! empty( $template ) ) {
						include( $template );
					}

					do_action( 'somdn_after_variation_wrap', $product_id, $variation_id );
				 
				/**
				 * 2. Button Only
				 */
				} elseif ( 2 == $multi_type ) {

					do_action( 'somdn_before_variation_wrap', $product_id, $variation_id );

					/**
					 * Load the multi-file button only template
					 */
					$template = somdn_get_template( 'variation-multi-file-button' );
					if ( ! empty( $template ) ) {
						include( $template );
					}

					do_action( 'somdn_after_variation_wrap', $product_id, $variation_id );

				/**
				 * 3. Button + Checkboxes
				 */
				} elseif ( 3 == $multi_type ) {

					do_action( 'somdn_before_variation_wrap', $product_id, $variation_id );
				
					/**
					 * Load the multi-file button & checkboxes template
					 */
					$template = somdn_get_template( 'variation-multi-file-button-checkboxes' );
					if ( ! empty( $template ) ) {
						include( $template );
					}

					do_action( 'somdn_after_variation_wrap', $product_id, $variation_id );

				/**
				 * 4. Button + Links
				 */
				} elseif ( 4 == $multi_type ) {

					do_action( 'somdn_before_variation_wrap', $product_id, $variation_id );

					/**
					 * Load the multi-file button & links template
					 */
					$template = somdn_get_template( 'variation-multi-file-button-links' );
					if ( ! empty( $template ) ) {
						include( $template );
					}

					do_action( 'somdn_after_variation_wrap', $product_id, $variation_id );

				/**
				 * 5. Button & Filenames
				 */
				} elseif ( 5 == $multi_type ) {

					do_action( 'somdn_before_variation_wrap', $product_id, $variation_id );

					/**
					 * Load the multi-file button & filenames template
					 */
					$template = somdn_get_template( 'variation-multi-file-button-filenames' );
					if ( ! empty( $template ) ) {
						include( $template );
					}

					do_action( 'somdn_after_variation_wrap', $product_id, $variation_id );

				}

			}

			echo '</div>';

			$content = ob_get_clean();
				
			if ( $echo ) {
				echo $content;
				//return;
			} else {
				//return $content;
			}

		}

	}

}