<?php
/**
 * Free Downloads - Multi-file links only download template
 * 
 * 
 * @version	2.4.92
 */

if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if ( is_single() ) do_action( 'somdn_before_add_to_cart_form' ); ?>

<?php if ( is_single() ) do_action( 'somdn_before_add_to_cart_button' ); ?>

<?php somdn_get_available_downloads_text(); ?>

<div class="somdn-download-wrap">
 
	<?php
					 
	$count = 0;

	foreach( $downloads as $key => $each_download )  {
					 
		$count++;

		if ( $shownumber ) {
			$shownumber = $count . '. ';
		} else {
			$shownumber = '';
		}

		$file_path = somdn_get_download_filepath_raw( $product, $key, $each_download, $each_download['id'] );
		$ext = somdn_get_ext_from_path( $file_path );
		if ( $ext == 'pdf' && $pdfenabled ) {
			$pdf_output = true;
		} else {
			$pdf_output = false;
		}

		?>

		<form class="somdn-download-form" action="<?php echo esc_url( get_permalink( $product_id ) ); ?>" method="post" id="somdn-md-form-<?php echo $count; ?>">

			<?php do_action( 'somdn_before_form_inputs_simple', $product_id ); ?>
								 
			<div class="somdn-form-table-bottom">

				<input type="hidden" name="action" value="somdn_download_multi_single">
				<input type="hidden" name="somdn_product" value="<?php echo $product_id; ?>">
				<input type="hidden" name="somdn_productfile" value="<?php echo $count; ?>">
				
				<?php if ( $pdf_output ) { ?>
				<input type="hidden" name="pdf" value="true">
				<?php } ?>

				<?php do_action( 'somdn_multi_download_link', $count, $linkcss, $shownumber, $each_download['name'], $linkclass ); ?>
										 
			</div>
						 
		</form>               
			 
	<?php } ?>
	
	<?php if ( is_single() ) do_action( 'somdn_after_add_to_cart_button' ); ?>
					 
</div>

<?php if ( is_single() ) do_action( 'somdn_after_add_to_cart_form' ); ?>