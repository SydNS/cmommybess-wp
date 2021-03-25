<?php
defined('ABSPATH') or die;

class NpWidgetsImporter {

    /**
     * @var NpWidgetsImporter
     */
    private $_contentImporter;

    /**
     * @var array
     * Common format => WP format mapping
     */
    private $_typeMap = array(
        'text'       => 'text',
        'search'     => 'search',
        'archives'    => 'archives',
        'vmenu'      => 'vmenuwidget',
        'login'      => 'loginwidget',
        'categories' => 'categories',
    );

    /**
     * @var array
     * Common format => WP format mapping
     */
    private $_sidebarsMap = array(
        'sidebar1'       => 'primary-widget-area',
        'sidebar2'       => 'secondary-widget-area',
        'content-before' => 'first-top-widget-area',
        'content-after'  => 'first-bottom-widget-area',
        'inactive'       => 'wp_inactive_widgets'
    );

    /**
     * NpWidgetsImporter constructor.
     *
     * @param NpContentImporter $content_importer base importer
     */
    public function __construct($content_importer) {
        $this->_contentImporter = $content_importer;
    }

    /**
     * Split 'text-12' into array('text', '12')
     * array(false, false) in case of invalid $widget_id
     *
     * @param string $widget_id
     *
     * @return array
     */
    private function _splitTypeId($widget_id) {
        $type = false;
        $id = false;
        if (preg_match('/^(.*[^-])-([0-9]+)$/', $widget_id, $matches) && isset($matches[1]) && isset($matches[2])) {
            $type = $matches[1];
            $id = $matches[2];
        }
        return array($type, $id);
    }

    /**
     * Add widget
     *
     * @param string $sidebar - sidebar name
     * @param string $type
     * - text
     * - search
     * - archive
     * - vmenu
     * - login
     * - categories
     *
     * @return string - new widget identifier
     */
    public function addWidget($sidebar, $type) {
        $sidebar = _arr($this->_sidebarsMap, $sidebar, $sidebar);

        // gets the list of current sidebars and widgets from blog options
        $wp_sidebars = get_option('sidebars_widgets');

        if (!isset($wp_sidebars[$sidebar])) {
            $wp_sidebars[$sidebar] = array();
        }

        if (!isset($this->_typeMap[$type])) {
            $type = 'text';
        }
        $type = $this->_typeMap[$type];

        // gets the widget data
        $wp_widget = get_option('widget_' . $type);
        $wp_widget = $wp_widget ? $wp_widget : array();

        // new widget id is always unique
        $new_widget_id = 0;
        foreach ($wp_widget as $widget_id => $widget) {
            if (is_int($widget_id)) {
                $new_widget_id = max($new_widget_id, $widget_id);
            }
        }
        $new_widget_id++;
        $new_widget_name = $type . '-' . $new_widget_id;

        // gets widgets from the selected sidebar
        $wp_sidebar_widgets = $wp_sidebars[$sidebar];

        $wp_sidebar_widgets[] = $new_widget_name;

        // puts new sidebar widgets in the list of sidebars
        $wp_sidebars[$sidebar] = $wp_sidebar_widgets;

        update_option('sidebars_widgets', $wp_sidebars);

        // creates new widget
        $wp_widget[$new_widget_id] = array();

        // default Artisteer widgets
        if ($type == 'text') {
            $wp_widget[$new_widget_id]['text'] = '';
            $wp_widget[$new_widget_id]['filter'] = false;
        }
        if ($type == 'vmenuwidget') {
            $wp_widget[$new_widget_id]['source'] = 'Pages';
            $wp_widget[$new_widget_id]['nav_menu'] = 0;
        }
        if ($type == 'archives') {
            $wp_widget[$new_widget_id]['count'] = 0;
            $wp_widget[$new_widget_id]['dropdown'] = 0;
        }
        if ($type == 'categories') {
            $wp_widget[$new_widget_id]['count'] = '0';
            $wp_widget[$new_widget_id]['dropdown'] = '0';
            $wp_widget[$new_widget_id]['hierarchical'] = '0';
        }

        $wp_widget[$new_widget_id]['title'] = '';

        if (!isset($wp_widget['_multiwidget'])) {
            $wp_widget['_multiwidget'] = 1;
        }

        update_option('widget_' . $type, $wp_widget);
        return $new_widget_name;
    }

    /**
     * Update widget
     *
     * @param string      $widget_id
     * @param string      $title
     * @param string|null $content
     * @param array|null  $args
     *
     * @return bool - true it widgets was updated, false otherwise
     */
    public function updateWidget($widget_id, $title, $content = null, $args = null) {

        list($type, $id) = $this->_splitTypeId($widget_id);
        if (!$type) {
            return false;
        }

        $wp_widget = get_option('widget_' . $type);

        if (!$wp_widget || !isset($wp_widget[$id])) {
            return false;
        }

        if (!empty($title)) {
            $wp_widget[$id]['title'] = $title;
        }

        if (!empty($content) && $type == 'text') {
            $wp_widget[$id]['text'] = $content;
        }

        if (is_array($args)) {
            $wp_widget[$id] = array_merge($wp_widget[$id], $args);
        }

        if (!isset($wp_widget['_multiwidget'])) {
            $wp_widget['_multiwidget'] = 1;
        }

        update_option('widget_' . $type, $wp_widget);
        return true;
    }

