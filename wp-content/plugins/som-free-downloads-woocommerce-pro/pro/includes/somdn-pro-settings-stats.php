<?php
/**
 * Free Downloads - WooCommerce - Stats
 * 
 * @version 1.1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'somdn_settings_tabs_after_settings', 'somdn_settings_tabs_woo_stats', 8, 1 );
function somdn_settings_tabs_woo_stats( $active_tab ) { ?>
	<a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=stats" class="nav-tab <?php echo $active_tab == 'stats' ? 'nav-tab-active' : ''; ?>">Stats <span class="som-settings-ui-new">Beta</span></a>
<?php }

add_filter( 'somdn_get_settings_sub_tabs', 'somdn_get_settings_sub_tabs_stats', 50, 1 );
function somdn_get_settings_sub_tabs_stats( $extra_tabs ) {

	if ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'stats' ) {
			
		$active_section = isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : 'overview';

		ob_start(); ?>

		<ul class="subsubsub">
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=stats&section=overview" class="<?php echo $active_section == 'overview' ? 'current' : ''; ?>">Overview</a> | </li>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=stats&section=reports" class="<?php echo $active_section == 'reports' ? 'current' : ''; ?>">Reports</a>
		</ul>

		<?php

		$extra_tabs = ob_get_clean();

	}

	return $extra_tabs;

}

add_action( 'somdn_settings_after_settings', 'somdn_settings_pro_stats', 30, 1 );
function somdn_settings_pro_stats( $active_tab ) {
	if ( $active_tab == 'stats' ) {

		$active_section = isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : 'overview';

		if ( 'overview' == $active_section ) {

			somdn_stats_settings_overview();

			//somdn_support_guide();

		} elseif ( 'reports' == $active_section ) {

			somdn_stats_settings_content();

		}

		do_action( 'somdn_settings_pro_stats' );
	}
}

//add_action( 'somdn_settings_pro_stats', 'somdn_settings_pro_stats_content', 10 );
function somdn_settings_pro_stats_content() {
	somdn_stats_settings_content();
}

add_action( 'somdn_stats_errors', 'somdn_stats_errors_output', 50 );
function somdn_stats_errors_output() {

	if ( empty( $_REQUEST ) ) {
		return;
	}

	ob_start();

	$somdn_stats_errors = isset( $_REQUEST['somdn_stats_errors'] ) ? $_REQUEST['somdn_stats_errors'] : '' ;

	$somdn_errors_used = array();

	if ( ! empty( $somdn_stats_errors ) && is_array( $somdn_stats_errors ) ) :

		$allowed_tags = somdn_get_allowed_html_tags();

		foreach ( $somdn_stats_errors as $somdn_error ) :
			if ( ! empty( $somdn_error ) && is_array( $somdn_error ) ) :
				foreach ( $somdn_error as $error ) :

					$cleaned_error = wpautop( wp_kses( $error, $allowed_tags ) );

					if ( ! in_array( $cleaned_error, $somdn_errors_used ) ) :

						array_push( $somdn_errors_used, $cleaned_error ); ?>

						<div class="somdn-setting-warning-wrap somdn-setting-warning-alert">
							<?php echo $cleaned_error; ?>
						</div>

					<?php endif;

				endforeach;
			endif;
		endforeach;

	endif;

	$error_content = ob_get_clean();
	echo $error_content;

}

add_action( 'init', 'somdn_stats_export_init' );
function somdn_stats_export_init() {

	$_REQUEST['somdn_stats_errors'] = array();

	if ( ! isset( $_POST['somdn_export_data_nonce'] ) )
		return;

	if ( ! isset( $_POST['somdn_export_data'] ) )
		return;

	$nonce_key = sanitize_key( $_POST['somdn_export_data_nonce'] );
	if ( ! wp_verify_nonce( $nonce_key, 'somdn_export_data_nonce' ) ) {
		return;
	}

	$start_date = isset( $_POST['somdn_date_after'] ) ? sanitize_text_field( $_POST['somdn_date_after'] ) : '' ;
	$end_date = isset( $_POST['somdn_date_before'] ) ? sanitize_text_field( $_POST['somdn_date_before'] ) : '' ;

	$radio_value = '';
	$days_since = '';

	if ( isset( $_POST['somdn_stats_date_radio'] ) ) {
		$radio_value = $_POST['somdn_stats_date_radio'];
	}

	switch ( $radio_value ) {
		case 1:
			// 7 day report
			$days_since = 7;
			break;

		case 2:
			// 30 day report
			$days_since = 30;
			break;

		case 3:
			// 90 day report
			$days_since = 90;
			break;

		case 4:
			// 180 day report
			$days_since = 180;
			break;

		default:
			// Custom dates
			break;
	}

	if ( ! empty( $days_since ) ) {
		$start_date = date( 'Y-m-d', strtotime( '-' . $days_since . ' days' ) );
	}

	$download_args = array(
		'start_date' => $start_date,
		'end_date' => $end_date
	);

	$download_data = somdn_get_downloads_data( $download_args );

	if ( empty( $download_data ) ) {
		$no_data_error = __( 'No free download data found.', 'somdn-pro' );
		$errors['no_data_error'] = $no_data_error;
		array_push( $_REQUEST['somdn_stats_errors'], $errors);
		return;
	}

	$data_type = sanitize_text_field( $_POST['somdn_export_data'] );

	if ( empty( $data_type ) )
		return;

	switch ( $data_type ) {
		case 'csv':
			somdn_generate_test_csv( $download_data );
			break;

		default:
			// 'xlsx'
			somdn_generate_xlsx( $download_data );
			break;
	}

}

function somdn_generate_test_csv( $download_data = '' ) {

	// output headers so that the file is downloaded rather than displayed
	header('Content-Encoding: UTF-8');
	header('Content-type: text/csv;');
	$today = date( "Y-m-d-H-i-s" );
	$filename = 'free-downloads-' . $today . '.csv';
	header('Content-Disposition: attachment; filename="' . $filename . '"');

	// do not cache the file
	header('Pragma: no-cache');
	header('Expires: 0');

/*
	// Left here for legacy
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename=demo.csv');
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
*/

	// Required in order for HTML entities to correctly display
	echo "\xEF\xBB\xBF";

	// create a file
	$file = fopen('php://output', 'w');

	// output each row of the data and add to the CSV file
	foreach ( $download_data as $download ) {
		fputcsv($file, $download);
	}

	exit;

}

