<?php
/**
 * Free Downloads - Woo Compatibility
 * 
 * Compatibility functions for WooCommerce
 * 
 * @version	3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function somdn_memberships() {
	if ( function_exists( 'wc_memberships' ) ) {
		return true;
	} else {
		return false;
	}
}

function somdn_is_product_valid_wo_membership( $product_id ) {
	return apply_filters( 'somdn_is_product_valid_wo_membership', false, $product_id );
}

function somdn_is_product_valid_wo_membership_basic( $valid, $product_id, $hide_readmore ) {

	$product = somdn_get_product( $product_id );

	if ( empty( $product ) ) {
		return false;
	}

	// Check if product is a valid product type for downloading free
	if ( ! somdn_is_product_valid_type( $product, $product_id ) ) {
		return false;
	}

	// If the product is free and whether it is on sale and included in free downloads
	if ( ! somdn_is_product_free( $product, $product_id ) ) {
		return false;
	}

	// Are products included individually and if so is this product included? If not return false
	if ( ! somdn_is_product_included( $product, $product_id ) ) {
		return false;
	}

	// If this product has no files for download, return false
	if ( ! somdn_product_has_downloads( $product, $product_id ) )  {
		return false;
	}

	return true;

}

function somdn_product_valid_compat_woo_basic( $valid, $product, $product_id ) {

	if ( function_exists( 'wc_memberships' ) ) {
	
		$postype = get_post_type( $product_id );

		/**
		 * Membership option values
		 * 
		 * 1 = Include Membership restricted items (default)
		 * 2 = Exclude Membership restricted items
		 * 3 = Members only
		 * 
		 */
		$membership_options = get_option( 'somdn_memberships_settings' );
		$option = ( isset( $membership_options['somdn_memberships_global'] ) && $membership_options['somdn_memberships_global'] ) ? $membership_options['somdn_memberships_global'] : 1 ;
		
		$has_access = somdn_is_user_member_purchase( $product_id );

		/**
		 * If product is a membership plan, prevent.
		 */
		if ( 'wc_membership_plan' == $postype ) {
			return false;
		}

		/**
		 * If product is restricted and restricted items are excluded, prevent.
		 */
		if ( somdn_is_member_restricted( $product_id ) && $option == 2 ) {
			return false;
		}

		/**
		 * If product is not restricted and only restricted items allowed, prevent.
		 * This prevents free download of any other other products entirely.
		 */
		if ( ! somdn_is_member_restricted( $product_id ) && $option == 3 ) {
			return false;
		}

		/**
		 * If product is restricted and the user has access, and only restricted items allowed, allow.
		 */	
		if ( ( $has_access && $option == 3 ) ) {
			return true;
		}

		/**
		 * If the user has a membership that allows free downloads.
		 */	
		if ( ( $option == 4 ) ) {
			if ( somdn_user_has_free_membership( $product_id ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Default behaviour. If restricted and user does not have access, prevent.
		 */	
		if ( ! $has_access ) {
			return false;
		}

	}

	if ( class_exists( 'WC_Subscriptions_Product' ) ) {
		$subscriptions = array( 'subscription', 'variable-subscription' );
		if ( has_term( $subscriptions, 'product_type', $product_id ) ) {
			return false;
		}
	}

	return true;

}

function somdn_user_has_free_membership( $product_id ) {

	if ( ! is_user_logged_in() ) return false;

	$plans = wc_memberships_get_membership_plans();
	$plan_ids = array();
	$included_plans = array();

	if ( ! empty( $plans ) ) {
		foreach ( $plans as $plan ) {
			$plan_id = $plan->get_id();
			$plan_ids[] = $plan_id;
			$included = get_post_meta( $plan_id, 'somdn_membership_include_free_download', true );
			if ( ! empty( $included ) ) {
				$included_plans[] = $plan_id;
				if ( wc_memberships_is_user_active_member( get_current_user_id(), $plan_id ) ) {
					return true;
				}
			}
		}
	}

	return false;

}

function somdn_is_product_member_free( $free, $product, $product_id ) {

	/**
	 * Check if product has a 100% membership discount and free discounted products are included.
	 *
	 * @since 2.4.2
	 * @param bool $free Boolean for whether this product is free
	 * @param int $product_id WooCommerce Product ID
	 * @return bool $free, if user has a 100% discount for this product return is True, otherwise default
	 */
	if ( somdn_memberships() ) {

		//if ( wc_memberships_product_has_member_discount( $product_id ) ) {
		if ( somdn_wc_memberships_product_has_member_discount( $product_id ) ) {

			/**
			 * Do the settings include 100% membership discounts. If not, return.
			 */

			$membership_options = get_option( 'somdn_memberships_settings' );
			$discounts = ( isset( $membership_options['somdn_memberships_discounts'] ) && $membership_options['somdn_memberships_discounts'] ) ? true : false ;

			if ( ! $discounts ) return $free;

			/**
			 * Does this product have membership discounts
			 */
			$product_discount = somdn_wc_memberships_product_has_member_discount( $product_id );
			//$product_discount = wc_memberships_product_has_member_discount( $product_id );

			/**
			 * Does the user have a 100% discount for this product
			 */
			$full_discount = somdn_is_full_discount( $product_id );

			if ( $product_discount && $full_discount ) {
				/**
				 * Product has a discount, and user is entitled to 100% discount. Allow free download.
				 */	
				$free = true;
			}

		}

	}

	return $free;

}

if ( ! function_exists( 'somdn_is_full_discount' ) ) {

	/**
	 * Check to see if the user has an active membership plan with a 100% discount for a product
	 *
	 * @since 2.4.3
	 * @param int $product_id WooCommerce Product ID
	 * @return bool True, if user has a 100% discount for this product
	 */
	function somdn_is_full_discount( $product_id ) {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user_id = get_current_user_id();

		$memberships = somdn_wc_memberships_get_user_active_memberships( $user_id );

		if ( ! empty( $memberships ) ) {

			foreach ( $memberships as $plan ) {
				$regprice = get_post_meta( $product_id, '_regular_price', true);
				$full_discount = somdn_get_is_full_discount( $product_id, $plan, $regprice );
				if ( $full_discount ) {
					return true;
				}
			}

		}

		return false;

	}

}

if ( ! function_exists( 'somdn_get_is_full_discount' ) ) {

	/**
	 * Check to see if the product discount amount or percentage means the product is free
	 *
	 * @since 2.4.2
	 * @param int $product_id WooCommerce Product ID
	 * @param post $plan WooCommerce Plan
	 * @param float $regprice Product regular price
	 * @return bool True, if user has a 100% discount for this product
	 */
	function somdn_get_is_full_discount( $product_id, $plan, $regprice ) {

		$member_discount = '';
		$full = false;
		$this_plan = $plan->get_plan();

		// get all available discounts for this product
		$all_discounts = wc_memberships()->get_rules_instance()->get_product_purchasing_discount_rules( $product_id );

		foreach ( $all_discounts as $discount ) {
			// only get discounts that match the current membership plan & are active
			if ( $discount->is_active() && $this_plan->id == $discount->get_membership_plan_id() ) {

				switch( $discount->get_discount_type() ) {

					case 'amount':
						$member_discount = $discount->get_discount_amount();
						if ( $member_discount >= $regprice ) {
							$full = true;
						}
					break;

					case 'percentage':
						$member_discount = $discount->get_discount_amount();
						if ( $member_discount >= 100.0 ) {
							$full = true;
						}
					break;

				}
			}
		}

		return $full;
	}

}

if ( ! function_exists( 'somdn_wc_memberships_get_user_active_memberships' ) ) {

	/**
	 * Get the user's active membership plans
	 *
	 * @since 2.4.2
	 * @param int $user_id current user ID
	 * @param array $args Optional arguments
	 * @return array of active memberships
	 */
	function somdn_wc_memberships_get_user_active_memberships( $user_id = null, $args = array() ) {

		$user_id = get_current_user_id();
		$args = array( 
		    'status' => array( 'active', 'complimentary' ),
		);  
		$active_memberships = wc_memberships_get_user_memberships( $user_id, $args );
		return $active_memberships;

	}

}

if ( ! function_exists( 'somdn_wc_memberships_product_has_member_discount' ) ) {

	/**
	 * Check if the product (or current product) has any member discounts
	 *
	 * @since 2.4.2
	 * @param int $product_id Product ID. Optional, defaults to current product.
	 * @return boolean True, if is elgibile for discount, false otherwise
	 */
	function somdn_wc_memberships_product_has_member_discount( $product_id = null ) {

		if ( ! $product_id ) {

			global $product;
			$product_id = somdn_get_product_id( $product );
		}

		/*
		 * Compatibility check between WooCommerce Memberships 1.9 and 1.8
		 */
		if ( method_exists( wc_memberships()->get_rules_instance(), 'product_has_purchasing_discount_rules' ) ) {
			// 1.9
			return wc_memberships()->get_rules_instance()->product_has_purchasing_discount_rules( $product_id );
		} else {
			// 1.8
			return wc_memberships()->get_rules_instance()->product_has_member_discount( $product_id );
		}
		
	}
}

/*
 * Legacy function
 */
if ( ! function_exists( 'somdn_wc_memberships_user_has_member_discount' ) ) {

	/**
	 * Check if the current user is eligible for member discount for the current product
	 *
	 * @since 2.4.2
	 * @param int $product_id Product ID. Optional, defaults to current product.
	 * @return boolean True, if is elgibile for discount, false otherwise
	 */
	function somdn_wc_memberships_user_has_member_discount( $product_id = null ) {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( ! $product_id ) {

			global $product;
			$product_id = somdn_get_product_id( $product );
		}

		$product      = somdn_get_product( $product_id );
		$user_id      = get_current_user_id();
		$has_discount = wc_memberships()->get_rules_instance()->user_has_product_member_discount( $user_id, $product_id );

		if ( ! $has_discount && $product->has_child() ) {
			foreach ( $product->get_children( true ) as $child_id ) {

				$has_discount = wc_memberships()->get_rules_instance()->user_has_product_member_discount( $user_id, $child_id );

				if ( $has_discount ) {
					break;
				}
			}
		}

		return $has_discount;
	}
}

function somdn_is_user_member_purchase( $product_id ) {

	$has_access = current_user_can( 'wc_memberships_purchase_restricted_product', $product_id );

	if ( $has_access ) {
		return true;
	} else {
		return false;
	}

	return true;

}

function somdn_is_member_restricted( $product_id ) {

	$post_id = $product_id;

	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}

	/*
	 * Compatibility check between WooCommerce Memberships 1.9 and 1.8
	 */
	if ( method_exists( wc_memberships()->get_rules_instance(), 'get_product_restriction_rules' ) ) {
		// 1.9
		$rules = wc_memberships()->get_rules_instance()->get_product_restriction_rules( $post_id );
	} else {
		// 1.8
		$rules = wc_memberships()->get_rules_instance()->get_the_product_restriction_rules( $post_id );
	}

	$is_resticted = false;

	if ( ! empty( $rules ) ) {

		foreach ( $rules as $rule ) {

			if ( 'purchase' == $rule->get_access_type() ) {
				$is_resticted = true;
			}
		}
	}

	return $is_resticted;

}

