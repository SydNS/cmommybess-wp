<?php
defined('ABSPATH') or die;

if (isset($_GET['action']) && $_GET['action'] === 'np_route_service_worker') {
    $content = file_get_contents(APP_PLUGIN_PATH . 'editor/assets/app/sw.js');
    header('Content-Type: application/javascript');
    echo $content;
    exit();
}