function somdn_generate_xlsx( $download_data = '' ) {

	$headers = array_shift( $download_data );
	$headers_all = somdn_stats_get_headers_all();
	$new_headers = array();
	foreach ( $headers_all as $heading => $content ) {
		$title = $content['title'];
		$content_type = ( isset( $content['content'] ) && ! empty( $content['content'] ) ) ? $content['content'] : 'GENERAL';
		$new_headers[$title] = $content_type;
	}

	$today = date( "Y-m-d-H-i-s" );
	$filename = 'free-downloads-' . $today . '.xlsx';

	require_once( SOMDN_PATH_PRO . 'includes/somdn-pro-settings-stats-excel.php' );
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
	error_reporting(E_ALL & ~E_NOTICE);
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');

	$header = $new_headers;

	$current_user_id = get_current_user_id();
	$author = 'WordPress';

	if ( $current_user_id ) {
		$user = get_user_by( 'ID', $current_user_id );
		$author = $user->user_login;
	}

	$writer = new XLSXWriter();
	$writer->setAuthor( $author );
/*
	$header_styles = array(
		'font' => 'Calibri',
		'font-size' => 11,
		'halign' => 'left',
		'valign' => 'top',
		'auto_filter' => true
	);
*/
	$header_styles = array(
		'font' => 'Calibri',
		'font-size' => 11,
		'halign' => 'left',
		'valign' => 'top',
		'wrap_text' => true
	);
	$body_styles = array(
		'font' => 'Calibri',
		'font-size' => 11,
		'halign' => 'left',
		'valign' => 'top',
		'wrap_text' => true
	);

	$writer->writeSheetHeader( 'Free Downloads', $header, $header_styles );

	foreach( $download_data as $row ) {
		$writer->writeSheetRow( 'Free Downloads', $row, $body_styles );
	}

	$writer->writeToStdOut();
	exit;

}

add_filter( 'somdn_stats_get_custom_field_product_name', 'somdn_stats_get_product_name', 10, 5 );
function somdn_stats_get_product_name( $value, $download, $download_id, $header, $post_meta ) {
	$product_name = get_the_title( get_post_meta( $download_id, 'somdn_product_id', true ) );
	$value = html_entity_decode( $product_name, ENT_QUOTES, 'UTF-8' );
	return $value;
}

add_filter( 'somdn_stats_get_custom_field_somdn_username', 'somdn_stats_get_username', 20, 5 );
function somdn_stats_get_username( $value, $download, $download_id, $header, $post_meta ) {
	$user_name = '';
	$user_id = get_post_meta( $download_id, 'somdn_user_id', true );
	$user = isset( $user_id ) && $user_id != 0 ? get_userdata( $user_id ) : '' ;
	if ( ! empty( $user ) ) {
		//$value = html_entity_decode( $value, ENT_QUOTES, 'UTF-8' );
		$user_name = $user->user_login;
	}
	return $user_name;
}