    /**
     * Delete widget
     *
     * @param string $widget_id
     * @param bool   $force_delete - delete widget or move it to inactive
     *
     * @return bool
     */
    public function deleteWidget($widget_id, $force_delete = false) {
        $widget_exist = false;
        $wp_sidebars = get_option('sidebars_widgets');
        foreach ($wp_sidebars as $sidebar_id => $widgets) {
            if (is_array($widgets)) {
                $new_widgets = array();
                foreach ($widgets as $widget) {
                    if ($widget != $widget_id) {
                        $new_widgets[] = $widget;
                        $widget_exist = true;
                    }
                }
                $wp_sidebars[$sidebar_id] = $new_widgets;
            }
        }
        if (!$force_delete && $widget_exist) {
            if (!is_array($wp_sidebars['wp_inactive_widgets'])) {
                $wp_sidebars['wp_inactive_widgets'] = array();
            }
            $wp_sidebars['wp_inactive_widgets'][] = $widget_id;
        }
        update_option('sidebars_widgets', $wp_sidebars);

        if ($force_delete && $widget_exist) {
            list($type, $id) = $this->_splitTypeId($widget_id);
            if (!$type) {
                return false;
            }

            $wp_widget = get_option('widget_' . $type);
            if (!$wp_widget || !isset($wp_widget[$id])) {
                return false;
            }
            unset($wp_widget[$id]);
            if (!isset($wp_widget['_multiwidget'])) {
                $wp_widget['_multiwidget'] = 1;
            }
            update_option('widget_' . $type, $wp_widget);
        }
        return true;
    }

    /**
     * Deactivate all active widgets
     */
    public function deactivateAllWidgets() {
        $wp_sidebars = get_option('sidebars_widgets');
        if (!is_array($wp_sidebars['wp_inactive_widgets'])) {
            $wp_sidebars['wp_inactive_widgets'] = array();
        }
        foreach ($wp_sidebars as $sidebar_id => $widgets) {
            if ('wp_inactive_widgets' != $sidebar_id && is_array($widgets)) {
                $wp_sidebars['wp_inactive_widgets'] = array_merge($wp_sidebars['wp_inactive_widgets'], $widgets);
                $wp_sidebars[$sidebar_id] = array();
            }
        }
        update_option('sidebars_widgets', $wp_sidebars);
    }

    /**
     * Import sidebars
     *
     * @param array $sidebars
     * @param array $widgets
     *
     * @return array - list of added widget ids
     */
    public function importSidebars($sidebars, $widgets) {
        $added_widgets = array();

        foreach ($sidebars as $sidebar) {
            $sidebar_widgets = _arr($sidebar, 'widgets');
            $sidebar_name = _arr($sidebar, 'name');

            if (empty($sidebar_widgets) || empty($sidebar_name)) {
                continue;
            }

            $widgets_placeholders = explode(',', $sidebar_widgets);
            foreach ($widgets_placeholders as $placeholder) {
                list(, $id) = $this->_contentImporter->parsePlaceholder($placeholder);
                if (!$id || empty($widgets[$id])) {
                    continue;
                }
                $widget = $widgets[$id];

                if (isset($widget['content'])) {
                    $widget['content'] = $this->_contentImporter->_processContent($widget['content']);
                }

                $widget_type = _arr($widget, 'type');

                $widget_id = $this->addWidget($sidebar_name, $widget_type);

                $args = null;
                if ($widget_type === 'vmenu') {
                    $args = array(
                        'source' => 'Custom Menu',
                        'nav_menu' => _arr($this->_contentImporter->vmenus, $id, ''),
                    );
                }
                $this->updateWidget($widget_id, _arr($widget, 'title', ''), _arr($widget, 'content', ''), $args);

                if (!empty($widget['pageHead'])) {
                    list($type, $id) = $this->_splitTypeId($widget_id);

                    $wp_widget = get_option('widget_' . $type);
                    if ($wp_widget && isset($wp_widget[$id])) {
                        $wp_widget[$id]['theme_widget_styling'] = $this->_contentImporter->_processContent($widget['pageHead']);
                        update_option('widget_' . $type, $wp_widget);
                    }
                }
                $added_widgets[] = $widget_id;
            }
        }
        return $added_widgets;
    }

