<?php
/**
 * Free Downloads - Woo QuickView Template
 * 
 * 
 * @version	3.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php
	global $product;
	$product_id = intval( somdn_get_product_id( $product ) );
	if ( empty( $product_id ) ) {
		return;
	}
?>

<div class="somdn-qview-wrap">

	<div class="somdn-qview-wrap-bg"></div>

	<div class="somdn-qview-window-wrap">

		<div class="somdn-qview-window">

			<div class="somdn-qview-body">
			
				<button title="Close (Esc)" type="button" class="somdn-qview-close">×</button>

					<?php

						$src = '';

						if ( ! empty( $variation_id ) ) {
							$src = get_the_post_thumbnail_url( $variation_id, 'large' );
						}

						if ( empty( $src ) ) {
							$src = get_the_post_thumbnail_url( $product_id, 'large' );
						}

						if ( empty( $src ) ) {
							$src = wc_placeholder_img_src();
						}

					?>

				<div class="somdn-qview-left somdn-qview-img-bg" style="background-image: url(<?php echo $src; ?>)">
					<?php echo '<img class="somdn-qview-image" src="' . $src . '">'; ?>
				</div>

				<div class="somdn-qview-right">

					<div class="somdn-qview-summary">
						<!-- Product Title -->
						<?php do_action( 'somdn_before_quickview_title_wrap' ); ?>

						<div class="somdn-qview-title-wrap">
							<?php do_action( 'somdn_before_quickview_title' ); ?>

							<?php $link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink(), $product );
							echo '<a href="' . esc_url( $link ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">'; ?>
								<h2 class="somdn-qview-title"><?php the_title();?></h2>
							<?php echo '</a>'; ?>

							<?php do_action( 'somdn_after_quickview_title' ); ?>
						</div>

						<?php do_action( 'somdn_after_quickview_title_wrap' ); ?>
			
						<!-- Product Price -->
						<?php do_action( 'somdn_before_quickview_price' ); ?>
						<?php if ( $price_html = $product->get_price_html() ) : ?>
							<span class="price somdn-qview-price"><?php echo $price_html; ?></span>
						<?php endif; ?>
						<?php do_action( 'somdn_after_quickview_price' ); ?>
			
						<!-- Product short description -->
						<?php do_action( 'somdn_before_quickview_excerpt' ); ?>
						<?php woocommerce_template_single_excerpt(); ?>
						<?php do_action( 'somdn_after_quickview_excerpt' ); ?>

						<!-- Product cart link -->
						<?php do_action( 'somdn_before_quickview_cart' ); ?>
						<?php //echo do_shortcode( '[download_now id="' . $product_id . '"]' ); ?>
						<?php //woocommerce_template_loop_add_to_cart(); ?>
						<?php somdn_quickview_add_to_cart( $product, $product_id ); ?>
						<?php do_action( 'somdn_after_quickview_cart' ); ?>
					</div>

				</div>

			</div>

		</div>

	</div>

</div>