function somdn_get_downloads_data( $args ) {

	$defaults = array(
		'start_date' => '',
		'end_date' => '',
		'day' => '',
		'week' => '',
		'month' => '',
		'year' => '',
		'objects' => false,
		'orderby' => 'date',
		'order' => 'DESC'
	);

	$args = wp_parse_args( $args, $defaults );

	// Set up the data array, which will contain all rows
	$download_data = array();

	$end_date = '';
	$start_date = '';

	$date_query_vars = array();

	if ( ! empty( $args['end_date'] ) ) {
		$end_date = date( 'Y-m-d', strtotime( $args['end_date'] ) );
		$date_query_vars['before'] = $end_date;
	}

	if ( ! empty( $args['start_date'] ) ) {
		$start_date = date( 'Y-m-d', strtotime( $args['start_date'] ) );
		$date_query_vars['after'] = $start_date;
	}

	if ( ! empty( $args['day'] ) ) {
		$date_query_vars['day'] = intval( $args['day'] );
	}
	if ( ! empty( $args['week'] ) ) {
		$date_query_vars['week'] = intval( $args['week'] );
	}
	if ( ! empty( $args['month'] ) ) {
		$date_query_vars['month'] = intval( $args['month'] );
	}
	if ( ! empty( $args['year'] ) ) {
		$date_query_vars['year'] = intval( $args['year'] );
	}

	$stats_args = array(
		'posts_per_page' => -1,
		'post_type' => 'somdn_tracked',
		'order' => $args['order'],
		'orderby' => $args['orderby'],
		'date_query' => array( $date_query_vars )
	);

	//echo '<p>$stats_args</p>';
	//echo '<pre>';
	//print_r($stats_args);
	//echo '</pre>';

	$downloads = new WP_Query( $stats_args );

	$downloads_array = $downloads->posts;

	if ( empty( $downloads_array ) ) {
		// Return an empty array if no downloads found
		return array();
	}

	if ( $args['objects'] == true ) {
		return $downloads_array;
	}

	$headings = somdn_stats_get_headers_array();
	$header_content = somdn_stats_get_headers_all();

	$download_data[] = $headings; // The first row is the stat headings

	//$meta_ran = false;

	// Loop through each tracked download
	foreach ( $downloads_array as $download ) {

		$download_id = $download->ID;
		$row = array();
		$post_meta = get_post_custom( $download_id );

		//if ( $meta_ran == false ) {
		//	somdn_debug_array($post_meta);
		//	$meta_ran = true;
		//}

		// Loop through each heading
		foreach ( $header_content as $header => $content ) {

			// $header is the meta field id

			$type = $content['type'];
			$title = $content['title'];

			if ( $type == 'meta' ) {

				$row_meta = get_post_meta( $download_id, $header, true );

				$row_data = ( isset( $row_meta ) && ! empty( $row_meta ) ? $row_meta : '' );
				$row_new = '';

				if ( $row_data && is_array( $row_data ) ) {
					//echo $post_meta[$header][0] . '<br>';
					//print_r($row_data);
					foreach ( $row_data as $value ) {
						$row_new .= $value . "\r";
					}
					//$row_new = rtrim($row_new, ', ');
				} else {
					$row_new = esc_html( $row_data );
				}

				//$row_data = $row_new;

				//if ( $title == 'Files' ) {
				//	$row_new = $post_meta[$header][0];
					//$row_new_string = '';
					//if ( is_array( $row_new ) ) {
					//	foreach ($row_new as $value) {
					//		$row_new_string .= $value . ', ';
					//	}
					//}
				//	$row[] = 'Some file';
				//} else {
				//	$row[] = ( isset( $post_meta[$header] ) && ! empty( $post_meta[$header] ) ? $post_meta[$header][0] : '' );
				//}
				//
				$row[] = $row_new;

			} elseif ( $type == 'default' ) {

				if ( $header == 'ID' ) {
					$row[] = $download_id;
				} elseif ( $header == 'post_date' ) {
					$row[] = get_the_date( 'Y-m-d', $download_id );
				} else {
					$row[] = 'Default';
				}

			} elseif ( $type == 'custom' ) {

				$row[] = apply_filters( 'somdn_stats_get_custom_field_' . $header, 'Custom', $download, $download_id, $header, $post_meta );

			} else {

				$row[] = array();

			}

		}

		$download_data[] = $row;

	}

	//somdn_debug_array($download_data);
	//exit;

	return $download_data;

}

function somdn_stats_get_headers_array() {
	$headers = somdn_stats_get_headers_all();
	$header_titles = array();
	foreach ( $headers as $heading => $content ) {
		$title = $content['title'];
		$header_titles[] = $title;
	}
	return $header_titles;
}

