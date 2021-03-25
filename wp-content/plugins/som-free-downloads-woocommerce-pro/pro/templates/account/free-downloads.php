<?php
/**
 * Free Downloads - WooCommerce - Free downloads history
 *
 *
 * @version	3.0.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$track_options = get_option( 'somdn_pro_track_settings' );
$title = isset( $options['somdn_download_history_title_enable'] ) ? $options['somdn_download_history_title_enable'] : 0 ;
$title_text = isset( $options['somdn_download_history_title'] ) ? $options['somdn_download_history_title'] : '' ;
$message = isset( $options['somdn_download_history_message'] ) ? $options['somdn_download_history_message'] : '' ;
$button_text = isset( $options['somdn_download_history_button'] ) ? $options['somdn_download_history_button'] : '' ;

$hide_puchased = isset( $track_options['somdn_download_history_hide_purchase'] ) ? $track_options['somdn_download_history_hide_purchase'] : 0 ;

if ( $hide_puchased ) {
	$downloads = WC()->customer->get_downloadable_products();
	$has_downloads = (bool) $downloads;
	if ( ! $has_downloads ) { ?>
		<style>
		body.woocommerce-downloads .woocommerce-MyAccount-content .woocommerce-order-downloads:not(.somdn-account-downloads-section),
		body.woocommerce-downloads .woocommerce-Message.woocommerce-Message--info.woocommerce-info {
			display: none!important;
		}
		</style>
	<?php }
}


if ( ! empty( $title ) ) {
	if ( empty( $title_text ) ) {
		$title_text = __( 'Free Downloads', 'somdn-pro' );
	} else {
		$title_text = esc_html( $title_text );
	}
}

$allowed_tags = somdn_get_allowed_html_tags();

if ( ! empty( $message ) ) {
	$message = wpautop( wp_kses( $message, $allowed_tags ) );
}

if ( empty( $button_text ) ) {
	$button_text = __( 'Download', 'somdn-pro' );
} else {
	$button_text = esc_html( $button_text );
}

$somdn_download_history = get_posts(
	array(
		'fields' => 'ids',
		'posts_per_page' => -1,
		'post_type' => 'somdn_tracked',
		'meta_key' => 'somdn_user_id',
		'meta_value' => get_current_user_id(),
	)
);

if (empty($somdn_download_history)) {
	return;
}

?>
<section class="woocommerce-order-downloads somdn-account-downloads-section">

	<?php if ( ! empty( $title ) ) { ?>
		<h2 class="woocommerce-order-downloads__title"><?php echo $title_text; ?></h2>
	<?php } ?>

	<?php if ( ! empty( $message ) ) { ?>
		<div class="somdn-account-downloads-message"><?php echo $message; ?></div>
	<?php } ?>

	<?php do_action( 'somdn_after_account_downloads_title' ); ?>
	<table class="somdn-account-downloads-table woocommerce-table woocommerce-table--order-downloads shop_table order_details">
		<thead>
			<tr>
				<?php foreach ( somdn_get_account_downloads_columns() as $column_id => $column_name ) : ?>
				<th class="somdn-<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
		</thead>

		<?php

			$owned_settings = get_option( 'somdn_owned_settings' );
			$include_owned = ( isset( $owned_settings['somdn_owned_enable'] ) && $owned_settings['somdn_owned_enable'] ) ? true : false ;
			$hide_owned_history = apply_filters( 'somdn_hide_owned_download_history', false );

			//var_dump($somdn_download_history);

			// Create an array to hold unique product/variation IDs
			$somdn_download_ids = array();

			// Loop through the tracked downloads
			foreach( $somdn_download_history as $download ) {

				$product_id = get_post_meta( $download, 'somdn_product_id', true);
				$variation_id = get_post_meta( $download, 'somdn_variation_id', true );

				$product_id = ! empty( $product_id ) ? intval( $product_id ) : null ;
				$variation_id = ! empty( $variation_id ) ? intval( $variation_id ) : null ;

				if ( $include_owned && $hide_owned_history ) {
					$owned = somdn_is_download_owned( false, '', $product_id );
					if ( $owned ) {
						continue;
					}
				}

				if ( $variation_id && $product_id ) {
					if ( ! in_array( $variation_id, $somdn_download_ids ) ) {
						$somdn_download_ids[$variation_id] = array(
							'type' => 'variation',
							'product' => $product_id
						);
					}
				} elseif ( $product_id ) {
					if ( ! in_array( $product_id, $somdn_download_ids ) ) {
						$somdn_download_ids[$product_id] = array(
							'type' => 'product',
							'product' => $product_id
						);
					}
				}

			}

			//var_dump($somdn_download_ids);
			//somdn_debug_array( $somdn_download_ids );

		?>
		<?php foreach ( $somdn_download_ids as $somdn_download => $data ) :

			$type = isset( $data['type'] ) && ! empty( $data['type'] ) ? sanitize_text_field( $data['type'] ) : null ;
			$product_id = isset( $data['product'] ) && ! empty( $data['product'] ) ? intval( $data['product'] ) : null ;
			$variation_id = '';

			//echo '$somdn_download = ' . $somdn_download . '<br>';
			//continue;

			//echo "<strong>Product = $somdn_download</strong><br>";
			//somdn_debug_array( $somdn_download );

			if ( $type === 'variation' ) {

				$variation_id = intval( $somdn_download );

				if ( somdn_is_variable_product_valid( $product_id, $variation_id ) ) { ?>

					<tr>
						<?php foreach ( somdn_get_account_downloads_columns() as $column_id => $column_name ) : ?>
							<td class="somdn-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>"><?php
								if ( has_action( 'somdn_account_downloads_column_' . $column_id ) ) {
									do_action( 'somdn_account_downloads_column_' . $column_id, $product_id );
								} else {
									switch ( $column_id ) {
										case 'download-product' : ?>
											<a href="<?php echo esc_url( get_permalink( $variation_id ) ); ?>"><?php echo get_the_title( $variation_id ); ?></a>
											<?php
										break;
										case 'download-file' : ?>
											<?php echo do_shortcode( '[download_now id="'.$product_id.'" variation ="'.$variation_id.'" text="'.$button_text.'"]' ); ?>
											<?php
										break;
									}
								}
							?></td>
						<?php endforeach; ?>
					</tr>

				<?php }

			} else {

				if ( somdn_is_product_valid( $product_id ) ) { ?>

					<tr>
						<?php foreach ( somdn_get_account_downloads_columns() as $column_id => $column_name ) : ?>
							<td class="somdn-<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>"><?php
								if ( has_action( 'somdn_account_downloads_column_' . $column_id ) ) {
									do_action( 'somdn_account_downloads_column_' . $column_id, $product_id );
								} else {
									switch ( $column_id ) {
										case 'download-product' : ?>
											<a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>"><?php echo get_the_title( $product_id ); ?></a>
											<?php
										break;
										case 'download-file' : ?>
											<?php echo do_shortcode( '[download_now id="'.$product_id.'" text="'.$button_text.'"]' ); ?>
											<?php
										break;
									}
								}
							?></td>
						<?php endforeach; ?>
					</tr>

				<?php }

			}

		?>

		<?php endforeach; ?>
	</table>
</section>