function somdn_is_purchasable_compat( $purchasable ) {

	if ( somdn_ti_wishlist_exists() && somdn_is_single_product() ) {
		$purchasable = true;
	}

	return $purchasable;

}

add_action('wp_head', 'somdn_purchasable_compat_head');
function somdn_purchasable_compat_head() {

	if ( ! somdn_is_single_product() ) {
		return;
	}

	$product_id = somdn_get_product_id();
	if ( ! $product_id ) {
		return;
	}

	$valid_wp_head = somdn_is_product_valid( $product_id );

	if ( $valid_wp_head ) {

		if ( somdn_ti_wishlist_exists() ) {
			somdn_ti_wishlist_header();
		}

	}

}

function somdn_ti_wishlist_exists() {
	if ( class_exists( 'TInvWL_Public_AddToWishlist' ) ) {
		return true;
	}		
}

function somdn_ti_wishlist_add_to_cart() {
	if ( somdn_ti_wishlist_exists() ) {
		$position = tinv_get_option( 'add_to_wishlist', 'position' );
			if ( 'shortcode' != $position ) {
				return true;
			}
	}	
}

function somdn_ti_wishlist_show_link() {
	//echo do_shortcode( '[ti_wishlists_addtowishlist]' );
}

function somdn_ti_wishlist_header()
{
	do_action('somdn_ti_wishlist_header');
}

