<?php
defined('ABSPATH') or die;

require_once dirname(dirname(__FILE__)) . '/../includes/class-np-files-utility.php';

class NpUploadAction extends NpAction {

    /**
     * Process action entrypoint
     *
     * @return array
     *
     * @throws Exception
     */
    public static function process() {

        $fileName = _arr($_REQUEST, 'filename', '');
        $is_last = _arr($_REQUEST, 'last', '');

        if ('' === $fileName) {
            return array(
                'status' => 'error',
                'message' => 'Empty filename',
            );
        } else {
            try {
                $uploads_info = wp_upload_dir();
                $filesPath = $uploads_info['basedir'];
                $uploadHere = '';
                if (file_exists($filesPath) && is_writable($filesPath)) {
                    $uploadHere = $filesPath . '/' . $fileName;
                }
                if (!$uploadHere) {
                    return array(
                        'status' => 'error',
                        'message' => 'Upload dir ' . $uploadHere . ' don\'t writable',
                    );
                }
                $result = self::_uploadFileChunk($fileName, $is_last);
                if ($is_last) {
                    $uploads_info = wp_upload_dir();
                    $tmp_dir = $uploads_info['basedir'] . '/nicepage-export';
                    NpFilesUtility::emptyDir($tmp_dir, true);
                }
                if ($result['status'] == 'done' || $result['status'] == 'error') {
                    return $result;
                }
            } catch (Exception $e) {
                return array(
                    'status' => 'error',
                    'result' => $e->getMessage()
                );
            }
        }
        return array(
            'result' => 'done'
        );
    }

    /**
     * Process chunk
     *
     * @param string $filename - target file name
     * @param bool   $is_last  - is it chunk last one
     *
     * @return array
     *
     * @throws Exception
     */
    private static function _uploadFileChunk($filename, $is_last) {
        if (!isset($_FILES['chunk']) || !file_exists($_FILES['chunk']['tmp_name'])) {
            throw new Exception('Empty chunk data');
        }

        if (empty($_REQUEST['uploadId'])) {
            throw new Exception('Empty uploadId');
        }

        $content_range = $_SERVER['HTTP_CONTENT_RANGE'];
        if ('' === $content_range && '' === $is_last) {
            throw new Exception('Empty Content-Range header');
        }

        $range_begin = 0;

        if ($content_range) {
            $content_range = str_replace('bytes ', '', $content_range);
            list($range, $total) = explode('/', $content_range);
            list($range_begin, $range_end) = explode('-', $range);
        }

        $uploads_info = wp_upload_dir();
        $tmp_base_dir = $uploads_info['basedir'] . '/nicepage-export';
        $tmp_data_dir = $tmp_base_dir . '/data';
        $tmp_extracted_data_dir = $tmp_data_dir . '/extracted';
        $tmp_zip_path = $tmp_data_dir . '/' . basename($filename);

        NpFilesUtility::createDir($tmp_base_dir);

        $fh = fopen("$tmp_base_dir/lock", 'w');
        if (flock($fh, LOCK_EX)) {
            $prev_upload_id = file_exists("$tmp_base_dir/id") ? file_get_contents("$tmp_base_dir/id") : '';
            if ($prev_upload_id !== $_REQUEST['uploadId']) {
                // clear previous upload data
                NpFilesUtility::createDir($tmp_data_dir);
                NpFilesUtility::emptyDir($tmp_data_dir);
                file_put_contents("$tmp_base_dir/id", $_REQUEST['uploadId']);
                file_put_contents($tmp_zip_path, '');
            }

            $f = fopen($tmp_zip_path, 'r+');
            fseek($f, (int) $range_begin);
            fwrite($f, file_get_contents($_FILES['chunk']['tmp_name']));
            fclose($f);

            flock($fh, LOCK_UN);
            fclose($fh);
        }

        if ($is_last) {
            NpFilesUtility::createDir($tmp_extracted_data_dir);
            $result = self::uploadMediaFile($tmp_data_dir . '/' . $filename, 0, $filename);
            if (is_wp_error($result)) {
                return array(
                    'status' => 'error',
                    'message' => $result->get_error_message()
                );
            }
            return array(
                'status' => 'done',
                'result' => $result
            );
        }
        return array(
            'status' => 'processed'
        );
    }

    /**
     * @param string $file
     * @param int    $post_id
     * @param null   $title
     *
     * @return array
     */
    public static function uploadMediaFile($file, $post_id = 0, $title = null) {
        if (empty($file)) {
            return new \WP_Error('error', 'File is empty');
        }

        $file_array = array();
        $result = array();
        $allowed_mimes = get_allowed_mime_types();
        $allowed_types = array();
        foreach ($allowed_mimes as $ext => $allowed_mime) {
            $allowed_types[] = $ext;
        }
        $allowed_types_mask = implode("|", $allowed_types);

        // Get filename and store it into $file_array
        // Add more file types
        preg_match('/[^\?]+\.(' . $allowed_types_mask . ')\b/i', $file, $matches);
        $file_array['name'] = basename($matches[0]);

        // Download file into temp location.
        $file_array['tmp_name'] = $file;

        // If error storing temporarily, return the error.
        if (is_wp_error($file_array['tmp_name'])) {
            return new \WP_Error('error', 'Error while storing file temporarily');
        }

        // Store and validate
        $id = media_handle_sideload($file_array, $post_id, $title);

        // Unlink if couldn't store permanently
        if (is_wp_error($id)) {
            unlink($file_array['tmp_name']);
            return new \WP_Error('error', $id->get_error_message());
        }

        if (empty($id)) {
            return new \WP_Error('error', "Upload ID is empty");
        }
        $attachment = get_post($id);
        if ($attachment) {
            $result = array (
                'title' => $attachment->post_name,
                'url'   => $attachment->guid
            );
        }
        return $result;
    }

    /**
     * Add file type mimes
     *
     * @param array $mimes
     *
     * @return array
     */
    public static function addFileTypeMime($mimes = array()){
        $mimes['zip'] =  'application/zip';
        return $mimes;
    }
}

add_filter('upload_mimes', 'NpUploadAction::addFileTypeMime');
NpAction::add('np_upload_file', 'NpUploadAction');