<?php
defined('ABSPATH') or die;

class NpSavePageAction extends NpAction {

    /**
     * Process action entrypoint
     *
     * @return array
     *
     * @throws Exception
     */
    public static function process() {

        include_once dirname(__FILE__) . '/chunk.php';

        $saveType = isset($_REQUEST['saveType']) ? $_REQUEST['saveType'] : '';
        switch($saveType) {
        case 'base64':
            $_REQUEST = array_merge($_REQUEST, json_decode(base64_decode($_REQUEST['data']), true));
            break;
        case 'chunks':
            $chunk = new NpChunk();
            $ret = $chunk->save(self::getChunkInfo($_REQUEST));
            if (is_array($ret)) {
                return self::response(array($ret));
            }
            if ($chunk->last()) {
                $result = $chunk->complete();
                if ($result['status'] === 'done') {
                    $_REQUEST = array_merge($_REQUEST, json_decode(base64_decode($result['data']), true));
                } else {
                    $result['result'] = 'error';
                    return self::response(array($result));
                }
            } else {
                return self::response('processed');
            }
            break;
        default:
        }

        if (!isset($_REQUEST['id']) || !isset($_REQUEST['data'])) {
            return array(
                'status' => 'error',
                'type' => 'cmsSaveServerError',
                'message' => 'post parameter missing',
            );
        }
        if (!isset($_REQUEST['data']['publishNicePageCss']) || $_REQUEST['data']['publishNicePageCss'] === '') {
            return array(
                'status' => 'error',
                'type' => 'cmsSaveServerError',
                'message' => 'publishNicePageCss parameter missing',
            );
        }

        $request = $_REQUEST;

        if (!$saveType) {
            foreach ($request as $key => $value) {
                $request[$key] = stripslashes_deep($value);
            }
        }

        $post_id = $request['id'];
        if (isset($request['pageType'])) {
            $getCmsValue = array(
                'theme-template' => '',
                'np-template-header-footer-from-plugin' => 'html',
                'np-template-header-footer-from-theme' => 'html-header-footer'
            );
            $pageType = $getCmsValue[$request['pageType']];
        } else {
            $pageType = 'html';
        }
        NpMetaOptions::update($post_id, 'np_template', $pageType);
        $title = _arr($request, 'title', '');

        if (!$title) {
            return array(
                'result' => 'error',
                'type' => 'cmsSaveServerError',
                'message' => 'Page title missing',
            );
        }

        $data = &$request['data'];
        $fullRequest = &$request;

        if ($post_id <= 0) {
            $insert_data = array();

            $insert_data['post_type'] = 'page';
            $insert_data['post_status'] = 'publish';

            $post_id = wp_insert_post($insert_data);
            if (is_wp_error($post_id)) {
                //TODO: process error
            }
        }

        $post = get_post($post_id);

        if (!$post) {
            return array(
                'result' => 'error',
                'type' => 'cmsSaveServerError',
                'message' => 'Page not found'
            );
        }

        $customFontsCss = isset($request['customFontsCss']) ? $request['customFontsCss'] : '';
        if ($customFontsCss) {
            $base_upload_dir = wp_upload_dir();
            $customFontsPath = $base_upload_dir['basedir'] . '/nicepage-fonts/';
            if (!file_exists($customFontsPath)) {
                mkdir($customFontsPath);
            }
            $customFontsFilePath = $customFontsPath . 'fonts_' . $post_id . '.css';
            file_put_contents($customFontsFilePath, $customFontsCss);
        }

        $saveAndPublish = isset($request['saveAndPublish']) ? $request['saveAndPublish'] : null;
        $preview = isset($request['isPreview']) ? $request['isPreview'] : null;
        $data_provider = np_data_provider($post_id, $preview, $saveAndPublish);
        $data_provider->setSiteSettings(_arr($request, 'settings', ''));

        if ($title !== $post->post_title) {
            $title = self::_createUniqueTitle($title);
            wp_update_post(
                array(
                    'ID' => $post_id,
                    'post_title' => $title,
                    'post_status' => $post->post_status === 'auto-draft' ? 'draft' : $post->post_status,
                )
            );
            $post = get_post($post_id);
        }

        $publishHeaderFooter = NpSavePageAction::saveHeaderFooter($data_provider, $fullRequest);

        $publish_html = _arr($data, 'publishHtml', '');
        $data_provider->setPagePublishHtml($publish_html);
        $data_provider->setPageHtml(_arr($data, 'html', ''));
        $data_provider->setPageHead(_arr($data, 'head', ''));
        $data_provider->setPageBodyClass(_arr($data, 'bodyClass', ''));
        $data_provider->setPageBodyStyle(_arr($data, 'bodyStyle', ''));

        $data_provider->setHideHeader(_arr($data, 'hideHeader', 'false'));
        $data_provider->setHideFooter(_arr($data, 'hideFooter', 'false'));
        $data_provider->setHideBackToTop(_arr($data, 'hideBackToTop', 'false'));

        $fonts = _arr($data, 'fonts', '');
        if ($fonts) {
            $fonts = preg_replace('/[\"\']fonts.css[\"\']/',  APP_PLUGIN_URL . 'assets/css/fonts/fonts.css', $fonts);
            $fonts = preg_replace('/[\"\']page-fonts.css[\"\']/', APP_PLUGIN_URL . 'assets/css/fonts/page-' . $post_id . '-fonts.css', $fonts);
            $fonts = preg_replace('/[\"\']header-footer-fonts.css[\"\']/', APP_PLUGIN_URL . 'assets/css/fonts/header-footer-fonts.css', $fonts);
        }
        $data_provider->setPageFonts($fonts);
        self::saveLocalGoogleFonts(_arr($request, 'fontsData', ''), $post_id);

        $data_provider->setPageBacklink(_arr($data, 'backlink', ''));
        $data_provider->setStyleCss(_arr($data, 'publishNicePageCss', ''), $publish_html, $publishHeaderFooter);

        $data_provider->setPageKeywords(_arr($request, 'keywords', ''));
        $data_provider->setPageDescription(_arr($request, 'description', ''));
        $data_provider->setPageCanonical(_arr($request, 'canonical', ''));
        $data_provider->setPageMetaTags(_arr($request, 'metaTags', ''));
        $data_provider->setPageMetaGenerator(_arr($request, 'metaGeneratorContent', ''));
        $data_provider->setPageCustomHeadHtml(_arr($request, 'customHeadHtml', ''));
        $data_provider->setPageTitleInBrowser(_arr($request, 'titleInBrowser', ''));
        $data_provider->setFormsData(_arr($request, 'pageFormsData', ''));
        $dialogsData = _arr($request, 'dialogs', '');
        if ($dialogsData) {
            $data_provider->setDialogsData(json_decode($dialogsData, true));
        }
        $data_provider->setPublishDialogs(_arr($request, 'publishDialogs', ''));

        NpForms::updateForms($post_id);

        if ($data_provider->saveAndPublish) {
            np_data_provider($post_id, null, true)->clear();
            // create post_content for page indexing in search
            wp_update_post(array('ID' => $post_id, 'post_content' => apply_filters('np_create_excerpt', $data_provider->getPagePublishHtml())));
            $post = get_post($post_id);
        }
        if (!$data_provider->preview) {
            np_data_provider($post_id, true)->clear();
        }

        $result = self::getPost($post);
        return array(
            'result' => 'done',
            'data' => $result,
        );
    }

