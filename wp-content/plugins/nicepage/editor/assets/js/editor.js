/*exported appEditor */
/*global Utility, SessionTimeoutError, SecureLocalhostError, UploadImageError, CmsSaveServerError*/

var EditorUtility = {};

(function () {
    'use strict';

    EditorUtility.downloadByteArrayInline = function downloadByteArrayInline(url, withCredentials, numberOfTries, callback) {

        var xhr = new window.XMLHttpRequest();

        function onError() {
            if (numberOfTries >= 2) {
                callback(EditorUtility.createError(url, xhr));
            } else {
                setTimeout(function () {
                    downloadByteArrayInline(url, withCredentials, numberOfTries + 1, callback);
                }, 50);
            }
        }

        xhr.withCredentials = !!withCredentials;
        xhr.open("GET", url, true);
        xhr.responseType = "arraybuffer";
        xhr.onload = function () {
            if (xhr.readyState !== 4 || xhr.status !== 200) {
                onError(xhr);
            } else {
                var array = xhr.response ? new window.Uint8Array(xhr.response) : new window.Uint8Array("");
                callback(null, array);
            }
        };
        xhr.onerror = onError;
        xhr.send();
    };

    EditorUtility.createError = function (url, xhr) {
        if (['0', '-1', 'session_error'].indexOf(xhr.responseText) !== -1) {
            return new SessionTimeoutError('SessionTimeoutError');
        }
        if (xhr && xhr.status == 0 && parent.dataBridge.getInfo && parent.dataBridge.getInfo().siteIsSecureAndLocalhost) {
            return new SecureLocalhostError();
        }
        if (xhr && xhr.responseText && xhr.abort && xhr.abort.length === 1) {
            var error = null;
            try {
                error = JSON.parse(xhr.responseText);
            }
            catch(e){
            }
            if (error && error.type && (error.type === 'uploadImage' || error.type === 'uploadImageCmsError')) {
                return new UploadImageError(error.message, error.type);
            }
            if (error && error.type && error.type === 'cmsSaveServerError') {
                return new CmsSaveServerError(error.message, error.type);
            }
        }
        return Utility.createRequestError(url, xhr, xhr.status, 'Failed to send a request');
    };


    EditorUtility.showError = function showError(error) {
        if (!error) {
            return;
        }
        console.error(error);
    };


    EditorUtility.isBase64String = function isBase64String(str) {
        return typeof str === 'string' &&
            str.indexOf(';base64,') !== -1;
    };
})();

