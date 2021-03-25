<?php
defined('ABSPATH') or die;

class NpAction {

    public static $actions = array();

    /**
     * @return bool
     */
    public static function verifyNonceAndLoginUser() {
        $uid = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : 0;
        $nonce = isset($_REQUEST['_ajax_nonce']) ? $_REQUEST['_ajax_nonce'] : $_REQUEST['_wpnonce'];

        if (false !== wp_verify_nonce($nonce, 'np-upload')) {
            wp_clear_auth_cookie();
            wp_set_auth_cookie($uid);
            wp_set_current_user($uid);
            return true;
        }
        return false;
    }

    /**
     * Action on wp_ajax_{action}
     */
    public static function actionWrapper() {
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;

        if (null !== $action && isset(self::$actions[$action]) && is_callable(self::$actions[$action] . '::process')) {
            call_user_func(self::$actions[$action] . '::checkAjaxReferer');

            $result = call_user_func(self::$actions[$action] . '::process');
            if ($result !== null) {
                echo wp_json_encode($result);
            }
            die;
        }
        die('invalid_action');
    }

    /**
     * Action on wp_ajax_nopriv_{action}
     */
    public static function noprivActionWrapper() {
        if (self::verifyNonceAndLoginUser()) {
            self::actionWrapper();
        }
        die('session_error');
    }

    /**
     * Validate _ajax_nonce token
     */
    public static function checkAjaxReferer() {
        check_ajax_referer('np-upload');
    }

    /**
     * Add ajax action
     *
     * @param string   $action
     * @param callable $action_class
     */
    public static function add($action, $action_class) {
        if (is_callable("$action_class::process")) {
            remove_all_actions('wp_ajax_nopriv_'. $action);
            add_action('wp_ajax_nopriv_'. $action, "$action_class::noprivActionWrapper", 9);
            remove_all_actions('wp_ajax_'. $action);
            add_action('wp_ajax_' . $action,       "$action_class::actionWrapper", 9);
            self::$actions[$action] = $action_class;
        }
    }

    /**
     * Get ajax action url
     *
     * @param string $action
     *
     * @return string
     */
    public static function getActionUrl($action) {
        return add_query_arg(array('action' => $action), admin_url('admin-ajax.php'));
    }

    /**
     * Get post json in Nicepage-editor format
     *
     * @param WP_Post $post
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getPost($post) {
        if (is_int($post)) {
            $post = get_post($post);
        }
        if ($post === null) {
            throw new Exception('post is undefined');
        }

        $html_url = add_query_arg(array('pageId' => $post->ID), NpAction::getActionUrl('np_get_html'));
        $public_url = get_permalink($post->ID);
        $result = array(
            'siteId' => 1,
            'title' => $post->post_title,
            'publicUrl' => $public_url,
            'editorUrl' => add_query_arg(array('np_edit' => '1'), get_edit_post_link($post->ID, '')),
            'htmlUrl' => $html_url,
            'id' => (int) $post->ID,
            'order' => 0,
            'status' => 2,
        );
        return $result;
    }
}

require_once dirname(__FILE__) . '/upload-image.php';
require_once dirname(__FILE__) . '/upload-file.php';
require_once dirname(__FILE__) . '/save-page.php';
require_once dirname(__FILE__) . '/save-local-storage-key.php';
require_once dirname(__FILE__) . '/save-site-settings.php';
require_once dirname(__FILE__) . '/save-preferences.php';
require_once dirname(__FILE__) . '/save-menu-items.php';
require_once dirname(__FILE__) . '/route-service-worker.php';
require_once dirname(__FILE__) . '/get-site.php';
require_once dirname(__FILE__) . '/get-html.php';
require_once dirname(__FILE__) . '/get-site-posts.php';
require_once dirname(__FILE__) . '/get-posts-by-type.php';
require_once dirname(__FILE__) . '/clear-chunks.php';
require_once dirname(__FILE__) . '/remove-font.php';