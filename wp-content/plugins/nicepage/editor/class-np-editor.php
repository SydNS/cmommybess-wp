<?php
defined('ABSPATH') or die;

require_once dirname(__FILE__) . '/../includes/class-np-files-utility.php';
require_once dirname(__FILE__) . '/admin.php';
require_once dirname(__FILE__) . '/class-np-attachments.php';
require_once dirname(__FILE__) . '/actions/actions.php';
require_once dirname(__FILE__) . '/class-np-meta.php';


class NpEditor {

    public static $editorPostTypes = array('page');

    /**
     * Returns Nicepage domain need to be used
     *
     * @return string
     */
    public static function getDomain() {
        $domain = isset($_REQUEST['domain'])
            ? urldecode($_REQUEST['domain'])
            : (defined('NICEPAGE_DOMAIN') ? NICEPAGE_DOMAIN : '');

        $domain = preg_replace('#^https?:#', '', $domain); // remove protocol
        $domain = untrailingslashit($domain); // remove last slash
        return $domain;
    }


    /**
     * Get Nicepage start link (used as iframe src)
     *
     * @param array $args
     *  string domain
     *  int    post_id
     *
     * @return string
     */
    public static function getAppLink($args = array()) {
        $return = add_query_arg(array('page' => 'np_app'), admin_url() . 'edit.php?post_type=page&ver=' . urlencode('1616660787540'));

        $domain = _arr($args, 'domain', NpEditor::getDomain());
        if ($domain) {
            $return = add_query_arg(array('domain' => urlencode($domain)), $return);
        }

        if (isset($args['post_id'])) {
            $post_id = $args['post_id'];

            if (np_data_provider($post_id)->isNicepage()) {
                $return .= "#/builder/1/page/$post_id";
            } else {
                if (isset($args['page'])) {
                    $return .= "#/builder/1/theme/" . $args['page'];
                } else {
                    $return .= "#/landing";
                }
            }
        } else {
            $return .= "#/landing";
        }
        return $return;
    }

    /**
     * Returns true if this post can be edited in Nicepage
     *
     * @param WP_Post $post
     *
     * @return bool
     */
    public static function isAllowedForEditor($post) {
        $type = $post->post_type;

        if (!in_array($type, self::$editorPostTypes)) {
            return false;
        }

        if (get_option('page_for_posts') == $post->ID) {
            return false;
        }

        if (function_exists('wc_get_page_id') && wc_get_page_id('shop') == $post->ID) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if this post can be returned in getSitePosts
     *
     * @param WP_Post $post
     *
     * @return bool
     */
    public static function isAllowedForBuilder($post) {
        return $post->post_type === 'product' || $post->post_type === 'post';
    }

    /**
     * Filter on replace_editor
     * Prevent replacing editor for Nicepage pages
     *
     * @param bool    $return
     * @param WP_Post $post
     *
     * @return bool
     */
    public static function replaceEditorFilter($return, $post) {
        if (!empty($_GET['np_new']) || !empty($_GET['np_edit']) || np_data_provider($post->ID)->isNicepage()) {
            $_GET['classic-editor'] = true;
        }
        return $return;
    }

    /**
     * Filter on use_block_editor_for_post
     * Disable Gutenberg for Nicepage pages
     *
     * @param bool    $return
     * @param WP_Post $post
     *
     * @return bool
     */
    public static function disableGutenbergFilter($return, $post) {
        if (!empty($_GET['np_new']) || !empty($_GET['np_edit']) || np_data_provider($post->ID)->isNicepage()) {
            return false;
        }
        return $return;
    }
}

// old themler-core compatibility
remove_action('edit_form_after_title', 'upage_screenshorts', 100);
remove_action('admin_head', 'upage_preview_styles');

remove_filter('get_edit_post_link', 'np_edit_post_link_set_domain');
remove_action('edit_form_top', 'upage_update_post_set_domain_field');
remove_action('themler_edit_form_buttons', 'themler_add_upage_button');
remove_action('admin_menu', 'upage_add_editor_page');
remove_action('load-pages_page_np_editor', 'np_editor');

remove_action('upage_check_ajax_referer', 'upage_check_ajax_referer');
add_filter('use_block_editor_for_post', 'NpEditor::disableGutenbergFilter', 9, 2);
add_filter('replace_editor', 'NpEditor::replaceEditorFilter', 9, 2);

if (isset($_REQUEST['isPreview'])) {
    show_admin_bar(false);
}