    /**
     * Save local google fonts
     *
     * @param JInput $fontsData Data parameters
     * @param string $pageId    Page id
     *
     * @return array|void
     */
    public static function saveLocalGoogleFonts($fontsData, $pageId) {
        if (!$fontsData) {
            return;
        }

        $fontsFolder = APP_PLUGIN_PATH . 'assets/css/fonts';
        if (!file_exists($fontsFolder)) {
            if (false === @mkdir($fontsFolder, 0777, true)) {
                return;
            }
        }
        $fontsFiles = isset($fontsData['files']) ? $fontsData['files'] : array();
        foreach ($fontsFiles as $fontFile) {
            $fontData = json_decode($fontFile, true);
            if (!$fontData) {
                continue;
            }
            switch($fontData['fileName']) {
            case 'fonts.css':
                file_put_contents($fontsFolder . '/fonts.css', str_replace('fonts/', '', $fontData['content']));
                break;
            case 'page-fonts.css':
                file_put_contents($fontsFolder . '/page-' . $pageId . '-fonts.css', str_replace('fonts/', '', $fontData['content']));
                file_put_contents($fontsFolder . '/header-footer-fonts.css', str_replace('fonts/', '', $fontData['content']));
                break;
            case 'downloadedFonts.json':
                file_put_contents($fontsFolder . '/downloadedFonts.json', $fontData['content']);
                break;
            default:
                $content = '';
                $bytes = $fontData['content'];
                foreach ($bytes as $chr) {
                    $content .= chr($chr);
                }
                file_put_contents($fontsFolder . '/' . $fontData['fileName'], $content);
            }
        }
    }

