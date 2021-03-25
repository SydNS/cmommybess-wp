<?php
/**
 * Free Downloads - WooCommerce - Free downloads history
 * 
 * 
 * @version	3.0.4
 */

if ( ! defined( 'ABSPATH' ) ) exit;

	$download_limits = somdn_get_user_limits( get_current_user_id() );
	
	// Bail if user has no download limits
	if ( empty( $download_limits ) ) return;

	$limits_type   = $download_limits['type'];
	$limits_amount = $download_limits['amount'];
	if ( empty( $limits_amount ) ) {
		$limits_amount = 'Unlimited';
	}
	$limits_products = $download_limits['products'];
	if ( empty( $limits_products ) ) {
		$limits_products = 'Unlimited';
	}
	$limits_freq   = $download_limits['freq'];
	$limits_error  = $download_limits['error'];
	$freq_name     = $download_limits['freq_name'];

	//echo '<pre>';
	//print_r($download_limits);
	//echo '</pre>';

	$limit_options = get_option( 'somdn_pro_basic_limit_settings' );
	$title = isset( $options['somdn_pro_limit_acc_page_title_enable'] ) ? $options['somdn_pro_limit_acc_page_title_enable'] : 0 ;
	$title_text = isset( $options['somdn_pro_limit_acc_page_title'] ) ? $options['somdn_pro_limit_acc_page_title'] : '' ;
	$message = isset( $options['somdn_pro_limit_acc_page_message'] ) ? $options['somdn_pro_limit_acc_page_message'] : '' ;

	if ( ! empty( $title ) ) {
		if ( empty( $title_text ) ) {
			$title_text = __( 'Free Download Limits', 'somdn-pro' );
		} else {
			$title_text = esc_html( $title_text );
		}
	}

	$allowed_tags = somdn_get_allowed_html_tags();

	if ( ! empty( $message ) ) {
		$message = wpautop( wp_kses( $message, $allowed_tags ) );
	}

?>
<section class="woocommerce-order-downloads somdn-account-downloads-section somdn-account-download-limits">

	<?php if ( ! empty( $title ) ) { ?>
		<h2 class="woocommerce-order-downloads__title"><?php echo $title_text; ?></h2>
	<?php } ?>

	<?php if ( ! empty( $message ) ) { ?>
		<div class="somdn-account-downloads-message"><?php echo $message; ?></div>
	<?php } ?>

	<?php do_action( 'somdn_after_account_limits_title' ); ?>

	<div class="somdn-account-download-limits-wrap">
		<?php if ( $limits_products == 'Unlimited' ) { ?>
			<div class="somdn-account-download-limits-current">
				<span><?php printf( __( 'Current %1s: %2s Downloads', 'somdn-pro' ), ucfirst( $freq_name ), somdn_get_user_downloads_count( get_current_user_id() ), $limits_products ); ?></span>
			</div>
		<?php } elseif ( $limits_amount == 'Unlimited' ) { ?>	
			<div class="somdn-account-download-limits-current">
				<span><?php printf( __( 'Current %1s: %2s Products', 'somdn-pro' ), ucfirst( $freq_name ), somdn_get_user_downloads_count( get_current_user_id(), true ) ); ?></span>
			</div>
		<?php } else { ?>
			<div class="somdn-account-download-limits-current">
				<span><?php printf( __( 'Current %1s: %2s Downloads / %3s Products', 'somdn-pro' ), ucfirst( $freq_name ), somdn_get_user_downloads_count( get_current_user_id() ), somdn_get_user_downloads_count( get_current_user_id(), true ) ); ?></span>
			</div>
		<?php } ?>
		<div class="somdn-account-download-limits-remain">
			<span><?php printf( __( 'Download Allowance: %s', 'somdn-pro' ), $limits_amount ); ?></span>
		</div>
		<div class="somdn-account-download-limits-remain">
			<span><?php printf( __( 'Product Allowance: %s', 'somdn-pro' ), $limits_products ); ?></span>
		</div>
	</div>

	<?php do_action( 'somdn_after_account_limits' ); ?>

</section>