function somdn_stats_get_headers_all() {

	$headers = array(
		'ID' => array(
			'type' => 'default',
			'title' => 'Download ID',
			'content' => 'integer'
		),
		'post_date' => array(
			'type' => 'default',
			'title' => 'Date',
			'content' => 'dd/mm/yyyy'
		),
		'somdn_product_id' => array(
			'type' => 'meta',
			'title' => 'Product ID',
			'content' => 'integer'
		),
		'product_name' => array(
			'type' => 'custom',
			'title' => 'Product Name',
			'content' => 'string'
		),
		'somdn_download_files' => array(
			'type' => 'meta',
			'title' => 'Files',
			'content' => 'string'
		),
		'somdn_variation_id' => array(
			'type' => 'meta',
			'title' => 'Variation ID',
			'content' => 'integer'
		),
		'somdn_user_id' => array(
			'type' => 'meta',
			'title' => 'User ID',
			'content' => 'integer'
		),
		'somdn_username' => array(
			'type' => 'custom',
			'title' => 'Username',
			'content' => 'string'
		),
		'somdn_user_email' => array(
			'type' => 'meta',
			'title' => 'Email Address',
			'content' => 'string'
		),
		'somdn_user_ip' => array(
			'type' => 'meta',
			'title' => 'IP Address',
			'content' => 'string'
		),
		'somdn_user_subbed' => array(
			'type' => 'meta',
			'title' => 'Subscribed',
			'content' => 'integer'
		),
		'somdn_user_fname' => array(
			'type' => 'meta',
			'title' => 'First Name',
			'content' => 'string'
		),
		'somdn_user_lname' => array(
			'type' => 'meta',
			'title' => 'Last Name',
			'content' => 'string'
		),
		'somdn_user_tel' => array(
			'type' => 'meta',
			'title' => 'Telephone',
			'content' => 'string'
		),
		'somdn_user_company' => array(
			'type' => 'meta',
			'title' => 'Company',
			'content' => 'string'
		),
		'somdn_user_website' => array(
			'type' => 'meta',
			'title' => 'Website',
			'content' => 'string'
		)
	);

	return apply_filters( 'somdn_stats_get_headers_all', $headers );

}

/**
 * Custom excel report column example usage
/*
add_filter( 'somdn_stats_get_headers_all', 'somdn_stats_get_headers_all_demo', 10, 1 );
function somdn_stats_get_headers_all_demo( $headers ) {
	$new_header = array(
		'type' => 'custom',
		'title' => 'Demo',
		'content' => 'string'
		);
	$headers['somdn_demo'] = $new_header;
	return $headers;
}

add_filter( 'somdn_stats_get_custom_field_somdn_demo', 'somdn_stats_get_custom_field_somdn_demo_content', 10, 5 );
function somdn_stats_get_custom_field_somdn_demo_content( $value, $download, $download_id, $header, $post_meta ) {
	$value = (int) $download_id + 10;
	return $value;
}
*/

function somdn_stats_get_products_downloads( $days = '' ) {

	if ( ! empty( $days ) ) {
		$start_date = date( 'Y-m-d', strtotime( '-' . $days . ' days' ) );
	} else {
		$start_date = '';
	}

	// If false then only the monthly downloads will be shown
	$top_10_all_time = apply_filters('somdn_stats_page_top_10_all_time', true);

	if ( $top_10_all_time === true ) {
		$download_args = array(
			'objects' => true,
			'order' => 'ASC',
			'start_date' => $start_date
		);
	} else {
		$download_args = array(
			'objects' => true,
			'order' => 'ASC',
			'year' => date('Y'),
			'month' => date('n')
		);
	}

	$download_data = somdn_get_downloads_data( $download_args );

	//echo count($download_data);

	//return $download_data;

	$post_product_array = wp_list_pluck( $download_data, 'somdn_product_id', 'post_date' );

	unset($download_data);

	$post_date_array_clean = array();

	$product_downloads = array_count_values( $post_product_array );

	unset($post_product_array);

	return $product_downloads;

	$all_days = array();
	$all_days_full = array();

	for ($i=0; $i<$days; $i++) {
		array_push( $all_days, date( 'Y-m-d', strtotime( '-' . $i . ' days' ) ) );
	}

	$all_days = array_reverse( $all_days );

	foreach ( $all_days as $key => $value ) {
		$all_days_full[$value] = 0;
	}

	foreach ( $all_days_full as $key => $value ) {
		if ( isset( $date_downloads[$key] ) ) {
			$all_days_full[$key] = $date_downloads[$key];
		} else {
			$all_days_full[$key] = 0;
		}
	}

	return $all_days_full;

}

