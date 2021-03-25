<?php
defined('ABSPATH') or die;
// Write in db table name of first (white label) plugin used
global $table_prefix, $wpdb;
$tblname = 'pin';
$wp_track_table = $table_prefix . "$tblname";
if ($wpdb->get_var("show tables like '".$wp_track_table."'") != $wp_track_table) {
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $wp_track_table  (
            `id` int(11) NOT NULL AUTO_INCREMENT,                
            `records` text COLLATE utf8mb4_unicode_ci NOT NULL,
            `folder` text COLLATE utf8mb4_unicode_ci NOT NULL,
            PRIMARY KEY (`id`)
           ) $charset_collate";
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
    $nptablename =  $wpdb->prefix . 'pin';
    $check_val = $wpdb->query("SELECT records FROM `".$nptablename."` WHERE id = '1' LIMIT 1");
    if (!$check_val) {
        delete_option('themler_hide_import_notice');
        $wpdb->insert($nptablename, array('records' => 'Nicepage', 'folder' => 'nicepage'));
    }
}