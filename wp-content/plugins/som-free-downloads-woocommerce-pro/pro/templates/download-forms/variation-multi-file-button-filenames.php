<?php
/**
 * Free Downloads - WooCommerce - Multi-file button & filenames download template
 * 
 * 
 * @version	2.4.92
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<?php if ( is_single() ) do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<?php if ( is_single() ) do_action( 'woocommerce_before_add_to_cart_button' ); ?>

<div class="somdn-download-wrap">

	<?php somdn_get_available_downloads_text(); ?>

	<form action="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="somdn-download-form" method="post" class="somdn-button-form">

		<?php do_action( 'somdn_before_form_inputs_variation', $product_id, $variation_id ); ?>
			 
		<p>
			 
			<?php
					 
			$count = 0;
						 
			foreach( $downloads as $key => $each_download )  {
						 
				$count++;
								 
				if ( $shownumber ) {
					$shownumber = $count . '. ';
				} else {
					$shownumber = '';
			} ?>

			<span style="display: inline-block;" class="somdn-download-filename"><?php echo $shownumber . $each_download['name']; ?></span><br>
				 
			<?php } ?>
					 
		</p>
					 
		<div class="somdn-form-table-bottom">

			<input type="hidden" name="action" value="somdn_download_all_files_variation">
			<input type="hidden" name="somdn_product" value="<?php echo $product_id; ?>">
			<input type="hidden" name="somdn_variation" value="<?php echo $variation_id; ?>">
			<input type="hidden" name="somdn_totalfiles" value="<?php echo count( $downloads ); ?>">

			<?php do_action( 'somdn_download_button', $buttontext, $buttoncss, $archive, $product_id, $buttonclass ); ?>
			
			<?php if ( is_single() ) do_action( 'woocommerce_after_add_to_cart_button' ); ?>
						
		</div>
			 
	</form>
	 
</div>

<?php if ( is_single() ) do_action( 'woocommerce_after_add_to_cart_form' ); ?>