var DataUploader = (function () {
    'use strict';

    function convertToBase64(content) {
        return btoa(encodeURIComponent(content).replace(/%([0-9A-F]{2})/g, function (match, p1) {
            return String.fromCharCode(parseInt(p1, 16))
        }));
    }

    function saveAsChunk(url, data, callback) {
        var dataBase64 = convertToBase64(JSON.stringify(data));
        ChunksUploader.chunkedRequest({
            'save': {
                'post': $.extend(true, {}, window.nicepageSettings.ajaxData, {data: dataBase64}),
                'url': url
            },
            'clear': {
                'post': window.nicepageSettings.ajaxData,
                'url': window.nicepageSettings.actions.clearChunks
            }
        }, function (error, response) {
            if (!error) {
                sessionStorage.setItem('saveType', 'chunks');
            }
            callback(error, response);
        });
    }

    function saveAsBase64(url, data, callback) {
        var dataBase64 = convertToBase64(JSON.stringify(data));
        doRequest(url, {saveType: 'base64', data: dataBase64}, function (error, response) {
            if (error || (response && response.result === 'error')) {
                saveAsChunk(url, data, callback);
            } else {
                sessionStorage.setItem('saveType', 'base64');
                callback(error, response);
            }
        });
    }

    function saveAsRaw(url, data, callback) {
        doRequest(url, data, function (error, response) {
            if (error || (response && response.result === 'error')) {
                saveAsBase64(url, data, callback)
            } else {
                callback(error, response);
            }
        });
    }

    function saveData(actionUrl, data, callback) {
        var saveType = sessionStorage.getItem('saveType');
        if (saveType === 'chunks') {
            saveAsChunk(actionUrl, data, callback);
        } else if (saveType === 'base64') {
            saveAsBase64(actionUrl, data, callback);
        } else {
            saveAsRaw(actionUrl, data, callback)
        }
    }

    function savePage(postData, callback) {
        saveData(window.nicepageSettings.actions.savePage, postData, callback);
    }

    function saveLocalStorageKey(json, callback) {
        saveData(window.nicepageSettings.actions.saveLocalStorageKey, {json: json}, callback);
    }

    function saveSiteSettings(data, callback) {
        saveData(window.nicepageSettings.actions.saveSiteSettings, {settings: data}, callback);
    }

    function savePreferences(data, callback) {
        saveData(window.nicepageSettings.actions.savePreferences, {settings: data}, callback);
    }

    function saveMenuItems(data, callback) {
        doRequest(window.nicepageSettings.actions.saveMenuItems, {menuData: data}, callback);
    }

    function removeFont(data, callback) {
        doRequest(window.nicepageSettings.actions.removeFont, {fileName: data}, callback);
    }

    function uploadImage(imageData, callback) {

        var mimeType = imageData.mimeType;
        var fileName = imageData.fileName || 'image.png';

        if (imageData.data instanceof Uint8Array || imageData.data instanceof File || imageData.data instanceof Blob) {
            upload(imageData.data);
            return;
        }

        if (EditorUtility.isBase64String(imageData.data)) {
            var parts = imageData.data.split(';base64,');
            mimeType = parts[0].split(':')[1];
            var raw = window.atob(parts[1]);
            var rawLength = raw.length;

            var uInt8Array = new Uint8Array(rawLength);

            for (var i = 0; i < rawLength; i++) {
                uInt8Array[i] = raw.charCodeAt(i);
            }
            upload(uInt8Array);
            return;
        }

        EditorUtility.downloadByteArrayInline(imageData.data, false, 1, function (error, array) {
            if (error) {
                callback(error);
                return;
            }
            upload(array);
        });

        /**
         * @param {Uint8Array|Blob|File} fileData
         */
        function upload(fileData) {
            var uploader = new ImageUploader(fileData, $.extend(true, {
                url: window.nicepageSettings.actions.uploadImage,
                type: mimeType,
                fileName: fileName,
                params: imageData.params
            }, window.nicepageSettings.uploadFileOptions), callback);
            uploader.upload();
        }
    }

    function uploadFile(fileData, callback) {
        var uploader = new FileUploader(fileData, callback);
        uploader.upload();
    }

    function getSite(callback) {
        doRequest(window.nicepageSettings.actions.getSite, {}, callback);
    }

    function getSitePosts(options, callback) {
        doRequest(window.nicepageSettings.actions.getSitePosts, {options: options}, callback);
    }

    function getPosts(source, callback) {
        doRequest(window.nicepageSettings.actions.getPosts, {category: source}, callback);
    }

    function getProducts(source, callback) {
        doRequest(window.nicepageSettings.actions.getProducts, {category: source}, callback);
    }

    function doRequest(url, data, onError, onSuccess) {
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: $.extend(true, {}, window.nicepageSettings.ajaxData, data),
            timeout: 60000 // sets timeout to 1 minute
        }).done(function requestSuccess(response, status, xhr) {
            if (response.result === 'done') {
                if (typeof onSuccess === 'undefined') {
                    onError(null, response);
                } else {
                    onSuccess(response);
                }
                return;
            }
            onError(EditorUtility.createError(url, xhr));
        }).fail(function requestFail(xhr) {
            onError(EditorUtility.createError(url, xhr));
        });
    }

    function loggedInWrap(func) {
        var wrapped = function (data, callback) {
            func(data, function (error, response) {
                if (error instanceof SessionTimeoutError && typeof parent.dataBridge.doLoggedIn === 'function') {
                    parent.dataBridge.doLoggedIn(function (success) {
                        if (success) {
                            wrapped(data, callback);
                        } else {
                            callback(error, response);
                        }
                    });
                } else {
                    callback(error, response);
                }
            })
        };
        return wrapped;
    }

    return {
        savePage: loggedInWrap(savePage),
        saveLocalStorageKey: loggedInWrap(saveLocalStorageKey),
        saveSiteSettings: loggedInWrap(saveSiteSettings),
        savePreferences: loggedInWrap(savePreferences),
        saveMenuItems: loggedInWrap(saveMenuItems),
        removeFont: loggedInWrap(removeFont),
        uploadImage: loggedInWrap(uploadImage),
        uploadFile: loggedInWrap(uploadFile),
        getSite: loggedInWrap(getSite),
        getSitePosts: loggedInWrap(getSitePosts),
        getPosts: loggedInWrap(getPosts),
        getProducts: loggedInWrap(getProducts),
    };
})();

