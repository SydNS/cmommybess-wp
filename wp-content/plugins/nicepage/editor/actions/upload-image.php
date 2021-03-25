<?php
defined('ABSPATH') or die;

class NpUploadImageAction extends NpAction {

    /**
     * Process action entrypoint
     *
     * @return array
     */
    public static function process() {

        if (!current_user_can('upload_files')) {
            return array(
                'status' => 'error',
                'type' => 'uploadImage',
                'message' => 'You do not have permissions to upload files. Please contact your server administrator',
            );
        }

        if (!isset($_POST['html-upload']) || empty($_FILES)) {
            return array(
                'status' => 'error',
                'type' => 'uploadImage',
                'message' => 'Invalid parameters',
            );
        }

        $post_id = 0;
        if (isset($_REQUEST['pageId'])) {
            $post_id = absint($_REQUEST['pageId']);
            if (!get_post($post_id) || !current_user_can('edit_post', $post_id)) {
                $post_id = 0;
            }
        }

        check_admin_referer('media-form');

        $upload_id = media_handle_upload('async-upload', $post_id);

        if (is_wp_error($upload_id)) {
            $error_message = $upload_id->get_error_message();
            $max_size_limit = stripos($error_message, 'upload_max_filesize');
            if ($max_size_limit) {
                return array(
                    'status' => 'error',
                    'type' => 'uploadImage',
                    'message' => 'The file size exceeds the maximum upload file size. Increase upload_max_filesize in php.ini or contact your server administrator.',
                );
            } else {
                return array(
                    'status' => 'error',
                    'type' => 'uploadImageCmsError',
                    'message' => $upload_id->get_error_message(),
                );
            }
        }

        return array(
            'status' => 'done',
            'image' => NpBuilderSerializer::serializeImageAttachment(get_post($upload_id)),
        );
    }
}
NpAction::add('np_upload_image', 'NpUploadImageAction');