add_action('somdn_ti_wishlist_header', 'somdn_ti_wishlist_header_output');
function somdn_ti_wishlist_header_output()
{ ?>
	<style>
		.single-product div.product .summary form.cart { display: none!important; }
		.tinv-wraper.woocommerce.tinv-wishlist.tinvwl-shortcode-add-to-cart { padding-bottom: 15px; }
	</style>
<?php }

function somdn_hide_cart_style()
{
	do_action('somdn_hide_cart_style');
}

add_action('somdn_hide_cart_style', 'somdn_hide_cart_style_output');
function somdn_hide_cart_style_output()
{ ?>
	<style>
		.single-product div.product .summary form.cart { display: none!important; }
	</style>
<?php }

add_filter( 'wc_membership_plan_data_tabs', 'somdn_wc_membership_plan_data_tabs', 50, 1 );
function somdn_wc_membership_plan_data_tabs( $tabs ) {

	$free_downloads = array(
		'label'  => __( 'Free Downloads', 'somdn-pro' ),
		'target' => 'membership-plan-data-somdn-free-downloads'
	);
	$tabs['somdn_free_downloads'] = $free_downloads;
	return $tabs;
}

add_action( 'wc_membership_plan_data_panels', 'somdn_wc_membership_plan_free_downloads_panel' );
function somdn_wc_membership_plan_free_downloads_panel() { ?>

	<div id="membership-plan-data-somdn-free-downloads" class="panel woocommerce_options_panel">

	<?php
		global $post;
		$post_id = $post->ID;
	?>

	<?php $current_value = get_post_meta( $post_id, 'somdn_membership_include_free_download', true ); ?>

	<div class="options_group">

		<p class="form-field">
			<label for="somdn_membership_include_free_download">Allow Free Downloads</label>
			<span class="somdn-woo-meta-span">
				<label class="label-checkbox">
				<input style="margin-right: 10px;" type="checkbox" id="somdn_membership_include_free_download" name="somdn_membership_include_free_download" value="1" <?php checked( 1, $current_value ); ?>>Allow Free Downloads for these members</label>
				<span class="woocommerce-help-tip" data-tip="Make sure the 'Include selected memberships only' option is selected in Free Downloads"></span>
				<br>
				<span class="description">Only applies if "Include selected memberships only" free downloads option is set.</span>
			</span>
		</p>

	</div>

	<?php do_action( 'somdn_memberships_meta_after_options_group', $post_id ); ?>

	</div>
	<?php

}

