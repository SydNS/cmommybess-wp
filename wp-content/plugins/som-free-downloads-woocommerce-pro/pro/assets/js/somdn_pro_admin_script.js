(function($) {

	$( document ).ready(function() {

		if ( $( 'body.post-php.post-type-somdn_tracked #somdn_tracked_download_details' ).length ) {

			$( 'body.post-type-somdn_tracked #somdn_tracked_download_details' ).removeClass( 'closed' );

			$( 'body.post-type-somdn_tracked .wp-heading-inline ~ .page-title-action' ).remove();
			$( 'body.post-type-somdn_tracked .wp-heading-inline, body.post-type-somdn_tracked hr.wp-header-end' ).remove();

			$( 'body.post-type-somdn_tracked .meta-box-sortables' ).sortable({
				disabled: true
			});

			$( 'body.post-type-somdn_tracked .postbox .hndle' ).css( 'cursor', 'pointer' );

			//var download_id = $( 'body.post-type-somdn_tracked #tracked-id' ).val();
			//$( '<span> #' + download_id + '</span>' ).appendTo( '.wp-heading-inline' );

		}

		if ( $( '.somdn-settings-table-no-top.checkbox-error-setting-wrap' ).length ) {

			var setting_display_type = $( '#somdn_pro_newsletter_display_type_select' ).find( ':selected' ).val();
			var setting_checkbox_error_wrap = $( '.somdn-settings-table-no-top.checkbox-error-setting-wrap' );
			if ( setting_display_type && setting_checkbox_error_wrap.length ) {
				if ( setting_display_type == 3 ) {
					$( setting_checkbox_error_wrap ).css( 'display', 'table-row' );
				}
			}

			if ( setting_checkbox_error_wrap.length ) {

				$( '#somdn_pro_newsletter_display_type_select' ).on( 'change', function (e) {
					var optionSelected = $( '#somdn_pro_newsletter_display_type_select:selected', this );
					var valueSelected = this.value;
					if ( valueSelected == 3 ) {
						$( setting_checkbox_error_wrap ).css( 'display', 'table-row' );
					} else {
						$( setting_checkbox_error_wrap ).css( 'display', '' );
					}
				});

			}

		}

		$( 'body' ).on( 'click', '#somdn_role_limit_exclude', function (e) {
			var checked = this.checked;
			$('#somdn_role_limit_period' ).prop('disabled', checked);
			$('#somdn_role_limit_downloads' ).prop('disabled', checked);
			$('#somdn_role_limit_products' ).prop('disabled', checked);
			if (checked) {
				$('#somdn_role_limit_error_wrap' ).css('display', 'none');
			} else {
				$('#somdn_role_limit_error_wrap' ).css('display', 'block');
			}
		});

		$( 'body' ).on( 'click', '#somdn_user_limit_exclude', function (e) {
			var checked = this.checked;
			$('#somdn_user_limit_period' ).prop('disabled', checked);
			$('#somdn_user_limit_downloads' ).prop('disabled', checked);
			$('#somdn_user_limit_products' ).prop('disabled', checked);
			if (checked) {
				$('#somdn_user_limit_error_row' ).css('display', 'none');
			} else {
				$('#somdn_user_limit_error_row' ).css('display', '');
			}
		});

	});

})( jQuery );