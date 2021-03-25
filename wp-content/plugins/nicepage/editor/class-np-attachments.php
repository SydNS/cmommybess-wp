<?php
defined('ABSPATH') or die;

class NpAttachments {

    /**
     * Returns default image url
     *
     * @return string
     */
    public static function getDefaultImageUrl() {
        return APP_PLUGIN_URL . 'assets/images/default-image.jpg';
    }

    /**
     * Returns default logo url
     *
     * @return string
     */
    public static function getDefaultLogoUrl() {
        return APP_PLUGIN_URL . 'assets/images/default-logo.png';
    }

    /**
     * Returns image attachment by relative path
     *
     * @param string $relative_path
     *
     * @return WP_Post|null
     */
    public static function getImageByPath($relative_path) {
        $original_name = preg_replace('#^\/#', '', $relative_path);
        $original_name = preg_replace('#-\d+x\d+.#', '.', $original_name);

        $attachments = get_posts(
            array(
                'post_type' => 'attachment',
                'posts_per_page' => 1,
                'meta_query' => array(
                    array(
                        'key'   => '_wp_attached_file',
                        'value' => $original_name,
                    ),
                )
            )
        );
        foreach ($attachments as $attachment) {
            return $attachment;
        }
        return null;
    }
}