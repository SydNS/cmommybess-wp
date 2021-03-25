<?php
/**
 * Free Downloads - WooCommerce - Pro Settings
 * 
 * The custom settings and setting outputs for premium features
 * 
 * @version 1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load individual settings files
require_once( SOMDN_PATH_PRO . 'includes/somdn-pro-settings-limits.php' );
require_once( SOMDN_PATH_PRO . 'includes/somdn-pro-settings-tracking.php' );
require_once( SOMDN_PATH_PRO . 'includes/somdn-pro-settings-newsletter.php' );
require_once( SOMDN_PATH_PRO . 'includes/somdn-pro-settings-license.php' );
require_once( SOMDN_PATH_PRO . 'includes/somdn-pro-settings-include-exclude.php' );
require_once( SOMDN_PATH_PRO . 'includes/somdn-pro-settings-stats.php' );
require_once( SOMDN_PATH_PRO . 'includes/somdn-pro-settings-download-type.php' );
require_once( SOMDN_PATH_PRO . 'includes/somdn-pro-settings-emails.php' );

remove_action( 'somdn_support_after_logging', 'somdn_support_after_logging_basic' );

remove_filter( 'somdn_settings_header_title', 'somdn_settings_header_title_woo' );
add_filter( 'somdn_settings_header_title', 'somdn_settings_header_title_woo_pro', 10, 1 );
function somdn_settings_header_title_woo_pro( $title ) {
	return 'Free Downloads<br>WooCommerce Pro';
}

remove_filter( 'plugin_row_meta', 'somdn_plugin_row_meta', 10 );

remove_action( 'somdn_settings_bottom', 'somdn_get_settings_bottom_content', 10 );
add_action( 'somdn_settings_bottom', 'somdn_get_settings_bottom_pro', 10 );
function somdn_get_settings_bottom_pro() { ?>

	<div class="som-settings-container som-settings-message-footer">
		<div class="som-settings-row">
			<div class="som-settings-col-12">
				<p>If you like this plugin please leave us a <a href="https://wordpress.org/support/plugin/download-now-for-woocommerce/reviews/#new-post" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> review!</p>
			</div>
		</div>
	</div>

<?php

}

add_action( 'admin_init', 'somdn_pro_settings_init' );
function somdn_pro_settings_init() {

	do_action( 'somdn_pro_settings' );

}

add_action( 'somdn_after_require_login_settings', 'somdn_woo_grouped_message_setting', 10 );
function somdn_woo_grouped_message_setting() {
	add_settings_field( 
		'somdn_require_login_grouped_message',
		NULL,
		'somdn_require_login_grouped_message_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);
}

add_action( 'somdn_after_gen_settings_section', 'somdn_pro_global_site_settings', 5 );
function somdn_pro_global_site_settings() {
	add_settings_field( 
		'somdn_pro_disable_ecommerce', 
		__( 'Global Site Settings', 'somdn-pro' ), 
		'somdn_pro_disable_ecommerce_render', 
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);
}

function somdn_pro_disable_ecommerce_render() { 

	$options = get_option( 'somdn_gen_settings' ); ?>
	
	<label for="somdn_gen_settings[somdn_pro_disable_ecommerce]">
	<input type="checkbox" name="somdn_gen_settings[somdn_pro_disable_ecommerce]" id="somdn_gen_settings[somdn_pro_disable_ecommerce]"
	<?php
		$checked = isset( $options['somdn_pro_disable_ecommerce'] ) ? checked( $options['somdn_pro_disable_ecommerce'], true ) : '' ;
	?>
		value="1">
	Disable all eCommerce functionality
	</label>
	<p class="description">Notes:</p>
	<p class="description">This will prevent any product, paid or otherwise, from being purchased. All cart/checkout functionality is removed.</p>
	<p class="description">Free products still need to be free and set up correctly for free download to work.</p>
	<?php

}

function somdn_require_login_grouped_message_render() {
	$options = get_option( 'somdn_gen_settings' );
	$output_text = ( isset( $options['somdn_require_login_grouped_message'] ) && $options['somdn_require_login_grouped_message'] ) ? $options['somdn_require_login_grouped_message'] : '' ; ?>

		<p class="som-mar-bot-15">Message to display on grouped product page if not logged in.</p>
		<p class="description">Default: Only registered users can download the free product part of this group.</p>

		<div class="max-500w">

		<?php

			$editor_id = 'somdn_require_login_grouped_message';
			$settings = array(
				'media_buttons' => false,
				'tinymce'=> array(
					'toolbar1' => 'bold,italic,underline,alignleft,aligncenter,alignright,alignjustify,link,undo,redo',
					'toolbar2'=> false
				),
				'quicktags' => array( 'buttons' => 'strong,em,link,close' ),
				'editor_class' => 'required',
				'teeny' => true,
				'editor_height' => 150,
				'textarea_name' => 'somdn_gen_settings[somdn_require_login_grouped_message]'
			);
			$content = $output_text;

			wp_editor( $content, $editor_id, $settings );

		?>

		</div>

	<?php

}