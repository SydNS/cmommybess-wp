<?php
defined('ABSPATH') or die;

require_once dirname(__FILE__) . '/class-np-content-importer.php';
require_once dirname(__FILE__) . '/../includes/class-np-files-utility.php';

class NpImport {

    /**
     * Print import admin-page
     */
    public static function importPage() {
        include_once APP_PLUGIN_PATH . 'importer/import.php';
    }

    /**
     * Action on admin_head
     * Print import-page scripts
     */
    public static function printImportSettingsAction() {
        $user = wp_get_current_user();
?>
        <script>
            var importerSettingsNp = <?php
            echo wp_json_encode(
                array(
                    'actions' => array(
                        'uploadZip' => add_query_arg(array('action' => 'np_upload_chunk'), admin_url('admin-ajax.php')),
                    ),
                    'uid' => (int)$user->ID,
                    'ajax_nonce' => wp_create_nonce('np-importer'),
                    'chunkSize' =>  min(
                        wp_convert_hr_to_bytes(ini_get('post_max_size')),
                        wp_convert_hr_to_bytes(ini_get('upload_max_filesize')),
                        wp_convert_hr_to_bytes(ini_get('memory_limit'))
                    ),
                )
            ); // @codingStandardsIgnoreLine.
?>;
        </script>
        <script type="text/javascript" src="<?php echo APP_PLUGIN_URL . 'importer/assets/js/uploader.js?ver=' . APP_PLUGIN_VERSION; ?>"></script>
<?php
    }

