(function($) {

var somdn_product_id_mce;

function somdnSetMCEDefaults() {
	if ( typeof somdn_product_id !== 'undefined') {
		somdn_product_id_mce = somdn_product_id;
	} else {
		somdn_product_id_mce = '';
	}
}

	tinymce.PluginManager.add('somdn_download_now_tinymce', function( editor, url ) {

		somdnSetMCEDefaults()
	
		editor.addButton( 'somdn_download_now_tinymce', {

		title: 'Free Download',
		type: 'menubutton',
		classes: 'somdn-free-download-mce',
		icon: 'icon dashicons-download',
		menu: [
		
		{
			text: 'Download Link',
			icon: 'icon dashicons-admin-links',
	
			onclick: function() {
				var window;
				window = editor.windowManager.open( {
				title: 'Free Download Link',
				body: [
				{
					type   : 'container',
					name   : 'container',
					html   : '<p>This is some text</p>'
				},
				{
					type: 'textbox',
					id: 'somdn-link-product-id',
					name: 'product',
					label: 'Product ID',
					value: somdn_product_id_mce,
					onkeypress: function(e) { jQuery(e.target).css('border-color', ''); }
				},
				{
					type: 'listbox',
					name: 'align',
					label: 'Alignment',
					id: 'somdn-link-align',
					'values': [
						{text: 'left (default)', value: 'left'},
						{text: 'center', value: 'center'},
						{text: 'right', value: 'right'}
					]
				},
				{
					type: 'textbox',
					id: 'somdn-link-text',
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
	
					$( '#somdn-link-product-id' ).focus();
					$(inputs.get(0)).css('border-color', 'red');
					editor.windowManager.alert('Please enter a product ID.');
					return false;
				}
	
				editor.insertContent( somdnDownloadLinkShortcode( e.data.product, e.data.align, e.data.text ) );
		
			}
	
			});
	
			}
			
		},
		
		{
			text: 'Download Page',
			icon: 'icon dashicons-cart',
	
			onclick: function() {
				var window;
				window = editor.windowManager.open( {
				title: 'Free Download Page',
				body: [
				{
					type: 'textbox',
					id: 'somdn-page-product-id',
					name: 'product',
					label: 'Product ID',
					value: '',
					onkeypress: function(e) { jQuery(e.target).css('border-color', ''); }
				},
				{
					type: 'textbox',
					id: 'somdn-page-text',
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
	
					$( '#somdn-page-product-id' ).focus();
					$(inputs.get(0)).css('border-color', 'red');
					editor.windowManager.alert('Please enter a product ID.');
					return false;
				}
	
				editor.insertContent( somdnDownloadPageShortcode( e.data.product, e.data.text ) );
		
			}
	
			});
	
			}
			
		}	
		
		]

		});
	});


	function somdnDownloadLinkShortcode( product, align, text ) {

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

	function somdnDownloadPageShortcode( product, text ) {

		var shortcode = '';
		
		var codeStart = '[download_now_page id="' + product + '"';	
		
		var codeEnd = ']';
		
		if ( text ) {
			codeStart = codeStart + ' text="' + text + '"';
		}
		
		shortcode = codeStart + codeEnd;
		
		return shortcode;
	
	}

})( jQuery );