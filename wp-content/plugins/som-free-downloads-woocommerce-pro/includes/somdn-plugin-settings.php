<?php
/**
 * Free Downloads - Settings
 * 
 * The custom settings and setting outputs.
 * 
 * @version	3.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'somdn_settings_bottom', 'somdn_get_settings_bottom_content', 10 );

add_action( 'admin_init', 'somdn_settings_init' );
function somdn_settings_init() { 

	register_setting( 'somdn_gen_settings', 'somdn_gen_settings' );

	add_settings_section(
		'somdn_gen_settings_section',
		__( 'General Settings', 'somdn-pro' ),
		'somdn_gen_settings_section_callback',
		'somdn_gen_settings'
	);

	do_action( 'somdn_after_gen_settings_section' );

	add_settings_field(
		'somdn_require_login',
		__( 'Files', 'somdn-pro' ),
		'somdn_require_login_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);

	add_settings_field(
		'somdn_require_login_message',
		NULL,
		'somdn_require_login_message_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);

	do_action( 'somdn_after_require_login_settings' );

	do_action( 'somdn_after_include_archive_items_settings' );

	add_settings_field(
		'somdn_indy_items',
		NULL,
		'somdn_indy_items_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);

	add_settings_field(
		'somdn_indy_exclude_items',
		NULL,
		'somdn_indy_exclude_items_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);

	add_settings_field(
		'somdn_disable_security_key_check',
		NULL,
		'somdn_disable_security_key_check_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);

	do_action( 'somdn_after_files_settings' );

	add_settings_field(
		'somdn_download_counts_output',
		__( 'Download Counts', 'somdn-pro' ),
		'somdn_download_counts_output_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);

	$gen_options = get_option( 'somdn_gen_settings' );
	$somdn_download_output_enabled = isset( $gen_options['somdn_download_counts_output'] ) ? $gen_options['somdn_download_counts_output'] : '' ;

	if ( $somdn_download_output_enabled ) :

		add_settings_field(
			'somdn_download_counts_output_text',
			NULL,
			'somdn_download_counts_output_text_render',
			'somdn_gen_settings',
			'somdn_gen_settings_section',
			array( 'class' => 'somdn_download_counts_output_text' )
		);

	endif;

	do_action( 'somdn_after_download_count_settings' );

	add_settings_field(
		'somdn_button_class',
		__( 'Button classes', 'somdn-pro' ),
		'somdn_button_class_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);

	add_settings_field(
		'somdn_button_css',
		__( 'Button CSS', 'somdn-pro' ),
		'somdn_button_css_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);

	add_settings_field(
		'somdn_link_class',
		__( 'Link classes', 'somdn-pro' ),
		'somdn_link_class_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);

	add_settings_field(
		'somdn_link_css',
		__( 'Link CSS', 'somdn-pro' ),
		'somdn_link_css_render',
		'somdn_gen_settings',
		'somdn_gen_settings_section'
	);

	do_action( 'somdn_after_link_css_settings' );

	register_setting( 'somdn_single_settings', 'somdn_single_settings' );

	add_settings_section(
		'somdn_single_settings_section',
		__( 'Single File Settings', 'somdn-pro' ),
		'somdn_single_settings_section_callback',
		'somdn_single_settings'
	);

	add_settings_field(
		'somdn_single_type',
		__( 'Display method', 'somdn-pro' ),
		'somdn_single_type_render',
		'somdn_single_settings',
		'somdn_single_settings_section'
	);

	add_settings_field(
		'somdn_single_button_text',
		__( 'Button text', 'somdn-pro' ),
		'somdn_single_button_text_render',
		'somdn_single_settings',
		'somdn_single_settings_section'
	);

	add_settings_field(
		'somdn_single_button_filename',
		__( 'Show filename', 'somdn-pro' ),
		'somdn_single_button_filename_render',
		'somdn_single_settings',
		'somdn_single_settings_section'
	);

	add_settings_field(
		'somdn_single_force_zip',
		__( 'Force ZIP', 'somdn-pro' ),
		'somdn_single_force_zip_render',
		'somdn_single_settings',
		'somdn_single_settings_section'
	);

	register_setting( 'somdn_multi_settings', 'somdn_multi_settings' );

	add_settings_section(
		'somdn_multi_settings_section',
		__( 'Multiple File Settings', 'somdn-pro' ),
		'somdn_multi_settings_section_callback',
		'somdn_multi_settings'
	);

	add_settings_field(
		'somdn_display_type',
		__( 'Display method', 'somdn-pro' ),
		'somdn_display_type_render',
		'somdn_multi_settings',
		'somdn_multi_settings_section'
	);

	add_settings_field(
		'somdn_multi_button_text',
		__( 'Button text', 'somdn-pro' ),
		'somdn_multi_button_text_render',
		'somdn_multi_settings',
		'somdn_multi_settings_section'
	);
	
	add_settings_field(
		'somdn_available_downloads_text',
		__( 'File list text', 'somdn-pro' ),
		'somdn_available_downloads_text_render',
		'somdn_multi_settings',
		'somdn_multi_settings_section'
	);

	add_settings_field(
		'somdn_checkbox_error_text',
		__( 'Checkbox error text', 'somdn-pro' ),
		'somdn_checkbox_error_text_render',
		'somdn_multi_settings',
		'somdn_multi_settings_section'
	);

	add_settings_field(
		'somdn_select_all',
		__( 'Customise', 'somdn-pro' ),
		'somdn_select_all_render',
		'somdn_multi_settings',
		'somdn_multi_settings_section'
	);

	add_settings_field(
		'somdn_show_numbers',
		NULL,
		'somdn_show_numbers_render',
		'somdn_multi_settings',
		'somdn_multi_settings_section'
	);

	register_setting( 'somdn_owned_settings', 'somdn_owned_settings' );

	add_settings_section(
		'somdn_owned_settings_section',
		__( 'Owned Products Settings', 'somdn-pro' ),
		'somdn_owned_settings_section_callback',
		'somdn_owned_settings'
	);

	add_settings_field(
		'somdn_owned_enable',
		__( 'Include Purchased Products', 'somdn-pro' ),
		'somdn_owned_enable_render',
		'somdn_owned_settings',
		'somdn_owned_settings_section'
	);

	$owned_options = get_option( 'somdn_owned_settings' );
	$somdn_owned_enabled = isset( $owned_options['somdn_owned_enable'] ) ? $owned_options['somdn_owned_enable'] : '' ;

	if ( $somdn_owned_enabled ) :

		add_settings_field(
			'somdn_owned_button_text',
			__( 'Button Text', 'somdn-pro' ),
			'somdn_owned_button_text_render',
			'somdn_owned_settings',
			'somdn_owned_settings_section'
		);

		add_settings_field(
			'somdn_owned_badge_text',
			__( 'Badge Text', 'somdn-pro' ),
			'somdn_owned_badge_text_render',
			'somdn_owned_settings',
			'somdn_owned_settings_section'
		);

		add_settings_field(
			'somdn_owned_badge_hide',
			__( 'Hide Badge', 'somdn-pro' ),
			'somdn_owned_badge_hide_render',
			'somdn_owned_settings',
			'somdn_owned_settings_section'
		);

	endif;

	register_setting( 'somdn_docviewer_settings', 'somdn_docviewer_settings' );

	add_settings_section(
		'somdn_docviewer_settings_section',
		__( 'PDF Viewer Settings', 'somdn-pro' ),
		'somdn_docviewer_settings_section_callback',
		'somdn_docviewer_settings'
	);

	add_settings_field(
		'somdn_docviewer_enable',
		__( 'Enable PDF Viewer', 'somdn-pro' ),
		'somdn_docviewer_enable_render',
		'somdn_docviewer_settings',
		'somdn_docviewer_settings_section'
	);

	add_settings_field( 
		'somdn_docviewer_single_display', 
		__( 'Single file display', 'somdn-pro' ), 
		'somdn_docviewer_single_display_render', 
		'somdn_docviewer_settings', 
		'somdn_docviewer_settings_section' 
	);

	add_settings_field( 
		'somdn_docviewer_single_link_text', 
		__( 'Link/Button Text', 'somdn-pro' ), 
		'somdn_docviewer_single_link_text_render', 
		'somdn_docviewer_settings', 
		'somdn_docviewer_settings_section' 
	);

	do_action( 'somdn_after_docviewer_settings' );

	register_setting( 'somdn_debug_settings', 'somdn_debug_settings' );

	add_settings_section(
		'somdn_debug_settings_section',
		__( 'Debugging', 'somdn-pro' ),
		'somdn_debug_settings_section_callback',
		'somdn_debug_settings'
	);

	add_settings_field(
		'somdn_debug_logging_enable',
		__( 'Debug Logging', 'somdn-pro' ),
		'somdn_debug_logging_enable_render',
		'somdn_debug_settings',
		'somdn_debug_settings_section'
	);

}

function somdn_debug_settings_section_callback() { ?>
	<p><?php _e( 'Settings related to plugin debugging.', 'somdn-pro' ); ?></p>
<?php }

function somdn_debug_logging_enable_render() {

	$options = get_option( 'somdn_debug_settings' );
	$value = isset( $options['somdn_debug_logging_enable'] ) ? $options['somdn_debug_logging_enable'] : '' ; ?>
	
	<label for="somdn_debug_settings[somdn_debug_logging_enable]">
	<input type="checkbox" name="somdn_debug_settings[somdn_debug_logging_enable]" id="somdn_debug_settings[somdn_debug_logging_enable]"
	<?php
		$checked = isset( $options['somdn_debug_logging_enable'] ) ? checked( $options['somdn_debug_logging_enable'], true ) : '' ;
	?>
		value="1">
	Show debug messages in error logs
	</label>

	<?php

}

function somdn_owned_settings_section_callback() { 
	echo __( 'Customise the behaviour for products already purchased.', 'somdn-pro' );
}

function somdn_owned_enable_render() {

	$options = get_option( 'somdn_owned_settings' );
	$value = isset( $options['somdn_owned_enable'] ) ? $options['somdn_owned_enable'] : '' ; ?>
	
	<label for="somdn_owned_settings[somdn_owned_enable]">
	<input type="checkbox" name="somdn_owned_settings[somdn_owned_enable]" id="somdn_owned_settings[somdn_owned_enable]"
	<?php
		$checked = isset( $options['somdn_owned_enable'] ) ? checked( $options['somdn_owned_enable'], true ) : '' ;
	?>
		value="1">
	Enable free downloads on product pages for paid products already owned by the user
	</label>

	<?php if ( ! $value ) { ?>
		<p class="description som-mar-bot-15"><?php _e( 'When enabled you will be able to customise this setting.', 'somdn-pro' ); ?></p>
	<?php } ?>

	<p class="description">Note: Products will be set to a 100% discount.</p>
	<p class="description">Note: This function takes into account the user's ability to download the file from their account page.</p>

	<?php

}

function somdn_owned_button_text_render() { 

	$options = get_option( 'somdn_owned_settings' ); ?>

	<input type="text" name="somdn_owned_settings[somdn_owned_button_text]" value="<?php
	
	echo $text = isset( $options['somdn_owned_button_text'] ) ? $options['somdn_owned_button_text'] : '' ;
	
	?>">
	<p><strong>Optional: </strong>Customise the download button text for owned items. Also applies to multiple files and PDFs.</p>
	<p class="description">Default: <strong>Download Again</strong></p>
	<?php

}

function somdn_owned_badge_text_render() { 

	$options = get_option( 'somdn_owned_settings' ); ?>

	<input type="text" name="somdn_owned_settings[somdn_owned_badge_text]" value="<?php
	
	echo $text = isset( $options['somdn_owned_badge_text'] ) ? $options['somdn_owned_badge_text'] : '' ;
	
	?>">
	<p><strong>Optional: </strong>Customise the text in the product badge. See example image on the right.</p>
	<p class="description">Default: <strong>OWNED!</strong></p>
	<?php

}

function somdn_owned_badge_hide_render() {

	$options = get_option( 'somdn_owned_settings' ); ?>
	
	<label for="somdn_owned_settings[somdn_owned_badge_hide]">
	<input type="checkbox" name="somdn_owned_settings[somdn_owned_badge_hide]" id="somdn_owned_settings[somdn_owned_badge_hide]"
	<?php
		$checked = isset( $options['somdn_owned_badge_hide'] ) ? checked( $options['somdn_owned_badge_hide'], true ) : '' ;
	?>
		value="1">
	Hide the badge
	</label>

	<?php

}

function somdn_gen_settings_section_callback() { 
	echo __( 'Customise the global plugin settings.', 'somdn-pro' );
}

function somdn_read_more_text_render() { 

	$options = get_option( 'somdn_gen_settings' );
	$value = ( isset( $options['somdn_read_more_text'] ) && $options['somdn_read_more_text'] ) ? $options['somdn_read_more_text']: '' ; ?>
	
	<input type="text" name="somdn_gen_settings[somdn_read_more_text]" value="<?php echo $value; ?>" class="som-setting-input">
	<p class="description">On shop / archive pages. Blank = <strong>Read More</strong></p>
	<p class="description">If "Show download on shop pages" is enabled, Blank = <strong>Download</strong></p>
	<p class="description">PDF Viewer text will still show for PDF files, if enabled.</p>
	<?php

}

function somdn_include_archive_items_render() { 

	$options = get_option( 'somdn_gen_settings' ); ?>
	
	<label for="somdn_gen_settings[somdn_include_archive_items]">
	<input type="checkbox" name="somdn_gen_settings[somdn_include_archive_items]" id="somdn_gen_settings[somdn_include_archive_items]"
	<?php
		$checked = isset( $options['somdn_include_archive_items'] ) ? checked( $options['somdn_include_archive_items'], true ) : '' ;
	?>
		value="1">
	Allow download on shop / archive pages
	</label>
	<p class="description">Note: This will replace the "Read More" button.</p>
	<?php

}

function somdn_hide_readmore_button_archive_render() { 

	$options = get_option( 'somdn_gen_settings' ); ?>
	
	<label for="somdn_gen_settings[somdn_hide_readmore_button_archive]">
	<input type="checkbox" name="somdn_gen_settings[somdn_hide_readmore_button_archive]" id="somdn_gen_settings[somdn_hide_readmore_button_archive]"
	<?php
		$checked = isset( $options['somdn_hide_readmore_button_archive'] ) ? checked( $options['somdn_hide_readmore_button_archive'], true ) : '' ;
	?>
		value="1">
	Hide "Read more" button on shop pages if user can't download free product
	</label>
	<p class="description">Note: For example if a membership is required to purchase, but would be free otherwise.</p>
	<?php

}

function somdn_require_login_render() { 

	$options = get_option( 'somdn_gen_settings' ); ?>
	
	<label for="somdn_gen_settings[somdn_require_login]">
	<input type="checkbox" name="somdn_gen_settings[somdn_require_login]" id="somdn_gen_settings[somdn_require_login]"
	<?php
		$checked = isset( $options['somdn_require_login'] ) ? checked( $options['somdn_require_login'], true ) : '' ;
	?>
		value="1">
	Only show the button to logged in users
	</label>
	<?php do_action( 'somdn_require_login_render_text' ); ?>
	<?php do_action( 'somdn_require_login_render_after_text' ); ?>
	<?php

}

function somdn_require_login_message_render() {
	$options = get_option( 'somdn_gen_settings' );
	$output_text = ( isset( $options['somdn_require_login_message'] ) && $options['somdn_require_login_message'] ) ? $options['somdn_require_login_message'] : '' ; ?>

		<p class="som-mar-bot-15">Message to display on product pages if not logged in.</p>
		<p class="description">Default: Only registered users can download this free product.</p>

		<div class="max-500w">

		<?php

			$editor_id = 'somdn_require_login_message';
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
				'textarea_name' => 'somdn_gen_settings[somdn_require_login_message]'
			);
			$content = $output_text;

			wp_editor( $content, $editor_id, $settings );

		?>

		</div>

	<?php

}

//add_action( 'somdn_require_login_render_text', 'somdn_require_login_render_no_message', 10 );
function somdn_require_login_render_no_message() { ?>
	<p class="description">Note: If enabled you need to fill in the custom message below.</p>
<?php }

function somdn_include_sale_items_render() { 

	$options = get_option( 'somdn_gen_settings' ); ?>

	<label for="somdn_gen_settings[somdn_include_sale_items]">
	<input type="checkbox" name="somdn_gen_settings[somdn_include_sale_items]" id="somdn_gen_settings[somdn_include_sale_items]"
	<?php
		$checked = isset( $options['somdn_include_sale_items'] ) ? checked( $options['somdn_include_sale_items'], true ) : '' ;
	?>
		value="1">
	Include paid items that are currently on sale for free
	</label>
	<p class="description">Not recommended if you use the "redirect" download method.</p>
	<?php

}

function somdn_indy_items_render() { 

	$options = get_option( 'somdn_gen_settings' ); ?>

	<label for="somdn_gen_settings_somdn_indy_items">
	<input type="checkbox" name="somdn_gen_settings[somdn_indy_items]" id="somdn_gen_settings_somdn_indy_items"
	<?php
		$checked = isset( $options['somdn_indy_items'] ) ? checked( $options['somdn_indy_items'], true ) : '' ;
	?>
		value="1">
	Include selected products only
	</label>
	<p class="description">Tick this box if you want to choose which products are included.</p>
	<?php do_action( 'somdn_after_indy_items_render' ); ?>
	<?php

}

function somdn_indy_exclude_items_render() { 

	$options = get_option( 'somdn_gen_settings' ); ?>

	<label for="somdn_gen_settings_somdn_indy_exclude_items">
	<input type="checkbox" name="somdn_gen_settings[somdn_indy_exclude_items]" id="somdn_gen_settings_somdn_indy_exclude_items"
	<?php
		$checked = isset( $options['somdn_indy_exclude_items'] ) ? checked( $options['somdn_indy_exclude_items'], true ) : '' ;
	?>
		value="1">
	Exclude selected products only
	</label>
	<p class="description">Tick this box if you want to choose which products are excluded.</p>
	<?php do_action( 'somdn_after_indy_exclude_items_render' ); ?>
	<?php

}

function somdn_disable_security_key_check_render() { 

	$options = get_option( 'somdn_gen_settings' ); ?>

	<label for="somdn_gen_settings_somdn_disable_security_key_check">
	<input type="checkbox" name="somdn_gen_settings[somdn_disable_security_key_check]" id="somdn_gen_settings_somdn_disable_security_key_check"
	<?php
		$checked = isset( $options['somdn_disable_security_key_check'] ) ? checked( $options['somdn_disable_security_key_check'], true ) : '' ;
	?>
		value="1">
	Disable checking download security keys
	</label>
	<p class="description"><strong>Not recommended</strong></p>
	<p class="description">Tick this box if you don't want to check security keys during downloads.<br>This might help improve compatibility with cache plugins.</p>
	<?php do_action( 'somdn_after_disable_security_key_check_render' ); ?>
	<?php

}

function somdn_download_counts_output_render() {

	$options = get_option( 'somdn_gen_settings' );
	$type = ( isset( $options['somdn_download_counts_output'] ) && $options['somdn_download_counts_output'] ) ? $options['somdn_download_counts_output'] : 0 ; ?>

	<p>Display the total downloads count on product pages.</p><br>

	<select name="somdn_gen_settings[somdn_download_counts_output]">
		<option value="0">— Don't show —</option>
		<option value="1" <?php selected( $options['somdn_download_counts_output'], 1 ); ?>>Above download button</option>
		<option value="2" <?php selected( $options['somdn_download_counts_output'], 2 ); ?>>Below download button</option>
	</select>

	<?php

}

function somdn_download_counts_output_text_render() { 

	$options = get_option( 'somdn_gen_settings' );
	$output_text = ( isset( $options['somdn_download_counts_output_text'] ) && $options['somdn_download_counts_output_text'] ) ? esc_html( $options['somdn_download_counts_output_text'] ) : '' ; ?>

	<p>Customise the text. Use {count} to show the number.</p>
	<p class="description">Default: (Downloads - {count})</p>
	
	<input type="text" name="somdn_gen_settings[somdn_download_counts_output_text]" value="<?php echo $output_text; ?>" class="som-setting-input">

	<?php

}

function somdn_button_class_render() { 

	$options = get_option( 'somdn_gen_settings' );
	$classvalue = ( isset( $options['somdn_button_class'] ) && $options['somdn_button_class'] ) ? $options['somdn_button_class'] : '' ; ?>
	
	<input type="text" name="somdn_gen_settings[somdn_button_class]" value="<?php echo $classvalue; ?>" class="som-setting-input">
	<p class="description">Add custom classes to the download button, separated by spaces.</p>
	<?php

}

function somdn_button_css_render() { 

	$options = get_option( 'somdn_gen_settings' );
	$cssvalue = ( isset( $options['somdn_button_css'] ) && $options['somdn_button_css'] ) ? $options['somdn_button_css'] : '' ; ?>
	
	<input type="text" name="somdn_gen_settings[somdn_button_css]" value="<?php echo $cssvalue; ?>" class="som-setting-input">
	<p class="description">For theme styling the button uses the following CSS classes:<br>
	<code>somdn-download-button <?php echo somdn_get_button_classes(); ?></code></p>
	<?php

}

function somdn_link_class_render() { 

	$options = get_option( 'somdn_gen_settings' );
	$classvalue = ( isset( $options['somdn_link_class'] ) && $options['somdn_link_class'] ) ? $options['somdn_link_class'] : '' ; ?>
	
	<input type="text" name="somdn_gen_settings[somdn_link_class]" value="<?php echo $classvalue; ?>" class="som-setting-input">
	<p class="description">Add custom classes to the download link, separated by spaces.</p>
	<?php

}

function somdn_link_css_render() { 

	$options = get_option( 'somdn_gen_settings' );
	$cssvalue = ( isset( $options['somdn_link_css'] ) && $options['somdn_link_css'] ) ? $options['somdn_link_css'] : '' ; ?>
	
	<input type="text" name="somdn_gen_settings[somdn_link_css]" value="<?php echo $cssvalue; ?>" class="som-setting-input">
	<p class="description">For theme styling the links use the following CSS class:<br>
	<code>somdn-download-link</code></p>
	<?php

}

function somdn_single_settings_section_callback() { 

	echo __( 'Customise how products with a single file are handled.', 'somdn-pro' );

}

function somdn_single_type_render() {

	$options = get_option( 'somdn_single_settings' ); ?>

	<select name="somdn_single_settings[somdn_single_type]">
		<option value="1" <?php selected( $options['somdn_single_type'], 1 ); ?>>Button</option>
		<option value="2" <?php selected( $options['somdn_single_type'], 2 ); ?>>Link</option>
	</select>

	<?php

}

function somdn_single_button_text_render() { 

	$options = get_option( 'somdn_single_settings' ); ?>

	<input type="text" name="somdn_single_settings[somdn_single_button_text]" value="<?php
	
	echo $text = isset( $options['somdn_single_button_text'] ) ? $options['somdn_single_button_text'] : '' ;
	
	?>">
	<p class="description">Blank = <strong>Download Now</strong></p>
	<?php

}

function somdn_single_button_filename_render() { 

	$options = get_option( 'somdn_single_settings' ); ?>
	
	<label for="somdn_single_settings[somdn_single_button_filename]">
	<input type="checkbox" name="somdn_single_settings[somdn_single_button_filename]" id="somdn_single_settings[somdn_single_button_filename]"
	<?php
		$checked = isset( $options['somdn_single_button_filename'] ) ? checked( $options['somdn_single_button_filename'], true ) : '' ;
	?>
		value="1">
	Show the filename instead of text on product pages
	</label>
	<p class="description">Will override the <em>Button Text</em> option and apply to buttons and links.</p>
	<?php

}

function somdn_single_force_zip_render() { 

	$options = get_option( 'somdn_single_settings' ); ?>
	
	<label for="somdn_single_settings[somdn_single_force_zip]">
	<input type="checkbox" name="somdn_single_settings[somdn_single_force_zip]" id="somdn_single_settings[somdn_single_force_zip]"
	<?php
		$checked = isset( $options['somdn_single_force_zip'] ) ? checked( $options['somdn_single_force_zip'], true ) : '' ;
	?>
		value="1">
	Force ZIP creation for single files
	</label>
	<p class="description">Will always create a ZIP file for downloads with single files.</p>
	<?php

}

function somdn_multi_settings_section_callback() { ?>

	<p>Customise how products with multiple files are handled.</p>
	
	<p>Allowing the downloading of multiple files at once means those files will be zipped, which might be an issue for files hosted externally. If you use external files and have any errors, select "Links Only" for the display method. Otherwise, you can select any others.</p>

<?php
/**
 * Add a notice if ZipArchive is not installed on the server, which is required by the zile zipper downloader
 *
 * @return Output a notice informing the user
 */
	if ( ! class_exists( 'ZipArchive' ) ) {
		$class = 'som-settings-style-settings-warning';
		$message = __( 'Your server does not have ZipArchive installed, therefore zipping free downloads won\'t work.', 'somdn-pro' );
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}
?>

