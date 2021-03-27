<?php
function advanced_import_allowed_html( $input ) {
	$allowed_html = wp_kses_allowed_html( 'post' );
	$output       = wp_kses( $input, $allowed_html );
	return $output;
}

function advanced_import_current_url() {
	global $pagenow;
	$current_url = $pagenow == 'tools.php' ? admin_url( 'tools.php?page=advanced-import-tool' ) : admin_url( 'themes.php?page=advanced-import' );
	return $current_url;
}

function advanced_import_get_current_theme_author() {
	$current_theme = wp_get_theme();
	return $current_theme->get( 'Author' );
}
function advanced_import_get_current_theme_slug() {
	$current_theme = wp_get_theme();
	return $current_theme->stylesheet;
}
function advanced_import_get_theme_screenshot() {
	$current_theme = wp_get_theme();
	return $current_theme->get_screenshot();
}
function advanced_import_get_theme_name() {
	$current_theme = wp_get_theme();
	return $current_theme->get( 'Name' );
}
