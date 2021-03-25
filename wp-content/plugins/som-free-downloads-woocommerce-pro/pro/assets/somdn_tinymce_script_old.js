(function($) {

	tinymce.PluginManager.add('somdn_free_download_button', function( editor, url ) {
		editor.addButton( 'somdn_free_download_button', {
			title: 'Free Download Link',
			icon: 'icon dashicons-download',
	
			onclick: function() {
				var window;
				window = editor.windowManager.open( {
				title: 'Free Download Link',
				classes: 'somdn-free-download-mce',
				body: [
				{
					type: 'textbox',
					id: 'somdn-product-id',
					name: 'product',
					label: 'Product ID',
					value: '',
					onkeypress: function(e) { jQuery(e.target).css('border-color', ''); }
				},
				{
					type: 'listbox',
					name: 'align',
					label: 'Alignment',
					id: 'somdn-align',
					'values': [
						{text: 'left (default)', value: 'left'},
						{text: 'center', value: 'center'},
						{text: 'right', value: 'right'}
					]
				},
				{
					type: 'textbox',
					id: 'somdn-text',
					name: 'text',
					label: 'Button Text',
					value: '',
					placeholder: '(optional)'
				}],
	
			onsubmit: function( e ) {
	
				if ( ! e.data.product ) {
			
					e.preventDefault();
	
					var window_id = this._id;
					var inputs = jQuery('#' + window_id + '-body').find('.mce-formitem input');
	
					$( '#somdn-product-id' ).focus();
					$(inputs.get(0)).css('border-color', 'red');
					editor.windowManager.alert('Please enter a product ID.');
					return false;
				}
	
				editor.insertContent( somdnShortcode( e.data.product, e.data.align, e.data.text ) );
		
			}
	
			});
	
			}

		});
	});


	function somdnShortcode( product, align, text ) {
	
		var shortcode = '';
	
		if ( align == 'left' ) {
			var codeStart = '[download_now id="' + product + '"';	
		} else {
			var codeStart = '[download_now id="' + product + '" align="' + align + '"';
		}
		
		var codeEnd = ']';
		
		if ( text ) {
			codeStart = codeStart + ' text="' + text + '"';
		}
		
		shortcode = codeStart + codeEnd;
		
		return shortcode;
	
	}

})( jQuery );