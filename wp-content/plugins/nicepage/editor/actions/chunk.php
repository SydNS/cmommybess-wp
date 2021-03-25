<?php
defined('ABSPATH') or die;

/**
 * Class NpChunk
 */
class NpChunk
{
    public $UPLOAD_PATH;
    private $_lastChunk = null;
    private $_chunkFolder = '';
    private $_lockFile = '';
    private $_isLast = false;

    /**
     * NpChunk constructor.
     */
    public function __construct()
    {
        $this->UPLOAD_PATH = APP_PLUGIN_PATH;
        $this->_chunkFolder = $this->UPLOAD_PATH . 'default';
    }

    /**
     * Save chunk
     *
     * @param array $info Chunk info
     *
     * @return array|bool
     *
     * @throws Exception
     */
    public function save($info)
    {
        $ret = $this->validate($info);
        if ('' !== $ret) {
            return array(
                'status' => 'error',
                'data' => $ret
            );
        }

        $this->_lastChunk = $info;
        $this->_chunkFolder = $this->UPLOAD_PATH . $info['id'];
        $this->_lockFile = $this->_chunkFolder . '/lock';

        if (!is_dir($this->_chunkFolder)) {
            NpFilesUtility::createDir($this->_chunkFolder);
        }

        $f = fopen($this->_lockFile, 'c');

        if (flock($f, LOCK_EX)) {
            $chunks = array_diff(scandir($this->_chunkFolder), array('.', '..', 'lock'));

            if ((int) $this->_lastChunk['total'] === count($chunks) + 1) {
                $this->_isLast = true;
            }

            $content = $info['content'];

            if (!empty($this->_lastChunk['blob'])) {
                if (empty($_FILES['content']['tmp_name'])) {
                    return array(
                        'status' => 'error',
                        'data' => 'Chunk content is empty: ' . print_r($_FILES, true)
                    );
                }
                $content_path = $_FILES['content']['tmp_name'];
                if (file_exists($content_path)) {
                    $content = file_get_contents($content_path);
                } else {
                    $content = '';
                }
                wp_delete_file($_FILES['content']['tmp_name']);
            }

            file_put_contents($this->_chunkFolder . '/' . (int) $info['current'], $content);

            flock($f, LOCK_UN);
            return true;
        } else {
            return array(
                'status' => 'error',
                'data' => 'Couldn\'t lock the file: ' . $this->_lockFile
            );
        }
    }

    /**
     * Checking chunk for last
     *
     * @return bool
     */
    public function last()
    {
        return $this->_isLast;
    }

    /**
     * Complete content
     *
     * @return array
     *
     * @throws Exception
     */
    public function complete()
    {
        $content = '';
        for ($i = 1, $count = (int) $this->_lastChunk['total']; $i <= $count; $i++) {
            if (!file_exists($this->_chunkFolder . "/$i")) {
                return array(
                    'status' => 'error',
                    'data' => 'Missing chunk #' . $i . ' : ' . implode(' / ', scandir($this->_chunkFolder))
                );
            }
            $data = file_get_contents($this->_chunkFolder . "/$i");
            $content .= $data;
        }
        NpFilesUtility::emptyDir($this->_chunkFolder, true);

        return array(
            'status' => 'done',
            'data' => $content
        );
    }

    /**
     * Validate chunk info
     *
     * @param array $info Chunk info
     *
     * @return string
     */
    public function validate($info)
    {
        $errors = array();
        if (!isset($info['id']) || !$info['id']) {
            $errors[] = 'Invalid id';
        }
        if (!isset($info['total']) || (int) $info['total'] < 1) {
            $errors[] = 'Invalid chunks total';
        }
        if (!isset($info['current']) || (int) $info['current'] < 1) {
            $errors[] = 'Invalid current chunk number';
        }
        if (empty($_FILES['content']) && empty($info['content'])) {
            $errors[] = 'Invalid chunk content';
        }
        if (count($errors) < 1) {
            return '';
        } else {
            return implode(', ', $errors);
        }
    }

    /**
     * Remove chunk by id
     *
     * @param string $id Chunk id
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function clearChunksById($id) {
        $chunkUploadPath = APP_PLUGIN_PATH . $id;
        if ($id && is_dir($chunkUploadPath)) {
            NpFilesUtility::emptyDir($chunkUploadPath, true);
            return true;
        } else {
            return false;
        }
    }
}