function somdn_stats_settings_overview() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12 som-settings-guide">

					<div class="som-settings-gen-settings-form-wrap">
						<?php do_action( 'somdn_stats_errors' ); ?>
					</div>

			</div>

		</div>
	</div>

	<?php $track_options = get_option( 'somdn_pro_track_settings' );
	$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : 0 ;
	if ( ! $track_enabled ) : ?>

		<div class="som-settings-container">
			<div class="som-settings-row">
			
				<div class="som-settings-col-12 som-settings-guide">

						<div class="som-settings-gen-settings-form-wrap">
							<div class="somdn-setting-warning-wrap somdn-setting-warning-alert">
								<?php echo '<p><a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=tracking">Download tracking</a> is disabled.</p>';?>
							</div>
						</div>

				</div>

			</div>
		</div>

	<?php return; endif ; ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-4 som-settings-guide">

					<div class="som-settings-gen-settings-form-wrap">

							<?php

							$show_top_10 = apply_filters('somdn_stats_page_show_top_10', true);
							// If false then only then monthly instead of all time
							$top_10_all_time = apply_filters('somdn_stats_page_top_10_all_time', true);
							$top_10_text = ( $top_10_all_time === true ) ? 'All Time' : 'This Month';

							// Get all downloads

							$day = date('d');
							$week = date('W');
							$month = date('n');
							$year = date('Y');

							$show_day = apply_filters('somdn_stats_page_show_day', true);
							if ( $show_day === true ) {
								$today_args = array(
									'objects' => true,
									'day' => $day,
									'week' => $week,
									'year' => $year
								);
								$today_download_data = somdn_get_downloads_data( $today_args );
								$today_count = count( $today_download_data );
								unset($today_download_data);
								//echo '<p>$today_count = ' . $today_count . '</p>';
							}

							$show_week = apply_filters('somdn_stats_page_show_week', true);
							if ( $show_week === true ) {
								$week_args = array(
									'objects' => true,
									'week' => $week,
									'year' => $year
								);
								$week_download_data = somdn_get_downloads_data( $week_args );
								$week_count = count( $week_download_data );
								unset($week_download_data);
								//echo '<p>$week_count = ' . $week_count . '</p>';
							}

							$show_month = apply_filters('somdn_stats_page_show_month', true);
							if ( $show_month === true ) {
								$month_args = array(
									'objects' => true,
									'year' => $year,
									'month' => $month
								);
								$month_download_data = somdn_get_downloads_data( $month_args );
								$month_count = count( $month_download_data );
								unset($month_download_data);
								//echo '<p>$month_count = ' . $month_count . '</p>';
							}

							$today_date = date( 'jS M Y' );

							?>

						<div class="somdn-stats-box-wrap">
							<h3>Downloads Overview  <span class="description"> <?php echo $today_date; ?></span></h3>
							<ul class="somdn-stats-box-list">
								<?php if ($show_day) echo "<li>Today: <strong>$today_count</strong></li>"; ?>
								<?php if ($show_week) echo "<li>This Week: <strong>$week_count</strong></li>"; ?>
								<?php if ($show_month) echo "<li>This Month: <strong>$month_count</strong></li>"; ?>
							</ul>
						</div>

					</div>

			</div>

			<div class="som-settings-col-8 som-settings-guide">

					<div class="som-settings-gen-settings-form-wrap">

						<?php if ( $show_top_10 === true ) : ?>

							<div class="somdn-stats-box-wrap">
								<h3>Popular Products  <span class="description"> <?php echo $top_10_text; ?></span></h3>
								<?php do_action( 'somdn_get_stats_overview_graphs' ); ?>
							</div>

						<?php endif; ?>

					</div>

			</div>

		</div>
	</div>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12 som-settings-guide">

					<div class="som-settings-gen-settings-form-wrap">

						

					</div>

			</div>

		</div>
	</div>


<?php }

/*
add_filter('somdn_stats_page_top_10_all_time', 'somdn_stats_page_top_10_all_time_month');
function somdn_stats_page_top_10_all_time_month() {
	return false;
}
*/

function somdn_stats_top_10_products() {
	$products = somdn_stats_get_products_downloads();

	// Sort product array by most popular product ID
	arsort($products);

	$top_10 = array_slice( $products, 0, 10, true );
	return $top_10;
}

add_action( 'somdn_get_stats_overview_graphs', 'somdn_get_stats_overview_graphs_output' );
function somdn_get_stats_overview_graphs_output() {

	ob_start(); ?>

	<div class="somdn-stats-graph-wrapper">

		<div id="somdn-stats-products-wrap">

		</div>

	</div>

<script type="text/javascript">

(function($) {

	$( document ).ready(function() {

<?php $top_10 = somdn_stats_top_10_products(); ?>

$(window).resize(function() {
	if(this.resizeTO) clearTimeout(this.resizeTO);
		this.resizeTO = setTimeout(function() {
		$(this).trigger('resizeEnd');
	}, 500);
});

$(window).on('resizeEnd', function() {
	drawTop10();
});

google.charts.load('current', {packages: ['corechart', 'bar']});
google.charts.setOnLoadCallback(drawTop10);

function drawTop10() {

    var data = new google.visualization.DataTable();
      data.addColumn('string', 'Product');
      data.addColumn('number', 'Downloads');

      data.addRows([
			<?php foreach ( $top_10 as $product => $downloads ) { ?>
				<?php echo "['" . html_entity_decode( get_the_title( $product ) ) . " (#" . $product . ")', " . $downloads . "],"; ?>
			<?php } ?>
      ]);

      var options = {
        title: '',
        height: 400,
        hAxis: {
          title: '',
          textStyle : {
						fontSize: 12
					},
					format: '0'
        },
        vAxis: {
          title: '',
          textStyle : {
						fontSize: 10
					}
        },
        chartArea: {
        	height: '92%',
        	width: '100%',
        	left: 190,
        	right: 40,
        	bottom: 30,
        	top: 10
        },
        legend: 'none'
      };

      var chart = new google.visualization.BarChart(
        document.getElementById('somdn-stats-products-wrap'));

      chart.draw(data, options);

}

	});

})( jQuery );
</script>

<?php 

	$output = ob_get_clean();
	echo $output;

}