/**
 *
 * @param {Uint8Array|Blob|File} fileData
 * @param {object} options
 * @param {string} options.type MIME type
 * @param {string} options.formFileName
 * @param {string} options.fileName
 * @param callback
 * @constructor
 */
function ImageUploader(fileData, options, callback) {
    'use strict';

    var type = options.type || '';
    var file = new Blob([fileData], {type: type});

    this.upload = function upload() {

        setTimeout(function () {
            var formData = new FormData();
            formData.append(options.formFileName, file, options.fileName);

            var params = options.params;
            if (typeof params === 'object') {
                for (var i in params) {
                    if (params.hasOwnProperty(i)) {
                        formData.append(i, params[i]);
                    }
                }
            }

            return $.ajax({
                url: options.url,
                data: formData,
                type: 'POST',
                mimeType: 'application/octet-stream',
                processData: false,
                contentType: false,
                headers: {}
            }).done(function requestSuccess(response, status, xhr) {
                var result;
                try {
                    result = JSON.parse(response);
                } catch (e) {
                    callback(EditorUtility.createError(options.url, xhr));
                    return;
                }
                if (result.status === 'error') {
                    callback(EditorUtility.createError(options.url, xhr));
                } else {
                    callback(null, result);
                }
            }).fail(function requestFail(xhr) {
                callback(new UploadImageError(EditorUtility.createError(options.url, xhr).message, 'uploadImageCmsError'));
            });
        }, 0);
    };
}

