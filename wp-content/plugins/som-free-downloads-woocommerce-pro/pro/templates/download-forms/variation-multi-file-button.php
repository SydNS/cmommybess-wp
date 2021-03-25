<?php
/**
 * Free Downloads - WooCommerce - Multi-file button only download template
 * 
 * 
 * @version	2.4.92
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php if ( is_single() ) do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<div class="somdn-download-wrap">

	<?php

		$form_class = '';
		if ( $archive_enabled && $archive ) {
			$form_class = 'somdn-archive-download-form somdn-download-form';
		} else {
			$form_class = 'somdn-download-form';
		}

	?>

	<form class="<?php echo $form_class; ?>" action="<?php echo esc_url( get_permalink( $product_id ) ); ?>" method="post">

		<?php do_action( 'somdn_before_form_inputs_variation', $product_id, $variation_id ); ?>
					 
		<div class="somdn-form-table-bottom"<?php if ( $archive_enabled && $archive ) echo ' style="text-align: center!important;"'; ?>>
		
			<?php if ( is_single() ) do_action( 'woocommerce_before_add_to_cart_button' ); ?>

			<input type="hidden" name="action" value="somdn_download_all_files_variation">
			<input type="hidden" name="somdn_product" value="<?php echo $product_id; ?>">
			<input type="hidden" name="somdn_variation" value="<?php echo $variation_id; ?>">
			<input type="hidden" name="somdn_totalfiles" value="<?php echo count( $downloads ); ?>">
							
			<?php do_action( 'somdn_download_button', $buttontext, $buttoncss, $archive, $product_id, $buttonclass, true ); ?>
			
			<?php if ( is_single() ) do_action( 'woocommerce_after_add_to_cart_button' ); ?>

		</div>
			 
	</form>
	 
</div>

<?php if ( is_single() ) do_action( 'woocommerce_after_add_to_cart_form' ); ?>