<?php
	
}

function somdn_display_type_render() {

	$options = get_option( 'somdn_multi_settings' );
	$type = ( isset( $options['somdn_display_type'] ) && $options['somdn_display_type'] ) ? $options['somdn_display_type'] : '1' ; ?>

	<select name="somdn_multi_settings[somdn_display_type]">
		<option value="1" <?php selected( $options['somdn_display_type'], 1 ); ?>>Links Only (default)</option>
		<option value="2" <?php selected( $options['somdn_display_type'], 2 ); ?>>Button Only (download all)</option>
		<option value="3" <?php selected( $options['somdn_display_type'], 3 ); ?>>Button + Checkboxes</option>
		<option value="4" <?php selected( $options['somdn_display_type'], 4 ); ?>>Button + Links</option>
		<option value="5" <?php selected( $options['somdn_display_type'], 5 ); ?>>Button + Filenames</option>
	</select>

	<?php

}

function somdn_available_downloads_text_render() { 

	$options = get_option( 'somdn_multi_settings' ); ?>

	<input type="text" name="somdn_multi_settings[somdn_available_downloads_text]" value="<?php
	
	echo $text = isset( $options['somdn_available_downloads_text'] ) ? $options['somdn_available_downloads_text'] : '' ;
	
	?>">
	<p class="description">Blank = <strong><span class="somdn-available-downloads"><em>Available Downloads:</em></span></strong></p>
	<?php

}