    /**
     * Action on wp_ajax_np_upload_chunk
     * Upload chunk entrypoint
     */
    public static function uploadChunkAction() {
        check_ajax_referer('np-importer');
        $is_last = false;

        try {
            $filename = _arr($_REQUEST, 'filename', '');

            if ('' === $filename) {
                throw new Exception('Empty file name');
            }

            $is_last = _arr($_REQUEST, 'last', '');
            $result = self::_uploadFileChunk($filename, $is_last);
            echo wp_json_encode($result);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        if ($is_last) {
            $uploads_info = wp_upload_dir();
            $tmp_dir = $uploads_info['basedir'] . '/nicepage-export';
            NpFilesUtility::emptyDir($tmp_dir, true);
        }
        die;
    }

    /**
     * Process chunk
     *
     * @param string $filename - target file name
     * @param bool   $is_last  - is it chunk last one
     *
     * @return array
     *
     * @throws Exception
     */
    private static function _uploadFileChunk($filename, $is_last) {
        if (!isset($_FILES['chunk']) || !file_exists($_FILES['chunk']['tmp_name'])) {
            throw new Exception('Empty chunk data');
        }

        if (empty($_REQUEST['uploadId'])) {
            throw new Exception('Empty uploadId');
        }

        $content_range = $_SERVER['HTTP_CONTENT_RANGE'];
        if ('' === $content_range && '' === $is_last) {
            throw new Exception('Empty Content-Range header');
        }

        $range_begin = 0;

        if ($content_range) {
            $content_range = str_replace('bytes ', '', $content_range);
            list($range, $total) = explode('/', $content_range);
            list($range_begin, $range_end) = explode('-', $range);
        }

        $uploads_info = wp_upload_dir();
        $tmp_base_dir = $uploads_info['basedir'] . '/nicepage-export';
        $tmp_data_dir = $tmp_base_dir . '/data';
        $tmp_extracted_data_dir = $tmp_data_dir . '/extracted';
        $tmp_zip_path = $tmp_data_dir . '/' . basename($filename);

        NpFilesUtility::createDir($tmp_base_dir);

        $fh = fopen("$tmp_base_dir/lock", 'w');
        if (flock($fh, LOCK_EX)) {
            $prev_upload_id = file_exists("$tmp_base_dir/id") ? file_get_contents("$tmp_base_dir/id") : '';
            if ($prev_upload_id !== $_REQUEST['uploadId']) {
                // clear previous upload data
                NpFilesUtility::createDir($tmp_data_dir);
                NpFilesUtility::emptyDir($tmp_data_dir);
                file_put_contents("$tmp_base_dir/id", $_REQUEST['uploadId']);
                file_put_contents($tmp_zip_path, '');
            }

            $f = fopen($tmp_zip_path, 'r+');
            fseek($f, (int) $range_begin);
            fwrite($f, file_get_contents($_FILES['chunk']['tmp_name']));
            fclose($f);

            flock($fh, LOCK_UN);
            fclose($fh);
        }

        if ($is_last) {
            NpFilesUtility::createDir($tmp_extracted_data_dir);
            NpFilesUtility::extractZip($tmp_zip_path, $tmp_extracted_data_dir);
            self::_importData($tmp_extracted_data_dir);

            return array(
                'status' => 'done'
            );
        }

        return array(
            'status' => 'processed'
        );
    }

    /**
     * Do import content from specified data source path
     *
     * @param string $path
     */
    private static function _importData($path) {
        if (!file_exists($path . '/content.json')) {
            if (file_exists($path . '/nicepage/content/content.json')) { // import plugin zip
                $path .= '/nicepage/content';
            } else if (file_exists($path . '/content/content.json')) { // import theme zip
                $path .= '/content';
            }
        }
        $import = new NpContentImporter($path);
        $remove_prev = !!_arr($_REQUEST, 'removePrev');
        $import->import($remove_prev);
    }

    /**
     * Activate.
     *
     * Set activation hook.
     *
     * Fired by `register_activation_hook` when the plugin is activated.
     */
    public static function activation() {
        set_transient('np_activation_redirect', true, MINUTE_IN_SECONDS);
    }

    /**
     * @access public
     */
    public static function redirectToPluginWizard() {
        if (!get_transient('np_activation_redirect')) {
            return;
        }
        if (wp_doing_ajax()) {
            return;
        }
        delete_transient('np_activation_redirect');
        if (is_network_admin() || isset($_GET['activate-multi'])) {
            return;
        }
        global $submenu;
        foreach ($submenu['np_app'] as $pluginMenu) {
            $has_wizard_page = in_array(APP_PLUGIN_WIZARD_NAME, $pluginMenu);
            if ($has_wizard_page) {
                break;
            }
        }
        if (!$has_wizard_page) {
            return;
        }
        global $pagenow;
        if ($pagenow !== 'plugins.php') {
            return;
        }
        wp_safe_redirect(admin_url('admin.php?page=np_wizard'));
        exit;
    }

    /**
     * Print wizard admin-page
     */
    public static function wizardPage() {
        include_once APP_PLUGIN_PATH . 'importer/wizard.php';
        $options['page_slug'] = 'plugin-wizard';
        $options['page_title'] = APP_PLUGIN_WIZARD_NAME;
        if (class_exists('Pwizard')) {
            $Pwizard = new Pwizard($options);
        }
    }

    /**
     * Import custom parameters
     *
     * @throws Exception
     */
    public static function importCustomParameters() {
        include_once ABSPATH . WPINC . '/pluggable.php';

        $currentPath = dirname(dirname(__FILE__));
        $contentPath = '';

        if (file_exists($currentPath . '/content/content.json')) {
            $contentPath = $currentPath . '/content/content.json';
        }

        if (!$contentPath && file_exists(get_template_directory() . '/content/content.json')) {
            $contentPath = get_template_directory() . '/content/content.json';
        }

        if ($contentPath) {
            $import = new NpContentImporter(dirname($contentPath));
            if (!get_option('headerNp') && !get_option('footerNp')) {
                $import->setHeaderFooterImagesPlaceHolders();
                $import->importImages();
                $import->importParameters(true);
            }
            $import->importClientLicenseMode();
        }
    }

}

add_action('admin_head', 'NpImport::printImportSettingsAction');
add_action('wp_ajax_np_upload_chunk', 'NpImport::uploadChunkAction', 9);

require_once dirname(__FILE__) . '/class-np-import-notice.php';