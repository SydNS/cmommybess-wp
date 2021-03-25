<?php
/**
 * Free Downloads - WooCommerce - Advanced Include / Exclude
 * 
 * @version 3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'somdn_settings_subtabs_after_multiple' , 'somdn_settings_subtabs_include_exclude', 30 );
function somdn_settings_subtabs_include_exclude( $active_section ) {
	$nav_active = ( $active_section == 'include_exclude' ) ? 'current' : '' ;
	echo '<li><a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=include_exclude" class="' . $nav_active . '">Advanced Product Restrictions</a> | </li>';
}

add_action( 'somdn_settings_page_content' , 'somdn_settings_include_exclude_settings', 10 );
function somdn_settings_include_exclude_settings( $active_section ) {
	if ( $active_section == 'include_exclude' ) {
		somdn_include_exclude_settings_content();
	}
}

function somdn_include_exclude_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7">

				<form action="options.php" class="som-settings-settings-form" method="post">

					<div class="som-settings-gen-settings-form-wrap">

						<?php

							settings_fields( 'somdn_pro_include_settings' );
							somdn_do_custom_settings_sections( 'somdn_pro_include_settings', true );
							submit_button();

						?>

					</div>

				</form>

			</div>

		</div>
	</div>

<?php }

add_action( 'somdn_pro_settings', 'somdn_pro_settings_include_exclude', 20 );
function somdn_pro_settings_include_exclude() {

	register_setting( 'somdn_pro_include_settings', 'somdn_pro_include_product_settings' );

	add_settings_section(
		'somdn_pro_include_product_settings_section',
		__( 'Products', 'somdn-pro' ),
		'somdn_pro_include_product_settings_section_callback',
		'somdn_pro_include_settings'
	);

	add_settings_field(
		'somdn_pro_include_product_setting',
		NULL,
		'somdn_pro_include_product_setting_render',
		'somdn_pro_include_settings',
		'somdn_pro_include_product_settings_section',
		array( 'class' => 'somdn_setting_no_th somdn_setting_no_bot' )
	);

	add_settings_field(
		'somdn_pro_include_product_list',
		NULL,
		'somdn_pro_include_product_list_render',
		'somdn_pro_include_settings',
		'somdn_pro_include_product_settings_section',
		array( 'class' => 'somdn_setting_no_th' )
	);

	register_setting( 'somdn_pro_include_settings', 'somdn_pro_include_cat_settings' );

	add_settings_section(
		'somdn_pro_include_cat_settings_section',
		__( 'Product Categories', 'somdn-pro' ),
		'somdn_pro_include_cat_settings_section_callback',
		'somdn_pro_include_settings'
	);

	add_settings_field(
		'somdn_pro_include_cat_setting',
		NULL,
		'somdn_pro_include_cat_setting_render',
		'somdn_pro_include_settings',
		'somdn_pro_include_cat_settings_section',
		array( 'class' => 'somdn_setting_no_th somdn_setting_no_bot' )
	);

	add_settings_field(
		'somdn_pro_include_cat_list',
		NULL,
		'somdn_pro_include_cat_list_render',
		'somdn_pro_include_settings',
		'somdn_pro_include_cat_settings_section',
		array( 'class' => 'somdn_setting_no_th' )
	);

	register_setting( 'somdn_pro_include_settings', 'somdn_pro_include_tag_settings' );

	add_settings_section(
		'somdn_pro_include_tag_settings_section',
		__( 'Product Tags', 'somdn-pro' ),
		'somdn_pro_include_tag_settings_section_callback',
		'somdn_pro_include_settings'
	);

	add_settings_field(
		'somdn_pro_include_tag_setting',
		NULL,
		'somdn_pro_include_tag_setting_render',
		'somdn_pro_include_settings',
		'somdn_pro_include_tag_settings_section',
		array( 'class' => 'somdn_setting_no_th somdn_setting_no_bot' )
	);

	add_settings_field(
		'somdn_pro_include_tag_list',
		NULL,
		'somdn_pro_include_tag_list_render',
		'somdn_pro_include_settings',
		'somdn_pro_include_tag_settings_section',
		array( 'class' => 'somdn_setting_no_th' )
	);

}

function somdn_pro_include_product_settings_section_callback() {

	echo '<p>Create a list of products that are either excluded from any free download, or a list of only the products that free downloading applies to.</p>';

	$products = wc_get_products( array(
		'downloadable' => true
	) );

	if ( empty( $products ) ) {
		echo '<div class="somdn-setting-warning-wrap somdn-setting-warning-alert"><p>You have no products set up.</p></div>';
	}

}

function somdn_pro_include_product_setting_render() {

	$products = wc_get_products( array(
		'downloadable' => true
	) );

	$genoptions = get_option( 'somdn_gen_settings' );
	$somdn_indy = isset( $genoptions['somdn_indy_items'] ) ? $genoptions['somdn_indy_items'] : false ;
	$somdn_indy_excl = isset( $genoptions['somdn_indy_exclude_items'] ) ? $genoptions['somdn_indy_exclude_items'] : false ;

	$options = get_option( 'somdn_pro_include_product_settings' );
	$value = isset( $options['somdn_pro_include_product_setting'] ) ? $options['somdn_pro_include_product_setting'] : 0 ;

	$disabled = '';
	$indy_set = false;

	if ( ( empty( $products ) ) || $somdn_indy || $somdn_indy_excl ) {
		$disabled = ' disabled';
	}

	if ( $somdn_indy || $somdn_indy_excl ) {
		$indy_set = true;
	}

	if ( $indy_set ) {
		echo '<div class="somdn-setting-warning-wrap somdn-setting-warning-alert"><p>This product section is disabled while using the <em>Include/Exclude selected products only</em> option on the <a href="' . somdn_get_plugin_link_full() . '&tab=settings">General Settings</a> page.</p></div>';
	}

	?>

	<p class="size-15"><strong>Only the following products are 
		<select name="somdn_pro_include_product_settings[somdn_pro_include_product_setting]" id="somdn_pro_include_product_settings[somdn_pro_include_product_setting]"<?php echo $disabled; ?>>
			<option value=""> — </option>
			<option value="included" <?php selected( $value, 'included' ); ?>>Included</option>
			<option value="excluded" <?php selected( $value, 'excluded' ); ?>>Excluded</option>
		</select>
	for free downloads.</strong></p>

<?php }

function somdn_pro_include_product_list_render() {

	$products = wc_get_products( array(
		'downloadable' => true
	) );

	$genoptions = get_option( 'somdn_gen_settings' );
	$somdn_indy = isset( $genoptions['somdn_indy_items'] ) ? $genoptions['somdn_indy_items'] : false ;
	$somdn_indy_excl = isset( $genoptions['somdn_indy_exclude_items'] ) ? $genoptions['somdn_indy_exclude_items'] : false ;

	$options = get_option( 'somdn_pro_include_product_settings' );
	$value = isset( $options['somdn_pro_include_product_list'] ) ? $options['somdn_pro_include_product_list'] : '' ;

	$disabled = '';

	if ( ( empty( $products ) ) || $somdn_indy || $somdn_indy_excl ) {
		$disabled = ' disabled';
	}

	?>

	<div class="som-settings-pro-basic-limit-error-wrap somdn-select2-wrap">

		<select class="somdn-settings-product-cats somdn-select2 somdn-select2-search" name="somdn_pro_include_product_settings[somdn_pro_include_product_list][]" id="somdn_pro_include_product_settings[somdn_pro_include_product_list][]" multiple="multiple" style="width: 100%;"<?php echo $disabled; ?>>

		<?php if ( ! empty( $value ) && is_array( $value ) ) {
			foreach ( $value as $product ) {
				$product_id = $product;
				$product_name = html_entity_decode( get_the_title( $product_id ) ) . ' (#' . $product_id . ')';
				echo '<option value="' . $product_id . '" selected>' . $product_name . '</option>';
			}
		} ?>
		</select>
	</div>

<?php }

function somdn_pro_include_cat_settings_section_callback() {

	echo '<p>Create a list of product categories that are either excluded from any free download, or a list of only the product categories that free downloading applies to.</p>';

	$product_cats = get_terms( array(
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
	) );

	if ( empty( $product_cats ) ) {
		echo '<div class="somdn-setting-warning-wrap"><p>You have no product categories set up.</p></div>';
	}

}

function somdn_pro_include_cat_setting_render() {

	$options = get_option( 'somdn_pro_include_cat_settings' );
	$value = isset( $options['somdn_pro_include_cat_setting'] ) ? $options['somdn_pro_include_cat_setting'] : 0 ;

	?>

	<p class="size-15"><strong>Only the following product categories are 
		<select name="somdn_pro_include_cat_settings[somdn_pro_include_cat_setting]" id="somdn_pro_include_cat_settings[somdn_pro_include_cat_setting]">
			<option value=""> — </option>
			<option value="included" <?php selected( $value, 'included' ); ?>>Included</option>
			<option value="excluded" <?php selected( $value, 'excluded' ); ?>>Excluded</option>
		</select>
	for free downloads.</strong></p>

<?php }

function somdn_pro_include_cat_list_render() {

	$no_product_cats = false;

	$product_cats = get_terms( array(
		'taxonomy' => 'product_cat',
		'hide_empty' => false,
	) );

	if ( empty( $product_cats ) ) {
		$no_product_cats = true;
	}

	$options = get_option( 'somdn_pro_include_cat_settings' );
	$value = isset( $options['somdn_pro_include_cat_list'] ) ? $options['somdn_pro_include_cat_list'] : '' ;

	?>

	<div class="som-settings-pro-basic-limit-error-wrap somdn-select2-wrap">

		<select class="somdn-settings-product-cats somdn-select2 somdn-select2-no-search" name="somdn_pro_include_cat_settings[somdn_pro_include_cat_list][]" id="somdn_pro_include_cat_settings[somdn_pro_include_cat_list][]" multiple="multiple" style="width: 100%;"<?php echo $no_product_cats == true ? ' disabled' : ''; ?>>

		<?php if ( ! empty( $product_cats ) && ! $no_product_cats ) {
			foreach ( $product_cats as $product_cat ) {
				$product_cat_id = $product_cat->term_id;
				$product_cat_name = $product_cat->name;
				$selected = '';
				if ( ! empty( $value ) && is_array( $value ) ) {
					if ( in_array( $product_cat_id, $value) && ! empty( $value ) ) {
						$selected = ' selected';
					}
				}
				echo '<option value="' . $product_cat_id . '"' . $selected . '>' . $product_cat_name . '</option>';
			}
		} ?>
		</select>
	</div>

<?php }

function somdn_pro_include_tag_settings_section_callback() {

	echo '<p>Create a list of product tags that are either excluded from any free download, or a list of only the product tags that free downloading applies to.</p>';

	$product_tags = get_terms( array(
		'taxonomy' => 'product_tag',
		'hide_empty' => false,
	) );

	if ( empty( $product_tags ) ) {
		echo '<div class="somdn-setting-warning-wrap"><p>You have no product tags set up.</p></div>';
	}

}

function somdn_pro_include_tag_setting_render() {

	$options = get_option( 'somdn_pro_include_tag_settings' );
	$value = isset( $options['somdn_pro_include_tag_setting'] ) ? $options['somdn_pro_include_tag_setting'] : 0 ;

	?>

	<p class="size-15"><strong>Only the following product tags are 
		<select name="somdn_pro_include_tag_settings[somdn_pro_include_tag_setting]" id="somdn_pro_include_tag_settings[somdn_pro_include_tag_setting]">
			<option value=""> — </option>
			<option value="included" <?php selected( $value, 'included' ); ?>>Included</option>
			<option value="excluded" <?php selected( $value, 'excluded' ); ?>>Excluded</option>
		</select>
	for free downloads.</strong></p>

<?php }

function somdn_pro_include_tag_list_render() {

	$no_product_tags = false;

	$product_tags = get_terms( array(
		'taxonomy' => 'product_tag',
		'hide_empty' => false,
	) );

	if ( empty( $product_tags ) ) {
		$no_product_tags = true;
	}

	$options = get_option( 'somdn_pro_include_tag_settings' );
	$value = isset( $options['somdn_pro_include_tag_list'] ) ? $options['somdn_pro_include_tag_list'] : '' ;

	?>

	<div class="som-settings-pro-basic-limit-error-wrap somdn-select2-wrap">

		<select class="somdn-settings-product-tags somdn-select2 somdn-select2-no-search" name="somdn_pro_include_tag_settings[somdn_pro_include_tag_list][]" id="somdn_pro_include_tag_settings[somdn_pro_include_tag_list][]" multiple="multiple" style="width: 100%;"<?php echo $no_product_tags == true ? ' disabled' : ''; ?>>

		<?php if ( ! empty( $product_tags ) && ! $no_product_tags ) {
			foreach ( $product_tags as $product_tag ) {
				$product_tag_id = $product_tag->term_id;
				$product_tag_name = $product_tag->name;
				$selected = '';
				if ( ! empty( $value ) && is_array( $value ) ) {
					if ( in_array( $product_tag_id, $value) && ! empty( $value ) ) {
						$selected = ' selected';
					}
				}
				echo '<option value="' . $product_tag_id . '"' . $selected . '>' . $product_tag_name . '</option>';
			}
		} ?>
		</select>
	</div>

<?php }

function somdn_get_included_products() {
	$options = get_option( 'somdn_pro_include_product_settings' );
	$value = isset( $options['somdn_pro_include_product_list'] ) ? $options['somdn_pro_include_product_list'] : array() ;
	return apply_filters( 'somdn_get_included_products', $value );
}

function somdn_get_included_categories() {
	$options = get_option( 'somdn_pro_include_cat_settings' );
	$value = isset( $options['somdn_pro_include_cat_list'] ) ? $options['somdn_pro_include_cat_list'] : array() ;
	return apply_filters( 'somdn_get_included_categories', $value );
}

function somdn_get_included_tags() {
	$options = get_option( 'somdn_pro_include_tag_settings' );
	$value = isset( $options['somdn_pro_include_tag_list'] ) ? $options['somdn_pro_include_tag_list'] : array() ;
	return apply_filters( 'somdn_get_included_tags', $value );	
}

function somdn_is_product_included_advanced( $included, $product, $product_id ) {

	$advanced_include = $included;

	$include_product_options = get_option( 'somdn_pro_include_product_settings' );
	$include_product_type = isset( $include_product_options['somdn_pro_include_product_setting'] ) ? $include_product_options['somdn_pro_include_product_setting'] : 0 ;

	$include_cat_options = get_option( 'somdn_pro_include_cat_settings' );
	$include_cat_type = isset( $include_cat_options['somdn_pro_include_cat_setting'] ) ? $include_cat_options['somdn_pro_include_cat_setting'] : 0 ;

	$include_tag_options = get_option( 'somdn_pro_include_tag_settings' );
	$include_tag_type = isset( $include_tag_options['somdn_pro_include_tag_setting'] ) ? $include_tag_options['somdn_pro_include_tag_setting'] : 0 ;

	// Before any checks, by default all products and taxonomy are included
	$product_valid = true;
	$category_valid = true;
	$tag_valid = true;

	$products = somdn_get_included_products();
	$categories = somdn_get_included_categories();
	$tags = somdn_get_included_tags();

	//echo ' $include_product_type = ' . $include_product_type;

	// Check if specific product restrictions are in place
	if ( ! empty( $include_product_type ) ) {

		if ( ! empty( $products ) ) {

			switch ( $include_product_type ) {
				case 'included':
					if ( ! in_array( $product_id, $products ) ) {
						$product_valid = false;
					}
					break;
				case 'excluded':
					if ( in_array( $product_id, $products ) ) {
						$product_valid = false;
					}
					break;
				default:
					// Do nothing?
					break;
			}

		}

	}

	// Check if specific product category restrictions are in place
	if ( ! empty( $include_cat_type ) ) {

		if ( ! empty( $categories ) ) {

			switch ( $include_cat_type ) {
				case 'included':
					if ( ! has_term( $categories, 'product_cat', $product_id ) ) {
						$category_valid = false;
					}
					break;
				case 'excluded':
					if ( has_term( $categories, 'product_cat', $product_id ) ) {
						$category_valid = false;
					}
					break;
				default:
					// Do nothing?
					break;
			}

		}

	}

	// Check if specific product tag restrictions are in place
	if ( ! empty( $include_tag_type ) ) {

		if ( ! empty( $tags ) ) {

			switch ( $include_tag_type ) {
				case 'included':
					if ( ! has_term( $tags, 'product_tag', $product_id ) ) {
						$tag_valid = false;
					}
					break;
				case 'excluded':
					if ( has_term( $tags, 'product_tag', $product_id ) ) {
						$tag_valid = false;
					}
					break;
				default:
					// Do nothing?
					break;
			}

		}
		
	}

	// Check if everything is valid so far
	$all_valid = ( $category_valid && $tag_valid && $product_valid ) ? true : false ;

	if ( $all_valid ) {
		$included = $advanced_include = true;
	} else {
		$included = $advanced_include = false;
	}

	// If we're not looking at the advanced product list, call default individual product check
	if ( empty( $include_product_type ) ) {
		$advanced_include = somdn_is_product_included_individual( $included, $product, $product_id );
	}

	return $advanced_include;

}


/**
 * Ajax search product data for a term and return array of data.
 *
 * @return array of products - array( 'ID', ' (#Title)' )
 */