    /**
     * Create unique page title based on specified string
     *
     * @param string $title
     *
     * @return string
     */
    private static function _createUniqueTitle($title) {
        while (($p = get_page_by_title($title)) && $p->post_title === $title) {
            if (preg_match('#(.*\s)(\d+)$#', $title, $match)) {
                $new_title = $match[1] . ($match[2] + 1);
                if ($title === $new_title) {
                    break;
                }
                $title = $new_title;
            } else {
                $title = $title . ' 1';
            }
        }
        return $title;
    }

    /**
     * @param string|array $result Result
     *
     * @return mixed|string
     */
    public static function response($result)
    {
        if (is_string($result)) {
            $result = array('result' => $result);
        }
        return $result;
    }

    /**
     * Get chunk info
     *
     * @param array $data Chunk data
     *
     * @return array
     */
    public static function getChunkInfo($data)
    {
        return array(
            'id' => $data['id'],
            'content' =>  $data['content'],
            'current' =>  $data['current'],
            'total' =>  $data['total'],
            'blob' => $data['blob'] == 'true' ? true : false
        );
    }

    /**
     * Save header and footer content
     *
     * @param NpDataProvider $data_provider
     * @param array          $data
     *
     * @return array $result
     */
    public static function saveHeaderFooter($data_provider, $data)
    {
        $result = array();
        $keys = array('header', 'footer');
        $publishHeaderFooter = '';
        foreach ($keys as $key) {
            $html = $data[$key];
            $htmlCss = $data[$key.'Css'];
            $htmlPhp =  $data['publish'.ucfirst($key)];
            $formsData = $data[$key . 'FormsData'];
            $dialogsData = $data[$key . 'Dialogs'];

            if ($html) {
                $publishPageParts = str_replace(
                    get_site_url(),
                    '[[site_path_live]]',
                    array(
                        'html'    => $html,
                        'htmlPhp' => $htmlPhp,
                        'htmlCss' => $htmlCss
                    )
                );
                $htmlPhp = $data_provider->setHeaderFooterPublishHtml($htmlPhp);
                $result[$key] = json_encode(
                    array(
                        'html'   => $publishPageParts['html'],
                        'php'    => $publishPageParts['htmlPhp'],
                        'styles' => $publishPageParts['htmlCss'],
                        'formsData' => $formsData,
                        'dialogs' => $dialogsData,                    )
                );
                $publishHeaderFooter .= $htmlPhp;
            } else {
                $result[$key] = "";
                if (get_option($key . 'Np')) {
                    $item = json_decode(get_option($key . 'Np'), true);
                    $publishHeaderFooter .= $item['php'];
                }
            }
        }
        // Save header and footer content data
        if ($result['header'] !== "") {
            $data_provider->setNpHeader($result['header']);
            NpForms::updateForms(0, 'header', $data['publishHeader']);
        }
        if ($result['footer'] !== "") {
            $data_provider->setNpFooter($result['footer']);
            NpForms::updateForms(0, 'footer', $data['publishFooter']);
        }
        return $publishHeaderFooter;
    }
}

NpAction::add('np_save_page', 'NpSavePageAction');
add_filter('np_create_excerpt', 'wp_strip_all_tags');