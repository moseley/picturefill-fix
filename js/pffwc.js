jQuery( document ).ready( function( $ ) {
	
	var last_src = $( 'div.images img:eq(0)' ).attr( 'src' );
	
	$( 'div.images img:eq(0)' ).on( 'load', function() {
		
		var product_img	= $( 'div.images img:eq(0)' );
		var src			= product_img.attr( 'src' );
		
		// Prevent double call
		if ( src !== last_src ) {
			
			last_src	= src;
			
			$.post(
				pffwc.ajax_url,
				{
					action: 'get_srcset',
					nonce: pffwc.nonce,
					src: src
				},
				function( srcset ) {
					if ( srcset !== '' ) {
						product_img.attr( 'srcset', srcset );
					}
				}
			);
			
		}
		
	} );
	
} );