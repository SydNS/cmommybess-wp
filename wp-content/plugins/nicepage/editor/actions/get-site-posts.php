<?php
defined('ABSPATH') or die;

class NpBuilderSerializer {

    public static $imageExtensions = array('png', 'jpg', 'gif', 'svg+xml', 'ico', 'jpeg', 'bmp');
    private static $_usedAttachments = array();

    /**
     * Get image json in Nicepage-editor format
     *
     * @param WP_Post $attachment
     * 
     * @return array
     */
    public static function serializeImageAttachment($attachment) {
        $result = array();
        $sizes = get_intermediate_image_sizes();
        $sizes[] = 'full';

        foreach ($sizes as $size) {
            $data = wp_get_attachment_image_src($attachment->ID, $size);
            if ($data && count($data) >= 3) {
                $result[] = array(
                    'url' => $data[0],
                    'width' => $data[1],
                    'height' => $data[2],
                );
            }
        }
        usort($result, 'NpBuilderSerializer::imageSizesComparator');
        $unique_result = array();
        for ($i = 0, $len = count($result); $i < $len; $i++) {
            if ($i === 0 || $result[$i]['width'] !== $result[$i - 1]['width'] || $result[$i]['height'] !== $result[$i - 1]['height']) {
                $unique_result[] = $result[$i];
            }
        }
        return array('sizes' => $unique_result, 'type' => 'image', 'id' => 'cms_' . $attachment->ID);
    }

    /**
     * Image sizes comparator
     *
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    public static function imageSizesComparator($a, $b) {
        return $a['width'] - $b['width'];
    }

    /**
     * Parse post content to retrieve images with absolute url
     *
     * @param string $content          - post content
     * @param bool   $include_external - if need to search external urls
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getAbsoluteImagesData($content, $include_external = false) {
        preg_match_all('#<img[^\'"]*?src=[\'"]((https?:)?\/\/([^\'"]+?))[\'"]#', $content, $matches);

        $result = array();
        $len = count($matches[1]);
        for ($i = 0; $i < $len; $i++) {
            $url = $matches[1][$i];

            $upload_info = wp_upload_dir();
            if ($upload_info['error']) {
                throw new Exception($upload_info['error']);
            }
            $upload_dir = $upload_info['basedir'];
            $upload_url = $upload_info['baseurl'];
            if (substr($url, 0, strlen($upload_url)) !== $upload_url) {
                if ($include_external) {
                    $result[] = array(
                        'url' => $url
                    );
                }
                continue;
            }
            $relative_path = substr($url, strlen($upload_url));
            $abs_path = NpFilesUtility::normalizePath($upload_dir . $relative_path);
            if (!is_file($abs_path)) {//TODO
                continue;
            }

            $result[] = array(
                'url' => $url,
                'relative_path' => $relative_path,
                'absolute_path' => $abs_path,
                'context' => $matches[0][$i],
            );
        }
        return $result;
    }

    /**
     * Parse post content to retrieve images with relative url
     *
     * @param string $content
     *
     * @return array
     *
     * @throws Exception
     */
    public static function getRelativeImagesData($content) {
        preg_match_all('#["\'\(]\/((\S+?)\.(' . implode('|', self::$imageExtensions) . '))["\'\)]#', $content, $matches);
        $result = array();
        $len = count($matches[0]);
        for ($i = 0; $i < $len; $i++) {
            $path = NpFilesUtility::normalizePath($_SERVER['DOCUMENT_ROOT'] . '/' . $matches[1][$i]);

            if (!is_file($path)) {
                continue;
            }

            $upload_info = wp_upload_dir();
            if ($upload_info['error']) {
                throw new Exception($upload_info['error']);
            }
            $relative_path = substr($path, strlen($upload_info['basedir']));

            $result[] = array(
                'url' => '/' . $matches[1][$i],
                'relative_path' => $relative_path,
                'absolute_path' => $path,
                'context' => $matches[0][$i],
            );
        }
        return $result;
    }


    /**
     * Get post images json in Nicepage-editor format
     *
     * @param WP_Post $post
     * @param string  $content
     *
     * @return array
     *
     * @throws Exception
     */
    public static function serializePostImages($post, &$content) {
        $result = array();

        if ($post->post_type === 'attachment') {
            $result[] = self::serializeImageAttachment($post);
        } else {
            $thumb_id = get_post_thumbnail_id($post->ID);
            if ($thumb_id) {
                $thumb_attachment = get_post($thumb_id);
                if ($thumb_attachment) {
                    $result[] = self::serializeImageAttachment($thumb_attachment);
                    self::$_usedAttachments[] = $thumb_attachment->ID;
                }
            }

            $absolute = self::getAbsoluteImagesData($content, true);
            $relative = self::getRelativeImagesData($content);
            $images_info = array();
            foreach (array_merge($absolute, $relative) as $info) {
                $attachment = isset($info['relative_path'])
                    ? NpAttachments::getImageByPath($info['relative_path'])
                    : null;
                if ($attachment) {
                    $info['attachment'] = $attachment;
                    $images_info[$attachment->ID] = $info;
                } else {
                    $result[] = array(
                        'sizes' => array(
                            array(
                                'url' => $info['url'],
                            ),
                        ),
                        'type' => 'image'
                    );
                }
            }

            foreach ($images_info as $info) {
                $attachment = $info['attachment'];
                $result[] = self::serializeImageAttachment($attachment);
                self::$_usedAttachments[] = $attachment->ID;
            }
        }
        return $result;
    }


