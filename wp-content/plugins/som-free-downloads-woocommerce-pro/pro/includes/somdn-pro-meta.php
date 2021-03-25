<?php
/**
 * Free Downloads - WooCommerce - Pro Meta
 * 
 * Custom post types & meta.
 * 
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function somdn_product_meta_box_html_pro( $post, $product_id ) {

	$track_options = get_option( 'somdn_pro_track_settings' );
	$capture_emails = isset( $track_options['somdn_capture_email_enable'] ) ? $track_options['somdn_capture_email_enable'] : false ;

	if ( $capture_emails ) { ?>

		<hr>
		<p>
			<input type="checkbox" name="somdn_exclude_email_capture" id="somdn_exclude_email_capture" value="somdn_exclude_email_capture" <?php echo ( somdn_product_meta_get_meta( 'somdn_exclude_email_capture' ) === 'somdn_exclude_email_capture' ) ? 'checked' : ''; ?>>
			<label for="somdn_exclude_email_capture">Exclude from email capture</label>
		</p>

	<?php }

}

function somdn_save_meta_product_meta_pro( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['download_now_nonce'] ) || ! wp_verify_nonce( $_POST['download_now_nonce'], '_download_now_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['somdn_exclude_email_capture'] ) ) {
		update_post_meta( $post_id, 'somdn_exclude_email_capture', sanitize_key( $_POST['somdn_exclude_email_capture'] ) );
	} else {
		update_post_meta( $post_id, 'somdn_exclude_email_capture', NULL );
	}
}

add_action( 'init', 'somdn_downloads_cpt', 50 );
function somdn_downloads_cpt() {

	/**
	 * Tracked downloads custom post type (reporting downloads, download limits)
	 */
	$somdn_tracked_downloads_labels = array(
		'name'                  => __( 'Tracked Free Downloads', 'somdn-pro' ),
		'singular_name'         => __( 'Tracked Download', 'somdn-pro' ),
		'menu_name'             => __( 'Tracked Downloads', 'somdn-pro' ),
		'name_admin_bar'        => __( 'Tracked Downloads', 'somdn-pro' ),
		'archives'              => __( 'Tracked Downloads', 'somdn-pro' ),
		'parent_item_colon'     => __( 'Parent Item:', 'somdn-pro' ),
		'all_items'             => __( 'Downloads', 'somdn-pro' ),
		'add_new_item'          => __( 'Add New Tracked Download', 'somdn-pro' ),
		'add_new'               => __( 'Add New', 'somdn-pro' ),
		'new_item'              => __( 'New Tracked Download', 'somdn-pro' ),
		'edit_item'             => __( 'Free Download', 'somdn-pro' ),
		'update_item'           => __( 'Update Tracked Download', 'somdn-pro' ),
		'view_item'             => __( 'View Tracked Download', 'somdn-pro' ),
		'search_items'          => __( 'Search Tracked Downloads', 'somdn-pro' ),
		'not_found'             => __( 'Not found', 'somdn-pro' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'somdn-pro' ),
		'featured_image'        => __( 'Featured Image', 'somdn-pro' ),
		'set_featured_image'    => __( 'Set featured image', 'somdn-pro' ),
		'remove_featured_image' => __( 'Remove featured image', 'somdn-pro' ),
		'use_featured_image'    => __( 'Use as featured image', 'somdn-pro' ),
		'insert_into_item'      => __( 'Insert into Tracked Download', 'somdn-pro' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Tracked Download', 'somdn-pro' ),
		'items_list'            => __( 'Tracked Downloads list', 'somdn-pro' ),
		'items_list_navigation' => __( 'Tracked Downloads list navigation', 'somdn-pro' ),
		'filter_items_list'     => __( 'Filter Tracked Downloads list', 'somdn-pro' ),
	);
	$somdn_tracked_downloads_rewrite = array(
		'slug'                  => 'tracked-downloads',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$somdn_tracked_downloads_capabilities = array(
		'edit_post'          => 'update_core',
		'read_post'          => 'update_core',
		'delete_post'        => 'update_core',
		'edit_posts'         => 'update_core',
		'edit_others_posts'  => 'update_core',
		'delete_posts'       => 'update_core',
		'publish_posts'      => 'update_core',
		'read_private_posts' => 'update_core',
		'create_posts' => false
	);
	$somdn_tracked_downloads_supports = array(
		'title',
		'content',
		'author'
	);
	$somdn_tracked_downloads_args = array(
		'label'                 => __( 'Tracked Download', 'somdn-pro' ),
		'description'           => __( 'Tracked Downloads', 'somdn-pro' ),
		'labels'                => apply_filters( 'somdn_downloads_labels', $somdn_tracked_downloads_labels ),
		'supports'              => array( 'title', 'author', 'custom-fields' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => false,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'product',
		//'capabilities'          => apply_filters( 'somdn_downloads_capabilities', $somdn_tracked_downloads_capabilities ),
		'rewrite'               => apply_filters( 'somdn_downloads_rewrite', $somdn_tracked_downloads_rewrite ),
		'map_meta_cap' => true
	);
	register_post_type( 'somdn_tracked', apply_filters( 'somdn_downloads_args', $somdn_tracked_downloads_args ) );

	/**
	 * Temp downloads custom post type (redirect, emailed links)
	 * Since these are not shown to the user or site admin, none of the strings need to be translatable
	 */
	$somdn_temp_downloads_labels = array(
		'name'                  => 'Temp Free Downloads',
		'singular_name'         => 'Temp Download',
		'menu_name'             => 'Temp Downloads',
		'name_admin_bar'        => 'Temp Downloads',
		'archives'              => 'Temp Downloads',
		'parent_item_colon'     => 'Parent Item:',
		'all_items'             => 'Downloads',
		'add_new_item'          => 'Add New Temp Download',
		'add_new'               => 'Add New',
		'new_item'              => 'New Temp Download',
		'edit_item'             => 'Free Download',
		'update_item'           => 'Update Temp Download',
		'view_item'             => 'View Temp Download',
		'search_items'          => 'Search Temp Downloads',
		'not_found'             => 'Not found',
		'not_found_in_trash'    => 'Not found in Trash',
		'featured_image'        => 'Featured Image',
		'set_featured_image'    => 'Set featured image',
		'remove_featured_image' => 'Remove featured image',
		'use_featured_image'    => 'Use as featured image',
		'insert_into_item'      => 'Insert into Temp Download',
		'uploaded_to_this_item' => 'Uploaded to this Temp Download',
		'items_list'            => 'Temp Downloads list',
		'items_list_navigation' => 'Temp Downloads list navigation',
		'filter_items_list'     => 'Filter Temp Downloads list',
	);
	$somdn_temp_downloads_rewrite = array(
		'slug'                  => 'temp-downloads',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$somdn_temp_downloads_capabilities = array(
		'edit_post'          => 'update_core',
		'read_post'          => 'update_core',
		'delete_post'        => 'update_core',
		'edit_posts'         => 'update_core',
		'edit_others_posts'  => 'update_core',
		'delete_posts'       => 'update_core',
		'publish_posts'      => 'update_core',
		'read_private_posts' => 'update_core',
		'create_posts' => false
	);
	$somdn_temp_downloads_supports = array(
		'title',
		'content',
		'author'
	);
	$somdn_temp_downloads_args = array(
		'label'                 => 'Temp Download',
		'description'           => 'Temp Downloads',
		'labels'                => apply_filters( 'somdn_temp_downloads_labels', $somdn_temp_downloads_labels ),
		'supports'              => array( 'title', 'author', 'custom-fields' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => false,
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		//'capabilities'          => apply_filters( 'somdn_downloads_capabilities', $somdn_tracked_downloads_capabilities ),
		'rewrite'               => apply_filters( 'somdn_temp_downloads_rewrite', $somdn_temp_downloads_rewrite ),
		'map_meta_cap' => true
	);
	register_post_type( 'somdn_temp_download', apply_filters( 'somdn_temp_downloads_args', $somdn_temp_downloads_args ) );

}
/*
add_filter( 'somdn_downloads_capabilities', 'somdn_downloads_capabilities_editor', 20, 1 );
function somdn_downloads_capabilities_editor( $args ) {

	$editor_capabilities = array(
		'edit_post'          => 'edit_posts',
		'read_post'          => 'edit_posts',
		'delete_post'        => 'edit_posts',
		'edit_posts'         => 'edit_posts',
		'edit_others_posts'  => 'edit_posts',
		'delete_posts'       => 'edit_posts',
		'publish_posts'      => 'edit_posts',
		'read_private_posts' => 'edit_posts',
		'create_posts' => false
	);

	return $editor_capabilities;

}
*/
add_action( 'do_meta_boxes', 'somdn_hide_publish_metabox' );
function somdn_hide_publish_metabox() {

	global $post;
	$post_type = get_post_type( $post );

	if ( $post_type == 'somdn_tracked' ) {
		remove_meta_box( 'submitdiv', 'somdn_tracked' , 'side' );
		remove_meta_box( 'postexcerpt', 'somdn_tracked', 'normal' );
		remove_meta_box( 'pageparentdiv', 'somdn_tracked', 'side' );
		remove_meta_box( 'commentsdiv', 'somdn_tracked', 'normal' );
		remove_meta_box( 'authordiv', 'somdn_tracked', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'somdn_tracked', 'normal' );
		remove_meta_box( 'titlediv', 'somdn_tracked', 'normal' );
		remove_meta_box( 'slugdiv', 'somdn_tracked', 'normal' );
		remove_meta_box( 'postcustom', 'somdn_tracked', 'normal' );

		//add_meta_box( 'woocommerce-order-actions', sprintf( __( '%s Actions', 'woocommerce' ), $post_type->labels->singular_name ), 'WC_Meta_Box_Order_Actions::output', $type, 'side', 'high' );

	}

	if ( $post_type == 'somdn_temp_download' ) {
		remove_meta_box( 'submitdiv', 'somdn_temp_download' , 'side' );
		remove_meta_box( 'postexcerpt', 'somdn_temp_download', 'normal' );
		remove_meta_box( 'pageparentdiv', 'somdn_temp_download', 'side' );
		remove_meta_box( 'commentsdiv', 'somdn_temp_download', 'normal' );
		remove_meta_box( 'authordiv', 'somdn_temp_download', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'somdn_temp_download', 'normal' );
		remove_meta_box( 'titlediv', 'somdn_temp_download', 'normal' );
		remove_meta_box( 'slugdiv', 'somdn_temp_download', 'normal' );
		remove_meta_box( 'postcustom', 'somdn_temp_download', 'normal' );

		//add_meta_box( 'woocommerce-order-actions', sprintf( __( '%s Actions', 'woocommerce' ), $post_type->labels->singular_name ), 'WC_Meta_Box_Order_Actions::output', $type, 'side', 'high' );

	}

}

add_action( 'admin_menu', 'somdn_free_downloads_menu', 99 ); 
function somdn_free_downloads_menu() {

	$tracked = somdn_are_downloads_tracked();

	if ( empty( $tracked ) ) {
		return;
	}

	add_submenu_page(
		'woocommerce',
		'Downloads',
		'Free Download Logs',
		'manage_woocommerce',
		'edit.php?post_type=somdn_tracked',
		NULL
	);

	$dev_debug = apply_filters( 'somdn_dev_debug', false );
	if ( $dev_debug ) {
		add_submenu_page(
			'woocommerce',
			'Temp Downloads',
			'Temp Downloads',
			'update_core',
			'edit.php?post_type=somdn_temp_download',
			NULL
		);
	}

}

add_action( 'admin_head', 'somdn_free_downloads_menu_highlight' );
/**
 * Keep menu open.
 */
function somdn_free_downloads_menu_highlight() {
	global $current_screen, $parent_file, $submenu_file;
	
	$base = $current_screen->base;
	$post_type = $current_screen->post_type;

	if ( $base == 'post' ) {
		if ( 'somdn_tracked' == $post_type ) {
			$parent_file = 'woocommerce';
			$submenu_file = 'edit.php?post_type=somdn_tracked';
			return;
		}
	}

	if ( $base == 'post' ) {
		if ( 'somdn_temp_download' == $post_type ) {
			$parent_file = 'woocommerce';
			$submenu_file = 'edit.php?post_type=somdn_temp_download';
			return;
		}
	}

}

add_filter( 'manage_somdn_tracked_posts_columns', 'somdn_tracked_columns' );
function somdn_tracked_columns( $columns ) {

	unset( $columns['title'] );
	unset( $columns['date'] );
	unset( $columns['author'] );

	//$columns['title']  = 'Product';
	$columns['somdn_tracked_title_column'] = 'Download';
	$columns['somdn_tracked_product'] = 'Product';
	$columns['somdn_tracked_user'] = 'User';
	$columns['somdn_tracked_date_column'] = 'Date';
	//$columns['somdn_tracked_total_column']  = 'Total';

	$customOrder = array( 'cb', 'somdn_tracked_title_column', 'somdn_tracked_product', 'somdn_tracked_user', 'somdn_tracked_date_column' );

	# return a new column array to wordpress.
	# order is the exactly like you set in $customOrder.
	foreach ($customOrder as $colname) {
		$new_cols[$colname] = $columns[$colname];
	}
	
	return $new_cols;

}

add_action( 'manage_somdn_tracked_posts_custom_column', 'somdn_tracked_columns_content', 50, 2 );
function somdn_tracked_columns_content( $column_name, $post_id ) {

	if ( $column_name == 'somdn_tracked_title_column' ) {
		echo '<a href="' . get_edit_post_link() . '" class="row-title">#' . get_the_id() . ' ' . get_the_title() . '</a>';
		//<small class="meta email"><a href="mailto:info@squareonemedia.co.uk">info@squareonemedia.co.uk</a></small>
	}

	if ( $column_name == 'somdn_tracked_date_column' ) {
		echo get_the_date( 'Y/m/d' );
	}

	if ( $column_name == 'somdn_tracked_user' ) {
		$author_id = get_post_field( 'post_author', $post_id );
		if ( $author_id ) {
			echo '<a href="edit.php?post_type=somdn_tracked&author=' . $author_id . '">' . get_the_author_meta( 'display_name' , $author_id ) . '</a>';
		} else {
			echo '<a href="edit.php?post_type=somdn_tracked&author=-1">Guest</a>';
		}
	}

	if ( $column_name == 'somdn_tracked_product' ) {
		echo '<a target="_blank" href="' . get_the_permalink( get_post_meta( $post_id, 'somdn_product_id', true ) ) . '" class="row-title">' . get_the_title( get_post_meta( $post_id, 'somdn_product_id', true ) ) . '</a>';
	}

	if ( $column_name == 'somdn_tracked_total_column' ) {
		//$product_id = get_post_meta( $post_id, 'support_forum_product_product_id', true );
		//$product = get_the_title( $product_id );
		//$product_link = get_the_permalink( $product_id );
		//echo '5';
	}

}

add_filter( 'manage_edit-somdn_tracked_sortable_columns', 'somdn_somdn_tracked_sort_columns' );
function somdn_somdn_tracked_sort_columns( $columns ) {
	$columns['somdn_tracked_title_column'] = array( 'id' );
	$columns['somdn_tracked_date_column'] = array( 'somdn_dl_date', 1 );
	return $columns;
}

add_filter( 'post_row_actions', 'somdn_tracked_remove_quick_edit' , 10, 2 );
function somdn_tracked_remove_quick_edit( $actions, $post ) {

	if ( $post->post_type == 'somdn_tracked' ) {
		unset( $actions['inline hide-if-no-js'] );
	}
	return $actions;

}

add_filter('post_row_actions','somdn_tracked_action_row', 10, 2);
function somdn_tracked_action_row( $actions, $post ) {
	//check for your post type
	if ( $post->post_type == 'somdn_tracked' ){
		$actions['edit'] = '<a href="'. get_edit_post_link( $post->ID ). '">View Details</a>';
	}
	return $actions;
}

function somdn_temp_download_details_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

add_action( 'add_meta_boxes', 'somdn_temp_download_details_add_meta_box' );
function somdn_temp_download_details_add_meta_box() {
	add_meta_box(
		'somdn_temp_download_details',
		__( 'Temp Download Details', 'somdn_temp_download_details' ),
		'somdn_temp_download_details_html',
		'somdn_temp_download',
		'normal',
		'core'
	);
}

function somdn_temp_download_details_html( $post ) {
	
	wp_nonce_field( '_somdn_temp_download_details_nonce', 'somdn_temp_download_details_nonce' ); ?>

	<?php

		$post_id = $post->ID;
		$download_type = get_post_meta( $post_id, 'download_type', true );
		$user_data = get_post_meta( $post_id, 'user_data', true );
		$download_data = get_post_meta( $post_id, 'download_data', true );
		$email_url = get_post_meta( $post_id, 'email_url', true );
		$expired = get_post_meta( $post_id, 'link_expired', true );

		if ( empty( $expired ) ) {
			$status = 'Valid';
		} else {
			$status = 'Expired';
		}

		?>

	<div class="somdn-tracked-download-headings">
		<h2>Temp Download Details - ID#<?php echo $post_id; ?></h2>
	</div>

	<div class="somdn-tracked-download-wrap">

		<div class="somdn-tracked-download-body">

			<?php
				$files_array = get_post_meta( $post_id, 'somdn_files_list', true );
			?>

<p>Status = <?php echo $status; ?></p>
<?php if ( ! empty( $email_url ) ) { ?>
<pre><?php print_r($email_url); ?></pre>
<?php } ?>
<p>$download_type</p>
<pre><?php print_r($download_type); ?></pre>
<p>$user_data</p>
<pre><?php print_r($user_data); ?></pre>
<p>$download_data</p>
<pre><?php print_r($download_data); ?></pre>

		</div>

	</div>

<?php

}

/**
 * Generated by the WordPress Meta Box generator
 * at http://jeremyhixon.com/tool/wordpress-meta-box-generator/
 */

function somdn_tracked_download_details_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

add_action( 'add_meta_boxes', 'somdn_tracked_download_details_add_meta_box' );
function somdn_tracked_download_details_add_meta_box() {
	add_meta_box(
		'somdn_tracked_download_details',
		__( 'Free Download Details', 'somdn_tracked_download_details' ),
		'somdn_tracked_download_details_html',
		'somdn_tracked',
		'normal',
		'core'
	);
}

function somdn_tracked_download_details_html( $post ) {
	
	wp_nonce_field( '_somdn_tracked_download_details_nonce', 'somdn_tracked_download_details_nonce' ); ?>

	<?php

		$post_id = $post->ID;
		$download = get_post_meta( $post_id, 'somdn_product_id', true );
		$download_title = get_the_title( $download );
		$user_id = get_post_meta( $post_id, 'somdn_user_id', true );
		$user = isset( $user_id ) && $user_id != 0 ? get_userdata( $user_id ) : '' ;
		$user_name = '';

		if ( ! empty( $user ) ) {
			$user_name = $user->user_login;
		}

		$user_email = get_post_meta( $post_id, 'somdn_user_email', true );
		if ( empty( $user_email ) ) {
			$user_email = 'N/A';
		}

		$user_subbed = get_post_meta( $post_id, 'somdn_user_subbed', true );
		if ( empty( $user_subbed ) ) {
			$user_subbed = 'N/A';
		} else {
			$user_subbed = 'Subscribed';
		}

		$user_fname = get_post_meta( $post_id, 'somdn_user_fname', true );
		if ( empty( $user_fname ) ) {
			$user_fname = 'N/A';
		}

		$user_lname = get_post_meta( $post_id, 'somdn_user_lname', true );
		if ( empty( $user_lname ) ) {
			$user_lname = 'N/A';
		}

		$user_tel = get_post_meta( $post_id, 'somdn_user_tel', true );
		if ( empty( $user_tel ) ) {
			$user_tel = 'N/A';
		}

		$user_company = get_post_meta( $post_id, 'somdn_user_company', true );
		if ( empty( $user_company ) ) {
			$user_company = 'N/A';
		}

		$user_website = get_post_meta( $post_id, 'somdn_user_website', true );
		if ( empty( $user_website ) ) {
			$user_website = 'N/A';
		}

		$user_ip = get_post_meta( $post_id, 'somdn_user_ip', true );
		$user_ip = isset( $user_ip ) ? $user_ip : 'N/A' ;

		$variation_id = get_post_meta( $post_id, 'somdn_variation_id', true );

		$download_files = get_post_meta( $post_id, 'somdn_download_files', true );

	?>

	<input type="hidden" id="tracked-id" value="<?php echo $post_id ; ?>">

	<div class="somdn-tracked-download-headings">
			<h2>Free Download #<?php echo $post_id; ?></h2>
	</div>

	<div class="somdn-tracked-download-wrap">

		<div class="somdn-tracked-download-product-wrap">

			<div class="somdn-tracked-download-product-image">

				<?php $src = get_the_post_thumbnail_url( $download );
					if ( empty( $src ) ) {
						$src = wc_placeholder_img_src();
					}
					echo '<img src="' . $src . '">';
				?>

				<div class="somdn-tracked-download-product-button">
					<a href="<?php echo get_the_permalink( $download ); ?>" target="_blank" class="button">View Product</a>
				</div>

			</div>

		</div>

		<div class="somdn-tracked-download-body">

			<div class="somdn-tracked-download-content-wrap">
				<div class="somdn-tracked-download-label">ID</div>
				<div class="somdn-tracked-download-content">#<?php echo $post_id; ?></div>
			</div>

			<div class="somdn-tracked-download-content-wrap">
				<div class="somdn-tracked-download-label">Product</div>
				<div class="somdn-tracked-download-content">
					<a href="<?php echo get_the_permalink( $download ); ?>" target="_blank"><?php echo $download_title; ?></a>
				</div>
			</div>

			<div class="somdn-tracked-download-content-wrap">
				<div class="somdn-tracked-download-label">Variation</div>
				<div class="somdn-tracked-download-content">
					<span><?php echo ! empty( $variation_id ) ? '#' . $variation_id : 'N/A'; ?></span>
				</div>
			</div>

			<div class="somdn-tracked-download-content-wrap">
				<div class="somdn-tracked-download-label">Files</div>
				<div class="somdn-tracked-download-content">
					<?php if ( ! empty( $download_files ) && is_array( $download_files ) ) {
						echo '<ul>';
						foreach ( $download_files as $file ) {
							echo '<li>' . esc_html( $file ) . '</li>';
						}
						echo '</ul>';
					} else {
						echo '<span>N/A</span>';
					} ?>
				</div>
			</div>

			<div class="somdn-tracked-download-content-wrap">
				<div class="somdn-tracked-download-label">User</div>
				<div class="somdn-tracked-download-content">
					<?php echo isset( $user_id ) && $user_id != 0 ? $user_name . ' <a target="_blank" href="user-edit.php?user_id=' . $user_id . '" class="somdn-tracked-download-userid">(' . $user_id . ')</a>' : '<span class="somdn-tracked-download-userid">Guest</span>' ; ?>
				</div>
			</div>

			<div class="somdn-tracked-download-content-wrap">
				<div class="somdn-tracked-download-label">Email</div>
				<div class="somdn-tracked-download-content">
					<?php echo $user_email; ?>
				</div>
			</div>

			<?php if ( $user_subbed != 'N/A' ) { ?>
				<div class="somdn-tracked-download-content-wrap">
					<div class="somdn-tracked-download-label">Newsletter</div>
					<div class="somdn-tracked-download-content">
						<?php echo $user_subbed; ?>
					</div>
				</div>
			<?php } ?>

			<?php if ( $user_fname != 'N/A' ) { ?>
				<div class="somdn-tracked-download-content-wrap">
					<div class="somdn-tracked-download-label">First Name</div>
					<div class="somdn-tracked-download-content">
						<?php echo $user_fname; ?>
					</div>
				</div>
			<?php } ?>

			<?php if ( $user_lname != 'N/A' ) { ?>
				<div class="somdn-tracked-download-content-wrap">
					<div class="somdn-tracked-download-label">Last Name</div>
					<div class="somdn-tracked-download-content">
						<?php echo $user_lname; ?>
					</div>
				</div>
			<?php } ?>

			<?php if ( $user_tel != 'N/A' ) { ?>
				<div class="somdn-tracked-download-content-wrap">
					<div class="somdn-tracked-download-label">Telephone</div>
					<div class="somdn-tracked-download-content">
						<?php echo $user_tel; ?>
					</div>
				</div>
			<?php } ?>

			<?php if ( $user_company != 'N/A' ) { ?>
				<div class="somdn-tracked-download-content-wrap">
					<div class="somdn-tracked-download-label">Company</div>
					<div class="somdn-tracked-download-content">
						<?php echo $user_company; ?>
					</div>
				</div>
			<?php } ?>

			<?php if ( $user_website != 'N/A' ) { ?>
				<div class="somdn-tracked-download-content-wrap">
					<div class="somdn-tracked-download-label">Website</div>
					<div class="somdn-tracked-download-content">
						<?php echo $user_website; ?>
					</div>
				</div>
			<?php } ?>

			<div class="somdn-tracked-download-content-wrap">
				<div class="somdn-tracked-download-label">Date</div>
				<div class="somdn-tracked-download-content">
					<?php the_time('l, F jS, Y \a\t g:ia'); ?>
				</div>
			</div>

			<div class="somdn-tracked-download-content-wrap">
				<div class="somdn-tracked-download-label">IP Address</div>
				<div class="somdn-tracked-download-content"><?php echo $user_ip; ?></div>
			</div>

		</div>

	</div>

<?php
}

//add_action( 'show_user_profile', 'somdn_user_download_limit_fields', 30 );
//add_action( 'edit_user_profile', 'somdn_user_download_limit_fields', 30 );
function somdn_user_download_limit_fields( $user ) {

	$user_id = $user->ID;

	?>

	<h3>User Specific Free Download Limits</h3>
	<span class="somdn-woo-meta-span" style="font-size: 13px;">Override global <a target="_blank" href="<?php echo somdn_get_plugin_link_full_admin(); ?>&tab=settings&section=limit">free download limits</a> <span class="woocommerce-help-tip" data-tip="Only applies if limit restrictions are enabled in Free Downloads."></span></span>
	<span class="somdn-woo-meta-span" style="font-size: 13px;">Note: Any other limit settings aren't inherited, so this form must be completed fully.</span>

<?php

		$limit_enable = intval( get_the_author_meta( 'somdn_user_limit_enable', $user_id ) );
		if ( empty( $limit_enable ) ) $limit_enable = '';

		$amount_value = intval( get_the_author_meta( 'somdn_user_limit_amount', $user_id ) );
		if ( empty( $amount_value ) ) $amount_value = '';

		$prod_value = intval( get_the_author_meta( 'somdn_user_limit_products', $user_id ) );
		if ( empty( $prod_value ) ) $prod_value = '';

		$freq_value = intval( get_the_author_meta( 'somdn_user_limit_freq', $user_id ) );
		if ( empty( $freq_value) ) $freq_value = '';

		$limit_error = stripslashes( get_the_author_meta( 'somdn_user_limit_error', $user_id ) );
		if ( empty( $limit_error ) ) $limit_error = '';

?>

	<table class="form-table somdn-setting-table">

		<tr>
			<th>Enable user limits</th>
			<td>

				<fieldset><legend class="screen-reader-text"><span>Enable user limit</span></legend>
					<label for="somdn_user_limit_enable">
					<input type="checkbox" name="somdn_user_limit_enable" id="somdn_user_limit_enable"
					<?php
						$checked = isset( $limit_enable ) ? checked( $limit_enable, true ) : '' ;
					?>
						value="1">
					Enable specific limit settings for this user to override any others
					</label>
				<br>
				</fieldset>

			</td>
		</tr>

		<tr>
			<th><label for="somdn_user_limit_freq">Download limit period</label></th>
			<td>
				<span class="somdn-woo-meta-span">
					<select name="somdn_user_limit_freq" id="somdn_user_limit_freq" style="float: none;">
						<option value="" <?php selected( $freq_value, '' ); ?> class="somdn_invalid_select">Please choose...</option>
						<option value="1" <?php selected( $freq_value, 1 ); ?>>Day</option>
						<option value="2" <?php selected( $freq_value, 2 ); ?>>Week</option>
						<option value="3" <?php selected( $freq_value, 3 ); ?>>Month</option>
						<option value="4" <?php selected( $freq_value, 4 ); ?>>Year</option>
					</select>
				</span>
				<p class="description">Set download limits for each day/week/month/year.</p>
			</td>
		</tr>

		<tr>
			<th><label for="somdn_user_limit_amount">Number of downloads</label></th>
			<td>
				<span class="somdn-woo-meta-span">
					<input type="number" min="0" max="1000" value="<?php echo $amount_value; ?>" name="somdn_user_limit_amount" id="somdn_user_limit_amount" placeholder="0 - 10000" class="somdn-number-input">
					<span class="description">Leave blank for unlimited</span>
				</span>
				<p class="description">Limit the number of times a download can be requested.</p>
			</td>
		</tr>

		<tr>
			<th><label for="somdn_user_limit_products">Number of products</label></th>
			<td>
				<span class="somdn-woo-meta-span">
					<input type="number" min="0" max="1000" value="<?php echo $amount_value; ?>" name="somdn_user_limit_products" id="somdn_user_limit_products" placeholder="0 - 10000" class="somdn-number-input">
					<span class="description">Leave blank for unlimited</span>
				</span>
				<p class="description">Limit the number of different products that can be downloaded.</p>
			</td>
		</tr>

		<tr>
			<th><label for="somdn_user_limit_error">Error Message</label></th>
			<td>
				<div class="som-settings-pro-basic-limit-error-wrap">
					<p>Customise the limit reached message for this user.</p>

					<?php

						$editor_id = 'somdn_user_limit_error';
						$settings = array(
							'media_buttons' => false,
							'tinymce'=> array(
								'toolbar1' => 'bold,italic,link,undo,redo',
								'toolbar2'=> false
							),
							'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
							'editor_class' => 'required',
							'teeny' => true,
							'editor_height' => 180,
							'textarea_name' => 'somdn_user_limit_error'
						);
						$content = $limit_error;

						wp_editor( $content, $editor_id, $settings );

					?>

					<br>
					<p class="description"><strong>Default for limited downloads, unlimited products:</strong><br>Your free download limit is (Number of downloads) downloads per (Download limit period).</p><br>
					<p class="description"><strong>Default for limited downloads, limited products:</strong><br>Your free download limit is (Number of downloads) downloads for (Number of products) products per (Download limit period).</p><br>
					<p class="description"><strong>Default for unlimited downloads, limited products:</strong><br>Your free download limit is (Number of products) products per (Download limit period).</p><br>

				</div>

			</td>
		</tr>

	</table>

<?php }