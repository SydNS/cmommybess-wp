<?php defined('ABSPATH') or die;

$domain = NpEditor::getDomain();

$loader_path = $domain
    ? $domain . '/Editor/loader.js'
    : APP_PLUGIN_URL . 'editor/assets/app/loader.js?ver=' . APP_PLUGIN_VERSION;

$sw_path = NpAction::getActionUrl('np_route_service_worker');

header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:400,300,700"/>
    <script>window.loadAppHook = parent.loadAppHook;</script>
    <script type="text/javascript" src="<?php echo APP_PLUGIN_URL; ?>editor/assets/js/editor.js?ver=<?php echo APP_PLUGIN_VERSION; ?>"></script>
    <script id="loader-script" type="text/javascript"
            src="<?php echo $loader_path; ?>"
            data-swurl="<?php echo $sw_path; ?>"
            data-assets="<?php echo APP_PLUGIN_URL; ?>editor/assets/app/"
            data-processor="wp">
    </script>
</head>
<body>

</body>
</html>