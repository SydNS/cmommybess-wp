<?php
/**
 * Grouped product add to cart (taken from WooCommerce and amended)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/grouped.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.3.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $post;

$genoptions = get_option( 'somdn_gen_settings' );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<div>
	<table cellspacing="0" class="woocommerce-grouped-product-list group_table somdn-grouped-table">
		<tbody>
			<?php
			$quantites_required      = false;
			$previous_post           = $post;
			$grouped_product_columns = apply_filters( 'woocommerce_grouped_product_columns', array(
				'quantity',
				'label',
				'price',
			), $product );

			foreach ( $grouped_products as $grouped_product ) {
				$post_object        = get_post( $grouped_product->get_id() );
				$quantites_required = $quantites_required || ( $grouped_product->is_purchasable() && ! $grouped_product->has_options() );
				$post               = $post_object; // WPCS: override ok.
				setup_postdata( $post );

				$product_id = $grouped_product->get_id();

				$valid_download = somdn_is_product_valid( $product_id );
				$valid_download_no_login = somdn_is_product_valid( $product_id, false );
				$logged_in = is_user_logged_in();

				$login_message = ( isset( $genoptions['somdn_require_login_grouped_message'] ) && $genoptions['somdn_require_login_grouped_message'] )
				? wpautop( wp_kses( $genoptions['somdn_require_login_grouped_message'], $allowed_tags ) )
				: wpautop( __( 'Only registered users can download the free product part of this group.', 'somdn-pro' ) );

				if ( $valid_download_no_login && ! $logged_in ) {
					echo '<tr id="product-' . esc_attr( get_the_ID() ) . '" class="woocommerce-grouped-product-list-item ' . esc_attr( implode( ' ', get_post_class() ) ) . ' somdn-group-not-valid">';
				} else {

					echo '<tr id="product-' . esc_attr( get_the_ID() ) . '" class="woocommerce-grouped-product-list-item ' . esc_attr( implode( ' ', get_post_class() ) ) . '">';

				}

				// Output columns for each product.
				foreach ( $grouped_product_columns as $column_id ) {
					do_action( 'woocommerce_grouped_product_list_before_' . $column_id, $grouped_product );

					switch ( $column_id ) {
						case 'quantity':

							if ( ! $valid_download && $valid_download_no_login ) :
								$value = '';
								break;
							endif;

							ob_start();

							$args = array(
								'somdn_group_loop' => true,
								'somdn_valid_download' => $valid_download,
								'valid_download_no_login' => $valid_download_no_login,
								'somdn_logged_in' => $logged_in
							);

							somdn_loop_add_to_cart_grouped( $args );

							$value = ob_get_clean();
							break;
						case 'label':
							$value  = '<label for="product-' . esc_attr( $grouped_product->get_id() ) . '">';
							$value .= $grouped_product->is_visible() ? '<a href="' . esc_url( apply_filters( 'woocommerce_grouped_product_list_link', get_permalink( $grouped_product->get_id() ), $grouped_product->get_id() ) ) . '">' . $grouped_product->get_name() . '</a>' : $grouped_product->get_name();
							$value .= '</label>';

							if ( ! $valid_download && $valid_download_no_login ) :
								$value .= $login_message;
							endif;

							break;
						case 'price':
							$value = $grouped_product->get_price_html() . wc_get_stock_html( $grouped_product );
							break;
						default:
							$value = '';
							break;
					}

					if ( ! $valid_download && $valid_download_no_login && $column_id == 'quantity' ) {
						// do nothing
					} elseif ( ! $valid_download && $valid_download_no_login && $column_id == 'label' ) {
						echo '<td colspan="2" class="woocommerce-grouped-product-list-item__' . esc_attr( $column_id ) . '">' . apply_filters( 'woocommerce_grouped_product_list_column_' . $column_id, $value, $grouped_product ) . '</td>'; // WPCS: XSS ok.
					} else {
						echo '<td class="woocommerce-grouped-product-list-item__' . esc_attr( $column_id ) . '">' . apply_filters( 'woocommerce_grouped_product_list_column_' . $column_id, $value, $grouped_product ) . '</td>'; // WPCS: XSS ok.
					}

					do_action( 'woocommerce_grouped_product_list_after_' . $column_id, $grouped_product );
				}

				echo '</tr>';
			}
			$post = $previous_post; // WPCS: override ok.
			setup_postdata( $post );
			?>
		</tbody>
	</table>

</div>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
