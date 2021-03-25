<?php

$isCart = function_exists('wc_get_product') ? is_cart() : false;
if ($isCart) {
    add_action('theme_content_styles', 'theme_cart_content_styles');
} else {
    add_action('theme_content_styles', 'theme_single_content_styles');
}

function theme_single_body_class_filter($classes) {
    $classes[] = 'u-body';
    return $classes;
}
add_filter('body_class', 'theme_single_body_class_filter');

function theme_single_body_style_attribute() {
    return "";
}
add_filter('add_body_style_attribute', 'theme_single_body_style_attribute');

function theme_single_body_back_to_top() {
    return <<<BACKTOTOP

BACKTOTOP;
}
add_filter('add_back_to_top', 'theme_single_body_back_to_top');


function theme_single_get_local_fonts() {
    return '';
}
add_filter('get_local_fonts', 'theme_single_get_local_fonts');

ob_start();
get_header();
$header = ob_get_clean();
if (function_exists('renderHeader')) {
    renderHeader($header, '', 'echo');
} else {
    echo $header;
}

if (!$isCart) {
    theme_layout_before('post');
}

while (have_posts()) {
    $is_singular = is_singular();
    if ($is_singular) {
        the_post();
    }
    get_template_part('template-parts/single-content');

    if ($is_singular && (comments_open() || get_comments_number())) {
        comments_template();
    }

    get_template_part('template-parts/single-navigation');
}

if (!$isCart) {
    theme_layout_after('post');
} ?>

<?php ob_start();
get_footer();
$footer = ob_get_clean();
if (function_exists('renderFooter')) {
    renderFooter($footer, '', 'echo');
} else {
    echo $footer;
}
if ($isCart) {
    remove_action('theme_content_styles', 'theme_cart_content_styles');
} else {
    remove_action('theme_content_styles', 'theme_single_content_styles');
}
remove_filter('body_class', 'theme_single_body_class_filter');