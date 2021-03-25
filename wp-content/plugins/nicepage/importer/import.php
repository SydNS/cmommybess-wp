<?php
defined('ABSPATH') or die;

$import_href = admin_url('admin.php?page=np_import');
?>

<div class="wrap">
    <h1><?php _e('Import', 'nicepage'); ?></h1>
<?php

$upload_dir = wp_upload_dir();
if (!empty($upload_dir['error'])) {
?>
    <div class="error">
        <p><?php _e('Before you can upload your import file, you will need to fix the following error:', 'nicepage'); ?></p>
        <p><strong><?php echo $upload_dir['error']; ?></strong></p>
    </div>
<?php
} else { ?>

    <p>
        <?php _e('Upload your (.zip) file and we&#8217;ll import the pages and images into this site.', 'nicepage'); ?>
    </p>
    <p>
        <?php _e('Choose a (.zip) file from your computer, then click Upload file and import.', 'nicepage'); ?>
    </p>

    <p>
        <input type="file" name="file" id="nicepage-file-field" />
    </p>
    <p>
        <label for="nicepage-remove-prev">Remove previously imported content</label>
        <input type="checkbox" id="nicepage-remove-prev" style="margin-left: 5px;" name="remove" value="0">
    </p>
    <p>
        <input type="submit" name="np-upload" id="np-upload" class="button button-primary" value="<?php _e('Upload file and import', 'nicepage'); ?>" disabled>
    </p>
<?php
}
?>
    <p id="nicepage-upload-progress" style="color: green; font-size: 14px;"></p>
    <style>
        #nicepage-upload-progress.upload-progress:before {
            background-image: url(<?php echo APP_PLUGIN_URL; ?>importer/assets/images/preloader-01.gif);
            background-size: 15px 15px;
            display: inline-block;
            width: 15px;
            height: 15px;
            content:"";
            margin-right: 5px;
        }
    </style>
    <p id="nicepage-upload-error" class="disabled" style="color: red; font-size: 14px;"></p>
</div>