<?php
/**
 * Free Downloads - Woo Meta
 * 
 * Various functions.
 * 
 * @version	3.0.9
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'add_meta_boxes', 'download_now_add_meta_box' );
function download_now_add_meta_box() {

	add_meta_box(
		'download_now-download-now',
		__( 'Free Downloads', 'somdn-pro' ),
		'somdn_product_meta_box_html',
		'product',
		'side',
		'default'
	);

}

add_filter( 'manage_product_posts_columns', 'somdn_product_post_column', 10, 1 );
add_action( 'manage_posts_custom_column', 'somdn_product_columns_content', 10, 2 );

function somdn_product_post_column( $columns ) {
	$columns['somdn_free_downloads'] = '<span class="dashicons dashicons-download" data-tip="Free D/Ls" title="Free D/Ls">Free D/Ls</span>';	
	return $columns;
}

function somdn_product_columns_content( $column_name, $post_id ) {

	if ( $column_name == 'somdn_free_downloads' ) {
		$download_count = get_post_meta( $post_id, 'somdn_dlcount', true ) ? get_post_meta( $post_id, 'somdn_dlcount', true ) : 0 ;
		echo '<span>' . $download_count . '</span>';
	}
	
}

add_filter( 'manage_edit-product_sortable_columns', 'somdn_product_sort_columns' );
function somdn_product_sort_columns( $columns ) {
	$columns['somdn_free_downloads'] = array( 'somdn_dlcount', 1 ); 
	return $columns;
}

add_action( 'pre_get_posts', 'somdn_product_sort_columns_query' );
function somdn_product_sort_columns_query( $query ) {

	if( ! is_admin() )
		return;

	$orderby = $query->get( 'orderby' );

	if ( 'somdn_dlcount' == $orderby ) {

		$query->set( 'meta_query',
			array(
				'relation' => 'OR',
				array(
					'key' => 'somdn_dlcount',
					'compare' => 'EXISTS',
					'type' => 'NUMERIC'
				),
				array(
				'key' => 'somdn_dlcount',
				'compare' => 'NOT EXISTS',
				)
			)
		);

/*
$args['meta_query'] = array(
	'relation' => 'OR',
	array(
		'key' => 'somdn_dlcount',
		'compare' => 'EXISTS',
		'type' => 'NUMERIC'
	),
	array(
		'key' => 'somdn_dlcount',
		'compare' => 'NOT EXISTS',
	)
);
*/
		//$query->set('meta_key','somdn_dlcount');
		//$query->set('orderby','meta_value');
		//$query->set('orderby', 'meta_value_num');
	}
}