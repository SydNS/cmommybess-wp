<?php
defined('ABSPATH') or die;

class NpGetPostsByTypeAction extends NpAction {

    /**
     * Process action entrypoint
     *
     * @return array
     */
    public static function process() {
        $source = _arr($_REQUEST, 'category', array());
        $controlName = _arr($_REQUEST, 'type', 'blog'); //blog or products
        $postsType = $controlName == 'blog' ? 'posts' : 'products';
        if (preg_match('/^tags:/', $source)) {
            ${$controlName} = array(
                'tags' => str_replace('tags:', '', $source),
                'id' => null,
                $postsType => array(),
            );
            ${$controlName}[$postsType] = NpAdminActions::getPostsByCategory($source, $controlName);
        } else {
            if (preg_match('/^productId:/', $source)) {
                $productId = str_replace('productId:', '', $source);
                ${$controlName} = array(
                    'productId' => $productId,
                    'id' => null,
                    $postsType => array(),
                );
                $item = get_post($productId);
                if ($item) {
                    ${$controlName}[$postsType] = array(NpAdminActions::getProductsPost($item));
                }
            } else if (preg_match('/^postId:/', $source)) {
                $postId = str_replace('postId:', '', $source);
                ${$controlName} = array(
                    'postId' => $postId,
                    'id' => null,
                    $postsType => array(),
                );
                $item = get_post($postId);
                if ($item) {
                    ${$controlName}[$postsType] = array(NpAdminActions::getBlogPost($item), 'full');
                }
            } else {
                ${$controlName} = array(
                    'category' => $source,
                    'id' => -1,
                    $postsType => array(),
                );
                if ($source) {
                    $source = $source == 'Featured products' ? 'featured' : $source;
                    $cat_id = NpAdminActions::getCatIdByType($source, $controlName);
                    if ($cat_id > 0) {
                        $posts = NpAdminActions::getPostsByCategory($source, $controlName);
                        ${$controlName}['id'] = $cat_id;
                        ${$controlName}[$postsType] = $posts;
                    }
                }
            }
        }
        return array(
            'result' => 'done',
            $controlName => ${$controlName},
        );
    }
}
NpAction::add('np_get_posts_by_type', 'NpGetPostsByTypeAction');