var Pwizard = (function($){

    var t;
    var current_step = '';
    var step_pointer = '';
    var callbacks = {
        do_next_step: function(btn) {
            do_next_step(btn);
        },
        import_content: function(btn){
            var content = new ContentManager(btn.text);
            content.init(btn);
        },
        replace_content: function(btn){
            var content = new ContentManager(btn.text);
            content.init(btn);
        }
    };

    function window_loaded() {
        var maxHeight = 0;
        $('.pwizard-menu li.step').each(function(index) {
            $(this).attr('data-height', $(this).innerHeight());
            if($(this).innerHeight() > maxHeight) {
                maxHeight = $(this).innerHeight();
            }
        });
        $('.pwizard-menu li .detail').each(function(index) {
            $(this).attr('data-height', $(this).innerHeight());
            $(this).addClass('scale-down');
        });
        $('.pwizard-menu li.step').css('height', maxHeight);
        $('.pwizard-menu li.step:first-child').addClass('active-step');
        $('.pwizard-nav li:first-child').addClass('active-step');
        $('.pwizard-wrap').addClass('loaded');
        // init button clicks:
        $('.p-do-it').on('click', function(e) {
            e.preventDefault();
            step_pointer = $(this).data('step');
            current_step = $('.step-' + $(this).data('step'));
            $('.pwizard-wrap').addClass('spinning');
            if($(this).data('callback') && typeof callbacks[$(this).data('callback')] != 'undefined'){
                // we have to process a callback before continue with form submission
                callbacks[$(this).data('callback')](this);
                return false;
            } else {
                return true;
            }
        });
    }

    function do_next_step(btn) {
        current_step.addClass('done-step');
        $('.nav-step-' + step_pointer).addClass('done-step');
        current_step.fadeOut(500, function() {
            current_step = current_step.next();
            step_pointer = current_step.data('step');
            current_step.fadeIn();
            current_step.addClass('active-step');
            $('.nav-step-' + step_pointer).addClass('active-step');
            $('.pwizard-wrap').removeClass('spinning');
        });
    }

    function ContentManager(btnText){

        function doAjax(action, url, _ajax_nonce) {
            return $.ajax({
                url: url,
                type: 'GET',
                data: ({
                    action: action,
                    _ajax_nonce: _ajax_nonce
                })
            });
        }

        var pAction;
        if (btnText === "Import Content") {
            pAction = pwizard_params.actionImportContent;
        } else {
            pAction = pwizard_params.actionReplaceContent
        }

        doAjax(pAction, pwizard_params.urlContent, pwizard_params.wpnonceContent).done(function (response) {
            complete();
        }).fail(function () {
            console.log('An error occurred while importing.');
        });

        return {
            init: function(btn){
                complete = function(){
                    do_next_step();
                };
            }
        }
    }

    return {
        init: function(){
			t = this;
			$(window_loaded);
        },
        callback: function(func){
            console.log(func);
            console.log(this);
        }
    }

})(jQuery);

Pwizard.init();