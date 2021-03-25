<?php
/**
 * Free Downloads - Shortcodes
 * 
 * @version	3.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_shortcode( 'download_now', 'somdn_single_shortcode' );
function somdn_single_shortcode( $atts ) {

	// Attributes
	$atts = shortcode_atts(
		array(
			'id' => '',
			'align' => 'left',
			'text' => ''
		),
		$atts,
		'download_now'
	);
	
	$product_id = $atts['id'];
	$align = $atts['align'];
	$shortcode_text = $atts['text'];

	// Bail if no product ID
	if ( empty( $product_id ) )
		return;

	$product = somdn_get_product( $product_id );

	// Bail if no product matches the productID
	if ( ! $product )
		return;

	$download_button = somdn_get_shortcode_product_content( $product_id, $shortcode_text );

	// Bail if no download button returned
	if ( ! $download_button ) {
		return;
	}

	$content = '<div class="somdn-shortcode-wrap ' . $align . '">' . $download_button . '</div>';
	return $content;

}

function somdn_get_shortcode_product_content( $product_id, $shortcode_text ) {

	$archive = true;
	$archive_enabled = true;

	$product = somdn_get_product( $product_id );
	if ( ! $product ) {
		return;
	}

	$valid_shortcode = somdn_is_product_valid( $product_id );
	if ( ! $valid_shortcode ) {
		return;
	}

	$downloads = somdn_get_files( $product );

	$downloads_count = count( $downloads );
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

	if ( $is_single_download ) {

		$single_type = 1;

		/**
		 * Load the single file only template
		 */
		include( SOMDN_PATH . 'templates/download-forms/single-file.php' );

	} else {

		/**
		 * Load the multi-file button only template
		 */
		include( SOMDN_PATH . 'templates/download-forms/multi-file-button.php' );

	}

	$content = ob_get_clean();
		
	return $content;

}

add_shortcode( 'download_now_page', 'somdn_single_shortcode_page' );
function somdn_single_shortcode_page( $atts ) {

	// Attributes
	$atts = shortcode_atts(
		array(
			'id' => '',
			'text' => ''
		),
		$atts,
		'download_now_page'
	);
	
	$product_id = $atts['id'];
	$text = $atts['text'];

	if ( ! $product_id ) {

		$product = somdn_get_product();

		if ( ! $product ) {
			return;
		}

	} else {

		$product = somdn_get_product( $product_id );

	}

	if ( ! $product ) {
		return;
	}

	$args = apply_filters( 'somdn_single_shortcode_page_args', array(
		'product' => $product,
		'echo' => false,
		'shortcode_text' => $text,
		'product_page_short' => true
	) );

	$content = somdn_product_page( $args );

	//$content = somdn_product_page( false, $product, false, false, false, $text, true ); // False $shortcode to replicate product page
	// function args = ( $archive = false, $product = '', $echo = true, $archive_enabled = false, $shortcode = false, $shortcode_text = '', $product_page_short = false )
	
	if ( ! $content ) {
		return;
	}

	return $content;

}