function somdn_checkbox_error_text_render() { 

	$options = get_option( 'somdn_multi_settings' ); ?>

	<input type="text" name="somdn_multi_settings[somdn_checkbox_error_text]" value="<?php
	
	echo $text = isset( $options['somdn_checkbox_error_text'] ) ? $options['somdn_checkbox_error_text'] : '' ;
	
	?>">
	<p class="description">Blank = <strong>Please select at least 1 checkbox</strong></p>
	<p class="description">Only shows if Button + Checkboxes is the display method.</p>
	<?php

}

function somdn_always_ZIP_render() { 

	$options = get_option( 'somdn_multi_settings' ); ?>
	
	<input type="checkbox" name="somdn_multi_settings[somdn_always_ZIP]"
	<?php
		$checked = isset( $options['somdn_always_ZIP'] ) ? checked( $options['somdn_always_ZIP'], true ) : '' ;
	?>
		value="1">
		
	<p class="description">Should download files always be zipped up, even if only 1 is selected?</p>
	<p>If more than 1 file downloaded, they will be zipped regardless.</p>
	<?php

}

function somdn_select_all_render() { 

	$options = get_option( 'somdn_multi_settings' ); ?>

	<label for="somdn_multi_settings[somdn_select_all]">
	<input type="checkbox" name="somdn_multi_settings[somdn_select_all]" id="somdn_multi_settings[somdn_select_all]"
	<?php
		$checked = isset( $options['somdn_select_all'] ) ? checked( $options['somdn_select_all'], true ) : '' ;
	?>
		value="1">
	Show Select All box
	</label>
	<p class="description">Only shows if Button + Checkboxes is the display method.</p>
	<?php

}

