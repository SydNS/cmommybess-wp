<?php
defined('ABSPATH') or die;

class NpRemoveFontAction extends NpAction {

    /**
     * Remove font on the disk by name
     *
     * @return array
     */
    public static function process() {
        include_once dirname(__FILE__) . '/chunk.php';
        $fileName = isset($_REQUEST['fileName']) ? $_REQUEST['fileName'] : '';
        $base_upload_dir = wp_upload_dir();
        $base_dir = $base_upload_dir['basedir'];
        $customFontPath = $base_dir . '/' . 'nicepage-fonts/fonts/' . $fileName;
        $success = true;
        if (file_exists($customFontPath)) {
            if (!is_dir($customFontPath)) {
                wp_delete_file($customFontPath);
            }
            if (file_exists($customFontPath)) {
                $success = false;
            }
        }
        return array(
            'result' => 'done',
            'success' => $success,
        );
    }
}
NpAction::add('np_remove_font', 'NpRemoveFontAction');