function FileUploader(fileData, callback) {
    var _file = fileData.data;
    if (_file instanceof Uint8Array) {
        _file = new Blob([_file]);
    }
    var maxChunkLength = 1024 * 1024; // 1 Mb
    var CHUNK_SIZE = parseInt(parent.dataBridge.cmsSettings.maxRequestSize || maxChunkLength, 10);
    var uploadedChunkNumber = 0, allChunks;
    var fileName = (_file.name || fileData.fileName || window.createGuid()).replace(/[^A-Za-z0-9\._]/g, '');
    var fileSize = _file.size || _file.length;
    var total = Math.ceil(fileSize / CHUNK_SIZE);

    var rangeStart = 0;
    var rangeEnd = CHUNK_SIZE;
    validateRange();

    var sliceMethod;

    if ('mozSlice' in _file) {
        sliceMethod = 'mozSlice';
    }
    else if ('webkitSlice' in _file) {
        sliceMethod = 'webkitSlice';
    }
    else {
        sliceMethod = 'slice';
    }

    this.upload = upload;

    function upload() {
        var data;

        setTimeout(function () {
            var requests = [];

            for (var chunk = 0; chunk < total - 1; chunk++) {
                data = _file[sliceMethod](rangeStart, rangeEnd);
                requests.push(createChunk(data));
                incrementRange();
            }

            allChunks = requests.length;

            $.when.apply($, requests).then(
                function success() {
                    var lastChunkData = _file[sliceMethod](rangeStart, rangeEnd);

                    createChunk(lastChunkData, {last: true})
                        .done(onUploadCompleted)
                        .fail(onUploadFailed);
                },
                onUploadFailed
            );
        }, 0);
    }

    function createChunk(data, params) {
        var formData = new FormData();
        formData.append('filename', fileName);
        formData.append('chunk', new Blob([data], {type: 'application/octet-stream'}), 'blob');

        var paramsData = $.extend(true, {uploadId: Math.random()}, params, window.nicepageSettings.uploadFileOptions && window.nicepageSettings.uploadFileOptions.params);

        if (typeof paramsData === 'object') {
            for (var i in paramsData) {
                if (paramsData.hasOwnProperty(i)) {
                    formData.append(i, paramsData[i]);
                }
            }
        }

        return $.ajax({
            url: window.nicepageSettings.actions.uploadFile,
            data: formData,
            type: 'POST',
            mimeType: 'application/octet-stream',
            processData: false,
            contentType: false,
            headers: (rangeEnd <= fileSize) ? {
                'Content-Range': ('bytes ' + rangeStart + '-' + rangeEnd + '/' + fileSize)
            } : {},
            success: onChunkCompleted,
            error: function (xhr, status) {
                alert('Failed  chunk saving');
            }
        });
    }

    function validateRange() {
        if (rangeEnd > fileSize) {
            rangeEnd = fileSize;
        }
    }

    function incrementRange() {
        rangeStart = rangeEnd;
        rangeEnd = rangeStart + CHUNK_SIZE;
        validateRange();
    }

    function onUploadCompleted(response, status, xhr) {
        var result;
        try {
            result = JSON.parse(response);
        } catch (e) {
            callback(EditorUtility.createError(window.nicepageSettings.actions.uploadFile, xhr));
            return;
        }
        callback(null, result);
    }

    function onUploadFailed(xhr, status) {
        alert('onUploadFailed');
    }

    function onChunkCompleted() {
        if (uploadedChunkNumber >= allChunks)
            return;
        ++uploadedChunkNumber;
    }
}