function somdn_show_numbers_render() { 

	$options = get_option( 'somdn_multi_settings' ); ?>

	<label for="somdn_multi_settings[somdn_show_numbers]">
	<input type="checkbox" name="somdn_multi_settings[somdn_show_numbers]" id="somdn_multi_settings[somdn_show_numbers]"
	<?php
		$checked = isset( $options['somdn_show_numbers'] ) ? checked( $options['somdn_show_numbers'], true ) : '' ;
	?>
		value="1">
	Show number next to filename
	</label>
	<p class="description">Only used when links or filenames are shown.</p>
	<?php

}

function somdn_multi_button_text_render() { 

	$options = get_option( 'somdn_multi_settings' ); ?>

	<input type="text" name="somdn_multi_settings[somdn_multi_button_text]" id="somdn_multi_settings[somdn_multi_button_text]" value="<?php
	
	echo $text = isset( $options['somdn_multi_button_text'] ) ? $options['somdn_multi_button_text'] : '' ;
	
	?>">
	<p class="description">Blank = <strong>Download All (.zip)</strong></p>
	<?php

}

function somdn_docviewer_settings_section_callback() { ?>

	<p>Rather than downloading a file as normal, PDF Viewer will open a preview of the PDF file attached to a product (regardless of your download settings). From there users can print or download the file. Visit the <a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=support&section=settings#docs">Settings Explained</a> page for more info.</p>

<?php
	
}

