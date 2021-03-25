<?php
/**
 * Registers a widget area.
 *
 * @link https://developer.wordpress.org/reference/functions/register_sidebar/
 */
function theme_widgets_init() {
    register_sidebar(array(
            'name'          => __('Widget Area 1', 'craftymommybeeswp'),
            'id'            => 'area_1',
            'description'   => __('Add widgets here to appear in your sidebar.', 'craftymommybeeswp'),
            'before_widget' => '<widget id="%1$s" name="%1$s" class="widget %2$s">',
            'after_widget'  => '</widget>',
            'after_title'   => '</' . 'title>',
            'before_title'  => '<title>',
        ));
register_sidebar(array(
            'name'          => __('Widget Area 2', 'craftymommybeeswp'),
            'id'            => 'area_2',
            'description'   => __('Add widgets here to appear in your sidebar.', 'craftymommybeeswp'),
            'before_widget' => '<widget id="%1$s" name="%1$s" class="widget %2$s">',
            'after_widget'  => '</widget>',
            'after_title'   => '</' . 'title>',
            'before_title'  => '<title>',
        ));
register_sidebar(array(
            'name'          => __('Widget Area 3', 'craftymommybeeswp'),
            'id'            => 'area_3',
            'description'   => __('Add widgets here to appear in your sidebar.', 'craftymommybeeswp'),
            'before_widget' => '<widget id="%1$s" name="%1$s" class="widget %2$s">',
            'after_widget'  => '</widget>',
            'after_title'   => '</' . 'title>',
            'before_title'  => '<title>',
        ));
register_sidebar(array(
            'name'          => __('Widget Area 4', 'craftymommybeeswp'),
            'id'            => 'area_4',
            'description'   => __('Add widgets here to appear in your sidebar.', 'craftymommybeeswp'),
            'before_widget' => '<widget id="%1$s" name="%1$s" class="widget %2$s">',
            'after_widget'  => '</widget>',
            'after_title'   => '</' . 'title>',
            'before_title'  => '<title>',
        ));
register_sidebar(array(
            'name'          => __('Widget Area 5', 'craftymommybeeswp'),
            'id'            => 'area_5',
            'description'   => __('Add widgets here to appear in your sidebar.', 'craftymommybeeswp'),
            'before_widget' => '<widget id="%1$s" name="%1$s" class="widget %2$s">',
            'after_widget'  => '</widget>',
            'after_title'   => '</' . 'title>',
            'before_title'  => '<title>',
        ));

}
add_action('widgets_init', 'theme_widgets_init');



function theme_register_unregister_widget_filters($sidebar_id, $add = true) {
    $widget_filters = array(
        
    );
    if (isset($widget_filters[$sidebar_id])) {
        if ($add) {
            add_filter('widget_text', $widget_filters[$sidebar_id], 1000);
        } else {
            remove_filter('widget_text', $widget_filters[$sidebar_id], 1000);
        }
    }
}

function theme_register_widget_filters($sidebar_id) {
    theme_register_unregister_widget_filters($sidebar_id, true);
}

function theme_unregister_widget_filters($sidebar_id) {
    theme_register_unregister_widget_filters($sidebar_id, false);
}

add_action('dynamic_sidebar_before', 'theme_register_widget_filters');
add_action('dynamic_sidebar_after', 'theme_unregister_widget_filters');