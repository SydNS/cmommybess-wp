<?php
defined('ABSPATH') or die;

class NpImportNotice {

    /**
     * Action on admin_notices
     * Print import content banner
     */
    public static function contentImportNoticeAction() {
?>
        <div id="content-import-notice" class="updated">
            <p>
                <?php _e('<strong>There is content included to Nicepage plugin.</strong><br>Would you like to install it?', 'nicepage'); ?>

                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                <a id="import-import-content" class="import-button button" href="#"><?php _e('Import content', 'nicepage'); ?></a>
                <a id="import-replace-content" class="<?php echo get_option('np_imported_content') === false ? 'hidden' : ''; ?> import-button button" href="#"><?php _e('Replace previously imported content', 'nicepage'); ?></a>
                <a id="import-hide-notice" class="import-button button" href="#"><?php _e('Hide notice', 'nicepage'); ?></a>
            </p>
        </div>
        <style>
            .import-button {
                text-decoration: none;
            }
            .import-button.importing:before {
                content: '';
                background-image: url('<?php echo APP_PLUGIN_URL; ?>/importer/assets/images/preloader-01.gif');
                display: inline-block;
                width: 13px;
                height: 13px;
                background-size: 100% 100%;
                margin-right: 5px;
            }
        </style>
        <script>
            jQuery(document).ready(function ($) {
                function doAjax(action) {
                    return $.ajax({
                        url: '<?php echo admin_url("admin-ajax.php"); ?>',
                        type: 'GET',
                        data: ({
                            action: action,
                            _ajax_nonce: '<?php echo wp_create_nonce('np-importer'); ?>'
                        })
                    });
                }
                function bindImportAction(action, btn) {
                    var captchaNotice = "<p>Keys for <strong>recaptcha</strong> replaced. If necessary, you can change keys manually in 'Site Settings' in the <strong>Nicepage</strong> plugin</p>";
                    var successMsg = <?php echo wp_json_encode(__('Content was successfully imported.', 'nicepage')); ?>;
                    var failMsg = <?php echo wp_json_encode(__('An error occurred while importing.', 'nicepage')); ?>;
                    var captchaKeys;
                    btn.unbind("click").click(function() {
                        $(this).addClass('importing');
                        doAjax(action).done(function (response) {
                            try {
                                captchaKeys = JSON.parse(response);
                            } catch (e) {
                                captchaKeys = null;
                            }
                            var captchaMsg = captchaKeys
                                && captchaKeys["newKeysEmpty"] === false
                                && captchaKeys["oldKeysEmpty"] === false ? captchaNotice : '';
                            $('#content-import-notice').html('<p>' + successMsg + '</p>' + captchaMsg);
                        }).fail(function () {
                            $('#content-import-notice')
                                .removeClass('updated').addClass('error')
                                .html('<p>' + failMsg + '</p>');
                        });
                    });
                }
                bindImportAction('np_import_content', $('#import-import-content'));
                bindImportAction('np_replace_content', $('#import-replace-content'));

                $('#import-hide-notice').unbind("click").click(function() {
                    $('#content-import-notice').remove();
                    doAjax('np_hide_import_notice');
                });
            });
        </script>
        <?php
    }

    /**
     * Action on init
     * Add import content banner if need
     */
    public static function addImportNoticeAction() {
        // hide old message import content because now wizard have import
        return;

        remove_action('admin_notices', 'themler_content_import_notice');

        if (!file_exists(APP_PLUGIN_PATH . 'content/content.json')) {
            return;
        }
        if (self::getImportNoticeOption()) {
            return;
        }

        add_action('admin_notices', 'NpImportNotice::contentImportNoticeAction');
    }

    /**
     * Remember to hide import content banner
     */
    public static function addImportNoticeOption() {
        update_option('themler_hide_import_notice', true);
    }

    /**
     * Returns true if no need to show import content banner
     *
     * @return bool
     */
    public static function getImportNoticeOption() {
        return get_option('themler_hide_import_notice');
    }

    /**
     * Remember to show import content banner
     */
    public static function removeImportNoticeOption() {
        delete_option('themler_hide_import_notice');
    }

    /**
     * Action on wp_ajax_np_hide_import_notice
     * Action to hide import content banner
     */
    public static function hideImportNoticeAction() {
        check_ajax_referer('np-importer');
        self::addImportNoticeOption();
    }

    /**
     * Action on wp_ajax_np_import_content
     * Action to import content
     */
    public static function importContentAction() {
        check_ajax_referer('np-importer');
        self::_importData(false);
        update_option('content_import_from_theme', 'ok');
        echo get_option('np_captcha_keys_options', '');
        exit;
    }

    /**
     * Action on wp_ajax_np_replace_content
     * Action to import content with replacement
     */
    public static function replaceContentAction() {
        check_ajax_referer('np-importer');
        self::_importData(true);
        update_option('content_import_from_theme', 'ok');
        echo get_option('np_captcha_keys_options', '');
        exit;
    }

    /**
     * Replace reCaptcha keys for import / change site settings
     */
    public static function replaceCaptchaKeysContact7Form() {
        $site_settings = json_decode(NpMeta::get('site_settings'));
        $result = array('newKeysEmpty' => true, 'oldKeysEmpty' => true);
        if (!isset($site_settings->captchaSiteKey) && !isset($site_settings->captchaSecretKey)) {
            return $result;
        }
        if (class_exists('WPCF7')) {
            if (method_exists('WPCF7', 'get_option') && method_exists('WPCF7', 'update_option')) {
                if ($site_settings->captchaSiteKey !== "" && $site_settings->captchaSecretKey !== "") {
                    $cf7_keys = WPCF7::get_option('recaptcha');
                    $new_keys = array($site_settings->captchaSiteKey => $site_settings->captchaSecretKey);
                    if (empty($cf7_keys)) {
                        WPCF7::update_option('recaptcha', $new_keys);
                    } else if ($cf7_keys !== $new_keys) {
                        WPCF7::update_option('recaptcha', $new_keys);
                    }
                    $result['newKeysEmpty'] = false;
                    $result['oldKeysEmpty'] = empty($cf7_keys);
                } else {
                    WPCF7::update_option('recaptcha', array());
                }
            }
        }
        update_option('np_captcha_keys_options', json_encode($result));
    }

    /**
     * Import content
     *
     * @param bool $remove_prev - if need to replace previously imported content
     */
    private static function _importData($remove_prev = false) {
        $content_dir = APP_PLUGIN_PATH . 'content';
        self::addImportNoticeOption();

        do_action('nicepage_import_content', $content_dir, $remove_prev);
    }

    /**
     * Action attached to register_activation_hook
     */
    public static function resetImportNotice() {
        self::removeImportNoticeOption();
    }

    /**
     * Action remove pin table in db
     */
    public static function removePluginDatabaseTable() {
        global $wpdb;
        $nptablename =  $wpdb->prefix . 'pin';
        $wpdb->query("DROP TABLE IF EXISTS ".$nptablename);
    }

    /**
     * After deactivate plugin import content from plugin folder again
     */
    public static function restartThemeImportContent() {
        delete_option('content_import_from_theme');
    }
}

add_action('init', 'NpImportNotice::addImportNoticeAction');
add_action('wp_ajax_np_hide_import_notice', 'NpImportNotice::hideImportNoticeAction', 9);
add_action('wp_ajax_np_import_content', 'NpImportNotice::importContentAction', 9);
add_action('wp_ajax_np_replace_content', 'NpImportNotice::replaceContentAction', 9);