function somdn_docviewer_enable_render() { 

	$options = get_option( 'somdn_docviewer_settings' ); ?>
	
	<label for="somdn_docviewer_settings[somdn_docviewer_enable]">
	<input type="checkbox" name="somdn_docviewer_settings[somdn_docviewer_enable]" id="somdn_docviewer_settings[somdn_docviewer_enable]"
	<?php
		$checked = isset( $options['somdn_docviewer_enable'] ) ? checked( $options['somdn_docviewer_enable'], true ) : '' ;
	?>
		value="1">
	</label>
	<?php

}

function somdn_docviewer_single_display_render() {

	$options = get_option( 'somdn_docviewer_settings' ); ?>

	<select name="somdn_docviewer_settings[somdn_docviewer_single_display]">
		<option value="1" <?php selected( $options['somdn_docviewer_single_display'], 1 ); ?>>Button</option>
		<option value="2" <?php selected( $options['somdn_docviewer_single_display'], 2 ); ?>>Link</option>
	</select>

	<?php

}

function somdn_docviewer_single_link_text_render() { 

	$options = get_option( 'somdn_docviewer_settings' );
	$value = ( isset( $options['somdn_docviewer_single_link_text'] ) && $options['somdn_docviewer_single_link_text'] ) ? $options['somdn_docviewer_single_link_text']: '' ; ?>
	
	<input type="text" name="somdn_docviewer_settings[somdn_docviewer_single_link_text]" value="<?php echo $value; ?>" class="som-setting-input">
	<p class="description">Blank = <strong>Download PDF</strong></p>
	<?php

}


