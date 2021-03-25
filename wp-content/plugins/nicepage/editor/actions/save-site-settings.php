<?php
defined('ABSPATH') or die;

class NpSaveSiteSettingsAction extends NpAction {

    /**
     * Process action entrypoint
     *
     * @return array
     *
     * @throws Exception
     */
    public static function process() {

        include_once dirname(__FILE__) . '/chunk.php';

        $saveType = $_REQUEST['saveType'];
        $request = array();
        switch($saveType) {
        case 'base64':
            $request = array_merge($_REQUEST, json_decode(base64_decode($_REQUEST['data']), true));
            break;
        case 'chunks':
            $chunk = new NpChunk();
            $ret = $chunk->save(NpSavePageAction::getChunkInfo($_REQUEST));
            if (is_array($ret)) {
                return NpSavePageAction::response(array($ret));
            }
            if ($chunk->last()) {
                $result = $chunk->complete();
                if ($result['status'] === 'done') {
                    $request = array_merge($_REQUEST, json_decode(base64_decode($result['data']), true));
                } else {
                    $result['result'] = 'error';
                    return NpSavePageAction::response(array($result));
                }
            } else {
                return NpSavePageAction::response('processed');
            }
            break;
        default:
            $request = stripslashes_deep($_REQUEST);
        }

        $settings = isset($request['settings']) && is_array($request['settings']) ? $request['settings'] : array();
        np_data_provider()->setSiteSettings($settings);
        $publishHeaderFooter = NpSavePageAction::saveHeaderFooter(np_data_provider(), $settings);

        $cookiesConsent = _arr($settings, 'cookiesConsent', '');
        $publishCookiesSection = '';
        if ($cookiesConsent) {
            $cookiesConsent['publishCookiesSection'] = np_data_provider()->replaceImagePaths($cookiesConsent['publishCookiesSection']);
            NpMeta::update('cookiesConsent', json_encode($cookiesConsent));
            $publishCookiesSection = $cookiesConsent['publishCookiesSection'];
        } else {
            $cookies = _arr($settings, 'cookies', 'default');
            $cookieConfirmCode = _arr($settings, 'cookieConfirmCode', 'default');
            $defaultCookiesSection = _arr($settings, 'cookiesSection', '');
            if ($cookies !== 'default'  && $cookieConfirmCode !== 'default') {
                $cookiesConsent = NpMeta::get('cookiesConsent') ? json_decode(NpMeta::get('cookiesConsent'), true) : array();
                $publishCookiesSection = isset($cookiesConsent['publishCookiesSection']) ? $cookiesConsent['publishCookiesSection'] : $defaultCookiesSection;
                $cookiesConsent = array(
                    'hideCookies' => $settings['cookies'] == 'false' ? 'true' : 'false',
                    'cookieConfirmCode' => $settings['cookieConfirmCode'],
                    'publishCookiesSection' => np_data_provider()->replaceImagePaths($publishCookiesSection),
                );
                NpMeta::update('cookiesConsent', json_encode($cookiesConsent));
            }
        }

        if (isset($settings['publishNicePageCss']) && $settings['publishNicePageCss']) {
            np_data_provider()->setStyleCss($settings['publishNicePageCss'], '', $publishHeaderFooter, $publishCookiesSection);
        }
        return array(
            'result' => 'done',
        );
    }
}
NpAction::add('np_save_site_settings', 'NpSaveSiteSettingsAction');