function somdn_stats_get_days_downloads( $days = '7' ) {

	$download_args = array(
		'objects' => true,
		'order' => 'ASC',
		'start_date' => date( 'Y-m-d', strtotime( '-' . $days . ' days' ) )
	);

	$download_data = somdn_get_downloads_data( $download_args );

	$post_date_array = wp_list_pluck( $download_data, 'post_date', 'ID' );

	$post_date_array_clean = array();
	foreach ( $post_date_array as $post_date ) {
		$new_date = date( 'Y-m-d', strtotime( $post_date ) );
		$post_date_array_clean[] = $new_date;
	}

	$date_downloads = array_count_values( $post_date_array_clean );

	$all_days = array();
	$all_days_full = array();

	for ($i=0; $i<$days; $i++) {
		array_push( $all_days, date( 'Y-m-d', strtotime( '-' . $i . ' days' ) ) );
	}

	$all_days = array_reverse( $all_days );

	foreach ( $all_days as $key => $value ) {
		$all_days_full[$value] = 0;
	}

	foreach ( $all_days_full as $key => $value ) {
		if ( isset( $date_downloads[$key] ) ) {
			$all_days_full[$key] = $date_downloads[$key];
		} else {
			$all_days_full[$key] = 0;
		}
	}

	return $all_days_full;

}

add_action( 'admin_enqueue_scripts', 'somdn_get_script_assets_pro_stats' );
function somdn_get_script_assets_pro_stats() {

	if ( ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'download_now_dashboard' ) ) {

		if ( ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'stats' ) ) {

			/**
			 * Google Charts
			 */
				wp_enqueue_script( 'google_charts', 'https://www.gstatic.com/charts/loader.js', array( 'jquery' ), '' );

		}

	}

}

function somdn_stats_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12 som-settings-guide">

					<div class="som-settings-gen-settings-form-wrap">
						<?php do_action( 'somdn_stats_errors' ); ?>
					</div>

			</div>

		</div>
	</div>

	<?php $track_options = get_option( 'somdn_pro_track_settings' );
	$track_enabled = isset( $track_options['somdn_pro_track_enable'] ) ? $track_options['somdn_pro_track_enable'] : 0 ;
	if ( ! $track_enabled ) : ?>

		<div class="som-settings-container">
			<div class="som-settings-row">
			
				<div class="som-settings-col-12 som-settings-guide">

						<div class="som-settings-gen-settings-form-wrap">
							<div class="somdn-setting-warning-wrap somdn-setting-warning-alert">
								<?php echo '<p><a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=tracking">Download tracking</a> is disabled.</p>';?>
							</div>
						</div>

				</div>

			</div>
		</div>

	<?php return; endif ; ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-10 som-settings-guide">

				<form action="" class="som-settings-settings-form" method="post">

					<div class="som-settings-gen-settings-form-wrap">

						<?php

						$date_selection = isset( $_POST['somdn_stats_date_radio'] ) ? $_POST['somdn_stats_date_radio'] : 1 ;

						?>

						<input type="radio" class="somdn-stats-date-radio" id="somdn-stats-date-radio-1" name="somdn_stats_date_radio" value="1" <?php checked( 1, $date_selection, true ); ?>>
						<label class="somdn-stats-date-radio-label" for="somdn-stats-date-radio-1">7 Days</label><br class="somdn-mb-only">

						<input type="radio" class="somdn-stats-date-radio" id="somdn-stats-date-radio-2" name="somdn_stats_date_radio" value="2" <?php checked( 2, $date_selection, true ); ?>>
						<label class="somdn-stats-date-radio-label" for="somdn-stats-date-radio-2">30 Days</label><br class="somdn-mb-only">

						<input type="radio" class="somdn-stats-date-radio" id="somdn-stats-date-radio-3" name="somdn_stats_date_radio" value="3" <?php checked( 3, $date_selection, true ); ?>>
						<label class="somdn-stats-date-radio-label" for="somdn-stats-date-radio-3">90 Days</label><br class="somdn-mb-only">

						<input type="radio" class="somdn-stats-date-radio" id="somdn-stats-date-radio-4" name="somdn_stats_date_radio" value="4" <?php checked( 4, $date_selection, true ); ?>>
						<label class="somdn-stats-date-radio-label" for="somdn-stats-date-radio-4">180 Days</label><br class="somdn-mb-only">

						<input type="radio" class="somdn-stats-date-radio" id="somdn-stats-date-radio-5" name="somdn_stats_date_radio" value="5" <?php checked( 5, $date_selection, true ); ?>>
						<label class="somdn-stats-date-radio-label" for="somdn-stats-date-radio-5">Custom</label><br class="somdn-mb-only">

						<?php do_action ( 'somdn_get_stats_reports_graphs' ); ?>

						<div class="somdn-stats-date-custom-wrap">
							<p>
								<label for="somdn_date_after">Start Date:</label>
								<input type="date" name="somdn_date_after" id="somdn_date_after" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value=""> <span class="description">Leave blank for beginning of time</span>
							</p>

							<p>
								<label for="somdn_date_before">End Date:</label>
								<input type="date" name="somdn_date_before" id="somdn_date_before" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" value=""> <span class="description">Leave blank for today</span>
							</p>
						</div>

						<div class="somdn-stats-date-export-wrap">
							<p>
								<?php wp_nonce_field( 'somdn_export_data_nonce', 'somdn_export_data_nonce' ); ?>
								<button class="button" type="submit" name="somdn_export_data" value="xlsx">Export XLSX</button>
								<button class="button" type="submit" name="somdn_export_data" value="csv">Export CSV</button>
							</p>
						</div>

					</div>

				</form>
			</div>

		</div>
	</div>

