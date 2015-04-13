/* global jQuery */
( function( $ ){

	$( function() {

		wp.media.editor.get().bind('open', function(){
			console.log('test');
		})

	} );

} )( jQuery );