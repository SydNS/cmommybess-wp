<?php
/**
 * Free Downloads - Woo Settings
 * 
 * WooCommerce specific functions/actions for settings
 * 
 * @version 3.1.5
 * @author  Square One Media
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'somdn_settings_tabs_after_settings', 'somdn_settings_tabs_woo_support', 10, 1 );
function somdn_settings_tabs_woo_support( $active_tab ) { ?>
	<a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
<?php }

add_action( 'somdn_settings_tabs_after_settings', 'somdn_settings_tabs_woo_more', 30, 1 );
function somdn_settings_tabs_woo_more( $active_tab ) { ?>
	<a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=more" class="nav-tab <?php echo $active_tab == 'more' ? 'nav-tab-active' : ''; ?>">More</a>
<?php }

add_action( 'somdn_after_file_loader', 'somdn_load_woo_basic_support', 10 );
function somdn_load_woo_basic_support() {
	require_once( SOMDN_PATH . 'woo-files/somdn-woo-settings-support.php' );
}

add_action( 'somdn_settings_subtabs_after_owned' , 'somdn_settings_subtabs_quickview', 10, 1 );
function somdn_settings_subtabs_quickview( $active_section ) {
	$nav_active = ( $active_section == 'quickview' ) ? 'current' : '' ;
	echo '<li><a href="' . somdn_get_plugin_link_full() . '&tab=settings&section=quickview" class="' . $nav_active . '">Quick View</a> <span class="som-settings-ui-new">Beta</span> | </li>';
}

add_action( 'somdn_settings_page_content' , 'somdn_settings_quickview_settings', 10, 1 );
function somdn_settings_quickview_settings( $active_section ) {
	if ( $active_section == 'quickview' ) {
		somdn_woo_quickview_settings_content();
	}
}

add_action('admin_menu', 'somdn_main_admin_menu', 95 );
function somdn_main_admin_menu() {

	add_submenu_page(
		'woocommerce',
		'Free Downloads',
		'Free Downloads',
		'manage_woocommerce',
		'download_now_dashboard',
		'somdn_options_page'
	);

}

add_action( 'somdn_after_require_login_settings', 'somdn_woo_archive_setting', 10 );
function somdn_woo_archive_setting() {
	add_settings_field( 
		'somdn_include_archive_items',
		NULL,
		'somdn_include_archive_items_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);
	add_settings_field( 
		'somdn_hide_readmore_button_archive',
		NULL,
		'somdn_hide_readmore_button_archive_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);
}

add_action( 'somdn_after_gen_settings_section', 'somdn_after_gen_settings_section_woo', 10 );
function somdn_after_gen_settings_section_woo() {
	add_settings_field( 
		'somdn_read_more_text', 
		__( 'Shop Button Text', 'somdn-pro' ), 
		'somdn_read_more_text_render', 
		'somdn_gen_settings', 
		'somdn_gen_settings_section' 
	);
}

add_action( 'somdn_after_include_archive_items_settings', 'somdn_after_include_archive_items_settings_woo', 10 );
function somdn_after_include_archive_items_settings_woo() {
	add_settings_field( 
		'somdn_include_sale_items',
		NULL,
		'somdn_include_sale_items_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);
}

function somdn_woo_quickview_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="som-settings-gen-settings-form-wrap">
			
						<?php

							settings_fields( 'somdn_woo_quickview_settings' );
							do_settings_sections( 'somdn_woo_quickview_settings' );
							submit_button();

						?>
			
					</div>
			
				</form>
		
			</div>

			<?php $img_location = plugins_url( '/images/', SOMDN_WOO_FILE ); ?>
			
			<div class="som-settings-col-5 som-settings-guide som-settings-multi-guide">

				<div class="som-settings-guide-img">
					<h3>Shop Listing (mouse hovered)</h3>
					<img src="<?php echo $img_location . 'quick-view-shop.png'; ?>">
				</div>

				<div class="som-settings-guide-img">
					<h3>Quick View Popup</h3>
					<img src="<?php echo $img_location . 'quick-view-shop-product.png'; ?>" style="width: 360px;">
				</div>

			</div>

		</div>
	</div>


<?php

}

function somdn_woo_quickview_settings_section_callback() { ?>
	<p><?php _e( 'Enable the Quick View feature for shop listing pages. Quick View will apply to all products.', 'somdn-pro' ); ?> </p>
	<p><?php _e( 'Quick View allows users to preview a product without going to the product page, by clicking a "Quick View" button when hovering their mouse over a product in the shop.', 'somdn-pro' ); ?></p>
	<p class="description"><?php _e( 'Note: This feature is currently in beta. Please report any issues you find.', 'somdn-pro' ); ?></p><br>
<?php }

add_action( 'somdn_after_docviewer_settings', 'somdn_woo_quickview_settings', 10 );
function somdn_woo_quickview_settings() {

	register_setting( 'somdn_woo_quickview_settings', 'somdn_woo_quickview_settings' );

	add_settings_section(
		'somdn_woo_quickview_settings_section',
		__( 'WooCommerce Quick View Settings', 'somdn-pro' ),
		'somdn_woo_quickview_settings_section_callback',
		'somdn_woo_quickview_settings'
	);

	add_settings_field(
		'somdn_woo_quickview_enable',
		__( 'Enable Quick View', 'somdn-pro' ),
		'somdn_woo_quickview_enable_render',
		'somdn_woo_quickview_settings',
		'somdn_woo_quickview_settings_section'
	);

	$quickview_options = get_option( 'somdn_woo_quickview_settings' );
	$quickview_enabled = isset( $quickview_options['somdn_woo_quickview_enable'] ) ? $quickview_options['somdn_woo_quickview_enable'] : 0 ;
	if ( ! empty( $quickview_enabled ) ) {

		add_settings_field(
			'somdn_woo_quickview_button_text',
			__( 'Button Settings', 'somdn-pro' ),
			'somdn_woo_quickview_button_text_render',
			'somdn_woo_quickview_settings',
			'somdn_woo_quickview_settings_section'
		);

		add_settings_field(
			'somdn_woo_quickview_button_colour',
			NULL,
			'somdn_woo_quickview_button_colour_render',
			'somdn_woo_quickview_settings',
			'somdn_woo_quickview_settings_section',
			array( 'class' => 'somdn-settings-table-no-top' )
		);

		add_settings_field(
			'somdn_woo_quickview_button_text_colour',
			NULL,
			'somdn_woo_quickview_button_text_colour_render',
			'somdn_woo_quickview_settings',
			'somdn_woo_quickview_settings_section',
			array( 'class' => 'somdn-settings-table-no-top' )
		);

	}

}

function somdn_woo_quickview_enable_render() {

	$options = get_option( 'somdn_woo_quickview_settings' );
	$value = isset( $options['somdn_woo_quickview_enable'] ) ? $options['somdn_woo_quickview_enable'] : 0 ; ?>
	
	<label for="somdn_woo_quickview_settings[somdn_woo_quickview_enable]">
	<input type="checkbox" name="somdn_woo_quickview_settings[somdn_woo_quickview_enable]" id="somdn_woo_quickview_settings[somdn_woo_quickview_enable]"
	<?php
		$checked = isset( $value ) ? checked( $value, true ) : '' ;
	?>
		value="1">
	Enable WooCommerce Quick View
	</label>

	<?php if ( ! $value ) { ?>
		<p class="description"><?php _e( 'Note: When enabled you will be able to customise this feature.', 'somdn-pro' ); ?></p>
	<?php } ?>

<?php

}

function somdn_woo_quickview_button_text_render() {

	$options = get_option( 'somdn_woo_quickview_settings' );
	$value = isset( $options['somdn_woo_quickview_button_text'] ) ? $options['somdn_woo_quickview_button_text'] : '' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Button Text</strong></p>

	<p class="som-mar-bot-15"><strong>Optional: </strong>Customise the quick view button text.</p>
	
	<input type="text" name="somdn_woo_quickview_settings[somdn_woo_quickview_button_text]" value="<?php echo $value; ?>" style="width: 300px; max-width: 100%;">
	<p class="description">Default: <strong>Quick View</strong></p>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_woo_quickview_button_colour_render() {

	$options = get_option( 'somdn_woo_quickview_settings' );
	$value = isset( $options['somdn_woo_quickview_button_colour'] ) ? $options['somdn_woo_quickview_button_colour'] : '#2679ce' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Background Colour</strong></p>

	<div class="somdn-wp-picker-container">
		<input type="text" name="somdn_woo_quickview_settings[somdn_woo_quickview_button_colour]" id="somdn-quickview-button-colour" value="<?php echo $value; ?>" class="somdn-colour-picker" data-default-color="#2679ce">
	</div>

	<hr class="som-setting-sep sep-300">

	<?php

}

function somdn_woo_quickview_button_text_colour_render() {

	$options = get_option( 'somdn_woo_quickview_settings' );
	$value = isset( $options['somdn_woo_quickview_button_text_colour'] ) ? $options['somdn_woo_quickview_button_text_colour'] : '#fff' ; ?>

	<p style="margin-bottom: 15px; font-size: 16px;"><strong>Font Colour</strong></p>

	<div class="somdn-wp-picker-container">
		<input type="text" name="somdn_woo_quickview_settings[somdn_woo_quickview_button_text_colour]" id="somdn-quickview-button-text-colour" value="<?php echo $value; ?>" class="somdn-colour-picker" data-default-color="#fff">
	</div>

	<hr class="som-setting-sep sep-300">

	<?php

}

add_filter( 'somdn_plugin_review_link', 'somdn_plugin_review_link_woo_free' );
function somdn_plugin_review_link_woo_free() {
	return 'https://wordpress.org/support/plugin/download-now-for-woocommerce/reviews/#new-post';
}

add_filter( 'somdn_settings_header_title', 'somdn_settings_header_title_woo', 10, 1 );
function somdn_settings_header_title_woo( $title ) {
	return 'Free Downloads<br>WooCommerce';
}

add_filter( 'somdn_get_settings_home', 'somdn_get_settings_home_woo', 10, 1 );
function somdn_get_settings_home_woo( $content ) {

	ob_start(); ?>

			<div class="som-settings-col-6">
	
				<p><strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong> is the definitive plugin for offering free downloads on your WooCommerce store.</p>
				
				<p>It allows users to bypass the checkout to download free products, supports single and multiple files, and is highly customisable.</p>
				
				<p>This plugin is safe and rock-solid secure, and everything is handled by your server including authentication, so you don&#39;t have to worry.</p>
				
				<p><strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong> is also fully integrated with the official Memberships and Subscriptions plugins for WooCommerce.</p>				
		
			</div>

			<?php $somdn_image_01 = plugins_url( '/images/download-image-01.png', SOMDN_WOO_FILE ); ?>
			
			<div class="som-settings-col-6 som-settings-guide">
			
				<div class="som-settings-guide-img">
					<img src="<?php echo $somdn_image_01; ?>">
				</div>
			
			</div>

	<?php $content = ob_get_clean();

	return $content;

}

function somdn_settings_more() {

	$responsive_youtube = plugins_url( '/assets/images/responsive-youtube.jpg', dirname(__FILE__) );
	$somdn_edd = plugins_url( '/assets/images/somdn-edd.jpg', dirname(__FILE__) );
	$strong_pass = plugins_url( '/assets/images/somspedd.png', dirname(__FILE__) );
	$reset_pass = plugins_url( '/assets/images/somfrp.png', dirname(__FILE__) );

	?>

	<div class="som-settings-container som-pad-top-10">
		<div class="som-settings-row">

			<div class="som-settings-col-12 som-settings-guide">

				<p class="som-pad-bot-30">Looking for more plugins by <strong>Square One Media?</strong></p>

				<div class="som-settings-plugin-other-wrap">

					<div class="som-settings-plugin-other">
						<a class="som-settings-plugin-other-link" href="https://squareonemedia.co.uk/products/responsive-videos/" target="_blank">
							<div class="som-settings-plugin-other-img">
								<img src="<?php echo $responsive_youtube; ?>">
							</div>
							<div class="som-settings-plugin-other-bottom">
								<h3>Responsive Videos</h3>
							</div>	
						</a>
					</div>

					<div class="som-settings-plugin-other">
						<a class="som-settings-plugin-other-link" href="https://wordpress.org/plugins/free-downloads-edd/" target="_blank">
							<div class="som-settings-plugin-other-img">
								<img src="<?php echo $somdn_edd; ?>">
							</div>
							<div class="som-settings-plugin-other-bottom">
								<h3>Free Downloads EDD</h3>
							</div>	
						</a>
					</div>	

					<div class="som-settings-plugin-other">
						<a class="som-settings-plugin-other-link" href="https://squareonemedia.co.uk/products/strong-passwords-edd/" target="_blank">
							<div class="som-settings-plugin-other-img">
								<img src="<?php echo $strong_pass; ?>">
							</div>
							<div class="som-settings-plugin-other-bottom">
								<h3>Strong Passwords EDD</h3>
							</div>	
						</a>
					</div>	

					<div class="som-settings-plugin-other">
						<a class="som-settings-plugin-other-link" href="https://squareonemedia.co.uk/products/frontend-reset-password/" target="_blank">
							<div class="som-settings-plugin-other-img">
								<img src="<?php echo $reset_pass; ?>">
							</div>
							<div class="som-settings-plugin-other-bottom">
								<h3>Frontend Reset Password</h3>
							</div>	
						</a>
					</div>

				</div>

			</div>

		</div>
	</div>

<?php

}

add_action( 'somdn_settings_after_settings', 'somdn_settings_more_settings', 10, 1 );
function somdn_settings_more_settings( $active_tab ) {
	if ( $active_tab == 'more' ) {
		somdn_settings_more();
	}
}

add_action( 'somdn_settings_tabs_after_settings' , 'somdn_settings_tabs_pro_settings', 60, 1 );
function somdn_settings_tabs_pro_settings( $active_tab ) {
	$nav_active = ( $active_tab == 'prosettings' ) ? ' nav-tab-active' : '' ;
	echo '<a href="' . somdn_get_plugin_link_full() . '&tab=prosettings" class="nav-tab' . $nav_active . '">Pro Edition</a>';
}

add_action( 'somdn_settings_after_settings', 'somdn_settings_pro_settings', 30, 1 );
function somdn_settings_pro_settings( $active_tab ) {
	if ( $active_tab == 'prosettings' ) {
		do_action( 'somdn_do_pro_settings_content' );
	}
}

add_action( 'somdn_do_pro_settings_content', 'somdn_do_pro_settings_content_basic', 10 );
function somdn_do_pro_settings_content_basic() {
	somdn_basic_pro_settings_content();
}

function somdn_basic_pro_settings_content() { ?>

	<div class="som-settings-container" style="padding-top: 20px;">
		<div class="som-settings-row">
		
			<div class="som-settings-col-6">
	
				<p><strong><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?></strong> is great, but did you know there's a better version?</p>

				<p>Upgrade to <strong style="font-size: 16px;"><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?> Pro</strong> today and get access to these fantastic features!</p>

				<ul class="som-settings-settings-features-list">
					<li><p>Full support for variable and grouped products</p></li>
					<li><p>Compatibility with WooCommerce PDF Watermark</p></li>
					<li><p>Option to serve your downloads after redirecting to a page or emailing a link</p></li>
					<li><p>Download limitations (daily, weekly, monthly, yearly)</p></li>
					<li><p>Detailed free download tracking with email capture and MailChimp subscriptions</p></li>
					<li><p>Show a list of the user's free download history and limits on their account page</p></li>
					<li><p>Access to Square One Media premium support</p></li>
					<li><p>Access to product feature requests</p></li>
					<li><p>Easy one-click plugin updates</p></li>
					<li><p>Loyalty discounts, special offers and more!</p></li>
				</ul>
				<br>
				<p><a class="btn som-settings-upgrade" href="https://squareonemedia.co.uk/products/free-downloads-woocommerce/" target="_blank">Upgrade Today!</a></p>
		
			</div>

			<?php $somdn_image_01 = plugins_url( '/assets/images/Free Downloads - WooCommerce Banner 1.jpg', dirname(__FILE__) ); ?>
			
			<div class="som-settings-col-6 som-settings-guide">
			
				<div class="som-settings-guide-img">
					<img src="<?php echo $somdn_image_01; ?>">
				</div>
			
			</div>

		</div>
	</div>


<?php }

add_action( 'somdn_support_after_logging', 'somdn_support_after_logging_basic', 10 );
function somdn_support_after_logging_basic() { ?>
	<p><em>Note:</em> <strong><a href="https://squareonemedia.co.uk/products/free-downloads-woocommerce/" target="_blank"><?php echo constant( 'SOMDN_PLUGIN_NAME_FULL' ); ?> Pro</a></strong> includes much more detailed and robust download tracking.</p>
<?php }