function somdn_options_page() { 

	somdn_get_settings_header_content();

	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'home';
	
	$active_section = isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : 'general';

	if ( $active_tab == 'home' ) {
	
		somdn_settings_home();

	}

	do_action( 'somdn_settings_after_home', $active_tab );
	
	if ( $active_tab == 'settings' ) {
	
		$active_section = isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : 'general';
		
		if ( 'general' == $active_section ) {
	
			somdn_gen_settings_content();
			
		} elseif ( 'single' == $active_section ) {
	
			somdn_single_settings_content();
		
		} elseif ( 'multiple' == $active_section ) {
	
			somdn_multi_settings_content();

		} elseif ( 'owned' == $active_section ) {
	
			somdn_owned_settings_content();

		} elseif ( 'docviewer' == $active_section ) {
	
			somdn_docviewer_settings_content();
			
		}
		
		do_action( 'somdn_settings_page_content', $active_section );
	
	}

	do_action( 'somdn_settings_after_settings', $active_tab, $active_section );

	do_action( 'somdn_settings_bottom' );

}

function somdn_get_settings_header_content() {

	somdn_get_admin_header(); ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
			<div class="som-settings-col-12 som-main-plugin-content">
				<h1><?php echo somdn_get_settings_header_title(); ?></h1>
			</div>
		</div>
	</div>

	<div class="som-settings-container som-settings-errors somdn-admin-notices">
		<div class="som-settings-row">
			<div class="som-settings-col-12">
				<div id="somdn-admin-notices"></div>
			</div>
		</div>
	</div>

	<div class="som-settings-container som-settings-errors">
		<div class="som-settings-row">
			<div class="som-settings-col-12">
				<?php settings_errors(); ?>
			</div>
		</div>
	</div>
	
	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12">
			
				<?php somdn_get_settings_tabs(); ?>
				
				<?php somdn_get_settings_sub_tabs(); ?>
	
			</div>
		</div>
	</div>

<?php

}