add_action( 'wc_memberships_save_meta_box', 'somdn_memberships_save', 10, 4 );
function somdn_memberships_save( $post_data, $this_id, $post_id, $post ) {
	if ( isset( $post_data['somdn_membership_include_free_download'] ) ) {
		update_post_meta( $post_id, 'somdn_membership_include_free_download', esc_attr( $post_data['somdn_membership_include_free_download'] ) );
	} else {
		update_post_meta( $post_id, 'somdn_membership_include_free_download', null );
	}
	do_action( 'somdn_memberships_save', $post_data, $this_id, $post_id, $post );
}

/**
 * Sets up the settings and setting pages for WooCommerce Memberships.
 *
 * @since 2.3.6
 */
add_action( 'somdn_settings_subtabs_after_multiple' , 'somdn_settings_subtabs_memberships', 20, 1 );
function somdn_settings_subtabs_memberships( $active_section ) {
	if ( ! somdn_memberships() ) return;
	$nav_active = ( $active_section == 'memberships' ) ? 'current' : '' ;
	echo '<li><a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=memberships" class="' . $nav_active . '">Memberships</a> | </li>';
}

add_action( 'admin_init', 'somdn_settings_memberships' );
function somdn_settings_memberships() {

	if ( ! somdn_memberships() ) return;

	register_setting( 'somdn_memberships_settings', 'somdn_memberships_settings' );

	add_settings_section(
		'somdn_memberships_settings_section', 
		__( 'WooCommerce Membership Settings', 'somdn-pro' ), 
		'somdn_memberships_settings_section_callback', 
		'somdn_memberships_settings'
	);

	add_settings_field( 
		'somdn_memberships_global', 
		__( 'Restricted Products', 'somdn-pro' ), 
		'somdn_memberships_global_render', 
		'somdn_memberships_settings', 
		'somdn_memberships_settings_section' 
	);

	add_settings_field( 
		'somdn_memberships_discounts', 
		__( 'Discount settings', 'somdn-pro' ), 
		'somdn_memberships_discounts_render', 
		'somdn_memberships_settings', 
		'somdn_memberships_settings_section' 
	);

}

