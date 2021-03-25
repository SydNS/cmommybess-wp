<?php
defined('ABSPATH') or die;

class NpMeta {

    public static $baseTableName = 'nicepage_meta';
    public static $_cache = array();

    /**
     * Initialize method
     */
    public static function init() {
        global $wpdb;
        $wpdb->nicepage_meta = $wpdb->prefix . self::$baseTableName;

        self::updateTable();
    }

    /**
     * Get nicepage_meta value
     *
     * @param string $meta_key
     * 
     * @return string|false
     */
    public static function get($meta_key) {
        if (!isset(self::$_cache[$meta_key])) {
            global $wpdb;
            self::$_cache[$meta_key] = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM $wpdb->nicepage_meta WHERE meta_key=%s", $meta_key));
        }
        return self::$_cache[$meta_key];
    }

    /**
     * Update nicepage_meta value
     *
     * @param string $meta_key
     * @param string $meta_value
     */
    public static function update($meta_key, $meta_value) {
        global $wpdb;

        // TODO: may be optimize using UPDATE
        $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->nicepage_meta WHERE meta_key=%s", $meta_key));
        $wpdb->query($wpdb->prepare("INSERT INTO $wpdb->nicepage_meta (meta_key, meta_value) VALUES (%s, %s)", $meta_key, $meta_value));
        self::$_cache[$meta_key] = $meta_value;
    }

    /**
     * Create nicepage_meta table if it not exists
     */
    public static function updateTable() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $wpdb->nicepage_meta (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          meta_key varchar(255) NOT NULL,
          meta_value longtext DEFAULT '' NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";

        include_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}
NpMeta::init();