    /**
     * Import content widget to position control
     *
     * @param string $sidebar
     * @param array  $widgetContent
     * @param bool   $removeWidgets
     */
    public static function importWidgetsContent($sidebar, $widgetContent, $removeWidgets) {
        $widgetContent['text'] = self::processLink($widgetContent['text']);
        $active_widgets = get_option('sidebars_widgets');
        if (!isset($active_widgets[$sidebar])) {
            // Okay, There is not sidebar yet.
            return;
        } else {
            $all_text_widgets = get_option('widget_text');
            $id_widgets = array_keys($all_text_widgets);
            $id_widgets = $id_widgets ? $id_widgets : array(-1);
            $counter = (int)max($id_widgets) + 1;
            if (!$counter) {
                $counter = 0;
            }
            $count_user_widgets = count($active_widgets[$sidebar]);

            if (!empty($active_widgets[$sidebar])) {
                if ($removeWidgets) {
                    // delete active widgets in our sidebar
                    $active_widgets[$sidebar] = array();
                }
            }
            $active_widgets[$sidebar][$count_user_widgets] = 'text-' . $counter;
            $all_text_widgets[$counter] = $widgetContent;
            // Now save the content for all text widgets.
            update_option('widget_text', $all_text_widgets);
            // Now save the $active_widgets array.
            update_option('sidebars_widgets', $active_widgets);
        }
    }

    /**
     * Rendering wp widgets in the editable header/footer
     *
     * @param array $props
     */
    public static function pluginWPWidget($props) {
        global $plugin_nosidebar_widgets;
        $plugin_nosidebar_widgets = array(
            'text' => array(
                'WP_Widget_Text',
                array(
                    'title' => 'title',
                    'text' => 'content',
                ),
                array(
                    'filter' => true,
                )
            ),
            'calendar' => array(
                'WP_Widget_Calendar',
                array(
                    'title' => 'title',
                )
            ),
            'searchWidget' => array(
                'WP_Widget_Search',
                array(
                    'title' => 'title',
                )
            ),
            'meta' => array(
                'WP_Widget_Meta',
                array(
                    'title' => 'title',
                )
            ),
            'pages' => array(
                'WP_Widget_Pages',
                array(
                    'title' => 'title',
                    'exclude' => array('excludes', ''),
                    'sortby' => array('order-by', 'ID'),
                )
            ),
            'tag-cloud' => array(
                'WP_Widget_Tag_Cloud',
                array(
                    'title' => 'title',
                    'taxonomy' => 'taxonomy',
                    'count' => 'tag-cloud-counts',
                )
            ),
            'menuWidget' => array(
                'WP_Nav_Menu_Widget',
                array(
                    'title' => 'title',
                    'nav_menu' => 'menu',
                )
            ),
            'categories' => array(
                'WP_Widget_Categories',
                array(
                    'title' => 'title',
                    'count' => 'show-post-counts',
                    'hierarchical' => 'show-hierarchy',
                    'dropdown' => 'display-as-dropdown',
                )
            ),
            'archives' =>array(
                'WP_Widget_Archives',
                array(
                    'title' => 'title',
                    'count' => 'show-post-counts',
                    'dropdown' => 'display-as-dropdown',
                )
            ),
            'comments' => array(
                'WP_Widget_Recent_Comments',
                array(
                    'title' => 'title',
                    'number' => 'posts-count',
                )
            ),
            'posts' => array(
                'WP_Widget_Recent_Posts',
                array(
                    'title' => 'title',
                    'number' => 'posts-count',
                    'show_date' => 'display-post-date',
                )
            ),
            'rss' => array(
                'WP_Widget_RSS',
                array(
                    'title' => 'title',
                    'url' => 'feed-url',
                    'items' => 'posts-count',
                    'show_summary' => 'display-item-content',
                    'show_author' => 'display-item-author',
                    'show_date' => 'display-item-date',
                )
            ),
        );

        $type = _arr($props, 'type', 'text');
        $data = $plugin_nosidebar_widgets[$type];
        $class = $data[0];
        $args = _arr($data, 2, array());
        foreach ($data[1] as $args_key => $source_key) {
            if (is_string($source_key)) {
                $args[$args_key] = _arr($props, $source_key);
            } else if (is_array($source_key)) {
                $args[$args_key] = _arr($props, $source_key[0], $source_key[1]);
            }
        }
        the_widget($class, $args);
    }

    /**
     * Process replace link [page_] and [blog_] to cms url
     *
     * @param string $content
     *
     * @return string
     */
    public static function processLink($content) {
        if (strpos($content, '[page_') !== false) {
            $params = get_option('np_page_ids');
            if ($params) {
                foreach ($params as $key => $value) {
                    $content = str_replace("[page_" . $key . "]", get_permalink($value), $content);
                }
            }
        }
        if (strpos($content, '[blog_') !== false) {
            $blogUrl = get_option('page_for_posts') ? get_permalink(get_option('page_for_posts')) : get_home_url();
            $content = preg_replace('/\[blog_[0-9]*?\]/', $blogUrl, $content);
        }
        return $content;
    }
}