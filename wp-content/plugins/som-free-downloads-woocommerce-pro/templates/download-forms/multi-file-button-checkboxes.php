<?php
/**
 * Free Downloads - Multi-file button & checkboxes download template
 * 
 * 
 * @version	3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if ( is_single() ) do_action( 'somdn_before_add_to_cart_form' ); ?>

<?php if ( is_single() ) do_action( 'somdn_before_add_to_cart_button' ); ?>

<div class="somdn-download-wrap">

	<?php somdn_get_available_downloads_text(); ?>
	 
	<form class="somdn-download-form somdn-checkbox-form" action="<?php echo esc_url( get_permalink( $product_id ) ); ?>" method="post">

		<?php do_action( 'somdn_before_form_inputs_simple', $product_id ); ?>
					 
		<div class="somdn-form-table-bottom somdn-form-validate">
			<p class="somdn-warn-text"><strong><?php echo somdn_get_checkbox_error_text(); ?></strong></p>
		</div>

		<?php

		$count = 0;

		foreach( $downloads as $key => $each_download )  {
				 
			$count++; ?>
						 
			<div class="somdn-form-table-bottom somdn-checkboxes-wrap">
				 
				<input class="somdn-inline-block somdn-checkbox-form-checkbox" type="checkbox" id="somdn_download_file_<?php echo $count . '-' . $product_id; ?>" name="somdn_download_file_<?php echo $count; ?>" value="1">
				<label class="somdn-inline-block" for="somdn_download_file_<?php echo $count . '-' . $product_id; ?>"><?php echo esc_html( $each_download['name'] ); ?></label>

			</div>
		 
		<?php } ?>
				 
		<?php if ( isset( $multioptions['somdn_select_all'] ) && $multioptions['somdn_select_all'] ) { ?>

			<div class="somdn-form-table-bottom somdn-checkboxes-wrap somdn-select-all-wrap">
					 
				<input class="somdn-inline-block somdn-checkbox-all" type="checkbox" id="somdn_download_files_all" name="somdn_download_files_all">
				<label class="somdn-inline-block" for="somdn_download_files_all"><?php _e( 'Select All', 'somdn-pro' ); ?></label>
							 
			</div>

		<?php } ?>

		<div class="somdn-form-table-bottom somdn-checkboxes-button-wrap">

			<input type="hidden" name="action" value="somdn_download_multi_checked">
			<input type="hidden" name="somdn_product" value="<?php echo $product_id; ?>">
			<input type="hidden" name="somdn_totalfiles" value="<?php echo count( $downloads ); ?>">
										
			<?php do_action( 'somdn_download_button', $buttontext, $buttoncss, $archive, $product_id, $buttonclass ); ?>
			
			<?php if ( is_single() ) do_action( 'somdn_after_add_to_cart_button' ); ?>
										
		</div>

	</form>

</div>

<?php if ( is_single() ) do_action( 'somdn_after_add_to_cart_form' ); ?>