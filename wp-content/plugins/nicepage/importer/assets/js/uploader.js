/*global importerSettingsNp */

jQuery(function() {
    'use strict';

    var fileInputNp = jQuery('#nicepage-file-field');
    var submitButtonNp = jQuery("[name=np-upload]");
    var installFromThemeButtonNp = jQuery("[name=nicepage-install-from-theme]");
    var removePrevCheckboxNp = jQuery("#nicepage-remove-prev");
    var progressBarNp = jQuery('#nicepage-upload-progress');
    var errorBarNp = jQuery('#nicepage-upload-error');
    var uploadFileNp = null;

    function updateProgressNp(value) {
        if (value === 100) {
            progressBarNp.html('Import completed').removeClass('upload-progress');
        } else {
            progressBarNp.html(value + '%').addClass('upload-progress');
        }
    }

    fileInputNp.bind({
        change: function() {
            if (this.files[0]) {
                uploadFileNp = this.files[0];
                submitButtonNp.removeAttr('disabled');
            } else {
                submitButtonNp.attr('disabled', '');
                uploadFileNp = null;
            }
            progressBarNp.html('').removeClass('upload-progress');
            errorBarNp.html('');
        }
    });

    function submit(file, options) {

        submitButtonNp.attr('disabled', '');
        installFromThemeButtonNp.attr('disabled', '');
        updateProgressNp(0);
        errorBarNp.html('');

        var onProgressNp = function(percents) {
            updateProgressNp(percents);
        };
        var onCompleteNp = function() {
            updateProgressNp(100);
            setTimeout(function() {
                fileInputNp.val('');
                submitButtonNp.attr('disabled', '');
                installFromThemeButtonNp.removeAttr('disabled');
                uploadFileNp = null;
            }, 200);
            jQuery('body').trigger('upload-complete');
        };
        var onErrorNp = function(xhr) {
            installFromThemeButtonNp.removeAttr('disabled');
            errorBarNp.html('Error occured: (status ' + xhr.status + ')<br>' + xhr.responseText);
            console.error(JSON.stringify(xhr, null, '\t'));
            jQuery('body').trigger('upload-error');
        };

        var uploaderNp = new ChunkedUploaderNp(
            file,
            {
                progress: onProgressNp,
                complete: onCompleteNp,
                error: onErrorNp
            },
            jQuery.extend(true, options || {}, {
                url: importerSettingsNp.uid,
                _wpnonce: importerSettingsNp.ajax_nonce,
                uploadId: Math.random(),
                removePrev: removePrevCheckboxNp.is(':checked') ? '1' : ''
            })
        );
        uploaderNp.uploadNp();
    }

    installFromThemeButtonNp.click(function() {
        submit(new Uint8Array(0), {
            fromTheme: true
        });
    });

    submitButtonNp.click(function() {

        if (!uploadFileNp) {
            return;
        }
        submit(uploadFileNp);
    });
});

function ChunkedUploaderNp(file, params, formParams) {
    'use strict';

    var upload_file = file;
    if (upload_file instanceof Uint8Array) {
        upload_file = new Blob([upload_file]);
    }
    var CHUNK_SIZE_NP = parseInt((importerSettingsNp.chunkSize || (1024 * 1024)) * 0.9);
    var uploadedChunkNumberNp = 0;
    var allChunks;
    var fileName = (upload_file.name || 'content').replace(/[^A-Za-z0-9\._]/g, '');
    var fileSizeNp = upload_file.size || upload_file.length;
    var totalNp = Math.ceil(fileSizeNp / CHUNK_SIZE_NP);

    var rangeStartNp = 0;
    var rangeEndNp = CHUNK_SIZE_NP;
    validateRangeNp();

    var sliceMethodNp;

    if ('mozSlice' in upload_file) {
        sliceMethodNp = 'mozSlice';
    } else if ('webkitSlice' in upload_file) {
        sliceMethodNp = 'webkitSlice';
    } else {
        sliceMethodNp = 'slice';
    }

    this.uploadNp = uploadNp;
    var requestsNp;

    function uploadNp() {
        var data;

        setTimeout(function () {
            requestsNp = [];

            for (var chunk = 0; chunk < totalNp - 1; chunk++) {
                data = upload_file[sliceMethodNp](rangeStartNp, rangeEndNp);
                requestsNp.push(createChunkNp(data, formParams));
                incrementRangeNp();
            }

            allChunks = requestsNp.length + 1;

            jQuery.when.apply(jQuery, requestsNp).then(
                function success() {
                    var lastChunkData = upload_file[sliceMethodNp](rangeStartNp, rangeEndNp);
                    createChunkNp(lastChunkData, jQuery.extend(true, {last: true}, formParams));
                },
                onUploadFailed
            );
        }, 0);
    }

    function createChunkNp(data, params) {
        var formData = new FormData();
        formData.append('filename', fileName);
        formData.append('chunk', new Blob([data], { type: 'application/octet-stream' }), 'blob');

        if (typeof params === 'object') {
            for (var i in params) {
                if (params.hasOwnProperty(i)) {
                    formData.append(i, params[i]);
                }
            }
        }

        var url = importerSettingsNp.actions.uploadZip;

        return jQuery.ajax({
            url: url,
            data: formData,
            type: 'POST',
            mimeType: 'application/octet-stream',
            processData: false,
            contentType: false,
            headers: (rangeEndNp <= fileSizeNp) ? {
                'Content-Range': ('bytes ' + rangeStartNp + '-' + rangeEndNp + '/' + fileSizeNp)
            } : {},
            success: onChunkCompleted,
            error: onUploadFailed
        });
    }

    function validateRangeNp() {
        if (rangeEndNp > fileSizeNp) {
            rangeEndNp = fileSizeNp;
        }
    }

    function incrementRangeNp() {
        rangeStartNp = rangeEndNp;
        rangeEndNp = rangeStartNp + CHUNK_SIZE_NP;
        validateRangeNp();
    }

    function onUploadFailed(xhr) {
        if (xhr.statusText === 'abort') {
            return;
        }

        if (requestsNp) {
            jQuery.each(requestsNp, function () {
                this.abort();
            });
        }
        params.error(xhr);
    }

    function onChunkCompleted(responseText, status, xhr) {
        var response;
        try {
            response = JSON.parse(responseText);
            if (response.status === 'error') {
                onUploadFailed(xhr);
                return false;
            }
        } catch(e) {
            onUploadFailed(xhr);
            return false;
        }

        ++uploadedChunkNumberNp;
        if (uploadedChunkNumberNp === allChunks) {
            params.complete();
        } else {
            params.progress(Math.round((100 * uploadedChunkNumberNp) / allChunks));
        }
    }
}