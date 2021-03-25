(function($) {

	$( document ).ready(function() {

		if ( $.fn.select2 ) {

			if ( $( '.somdn-select2.somdn-select2-no-search' ).length ) {
				$( '.somdn-select2.somdn-select2-no-search' ).select2();
			}

			if ( $( '.somdn-select2.somdn-select2-search' ).length ) {
				$('.somdn-select2.somdn-select2-search').select2({
					allowClear: false,
					ajax: {
						url: ajaxurl,
						dataType: 'json',
						delay: 250,
						width: '100%',
						data: function (params) {
							return {
								term: params.term, // the search query
								exclude: '10',
								action: 'somdn_search_products',
								security: somdn_select2_params.somdn_search_products_nonce
							};
						},
						processResults: function( data ) {
							var options = [];
							if ( data ) {

								// Returns an array of products with their ID and Title
								$.each( data, function( index, text ) {
									options.push( { id: text[0], text: text[1]  } );
								});
			 
							}
							return {
								results: options
							};
						},
						cache: true
					},
					minimumInputLength: 3
				});
			}

		}

	});

})( jQuery );