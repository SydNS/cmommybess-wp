//For wp 5.6.1 and more version
jQuery(document).ready(function($){

    // Check screen
    if(typeof window.wp.autosave === 'undefined')
        return;

    // Data Hack
    var initialCompareData = {
        post_title: $( '#title' ).val() || '',
        content: $( '#content' ).val() || '',
        excerpt: $( '#excerpt' ).val() || ''
    };

    var initialCompareString = window.wp.autosave.getCompareString(initialCompareData);

    // Fixed postChanged()
    window.wp.autosave.server.postChanged = function(){

        var changed = false;

        // If there are TinyMCE instances, loop through them.
        if ( window.tinymce ) {
            window.tinymce.each( [ 'content', 'excerpt' ], function( field ) {
                var editor = window.tinymce.get( field );

                if ( ( editor && editor.isDirty() ) || ($( '#' + field ).text() || '' ).replace(/\s/g, '') !== initialCompareData[ field ].replace(/\s/g, '') ) {
                    changed = true;
                    return false;
                }

            } );

            if ( ( $( '#title' ).val() || '' ) !== initialCompareData.post_title ) {
                changed = true;
            }

            return changed;
        }

        return window.wp.autosave.getCompareString() !== initialCompareString;

    }
});