function somdn_get_settings_header_title() {
	return apply_filters( 'somdn_settings_header_title', $title = '' );
}

function somdn_get_settings_tabs() {

	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'home'; ?>
		
	<h2 class="nav-tab-wrapper">
		<a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=home" class="nav-tab <?php echo $active_tab == 'home' ? 'nav-tab-active' : ''; ?>">Home</a>
			<?php do_action( 'somdn_settings_tabs_after_home', $active_tab ); ?>
		<a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
			<?php do_action( 'somdn_settings_tabs_after_settings', $active_tab ); ?>
	</h2>

<?php

}

function somdn_get_settings_sub_tabs() {

	if ( isset( $_GET[ 'tab' ] ) && $_GET[ 'tab' ] == 'settings' ) {
			
		$active_section = isset( $_GET[ 'section' ] ) ? $_GET[ 'section' ] : 'general'; ?>

		<ul class="subsubsub">
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings&section=general" class="<?php echo $active_section == 'general' ? 'current' : ''; ?>">General</a> | </li>
				<?php do_action( 'somdn_settings_subtabs_after_general', $active_section ); ?>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings&section=single" class="<?php echo $active_section == 'single' ? 'current' : ''; ?>">Single Files</a> | </li>
				<?php do_action( 'somdn_settings_subtabs_after_single', $active_section ); ?>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings&section=multiple" class="<?php echo $active_section == 'multiple' ? 'current' : ''; ?>">Multiple Files</a> | </li>
			<?php do_action( 'somdn_settings_subtabs_after_multiple', $active_section ); ?>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings&section=owned" class="<?php echo $active_section == 'owned' ? 'current' : ''; ?>">Owned Products</a> | </li>
				<?php do_action( 'somdn_settings_subtabs_after_owned', $active_section ); ?>
			<li><a href="<?php echo somdn_get_plugin_link_full(); ?>&tab=settings&section=docviewer" class="<?php echo $active_section == 'docviewer' ? 'current' : ''; ?>">PDF Settings</a> <?php //<span class="som-settings-ui-new">New</span> ?></li>
		</ul>

	<?php
	
		return;
	
	}

	$extra_tabs = apply_filters( 'somdn_get_settings_sub_tabs', '' );
	if ( ! empty( $extra_tabs ) ) {
		echo $extra_tabs;
		return;
	}

}

function somdn_get_settings_bottom_content() { ?>

	<div class="som-settings-container som-settings-message-footer">
		<div class="som-settings-row">
			<div class="som-settings-col-12">
				<p>If you like this plugin please leave us a <a href="<?php echo somdn_get_plugin_review_link(); ?>" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> review on WordPress.org!</p>
			</div>
		</div>
	</div>

<?php

}

function somdn_get_plugin_review_link() {
	return apply_filters( 'somdn_plugin_review_link', '' );
}

function somdn_settings_footer() {
	if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'download_now_dashboard' ) {
		somdn_get_admin_footer();
	}
}

function somdn_get_admin_header() {
	include_once( SOMDN_PATH . 'includes/somdn-settings-header.php' );
}

function somdn_get_admin_footer() {
	include_once( SOMDN_PATH . 'includes/somdn-settings-footer.php' );
}

add_action( 'admin_footer', 'somdn_settings_footer' );

function somdn_get_settings_home() {
	return apply_filters( 'somdn_get_settings_home', $content = '' );
}

function somdn_settings_home() { ?>

	<div class="som-settings-container som-pad-top-20">
		<div class="som-settings-row">

			<?php echo somdn_get_settings_home(); ?>

		</div>
	</div>

<?php

}

function somdn_gen_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-12">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="som-settings-gen-settings-form-wrap">
			
					<?php
					settings_fields( 'somdn_gen_settings' );
					do_settings_sections( 'somdn_gen_settings' );
					submit_button();
					?>
			
					</div>
			
				</form>
		
			</div>

		</div>
	</div>

<?php

}

function somdn_single_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="som-settings-gen-settings-form-wrap">
			
					<?php
					settings_fields( 'somdn_single_settings' );
					do_settings_sections( 'somdn_single_settings' );
					submit_button();
					?>
			
					</div>
			
				</form>
		
			</div>

			<?php $img_location = plugins_url( '/assets/images/', dirname(__FILE__) ); ?>
			
			<div class="som-settings-col-5 som-settings-guide som-settings-multi-guide">

				<div class="som-settings-guide-img">
					<img src="<?php echo $img_location . 'single-file-download.png'; ?>" style="width: 450px;">
				</div>

			</div>

		</div>
	</div>

<?php

}

