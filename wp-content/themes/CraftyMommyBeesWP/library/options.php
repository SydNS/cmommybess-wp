<?php
defined('ABSPATH') or die;

global $theme_default_options;

$theme_default_options = array(
    'colors_css' => '',

    'fonts_css' => '',
    'fonts_link' => '',

    'typography_css' => '',

    'logo_width' => '',
    'logo_height' => '',
    'logo_link' => '',

    'menu_trim_title' => 1,
    'menu_trim_len' => 45,
    'submenu_trim_len' => 40,
    'menu_use_tag_filter' => 1,
    'menu_allowed_tags' => 'span, img',
    'use_default_menu' => '',

    'excerpt_auto' => 1,
    'excerpt_words' => 40,
    'excerpt_min_remainder' => 5,
    'excerpt_strip_shortcodes' => '',
    'excerpt_use_tag_filter' => 1,
    'excerpt_allowed_tags' => 'a, abbr, blockquote, b, cite, pre, code, em, label, i, p, strong, ul, ol, li, h1, h2, h3, h4, h5, h6, object, param, embed',
    'show_morelink' => 1,
    'morelink_template' => '<a href="[url]">[text]</a>',

    'include_jquery' => '',

    'seo_og' => 1,
    'seo_ld' => 1,

    'sidebars_layout_blog' => '',
    'sidebars_layout_post' => '',
    'sidebars_layout_default' => '',
);

function theme_get_option($name) {
    $result = get_theme_mod($name);
    if ($result === false) {
        global $theme_default_options;
        $result = isset($theme_default_options[$name]) ? $theme_default_options[$name] : false;
    }
    return $result;
}