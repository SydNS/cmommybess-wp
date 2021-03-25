<?php
defined('ABSPATH') or die;

class NpSaveMenuItemsAction extends NpAction {

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

        if (!isset($request['menuData'])) {
            return array(
                'status' => 'error',
                'type' => 'cmsSaveServerError',
                'message' => 'No menu data to save',
            );
        }

        $menuData = $request['menuData'];
        $menuData['menuItems'] = json_decode($menuData['menuItems'], true);
        $menuOptions = $menuData['menuOptions'];
        $menuData['siteMenuId'] = isset($menuOptions['siteMenuId']) && $menuOptions['siteMenuId'] != '' ? $menuOptions['siteMenuId'] : NpAdminActions::getMenuId();

        $menu = wp_get_nav_menu_object($menuData['siteMenuId']);
        $old_items_ids = $menu ? get_objects_in_term($menu->term_id, 'nav_menu') : false;
        self::saveMenu($menuData, $old_items_ids);
        return array(
            'result' => 'done',
        );
    }

    /**
     * Save menu in editor
     *
     * @param array $menuData
     * @param array $old_items_ids ids old items for delete
     */
    public static function saveMenu($menuData, $old_items_ids) {
        if (isset($menuData['siteMenuId']) && $menuData['siteMenuId'] > 0 && is_array($old_items_ids)) {
            if (isset($menuData['menuItems']) && is_array($menuData['menuItems'])) {
                $result = self::_updateMenuElements($menuData, $menuData['siteMenuId']);
                if (isset($old_items_ids) && !empty($old_items_ids) && $result) {
                    self::_removeMenuItems($old_items_ids);
                }
            }
        } else {
            $menu_name = _arr($menuData, 'caption', 'Menu');
            $menu_new_id = self::_addMenu($menu_name);
            if (is_int($menu_new_id) && isset($menuData['menuItems']) && is_array($menuData['menuItems'])) {
                $result = self::_addMenuElements($menuData, $menu_new_id);
                if ($result) {
                    self::_setMenuArea($menu_new_id);
                }
            }
        }
    }

    /**
     * @param string $menu_name
     *
     * @return string $menu_new_id
     */
    private static function _addMenu($menu_name) {
        // generate unique name
        for ($i = 0; ; $i++) {
            $new_name = $menu_name . ($i ? ' #' . $i : '');
            $_possible_existing = get_term_by('name', $new_name, 'nav_menu');
            if (!$_possible_existing || is_wp_error($_possible_existing) || !isset($_possible_existing->term_id)) {
                $menu_name = $new_name;
                break;
            }
        }
        return $menu_new_id = wp_update_nav_menu_object(0, array('menu-name' => $menu_name));
    }

    /**
     * @param array $menuData
     * @param int   $menu_id
     *
     * @return bool $result
     */
    private static function _addMenuElements($menuData, $menu_id) {
        $order = 0;
        $id_map = array();
        foreach ($menuData['menuItems'] as $menu_item_id => $menu_item) {
            $id_map[$menu_item_id] = wp_update_nav_menu_item($menu_id, 0, array());
            $menu_item['id'] = $id_map[$menu_item_id];
            $menuData['menuItems'][$menu_item_id] = $menu_item;
        }
        // add parameter parent for items
        $menuData['menuItems'] = self::setParentId($menuData['menuItems']);

        foreach ($menuData['menuItems'] as $menu_item_id => $menu_item) {
            $menu_item_data = array();
            $menu_item_caption = $menu_item['name'];
            if ($menu_item_caption) {
                $menu_item_data['menu-item-title'] = $menu_item_caption;
            }
            $menu_item_parent = $menu_item['parent'];
            if ($menu_item_parent >= 0) {
                $menu_item_data['menu-item-parent-id'] = $menu_item_parent;
            }
            if (isset($menu_item['href'])) {
                $menu_item_href = $menu_item['href'];
            }
            $menu_item_data['menu-item-position'] = ++$order;
            if (isset($menu_item_href) && $menu_item_href) {
                $menu_item_object_id = url_to_postid($menu_item_href);
                if ($menu_item_object_id && $menu_item_object_id > 0) {
                    $postItem = get_post($menu_item_object_id);
                    if ($postItem) {
                        $menu_item_data['menu-item-type'] = 'post_type';
                        $menu_item_data['menu-item-object'] = $postItem->post_type;
                        $menu_item_data['menu-item-object-id'] = $menu_item_object_id;
                    }
                } else {
                    $menu_item_data['menu-item-type'] = 'custom';
                    $menu_item_data['menu-item-url'] = $menu_item_href;
                }
            }
            $resultSave = wp_update_nav_menu_item($menu_id, $menu_item['id'], $menu_item_data);
            if (is_wp_error($resultSave)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set parent for menu item
     *
     * @param array $items
     * @param array $parentIds
     *
     * @return array $items
     */
    public static function setParentId($items, $parentIds = array()) {
        $level = 0;
        foreach ($items as $index => $itemData) {
            $itemLevel = $itemData['level'];
            if ($itemLevel == 0) {
                $parentId = 0;
                $parentIds = array('0' => 0);
            } else if ($itemLevel > $level) {
                $parentId = $items[$index - 1]['id'];
                $parentIds[$itemLevel] = $parentId;
            } else {
                $parentId = $parentIds[$itemLevel];
            }
            $level = $itemLevel;
            $items[$index]['parent'] = $parentId;
        }
        return $items;
    }

    /**
     * @param array $menuData
     * @param int   $menu_id
     *
     * @return bool $result
     */
    private static function _updateMenuElements($menuData, $menu_id) {
        $id_map = array();
        foreach ($menuData['menuItems'] as $menu_item_id => $menu_item) {
            $id_map[$menu_item_id] = wp_update_nav_menu_item($menu_id, 0, array());
            $menu_item['id'] = $id_map[$menu_item_id];
            $menuData['menuItems'][$menu_item_id] = $menu_item;
        }
        $oldMenuItems = wp_get_nav_menu_items($menu_id);
        $oldMenuLinks = array();
        $order = 0;

        // add parameter parent for items
        $menuData['menuItems'] = self::setParentId($menuData['menuItems']);

        foreach ($oldMenuItems as $oldMenuItem) {
            array_push($oldMenuLinks, $oldMenuItem->url);
        }

        foreach ($menuData['menuItems'] as $menu_item_id => $menu_item) {
            $new_info = false;
            $foundKey = array_search($menu_item['href'], $oldMenuLinks);
            if ($foundKey !== false) {
                $new_info = $oldMenuItems[$foundKey];
                $menu_item_data = array();
                $old_item_url = get_post_meta($new_info->ID, '_menu_item_url', true);
                $old_item_classes = get_post_meta($new_info->ID, '_menu_item_classes', true);
                if (is_array($old_item_classes)) {
                    $old_item_classes = implode(' ', $old_item_classes);
                }
                $old_item_post_meta = get_post_meta($new_info->ID);
                $new_info->ID = $menu_item['id'];

                $menu_item_data['menu-item-db-id'] = $new_info->ID;
                $menu_item_data['menu-item-title'] = $menu_item['name'];
                $menu_item_data['menu-item-attr-title'] = $menu_item['name'];
                $menu_item_data['menu-item-parent-id'] = $menu_item['parent'];;
                $menu_item_href = $menu_item['href'];
                $menu_item_object_id = url_to_postid($menu_item_href);
                if ($menu_item_object_id && $menu_item_object_id > 0) {
                    $postObject = get_post($menu_item_object_id);
                    if ($postObject) {
                        $menu_item_data['menu-item-type'] = 'post_type';
                        $menu_item_data['menu-item-object'] = $postObject->post_type;
                        $menu_item_data['menu-item-object-id'] = $menu_item_object_id;
                    }
                } else {
                    $menu_item_data['menu-item-type'] = 'custom';
                    $menu_item_data['menu-item-url'] = $menu_item_href;
                }
                $menu_item_data['menu-item-position'] = ++$order;
                $resultSave = wp_update_nav_menu_item($menu_id, $new_info->ID, $menu_item_data);
                if (is_wp_error($resultSave)) {
                    return false;
                }

                if (isset($new_info->db_id)) {
                    $new_info->db_id = $new_info->ID;
                }
                if (isset($new_info->post_name)) {
                    $new_info->post_name = (string)$new_info->ID;
                }
                if (isset($new_info->post_title)) {
                    $new_info->post_title = $menu_item['name'];
                }
                if (isset($new_info->guid)) {
                    $new_info->guid = get_home_url().'/?p='.(string)$new_info->ID;
                }
                if (isset($new_info->title)) {
                    $new_info->title = $menu_item['name'];
                }
                if (isset($new_info->menu_item_parent) && (int)$new_info->menu_item_parent !== $menu_item['parent']) {
                    $new_info->menu_item_parent = (string)$menu_item['parent'];
                }
                if (isset($new_info->url) && $new_info->url !== $menu_item['href']) {
                    $new_info->url = $menu_item['href'];
                }
                $new_info->menu_order = $menu_item_data['menu-item-position'];
                $post_id = wp_update_post($new_info->to_array());
                if (is_wp_error($post_id)) {
                    return false;
                }

                foreach ($old_item_post_meta as $key => $value) {
                    if ($key === "_menu_item_classes") {
                        update_post_meta($post_id, $key, $old_item_classes);
                    } else {
                        update_post_meta($post_id, $key, $value[0]);
                    }
                }
                if ($old_item_url !== $menu_item['href']) {
                    update_post_meta($post_id, '_menu_item_url', $menu_item['href']);
                }
                update_post_meta($post_id, '_menu_item_menu_item_parent', $menu_item['parent']);
            } else {
                if (!$new_info) {
                    $menu_item_data['menu-item-title'] = $menu_item['name'];
                    $menu_item_data['menu-item-parent-id'] = $menu_item['parent'];;
                    $menu_item_href = $menu_item['href'];
                    $menu_item_object_id = url_to_postid($menu_item_href);
                    if ($menu_item_object_id && $menu_item_object_id > 0) {
                        $postObject = get_post($menu_item_object_id);
                        if ($postObject) {
                            $menu_item_data['menu-item-type'] = 'post_type';
                            $menu_item_data['menu-item-object'] = $postObject->post_type;
                            $menu_item_data['menu-item-object-id'] = $menu_item_object_id;
                        }
                    } else {
                        $menu_item_data['menu-item-type'] = 'custom';
                        $menu_item_data['menu-item-url'] = $menu_item_href;
                    }
                    $menu_item_data['menu-item-position'] = ++$order;
                    $resultSave = wp_update_nav_menu_item($menu_id, $menu_item['id'], $menu_item_data);
                    if (is_wp_error($resultSave)) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param array $menu_id
     */
    private static function _setMenuArea($menu_id) {
        $positions = self::getMenuPosition();
        if (is_string($positions) && $positions) {
            $positions = explode(',', $positions);
            $nav_menu_locations = get_nav_menu_locations();
            foreach ($positions as $position) {
                $position = trim($position);
                if ($position) {
                    $nav_menu_locations[$position] = $menu_id;
                }
            }
            set_theme_mod('nav_menu_locations', $nav_menu_locations);
        }
    }

    /**
     * @param array $items
     */
    private static function _removeMenuItems($items) {
        foreach ($items as $item) {
            wp_delete_post($item);
        }
    }

    /**
     * Get menu id from wp
     *
     * @return int|string
     */
    public static function getMenuPosition() {
        $menu_position = false;
        $locations = get_registered_nav_menus();
        $locationsKeys = array_keys($locations);
        for ($i = 0; $i < count($locationsKeys); $i++) {
            $menu_position = array_shift($locationsKeys);
            if ($menu_position) {
                break;
            }
        }
        return $menu_position;
    }
}
NpAction::add('np_save_menu_items', 'NpSaveMenuItemsAction');