function somdn_multi_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="som-settings-gen-settings-form-wrap">
			
					<?php
					settings_fields( 'somdn_multi_settings' );
					do_settings_sections( 'somdn_multi_settings' );
					submit_button();
					?>
			
					</div>
			
				</form>
		
			</div>

			<?php $img_location = plugins_url( '/assets/images/', dirname(__FILE__) ); ?>
			
			<div class="som-settings-col-5 som-settings-guide som-settings-multi-guide">
			
				<div class="som-settings-guide-img">
					<h2>Display Methods</h2>
					<p class="description">Twenty Seventeen theme shown</p>
				</div>

				<div class="som-settings-guide-img">
					<h3>Links</h3>
					<img src="<?php echo $img_location . 'multi-1.png'; ?>">
				</div>

				<div class="som-settings-guide-img">
					<h3>Button Only</h3>
					<img src="<?php echo $img_location . 'multi-2.png'; ?>">
				</div>

				<div class="som-settings-guide-img">
					<h3>Button + Checkboxes</h3>
					<img src="<?php echo $img_location . 'multi-3.png'; ?>">
				</div>

				<div class="som-settings-guide-img">
					<h3>Button + Links</h3>
					<img src="<?php echo $img_location . 'multi-4.png'; ?>">
				</div>

				<div class="som-settings-guide-img">
					<h3>Button + Filenames</h3>
					<img src="<?php echo $img_location . 'multi-5.png'; ?>">
				</div>

			</div>


		</div>
	</div>

<?php

}

function somdn_owned_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="som-settings-gen-settings-form-wrap">
			
					<?php
					settings_fields( 'somdn_owned_settings' );
					do_settings_sections( 'somdn_owned_settings' );
					submit_button();
					?>
			
					</div>
			
				</form>
		
			</div>

			<?php $img_location = plugins_url( '/assets/images/', dirname(__FILE__) ); ?>
			
			<div class="som-settings-col-5 som-settings-guide som-settings-multi-guide">
			
				<div class="som-settings-guide-img">
					<h2>Example</h2>
				</div>

				<div class="som-settings-guide-img">
					<img src="<?php echo $img_location . 'owned-example.png'; ?>">
				</div>

			</div>

		</div>
	</div>

<?php

}

function somdn_docviewer_settings_content() { ?>

	<div class="som-settings-container">
		<div class="som-settings-row">
		
			<div class="som-settings-col-7">
	
				<form action="options.php" class="som-settings-settings-form" method="post">
			
					<div class="som-settings-gen-settings-form-wrap">
			
					<?php
					settings_fields( 'somdn_docviewer_settings' );
					somdn_do_custom_settings_sections( 'somdn_docviewer_settings' );
					submit_button();
					?>
			
					</div>
			
				</form>
		
			</div>

		</div>
	</div>

<?php

}

add_action ( 'after_setup_theme', 'somdn_after_setup_plugin' );
function somdn_settings_link( $links ) {
	$somdn_page = somdn_get_plugin_link_full();
	$url = get_admin_url() . 'admin.php' . $somdn_page;
	$settings_link = '<a href="' . $url . '">' . __( 'Settings', 'somdn-pro' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}

function somdn_after_setup_plugin() {
	add_filter( 'plugin_action_links_' . SOMDN_PLUGIN_BASENAME, 'somdn_settings_link' );
}

add_filter( 'plugin_row_meta', 'somdn_plugin_row_meta', 10, 2 );
function somdn_plugin_row_meta( $links, $file ) {

	if ( SOMDN_PLUGIN_BASENAME == $file ) {
		$new_links = array(
			'more' => '<a href="https://squareonemedia.co.uk/shop/plugins/" target="_blank">More Plugins</a>'
				);
		
		$links = array_merge( $links, $new_links );
	}
	
	return $links;
}

function somdn_do_custom_settings_sections( $page, $small = false, $table = true ) {

	global $wp_settings_sections, $wp_settings_fields;

	if ( ! isset( $wp_settings_sections[$page] ) )
		return;

	foreach ( (array) $wp_settings_sections[$page] as $section ) {

		$wrapper_class = 'som-settings-setting-section-wrapper';

		if ( $small ) {
			$wrapper_class = 'som-settings-setting-section-wrapper wrapper-small';
		}

		echo '<div class="' . $wrapper_class . '">';

		if ( $section['title'] )
			echo "<h2>{$section['title']}</h2>\n";

		if ( $section['callback'] )
			call_user_func( $section['callback'], $section );

		if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
			continue;

		if ( $table ) {

			echo '<table class="form-table">';
			somdn_do_custom_settings_fields( $page, $section['id'], $table );
			echo '</table>';

		} else {

			echo '<div class="som-settings-setting-section-table">';
			somdn_do_custom_settings_fields( $page, $section['id'], $table );
			echo '</div>';

		}

		echo '</div>';
	}

}

function somdn_do_custom_settings_fields( $page, $section, $table = true ) {
	global $wp_settings_fields;

	if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
		return;
	}

	foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
		$class = '';

		if ( ! empty( $field['args']['class'] ) ) {
			$class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
		}

		if ( $table ) {

			echo "<tr{$class}>";

			if ( ! empty( $field['args']['label_for'] ) ) {
				echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
			} else {
				echo '<th scope="row">' . $field['title'] . '</th>';
			}

			echo '<td>';
			call_user_func( $field['callback'], $field['args'] );
			echo '</td>';
			echo '</tr>';

		} else {

			if ( ! empty( $field['args']['class'] ) ) {
				$class = ' class="som-settings-setting-section-table-row ' . esc_attr( $field['args']['class'] ) . '"';
			} else {
				$class = ' class="som-settings-setting-section-table-row"';
			}

			echo "<div{$class}>";

			if ( ! empty( $field['args']['label_for'] ) ) {
				echo '<div class="som-settings-setting-section-table-head"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
			} else {
				echo '<div class="som-settings-setting-section-table-head"><h4>' . $field['title'] . '</h4></div>';
			}

			echo '<div class="som-settings-setting-section-table-td">';
			call_user_func( $field['callback'], $field['args'] );
			echo '</div>';
			echo '</div>';

		}

	}
}