function somdn_search_products_ajax_callback() {

	check_ajax_referer( 'somdn-search-products', 'security' );

	$term     = stripslashes( $_GET['term'] );
	$excl     = array(); //array( stripslashes( $_GET['exclude'] ) );
	$products = somdn_search_products( $term );
	//$products = array_diff( $products, $excl );
	$return   = array();

	foreach ( $products as $product_id ) {
		$product_id_int = (int)$product_id;
		$return[] = array( $product_id_int, html_entity_decode( get_the_title( $product_id ) ) . ' (#' . $product_id_int . ')' );
	}

	echo json_encode( $return );
	die;
}

/**
 * Search product data for a term and return ids.
 *
 * @param  string $term
 * @return array of ids
 */
function somdn_search_products( $term ) {

	if ( empty( $term ) ) {
		wp_die();
	}

	$data_store = WC_Data_Store::load( 'product' );
	$ids        = $data_store->search_products( $term, 'downloadable', true );

	return array_filter( $ids );
}

add_action( 'somdn_after_indy_items_render', 'somdn_check_indy_exclude_settings_page' );
add_action( 'somdn_after_indy_exclude_items_render', 'somdn_check_indy_exclude_settings_page' );
function somdn_check_indy_exclude_settings_page() {

	$advanced_indy = false;

	$include_product_options = get_option( 'somdn_pro_include_product_settings' );
	$include_product_type = isset( $include_product_options['somdn_pro_include_product_setting'] ) ? $include_product_options['somdn_pro_include_product_setting'] : 0 ;
	$include_product_list = somdn_get_included_products();

	if ( $include_product_type ) {
		$advanced_indy = true;
	}

	if ( $advanced_indy ) {
		echo '<div class="somdn-setting-warning-wrap somdn-setting-warning-alert"><p>This option won\'t work while using the <em>Product</em> include/exclude settings on the <a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=include_exclude">Advanced Product Restrictions</a> page.</p></div>';
	}

}