    /**
     * Get post json in Nicepage-editor format
     *
     * @param WP_Post $post
     *
     * @return array
     */
    public static function serializePost($post) {
        remove_filter('the_content', 'Nicepage::theContentFilter');
        $content = apply_filters('the_content', $post->post_content);
        add_filter('the_content', 'Nicepage::theContentFilter');
        $result = array(
            'url' => get_permalink($post),
            'postType' => ($post->post_type === 'attachment' ? 'image' : $post->post_type),
            'id' => 'cms_' . $post->ID,
            'date' => $post->post_date,
            'link' => array(array('url' => get_permalink($post)/*'#product-' . $post->ID*/)),
            'author' => array(
                'name' => get_the_author_meta('display_name', $post->post_author),
                'profile' => get_the_author_meta('url', $post->post_author),
            ),
            'images' => self::serializePostImages($post, $content),
            'videos' => array(),
            'h1' => array(),
            'h2' => array(),
            'text' => array(),
        );

        if ($post->post_type === 'attachment') {
            $result['fileName'] = $post->post_title;
        }

        if ($post->post_type === 'product' && function_exists('wc_get_product')) {
            $product = NpDataProduct::getProduct($post->ID);
            $price_str = $product->get_price_html();
            $price_str = strip_tags(preg_replace('#<del>(.*?)<\/del>#', '', $price_str));
            $result['h2'][] = array('content' => $price_str, 'type' => 'h2');

            foreach ($result['link'] as &$link) {
                $link['content'] = 'Buy';
            }
        }
        if ($post->post_type !== 'attachment') {
            $result['h1'][] = array('content' => $post->post_title, 'type' => 'h1');
        }
        $result['text'][] = array('content' => $content);
        return $result;
    }
}


class NpGetSitePostsAction extends NpAction {

    /**
     * Process action entrypoint
     *
     * @return array
     */
    public static function process() {

        $options = _arr($_REQUEST, 'options', array());
        if (isset($options['pageId'])) {
            return array(
                'result' => 'error',
                'message' => 'deprecated parameter',
            );
        }

        if (isset($options['page'])) {
            $post = get_post($options['page']);
            $post_json = NpBuilderSerializer::serializePost($post);
            return array(
                'result' => 'done',
                'data' => array(
                    'posts' => array(
                        'text' => $post_json['text'],
                        'images' => $post_json['images'],
                        'url' => get_permalink($post->ID),
                    ),
                ),
            );
        }

        $posts_count_limit = 20;

        $result = array();

        $posts_arr = array();
        $products_arr = array();
        $images_arr = array();

        if (isset($options['pageNumber'])) {
            $posts_page_idx = $options['pageNumber'];
            $result_posts = self::_getSerializedPosts(
                array(
                    'post_type' => 'post',
                    'posts_per_page' => $posts_count_limit,
                    'offset' => ($posts_page_idx - 1) * $posts_count_limit,
                    'order' => 'DESC',
                    'orderby' => 'modified',
                    'post_status' => 'publish',
                )
            );
            $result['nextPage'] = $posts_page_idx + 1;
            $result['isMultiplePages'] = $result_posts['hasMore'];
            $posts_arr = $result_posts['posts'];
        }

        if (isset($options['productsPageNumber'])) {
            $products_page_idx = $options['productsPageNumber'];
            $result_products = self::_getSerializedPosts(
                array(
                    'post_type' => 'product',
                    'posts_per_page' => $posts_count_limit,
                    'offset' => ($products_page_idx - 1) * $posts_count_limit,
                    'order' => 'DESC',
                    'orderby' => 'modified',
                    'post_status' => 'publish',
                )
            );
            $result['nextProductsPage'] = $products_page_idx + 1;
            $result['isMultipleProducts'] = $result_products['hasMore'];
            $products_arr = $result_products['posts'];
        }

        if (isset($options['imagesPageNumber'])) {
            $images_page_idx = $options['imagesPageNumber'];

            $image_mime_types = NpBuilderSerializer::$imageExtensions;
            foreach ($image_mime_types as &$type) {
                $type = "image/$type";
            }
            $images = get_posts(
                array(
                    'post_type' => 'attachment',
                    'posts_per_page' => $posts_count_limit,
                    'offset' => ($images_page_idx - 1) * $posts_count_limit,
                    'order' => 'DESC',
                    'orderby' => 'modified',
                    'post_mime_type' => $image_mime_types,
                    's' => isset($options['term']) ? $options['term'] : '',
                )
            );

            foreach ($images as $post) {
                $images_arr[] = NpBuilderSerializer::serializePost($post);
            }
            $result['nextImagesPage'] = $images_page_idx + 1;
            $result['isMultipleImages'] = count($images) === $posts_count_limit;
        }

        $result['posts'] = array_merge($posts_arr, $products_arr);
        $result['images'] = $images_arr;

        return array(
            'result' => 'done',
            'data' => $result,
        );
    }

    /**
     * Get posts json in Nicepage-editor format
     *
     * @param array $query
     *
     * @return array
     */
    public static function _getSerializedPosts($query) {
        $posts = isset($query['post__in']) && empty($query['post__in']) ? array() : get_posts($query);
        $result = array();

        foreach ($posts as $post) {
            if (NpEditor::isAllowedForBuilder($post) && trim($post->post_content)) {
                $result[] = NpBuilderSerializer::serializePost($post);
            }
        }
        return array(
            'posts' => $result,
            'hasMore' => count($posts) === _arr($query, 'posts_per_page'),
        );
    }
}
NpAction::add('np_get_site_posts', 'NpGetSitePostsAction');