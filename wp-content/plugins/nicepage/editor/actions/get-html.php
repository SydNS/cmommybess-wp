<?php
defined('ABSPATH') or die;

class NpGetHtmlAction extends NpAction {

    /**
     * Get page html
     *
     * @param string $id
     *
     * @return string
     */
    public static function getHtml($id) {
        return np_data_provider($id)->getPageHtml();
    }

    /**
     * Process action entrypoint
     */
    public static function process() {
        echo self::getHtml($_REQUEST['pageId']);
    }
}

NpAction::add('np_get_html', 'NpGetHtmlAction');