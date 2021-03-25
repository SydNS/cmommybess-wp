(function($) {

	var clicked_button = '';

	/**
	 * Scripts relating to adding/removing the "loading" class which some themes use
	 */

	$( 'body' ).on( 'click', '.somdn-download-button:not(.somdn-checkbox-submit), .somdn-download-archive', function (e) {
	//LEGACY// $( '.somdn-download-button:not(.somdn-checkbox-submit), .somdn-download-archive' ).click( function(e) {
		// Assign the clicked link/button to the clicked_button variable for later
		clicked_button = $( this );
		var form = $( this ).closest( 'form' );
		if ( ! capture_email_active_page( form ) ) {
			//console.log('addClass');
			$( this ).addClass( 'loading' );
		}
	});

	$( 'body' ).on( 'click', '.somdn-download-button, .somdn-download-archive', function (e) {
		var element = $( this );
		setTimeout(
			function() {
				if ( element.hasClass( 'loading' ) ) {
					element.removeClass( 'loading' );
					//console.log('element.removeClass');
				}
			},
		2000);
	});

/*
	$( '.somdn-download-button, .somdn-download-archive' ).each( function() {
		$( this ).click(
		function(e) {
			var element = $( this );
			setTimeout(
				function() {
					if ( element.hasClass( 'loading' ) ) {
						element.removeClass( 'loading' );
						console.log('element.removeClass');
					}
				},
				2000);
		});
	});
*/

	$( 'body' ).on( 'somdn_email_capture_submit_clicked_success', function( event, click_event, button, form ) {

		// If a button or archive page link was clicked add the "loading" class and remove it after 2 seconds.
		if ( clicked_button.length ) {
			clicked_button.addClass('loading');
			//console.log('clicked_button addClass');
			setTimeout(
				function() {
					if ( clicked_button.hasClass( 'loading' ) ) {
						//console.log('clicked_button removeClass');
						clicked_button.removeClass( 'loading' );
					}
				},
				2000);
		}

	});

	/**********************************************************************/

	/**
	 * Scripts relating to download buttons that are actually anchor elements
	 */

	$( 'body' ).on( 'click', 'a.somdn-download-archive', function (e) {
	//LEGACY// $( 'a.somdn-download-archive' ).click( function(e) {
		//console.log('a.somdn-download-archive Click');
		e.stopImmediatePropagation();
		var form = $( this ).closest( 'form.somdn-archive-download-form' );
		if ( capture_email_active_page( form ) ) {
			//console.log( 'Capture email active' );
			somdn_open_email_capture( form, e );
		} else {
			//console.log( 'No capture email' );
			form.submit();
		}
		return false;
	});

	$( 'body' ).on( 'click', '.somdn-download-link', function (e) {
	//LEGACY// $( '.somdn-download-link' ).click( function(e) {
		//console.log('.somdn-download-link Click');
		e.stopImmediatePropagation();
		var form = $( this ).closest( 'form' );
		if ( capture_email_active_page( form ) ) {
			//console.log( 'Capture email active' );
			somdn_open_email_capture( form, e );
		} else {
			//console.log( 'No capture email' );
			form.submit();
		}
		return false;
	});

	/**********************************************************************/

	/**
	 * Scripts relating to the multiple file checkbox form
	 */

	$( 'body' ).on( 'click', '.somdn-checkbox-form .somdn-checkbox-submit', function (e) {
	//LEGACY// $( '.somdn-checkbox-form .somdn-checkbox-submit' ).click( function(e) {
		var form = $( this ).closest( 'form' );
		if ( $( form ).find( 'input[type="checkbox"]:checked' ).length == 0 ) {
			e.preventDefault();
			$( '.somdn-form-validate' ).css( 'display', 'block' );
		} else {
			$( '.somdn-form-validate' ).css( 'display', 'none' );
			$( this ).addClass( 'loading' );
			var form = $( this ).closest( 'form' );
			if ( capture_email_active_page( form ) ) {
				e.preventDefault();
				capture_email_checkbox_form(e, this);
			}
		}
	});

	$( 'body' ).on( 'click', '.somdn-select-all-wrap input[type="checkbox"]', function (e) {
	//LEGACY// $( '.somdn-select-all-wrap input[type="checkbox"]' ).click( function(e) {
		var c = this.checked;
		var form = $( this ).closest( 'form' );
		$( form ).find( '.somdn-checkboxes-wrap input[type="checkbox"]' ).prop( 'checked', c );
	});

	$( 'body' ).on( 'click', '.somdn-checkbox-form .somdn-checkbox-form-checkbox', function (e) {
	//LEGACY// $( '.somdn-checkbox-form .somdn-checkbox-form-checkbox' ).click( function(e) {
		var form = $( this ).closest( 'form' );
		var checkboxes = $( form ).find( '.somdn-checkbox-form-checkbox' );
		var checked_count = $( form ).find( '.somdn-checkbox-form-checkbox:checked' ).length;// How many checkboxes currently checked
		var count = checkboxes.length;// How many checkboxes in total
		if ( checked_count < count ) {
			$( form ).find( '.somdn-checkbox-all' ).prop( 'checked', false );
		} else {
			$( form ).find( '.somdn-checkbox-all' ).prop( 'checked', true );
		}
	});

	/**********************************************************************/

	/**
	 * Quick View scripts
	 */

	$( 'body' ).on( 'click', '.somdn-qview-link', function (e) {
	//LEGACY// $( '.somdn-qview-link' ).click( function(e) {
		//console.log('Quick View');
		var prod_wrap = $( this ).closest( 'li.product' );
		if ( qview_active() ) {
			e.preventDefault();
			somdn_open_qview( prod_wrap, event );
		} else {
			// Do nothing
		}
	});

	/**
	 * Check if Quick View is active globally
	 *
	 * @return bool
	 */
	function qview_active() {
		var qview_enabled = somdn_script_params.somdn_qview_active;
		if ( qview_enabled ) {
			return true;
		} else {
			return false;
		}	
	}

	/**
	 * Check if Quick View is active on the current page
	 *
	 * @return bool
	 */
	function qview_active_page( form ) {
		var qview_enabled = somdn_script_params.somdn_qview_active;
		if ( qview_enabled ) {
			return true;
		} else {
			return false;
		}	
	}

	/**
	 * Open the Quick View modal
	 */
	function somdn_open_qview( prod_wrap, event ) {

		//$( prod_wrap ).addClass( 'somdn-form-email-capture' );

		var the_qview_wrap = $( prod_wrap ).find( '.somdn-qview-wrap' );

		$( 'body' ).addClass( 'somdn-qview-open' );
		$( the_qview_wrap ).addClass( 'open' );

		$( 'body' ).trigger( 'somdn_open_qview', [ prod_wrap, event ] );

	}

	/**
	 * Close the Quick View modal
	 */
	function somdn_close_qview( prod_wrap, event ) {

		//$( prod_wrap ).removeClass( 'somdn-form-email-capture' );
		$( prod_wrap ).find( '.somdn-checkbox-form-checkbox, .somdn-checkbox-all' ).prop( 'checked', false );
		$( prod_wrap ).find( '.somdn-qview-wrap' ).removeClass( 'open' );
		$( 'body' ).removeClass( 'somdn-qview-open' );

		$( 'body' ).trigger( 'somdn_close_qview', [ prod_wrap, event ] );

	}

	/**
	 * Two events that close the Quick View modal
	 * 1. Clicking the X in the top right corner of the modal
	 * 2. Clicking anywhere outside the modal when it is open
	 */
	$( 'body' ).on( 'click', '.somdn-qview-wrap .somdn-qview-close', function (event) {
	//LEGACY// $( '.somdn-qview-wrap .somdn-qview-close' ).click( function( event ) {
		var prod_wrap =  $( this ).closest( 'li.product' );
		somdn_close_qview( prod_wrap, event );
	});

	$( document ).on( 'click', '.somdn-qview-wrap', function( event ) {
		var prod_wrap = $( this ).closest( 'li.product' );
		somdn_close_qview( prod_wrap, event );
	}).on( 'click' , '.somdn-qview-window', function(e) {
			e.stopPropagation();
	});

	/**********************************************************************/

	/**
	 * Scripts relating to the download forms where email capture is being used
	 */

	function capture_email_checkbox_form(e, this_var) {
		//console.log('capture email checkbox form');
		e.stopImmediatePropagation();
		var form = $( this_var ).closest( 'form' );
		if ( capture_email_active_page( form ) ) {
			//console.log( 'Capture email active' );
			somdn_open_email_capture( form, e );
		} else {
			//console.log( 'No capture email' );
			form.submit();
		}
		return false;
	}

	$( 'body' ).on( 'click', '.somdn-download-button:not(.somdn-checkbox-submit)', function (e) {
	//LEGACY// $( '.somdn-download-button:not(.somdn-checkbox-submit)' ).click( function(e) {
		e.stopImmediatePropagation();
		var form = $( this ).closest( 'form' );
		if ( capture_email_active_page( form ) ) {
			//console.log( 'Capture email active' );
			somdn_open_email_capture( form, e );
		} else {
			//console.log( 'No capture email' );
			form.submit();
		}
		return false;
	});

	/**
	 * Email Capture scripts
	 */

	/**
	 * Check if capture email is active globally
	 *
	 * @return bool
	 */
	function capture_email_active() {
		var capture_enabled = somdn_script_params.somdn_capture_emails_active;
		if ( capture_enabled ) {
			return true;
		} else {
			return false;
		}	
	}

	/**
	 * Check if capture email is active on the current page
	 *
	 * @return bool
	 */
	function capture_email_active_page( form ) {
		var the_email_wrap = $( form ).find( '.somdn-capture-email-wrap' );
		var the_email = $( the_email_wrap ).find( '.somdn-download-user-email' );
		var capture_enabled = somdn_script_params.somdn_capture_emails_active;
		if ( the_email.length && capture_enabled ) {
			return true;
		} else {
			return false;
		}	
	}
/*
	$( '.somdn-download-form:not(.somdn-checkbox-form)' ).on( 'submit', function( event ) {
		somdn_capture_email( $( this ), event );
	});
*/

	/**
	 * Open the email capture modal
	 */
	function somdn_open_email_capture( the_form, event ) {

		$( the_form ).addClass( 'somdn-form-email-capture' );

		var the_email_wrap = $( the_form ).find( '.somdn-capture-email-wrap' );
		var the_email = $( the_email_wrap ).find( '.somdn-download-user-email' );
		//var the_email_name = $( the_email_wrap ).find( '.somdn-download-user-name' );

		$( the_form ).find( '.somdn-capture-required' ).prop( 'required', true );
		$( the_form ).find( '.somdn-capture-checkbox-wrap input[type="checkbox"].somdn-checkbox-auto-blank' ).prop( 'checked', false );

		$( 'body' ).addClass( 'somdn-capture-email-open' );
		$( the_email_wrap ).addClass( 'open' );

		//$( the_form ).find( '.somdn-download-user-name' ).val( '' );

		$( 'body' ).trigger( 'somdn_open_email_capture', [ the_form, event ] );

	}

	/**
	 * Close the email capture modal
	 */
	function somdn_close_email_capture( the_form, event ) {

		$( the_form ).removeClass( 'somdn-form-email-capture' );
		$( the_form ).find( '.somdn-capture-email-wrap' ).removeClass( 'open' );
		$( 'body' ).removeClass( 'somdn-capture-email-open' );
		$( the_form ).find( '.somdn-capture-required' ).prop( 'required', false );

		$( 'body' ).trigger( 'somdn_close_email_capture', [ the_form, event ] );

	}

	/**
	 * Two events that close the email capture modal
	 * 1. Clicking the X in the top right corner of the modal
	 * 2. Clicking anywhere outside the modal when it is open
	 */
	$( 'body' ).on( 'click', '.somdn-capture-email-header .dashicons-no', function (event) {
	//LEGACY// $( '.somdn-capture-email-header .dashicons-no' ).click( function( event ) {
		var form = $( this ).closest( 'form' );
		somdn_close_email_capture( form, event );
	});

	$( document ).on( 'click', '.somdn-capture-email-wrap', function( event ) {
		var form = $( this ).closest( 'form' );
		somdn_close_email_capture( form, event );
	}).on( 'click' , '.somdn-capture-email-wrap-form', function(e) {
			e.stopPropagation();
	});

	/**
	 * Click event when the user clicks the email capture submit button
	 */
	$( 'body' ).on( 'click', '.somdn-capture-email-button', function (e) {
	//LEGACY// $( '.somdn-capture-email-button' ).click( function(e) {

		var button = this;
		var form = $( this ).closest( 'form' );

		$( 'body' ).trigger( 'somdn_email_capture_submit_clicked', [ e, button, form ] );

		var email_name = $( form ).find( 'input[name="somdn_download_user_name"]' );
		if ( email_name.length ) {
			if ( ! email_name.val() && $( email_name ).hasClass( 'somdn-capture-required' ) ) {
				alert( somdn_script_params.somdn_capture_fname_empty );
				e.preventDefault();
				return;
			}
			$( 'body input[name="somdn_download_user_name"]' ).each(function() {
				$( this ).val( email_name.val() );
			});
		}

		var email_lname = $( form ).find( 'input[name="somdn_download_user_lname"]' );
		if ( email_lname.length ) {
			if ( ! email_lname.val() && $( email_lname ).hasClass( 'somdn-capture-required' ) ) {
				alert( somdn_script_params.somdn_capture_lname_empty );
				e.preventDefault();
				return;
			}
			$( 'body input[name="somdn_download_user_lname"]' ).each(function() {
				$( this ).val( email_lname.val() );
			});
		}

		var email_tel = $( form ).find( 'input[name="somdn_download_user_tel"]' );
		if ( email_tel.length ) {
			if ( ! email_tel.val() && $( email_tel ).hasClass( 'somdn-capture-required' ) ) {
				alert( somdn_script_params.somdn_capture_tel_empty );
				e.preventDefault();
				return;
			}
			$( 'body input[name="somdn_download_user_tel"]' ).each(function() {
				$( this ).val( email_tel.val() );
			});
		}

		var email_company = $( form ).find( 'input[name="somdn_download_user_company"]' );
		if ( email_company.length ) {
			if ( ! email_company.val() && $( email_company ).hasClass( 'somdn-capture-required' ) ) {
				alert( somdn_script_params.somdn_capture_company_empty );
				e.preventDefault();
				return;
			}
			$( 'body input[name="somdn_download_user_company"]' ).each(function() {
				$( this ).val( email_company.val() );
			});
		}

		var email_website = $( form ).find( 'input[name="somdn_download_user_website"]' );
		if ( email_website.length ) {
			if ( ! email_website.val() && $( email_website ).hasClass( 'somdn-capture-required' ) ) {
				alert( somdn_script_params.somdn_capture_website_empty );
				e.preventDefault();
				return;
			}
			$( 'body input[name="somdn_download_user_website"]' ).each(function() {
				$( this ).val( email_website.val() );
			});
		}

		var user_email = $( form ).find( 'input[name="somdn_download_user_email"]' );
		if ( user_email.length ) {

			if ( ! user_email.val() ) {
				alert( somdn_script_params.somdn_capture_email_empty );
				e.preventDefault();
				return;
			}

			if ( ! isValidEmailAddress( user_email.val() ) ) {
				alert( somdn_script_params.somdn_capture_email_invalid );
				e.preventDefault();
				return;
			}

			$( 'body .somdn-download-user-email' ).each(function() {
				$( this ).val( user_email.val() );
			});

		}

		var sub_checkbox = $( form ).find( 'input[name="somdn_capture_email_subscribe"]' );
		if ( sub_checkbox.length ) {
			if ( ! $( sub_checkbox ).is( ':checked' ) && $( sub_checkbox ).hasClass( 'somdn-capture-required' ) ) {
				alert( somdn_script_params.somdn_capture_checkbox_error );
				e.preventDefault();
				return;
			}
		}

		somdn_close_email_capture( form, e );

		$( 'body' ).trigger( 'somdn_email_capture_submit_clicked_success', [ e, button, form ] );

	});

	$( 'body' ).on( 'somdn_email_capture_submit_clicked_success', function( event, click_event, button, form ) {
		//console.log('somdn_email_capture_submit_clicked_success')
		//click_event.preventDefault();
	});

function isValidEmailAddress(emailAddress) {
	var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
	return pattern.test(emailAddress);
};

	/**
	 * Scripts for processing AJAX data prior to download form submission
	 */
/*
	$( '.somdn-download-form' ).on( 'submit', function(e) {

		console.log( 'Submit' );

		return true;

		e.preventDefault();

		var form = this;
		var product_id = $( form ).find( 'input[name="somdn_product"]' ).val();
		var the_ajax_url = somdn_script_params.somdn_ajax_url;

		console.log( 'AJAX - let\'s do this!' );
		console.log( 'product = ' + product_id );
		console.log( 'url = ' + the_ajax_url );
		//return;

		$.ajax({
			url: the_ajax_url,
			data: {
				product_id: product_id,
				action: 'somdn_ajax_validate_download',
				security: somdn_script_params.somdn_ajax_nonce
			},
			success : function( response ) {
				console.log(response);
				if ( response == 'download_valid' ) {
					console.log( 'Valid download' );
					form.submit();
				} else {
					return false;
				}
			},
			error : function(error){console.log(error) }
		});

	});
*/

	/**********************************************************************/

	/**
	 * Scripts for manipulating variation product page
	 */
	$( document ).on( 'hide_variation', function( event, variation ) {

		$( '.somdn-download-wrap-variable' ).each(function() {
			$( this ).css( 'display', 'none' );
			var this_wrap_variation = $( this ).attr( 'data-somdn-var-id' );
			$( this ).insertAfter( '.somdn-variable-anchor[data-somdn-anchor-var-id="'+this_wrap_variation+'"]' );
		});

		var variation_wrap = $( '.variations_form' ).find( '.woocommerce-variation-add-to-cart' );
		$( variation_wrap ).css( 'display', '' );
		//console.log( 'hide_variation' );

	});

	$( document ).on( 'show_variation', function( event, variation ) {

		$( '.somdn-download-wrap-variable' ).each(function() {
			$( this ).css( 'display', 'none' );
			var this_wrap_variation = $( this ).attr( 'data-somdn-var-id' );
			$( this ).insertAfter( '.somdn-variable-anchor[data-somdn-anchor-var-id="'+this_wrap_variation+'"]' );
		});

		//console.log( 'show_variation' );
		var variation_wrap = $( '.variations_form' ).find( '.woocommerce-variation-add-to-cart' );
		var variation_id = variation['variation_id'];
		//console.log( variation_id_current );
		var variation_download = $( '*[data-somdn-var-id="'+variation_id+'"]' );
		if ( variation_download.length ) {
			$( variation_download ).find( '.single_add_to_cart_button' ).removeClass( 'disabled wc-variation-selection-needed wc-variation-is-unavailable' );
			$( variation_wrap ).css( 'display', 'none' );
			$( variation_download ).css( 'display', 'block' );
			$( variation_download ).insertAfter( variation_wrap );
		} else {
			$( variation_wrap ).css( 'display', '' );
		}

	});

	/**********************************************************************/

	/**
	 * Scripts for the download redirecting
	 */

	$( 'body' ).on( 'click', 'a.somdn-redirect-form-link', function (e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		//console.log( 'CLICK somdn-redirect-form-link' );
		var somdn_redirect_form = $( 'body' ).find( 'form#somdn_download_redirect_form' );
		if ( somdn_redirect_form.length ) {
			$( somdn_redirect_form ).addClass( 'somdn-redirect-download-submitted' );
			somdn_redirect_form.submit();
		}
	});

	$( document ).ready( function() {
		var somdn_redirect_form = $( 'body' ).find( 'form#somdn_download_redirect_form' ); 
		if ( somdn_redirect_form.length ) {
			var redirect_time = somdn_script_params.somdn_redirect_time;
			if (! redirect_time ) {
				redirect_time = 5000;
			}
			//console.log('redirect_time = ' + redirect_time);
			setTimeout(function() {
				if ( ! $( somdn_redirect_form ).hasClass( 'somdn-redirect-download-submitted' ) ) {
					somdn_redirect_form.submit();
					//console.log( 'somdn_redirect_form = ' + somdn_redirect_form );
					//console.log('Download initiated');
				}
			}, redirect_time);
		}
	});

})( jQuery );