var appEditor = (function () {
    'use strict';

    var appEditor = {};
    window.nicepageSettings = parent.dataBridge.settings;
    window.cmsSettings = parent.dataBridge.cmsSettings;

    /**
     *
     * @param {object} saveData
     * @param callback
     */
    appEditor.save = function save(saveData, callback) {
        if (saveData.id < 0 && window.nicepageSettings.pageId) {
            saveData.id = window.nicepageSettings.pageId;
        }
        if (saveData.id < 0 && window.nicepageSettings.startPageTitle) {
            saveData.title = window.nicepageSettings.startPageTitle;
        }

        DataUploader.savePage({
            id: saveData.id,
            data: {
                html: saveData.html,
                publishHtml: saveData.publishHtml,
                backlink: saveData.backlink,
                head: saveData.head,
                fonts: saveData.fonts,
                publishNicePageCss: saveData.publishNicePageCss,
                bodyClass: saveData.bodyClass,
                bodyStyle: saveData.bodyStyle,
                introImgStruct: saveData.introImgStruct,
                hideHeader: saveData.hideHeader,
                hideFooter: saveData.hideFooter,
                hideBackToTop: saveData.hideBackToTop
            },
            header: saveData.header,
            publishHeader: saveData.publishHeader,
            headerCss: saveData.headerCss,
            footer: saveData.footer,
            publishFooter: saveData.publishFooter,
            footerCss: saveData.footerCss,
            dialogs: saveData.dialogs,
            headerDialogs: saveData.headerDialogs,
            footerDialogs: saveData.footerDialogs,
            pageFormsData: saveData.pageFormsData,
            headerFormsData: saveData.headerFormsData,
            footerFormsData: saveData.footerFormsData,
            settings: saveData.settings,
            title: saveData.title,
            introHtml: saveData.introHtml,
            metaGeneratorContent: saveData.metaGeneratorContent,
            keywords: saveData.keywords,
            description: saveData.description,
            canonical: saveData.canonical,
            pageType: saveData.pageType,
            metaTags: saveData.metaTags,
            customHeadHtml: saveData.customHeadHtml,
            titleInBrowser: saveData.titleInBrowser,
            isPreview: saveData.isPreview,
            saveAndPublish: saveData.saveAndPublish,
            fontsData: saveData.fontsData,
            publishDialogs: saveData.publishDialogs,
            customFontsCss: saveData.customFontsCss,
        }, function (error, response) {
            if (error) {
                callback(error);
                return;
            }
            if (!response.data) {
                callback(new Error('appEditor ~ DataUploader.savePage ~ response.data is undefined'));
                return;
            }
            window.nicepageSettings.pageId = response.data.id;
            callback(null, response);
        });
    };

    /**
     *
     * @param {File|Blob} file
     * @param {object}    options
     * @param {string}    options.pageId
     * @param {function}  callback
     */
    appEditor.saveImage = function saveImage(file, options, callback) {
        DataUploader.uploadImage({
            mimeType: file.type,
            fileName: options.fileName || file.name,
            data: file,
            params: {pageId: options.pageId}
        }, callback);
    };

    appEditor.saveMediaFile = function saveMediaFilee(file, options, callback) {
        DataUploader.uploadFile({
            mimeType: file.type,
            fileName: options.fileName || file.name,
            data: file
        }, callback);
    }

    appEditor.getCloseUrl = function getCloseUrl(isNewPage) {
        var settings = window.nicepageSettings;
        if (isNewPage || !(settings.pageId && settings.startPageId)) {
            return settings.dashboardUrl;
        } else {
            return settings.editPostUrl.replace('{id}', settings.pageId || settings.startPageId);
        }
    };

    appEditor.getLoginUrl = function getLoginUrl() {
        return window.nicepageSettings.loginUrl.replace(encodeURIComponent('{id}'), window.nicepageSettings.pageId || window.nicepageSettings.startPageId);
    };

    return appEditor;
})();

