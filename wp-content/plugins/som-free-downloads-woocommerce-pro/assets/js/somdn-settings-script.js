(function($) {

	$( document ).ready(function() {

		// Move .updated and .error alert boxes to the Free Downloads error wrap if on the settings page
		if ( $( '#somdn-admin-notices' ).length ) {
			$( 'div.updated, div.error' ).each(function() {
				$( '#somdn-admin-notices' ).css( 'padding-top', '15px' );
				$( this ).css( 'margin', '5px 0 20px 0' );
				$( this ).appendTo( $( '#somdn-admin-notices' ) );
			});
			$( '#wpbody-content div.update-nag' ).each(function() {
				$( '#somdn-admin-notices' ).css( 'padding-top', '15px' );
				$( this ).css( 'margin', '5px 0 20px 0' );
				$( this ).appendTo( $( '#somdn-admin-notices' ) );
			});
		}

		$('.somdn-wp-picker-container .somdn-colour-picker').wpColorPicker({
			width: 250,
			hide: true
		});

		$( '#somdn_gen_settings_somdn_indy_items' ).change( function() {
			var c = this.checked;
			if ( c == true ) {
				$( '#somdn_gen_settings_somdn_indy_exclude_items' ).prop( 'checked', false );
			}
		});

		$( '#somdn_gen_settings_somdn_indy_exclude_items' ).change( function() {
			var c = this.checked;
			if ( c == true ) {
				$( '#somdn_gen_settings_somdn_indy_items' ).prop( 'checked', false );
			}
		});

		$( '#somdn-error-logs-copy' ).click( function(e) {

			var copyText = document.getElementById( 'somdn-error-logs-textbox-default' );
			var copyButton = $( this );
			copyText.select();
			var successful = document.execCommand('copy');

			if ( successful ) {

				copyButton.addClass('copied');

				setTimeout(
					function() {
						if ( copyButton.hasClass( 'copied' ) ) {
							copyButton.removeClass( 'copied' );
						}
					},
				2000);

			} else {

				alert( 'Something went wrong, sorry!' );

			}

		});

		$( '#somdn-error-logs-delete' ).click( function(e) {

			if ( ! confirm( 'Are you sure you want to delete the error logs?' ) ) {
				return false;
				//e.stopImmediatePropagation();
			}

		});

	});

})( jQuery );