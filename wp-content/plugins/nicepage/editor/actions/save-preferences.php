<?php
defined('ABSPATH') or die;

class NpSavePreferencesAction extends NpAction {

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

        $settings = isset($request['settings']) && is_array($request['settings']) ? $request['settings'] : array();
        $status = (isset($settings['disableAutosave']) && $settings['disableAutosave'] == '1') ? '0' : '1';
        $old_status = get_option('np_include_auto_save');
        if ($old_status !== $status) {
            $result = update_option('np_include_auto_save', $status) ? 'done' : 'error';
        } else {
            $result = 'done';
        }
        return array(
            'result' => $result,
        );
    }
}
NpAction::add('np_save_preferences', 'NpSavePreferencesAction');