var ChunksUploader = (function () {
    'use strict';

    var maxChunkLength = 4096 * 1000;
    var minChunkLength = 32 * 1000;
    var currentChunkLength = parseInt(parent.dataBridge.cmsSettings.maxRequestSize || maxChunkLength, 10);

    var ChunksUploader = {};

    function createChunks(request, chunkSize) {
        var chunks = [];
        var content = request.save.post.data;

        for (var offset = 0, strLen = content.length; offset < strLen; offset += chunkSize) {
            chunks.push(content.substring(offset, offset + chunkSize));
        }
        return chunks;
    }

    function saveChunk(request, chunk) {
        if (request.multipartForm) {
            return saveChunkMultipart(request, chunk);
        }
    }

    function saveChunkMultipart(request, chunk) {
        var data = jQuery.extend(true, {}, request.save.post);

        var saveUrl = request.save.url + '&saveType=chunks';
        var formData = new FormData();
        var ajax = {
            type: 'post',
            url: saveUrl,
            dataType: 'json',
            processData: false,
            contentType: false
        };

        formData.append('content', new Blob([chunk.content], {type: 'application/octet-stream'}), 'blob');

        for (var i in chunk) {
            if (chunk.hasOwnProperty(i) && i !== 'content') {
                formData.append(i, chunk[i]);
            }
        }

        for (i in data) {
            if (data.hasOwnProperty(i) && i !== 'data') {
                formData.append(i, data[i]);
            }
        }

        ajax.mimeType = 'application/octet-stream';
        formData.append('blob', true);
        formData.append('saveType', 'chunks');

        ajax.data = formData;

        ajax.success = function onChunkSuccess() {};
        ajax.error = function onChunkError(xhr, status, error) {};

        return $.ajax(ajax);
    }

    ChunksUploader.chunkedRequest = function (request, callback) {
        request.multipartForm = true;
        ChunksUploader.saveChunks(request, function (error, data) {
            callback(error, data);
        });
    };

    ChunksUploader.saveChunks = function (request, callback) {

        var saveChunksCallback = function (error, response) {
            callback(error, response);
        };

        var cookieName = 'requestChunkSize';
        var cookieValue = getCookie(cookieName);
        var id = Math.random().toString(36).substring(2);

        if (cookieValue) {
            currentChunkLength = Math.min(currentChunkLength, parseInt(cookieValue, 10));
        }

        var chunkLength = currentChunkLength;

        if (chunkLength < minChunkLength) {
            chunkLength = minChunkLength;
        }

        var chunks = createChunks(request, chunkLength);

        $.when(saveChunk(request, {
            id: id,
            content: chunks[0],
            current: 1,
            total: chunks.length
        }))
            .done(function firstChunkRequestSuccess(response, status, jqXhr) {
                setCookie(cookieName, chunkLength, {expires: 3600 * 24 * 365});
                if (response.result === 'done') {
                    saveChunksCallback(null, response);
                } else if (response.result === 'processed') {
                    var requests = [];
                    for (var i = 2; i <= chunks.length; i++) {
                        requests.push(saveChunk(request, {
                            id: id,
                            content: chunks[i - 1],
                            current: i,
                            total: chunks.length
                        }));
                    }
                    $.when.apply($, requests).then(function allChunksSuccess() {
                        var result;
                        if (requests.length > 1) {
                            [].slice.call(arguments).forEach(function (args) {
                                var data = getData(args);
                                if (data.result === 'done') {
                                    result = data;
                                }
                            });
                        } else {
                            result = getData(arguments);
                        }
                        saveChunksCallback(null, result); // all chunks success
                    }, function chunkFail(chunkXhr) {
                        $.each(requests, function () {
                            this.abort();
                        });
                        $.ajax({
                            type: 'post',
                            url: request.clear.url,
                            dataType: 'json',
                            data: jQuery.extend(true, {id: id}, request.clear.post)
                        }).done(function clearSuccess() {
                            saveChunksCallback(EditorUtility.createError(request.save.url, chunkXhr));
                        }).fail(function clearFail(clearXhr) {
                            saveChunksCallback(EditorUtility.createError(request.clear.url, clearXhr));
                        });
                    });
                } else {
                    saveChunksCallback(EditorUtility.createError(request.save.url, jqXhr));
                }
            })
            .fail(function firstChunkRequestFail(chunkXhr) {
                deleteCookie(cookieName);
                var newLength = parseInt(Math.min(chunkLength, chunks[0].length * 4 / 3) / 2, 10);
                if (newLength > minChunkLength) {
                    currentChunkLength = newLength;
                    ChunksUploader.saveChunks(request, saveChunksCallback);
                } else {
                    saveChunksCallback(EditorUtility.createError(request.save.url, chunkXhr));
                }
            });
    };

    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    function setCookie(name, value, props) {
        props = props || {};
        var exp = props.expires;
        if (typeof exp === "number" && exp) {
            var d = new Date();
            d.setTime(d.getTime() + exp * 1000);
            exp = props.expires = d;
        }
        if (exp && exp.toUTCString) {
            props.expires = exp.toUTCString();
        }

        value = encodeURIComponent(value);
        var updatedCookie = name + "=" + value;
        for (var propName in props) {
            if (props.hasOwnProperty(propName)) {
                updatedCookie += "; " + propName;
                var propValue = props[propName];
                if (propValue !== true) {
                    updatedCookie += "=" + propValue;
                }
            }
        }
        document.cookie = updatedCookie;
    }

    function deleteCookie(name) {
        setCookie(name, null, {expires: -1});
    }

    function getData(obj) {
        return obj && obj.length && obj[0] || {};
    }

    function getError(data) {
        return data.result === 'error' ? [(data.message || '').trim()] : [];
    }

    return ChunksUploader;
})();