function somdn_memberships_global_render() { ?>

	<p><strong>For products that require a membership to purchase them.</strong><br><br></p>

<?php

	$options = get_option( 'somdn_memberships_settings' );
	$optionvalue = ( isset( $options['somdn_memberships_global'] ) && $options['somdn_memberships_global'] ) ? $options['somdn_memberships_global'] : '' ;
	
	if ( ! $optionvalue ) {
		$optionvalue = 1;
	}

	?>

	<div class="som-settings-setting-wrapper">
	<label for="somdn_memberships_global_1">

	<input type="radio" id="somdn_memberships_global_1" name="somdn_memberships_settings[somdn_memberships_global]" value="1" <?php checked( 1, $optionvalue, true ); ?>>

	Include membership restricted items
	</label>
	<p class="description">This setting will enable restricted free products to be purchased by users with the correct membership.</p>
	
	</div>
	
	<div class="som-settings-setting-wrapper">

	<label for="somdn_memberships_global_2">

	<input type="radio" id="somdn_memberships_global_2" name="somdn_memberships_settings[somdn_memberships_global]" value="2" <?php checked( 2, $optionvalue, true ); ?>>

	Exclude membership restricted items
	</label>
	<p class="description">Any items that require a membership, regardless of price, will be excluded.</p>

	</div>

	<div class="som-settings-setting-wrapper">

	<label for="somdn_memberships_global_3">

	<input type="radio" id="somdn_memberships_global_3" name="somdn_memberships_settings[somdn_memberships_global]" value="3" <?php checked( 3, $optionvalue, true ); ?>>

	Members only
	</label>
	<p class="description">Excludes all free products except members only items.</p>

	</div>

	<div class="som-settings-setting-wrapper">

	<label for="somdn_memberships_global_4">

	<input type="radio" id="somdn_memberships_global_4" name="somdn_memberships_settings[somdn_memberships_global]" value="4" <?php checked( 4, $optionvalue, true ); ?>>

	Include selected memberships only
	</label>
	<p class="description">With this option selected you can set which memberships are eligible in the <a href="edit.php?post_type=wc_membership_plan">Membership Plans</a> screen.</p>

	</div>

	<?php

}

function somdn_memberships_discounts_render() { 

	$options = get_option( 'somdn_memberships_settings' ); ?>

	<label for="somdn_memberships_settings[somdn_memberships_discounts]">
	<input type="checkbox" name="somdn_memberships_settings[somdn_memberships_discounts]" id="somdn_memberships_settings[somdn_memberships_discounts]"
	<?php
		$checked = isset( $options['somdn_memberships_discounts'] ) ? checked( $options['somdn_memberships_discounts'], true ) : '' ;
	?>
		value="1">
	Include paid items that have 100% discounts for members.
	</label>
	<?php
}

add_action( 'somdn_settings_page_content' , 'somdn_settings_content_memberships', 10, 1 );
function somdn_settings_content_memberships( $active_section ) {
	if ( ! somdn_memberships() ) return;
	if ( 'memberships' == $active_section ) {
		somdn_memberships_settings_content();
	}
}

function somdn_memberships_settings_section_callback() { 
	echo __( 'Customise the experience for your WooCommerce Membership site.', 'somdn-pro' );
}

function somdn_memberships_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="som-settings-gen-settings-form-wrap">
			
					<?php
					settings_fields( 'somdn_memberships_settings' );
					do_settings_sections( 'somdn_memberships_settings' );
					submit_button();
					?>
			
					</div>
			
				</form>
		
			</div>

		</div>
	</div>

<?php

}