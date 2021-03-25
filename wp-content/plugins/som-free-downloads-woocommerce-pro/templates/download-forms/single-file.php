<?php
/**
 * Free Downloads - Single file download template
 * 
 * 
 * @version	2.4.92
 */

if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php if ( is_single() ) do_action( 'somdn_before_add_to_cart_form' ); ?>

<div class="somdn-download-wrap">

	<?php

		if ( $pdfenabled ) {

			foreach( $downloads as $key => $each_download ) {
				$file_path = somdn_get_download_filepath_raw( $product, $key, $each_download, $each_download['id'] );
				$ext = somdn_get_ext_from_path( $file_path );
				if ( $ext == 'pdf' ) {

					$pdf_output = true;

					if ( isset( $docoptions['somdn_docviewer_single_link_text'] ) && ! empty( $docoptions['somdn_docviewer_single_link_text'] ) ) {
						$buttontext = esc_html( $docoptions['somdn_docviewer_single_link_text'] );
					} else {
						$buttontext = $pdf_default;
					}

				} else {

					$pdf_output = false;

				}

			}

		}

		// If the output is a single product page summary and the option set set, the download text should be the filename
		if ( empty( $shortcode ) && ! $archive ) {
			if ( isset( $singleoptions['somdn_single_button_filename'] ) && ! empty( $singleoptions['somdn_single_button_filename'] ) ) {
				foreach( $downloads as $key => $each_download ) {
					$buttontext = esc_html( $each_download['name'] );
				}
			}
		}

	?>

	<?php

		$form_class = '';
		if ( $archive_enabled && $archive ) {
			$form_class = 'somdn-archive-download-form somdn-download-form';
		} else {
			$form_class = 'somdn-download-form';
		}

	?>

	<form class="<?php echo esc_attr( $form_class ); ?>" action="<?php echo esc_url( get_permalink( $product_id ) ); ?>" method="post">

			<?php do_action( 'somdn_before_form_inputs_simple', $product_id ); ?>
		
			<?php if ( is_single() ) do_action( 'somdn_before_add_to_cart_button' ); ?>

			<input type="hidden" name="action" value="somdn_download_single">
			<input type="hidden" name="somdn_product" value="<?php echo $product_id; ?>">
			
			<?php if ( $pdf_output ) { ?>
			<input type="hidden" name="pdf" value="true">
			<?php } ?>
			
			<?php

			if ( $pdf_output ) { ?>

				<?php if ( isset( $docoptions['somdn_docviewer_single_display'] ) && 2 == $docoptions['somdn_docviewer_single_display'] ) { ?>
				
					<?php if ( $archive_enabled && $archive ) { ?>
					
						<?php do_action( 'somdn_download_button', $buttontext, $buttoncss, $archive, $product_id, $buttonclass ); ?>
					
					<?php } else { ?>
					
						<?php do_action( 'somdn_single_download_link', $buttontext, $linkcss, $archive, $product_id, $linkclass ); ?>
						
					<?php } ?>
					
				<?php } else { ?>

					<?php do_action( 'somdn_download_button', $buttontext, $buttoncss, $archive, $product_id, $buttonclass ); ?>

				<?php } ?>

			<?php } else { ?>

				<?php if ( $single_type == 2 ) { ?>
				
					<?php do_action( 'somdn_single_download_link', $buttontext, $linkcss, $archive, $product_id, $linkclass ); ?>
					
				<?php } else { ?>
				
					<?php do_action( 'somdn_download_button', $buttontext, $buttoncss, $archive, $product_id, $buttonclass ); ?>
					
				<?php } ?>

			<?php } ?>
			
			<?php if ( is_single() ) do_action( 'somdn_after_add_to_cart_button' ); ?>


	</form>
	
</div>

<?php if ( is_single() ) do_action( 'somdn_after_add_to_cart_form' ); ?>