<?php
/*
$datetime1 = new DateTime($date_after);

$datetime2 = new DateTime($date_before);

$difference = $datetime1->diff($datetime2);

$weeks = (int)(($difference->days) / 7);

echo 'Difference: '.$difference->y.' years, '
                   .$difference->m.' months, '
                   .$weeks.' weeks, '
                   .$difference->d.' days.';

echo '<pre>';
print_r($difference);
echo '</pre>';

?>

						</p>

						<p>
							After = <?php echo date( $date_option, strtotime( $date_after ) ); ?><br>
							Before = <?php echo date( $date_option, strtotime( $date_before ) ); ?>
						</p>

<pre>
<?php print_r($_POST); ?>
</pre>
*/

?>

<?php }

add_action( 'somdn_get_stats_reports_graphs', 'somdn_get_reports_graphs_output' );
function somdn_get_reports_graphs_output() {

	$show_stats = apply_filters('somdn_stats_page_show_report_stats', true);

	if ( $show_stats === false ) {
		return;
	}

	ob_start(); ?>

	<div class="somdn-stats-graph-wrapper">
		<div class="somdn-stats-graph-wrap" id="somdn-stats-7-wrap">

		</div>

		<div class="somdn-stats-graph-wrap" id="somdn-stats-30-wrap">

		</div>

		<div class="somdn-stats-graph-wrap" id="somdn-stats-90-wrap">

		</div>

		<div class="somdn-stats-graph-wrap" id="somdn-stats-180-wrap">

		</div>
	</div>

<script type="text/javascript">

(function($) {

	$( document ).ready(function() {

		var current_chart = 7;

<?php $downloads_7 = somdn_stats_get_days_downloads(); ?>
<?php $downloads_30 = somdn_stats_get_days_downloads(30); ?>
<?php $downloads_90 = somdn_stats_get_days_downloads(90); ?> 
<?php $downloads_180 = somdn_stats_get_days_downloads(180); ?> 

$(window).resize(function() {
	if(this.resizeTO) clearTimeout(this.resizeTO);
		this.resizeTO = setTimeout(function() {
		$(this).trigger('resizeEnd');
	}, 500);
});

$(window).on('resizeEnd', function() {
	//console.log('current_chart = ' + current_chart);
	switch( current_chart ) {
		case 7:
			drawBasic7();
			break;
		case 30:
			drawBasic30();
			break;
		case 90:
			drawBasic90();
			break;
		case 180:
			drawBasic180();
			break;
		default:
			drawBasic7();
			break;
	}
});

google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBasic7);
//google.charts.setOnLoadCallback(drawBasic30);
//google.charts.setOnLoadCallback(drawBasic90);
//google.charts.setOnLoadCallback(drawBasic180);

	$( '#somdn-stats-date-radio-1' ).click( function(e) {
		drawBasic7();
	});

	$( '#somdn-stats-date-radio-2' ).click( function(e) {
		drawBasic30();
	});

	$( '#somdn-stats-date-radio-3' ).click( function(e) {
		drawBasic90();
	});

	$( '#somdn-stats-date-radio-4' ).click( function(e) {
		drawBasic180();
	});


function drawBasic7() {

    var data = new google.visualization.DataTable();
      data.addColumn('string', 'Date');
      data.addColumn('number', 'Downloads');

      //echo "['" . date( 'jS M', strtotime( $key ) ) . "', " . $download . "],";

      <?php $total_downloads = 0; ?>

      data.addRows([
			<?php foreach ( $downloads_7 as $key => $download ) { ?>
				<?php echo "['" . date( 'j M', strtotime( $key ) ) . "', " . $download . "],"; ?>
				<?php if ( ! empty( $download ) ) : $total_downloads += $download ; endif; ?>
			<?php } ?>
      ]);

      var total_downloads = <?php echo $total_downloads; ?>;

      var options = {
        title: 'Downloads last 7 days (' + total_downloads + ')',
        chartArea: {
        	height: '100%',
        	width: '100%',
        	left: 50,
        	right: 50,
        	bottom: 50,
        	top: 60
        },
        height: 400,
        hAxis: {
          title: '',
          textStyle : {
						fontSize: 12
					}
        },
        vAxis: {
          title: '',
          format: '0',
          textStyle : {
						fontSize: 11
					}
        },
        legend: 'none'
      };

      var chart = new google.visualization.LineChart(
        document.getElementById('somdn-stats-7-wrap'));

      chart.draw(data, options);

	    current_chart = 7;

}

function drawBasic30() {

    var data = new google.visualization.DataTable();
      data.addColumn('string', 'Date');
      data.addColumn('number', 'Downloads');

      //echo "['" . date( 'jS M', strtotime( $key ) ) . "', " . $download . "],";

      <?php $total_downloads = 0; ?>

      data.addRows([
			<?php foreach ( $downloads_30 as $key => $download ) { ?>
				<?php echo "['" . date( 'j M', strtotime( $key ) ) . "', " . $download . "],"; ?>
				<?php if ( ! empty( $download ) ) : $total_downloads += $download ; endif; ?>
			<?php } ?>
      ]);

      var total_downloads = <?php echo $total_downloads; ?>;

      var options = {
        title: 'Downloads last 30 days (' + total_downloads + ')',
        chartArea: {
        	height: '100%',
        	width: '100%',
        	left: 50,
        	right: 50,
        	bottom: 50,
        	top: 60
        },
        height: 400,
        hAxis: {
          title: '',
          textStyle : {
						fontSize: 12
					},
					showTextEvery: 2
        },
        vAxis: {
          title: '',
          format: '0',
          textStyle : {
						fontSize: 11
					}
        },
        legend: 'none'
      };

      var chart = new google.visualization.LineChart(
        document.getElementById('somdn-stats-30-wrap'));

      chart.draw(data, options);

      current_chart = 30;

}

function drawBasic90() {

    var data = new google.visualization.DataTable();
      data.addColumn('string', 'Date');
      data.addColumn('number', 'Downloads');

      //echo "['" . date( 'jS M', strtotime( $key ) ) . "', " . $download . "],";

      <?php $total_downloads = 0; ?>

      data.addRows([
			<?php foreach ( $downloads_90 as $key => $download ) { ?>
				<?php echo "['" . date( 'j M', strtotime( $key ) ) . "', " . $download . "],"; ?>
				<?php if ( ! empty( $download ) ) : $total_downloads += $download ; endif; ?>
			<?php } ?>
      ]);

      var total_downloads = <?php echo $total_downloads; ?>;

      var options = {
        title: 'Downloads last 90 days (' + total_downloads + ')',
        chartArea: {
        	height: '100%',
        	width: '100%',
        	left: 50,
        	right: 50,
        	bottom: 50,
        	top: 60
        },
        height: 400,
        hAxis: {
          title: '',
          textStyle : {
						fontSize: 10
					},
					showTextEvery: 4
        },
        vAxis: {
          title: '',
          format: '0',
          textStyle : {
						fontSize: 11
					}
        },
        legend: 'none'
      };

      var chart = new google.visualization.LineChart(
        document.getElementById('somdn-stats-90-wrap'));

      chart.draw(data, options);

      current_chart = 90;

}

function drawBasic180() {

    var data = new google.visualization.DataTable();
      data.addColumn('string', 'Date');
      data.addColumn('number', 'Downloads');

      //echo "['" . date( 'jS M', strtotime( $key ) ) . "', " . $download . "],";

      <?php $total_downloads = 0; ?>

      data.addRows([
			<?php foreach ( $downloads_180 as $key => $download ) { ?>
				<?php echo "['" . date( 'j M', strtotime( $key ) ) . "', " . $download . "],"; ?>
				<?php if ( ! empty( $download ) ) : $total_downloads += $download ; endif; ?>
			<?php } ?>
      ]);

      var total_downloads = <?php echo $total_downloads; ?>;

      var options = {
        title: 'Downloads last 180 days (' + total_downloads + ')',
        chartArea: {
        	height: '100%',
        	width: '100%',
        	left: 50,
        	right: 50,
        	bottom: 50,
        	top: 60
        },
        height: 400,
        hAxis: {
          title: '',
          textStyle : {
						fontSize: 10
					},
					showTextEvery: 8
        },
        vAxis: {
          title: '',
          format: '0',
          textStyle : {
						fontSize: 11
					}
        },
        legend: 'none',
        slantedText: true,
        slantedTextAngle: 90
      };

      var chart = new google.visualization.LineChart(
        document.getElementById('somdn-stats-180-wrap'));

      chart.draw(data, options);

      current_chart = 180;

}

	});

})( jQuery );
</script>

<?php 

	$output = ob_get_clean();
	echo $output;

}