/* global jQuery */
( function( $ ){

	$( function() {

		/*
		Set create gallery as default tab
		 */
		$( 'body' ).watch( {
			properties: 'attr_class',
			callback: function( data, i ){
				if( data.vals[ 0 ].indexOf( 'modal-open' ) !== -1 ){
					$( '.media-menu .media-menu-item:nth-child(2)' ).trigger( 'click' );
				}
			}
		} );

		/*
		Hide some options on gallery setting
		 */
		$( document ).arrive( '.collection-settings.gallery-settings', function() {

			var settings = $( '.collection-settings.gallery-settings' );
			settings.find( '[data-setting="link"]' ).parent().css( 'visibility', 'hidden');
			settings.find( '[data-setting="size"]' ).parent().css( 'visibility', 'hidden');

			settings.find( '[data-setting="columns"]' ).val( '1' ).trigger( 'change' );
			var column_options = settings.find( '[data-setting="columns"] option' );
			column_options.each( function(){
				var option = $( this );
				if( option.prop( 'value' ) > 2 ) {
					option.remove();
				}
			} );

		} );

	} );

} )( jQuery );