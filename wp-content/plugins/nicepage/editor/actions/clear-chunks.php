<?php
defined('ABSPATH') or die;

class NpClearChunksAction extends NpAction {

    /**
     * Clear chunk by id
     *
     * @return array
     */
    public static function process() {

        include_once dirname(__FILE__) . '/chunk.php';

        $id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
        if ($id) {
            NpChunk::clearChunksById($id);
        }
        return array(
            'result' => 'done',
        );
    }
}
NpAction::add('np_clear_chunks', 'NpClearChunksAction');