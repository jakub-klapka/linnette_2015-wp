( function( $, tinymce, wp ){

	/* Register the buttons */
     tinymce.create( 'tinymce.plugins.lumi_tupliky', {
          init : function( ed, url ) {
               ed.addButton( 'lumi_tupliky', {
                    title : 'Vložit ťuplíky',
                    onClick: function() {
						// $( 'body ').addClass( 'lumi_dont-switch-to-gallery' );
						// $( '.button.insert-media.add_media:first' ).click();
						// //Click to add media
						// $( 'body' ).watch( {
						// 	properties: 'attr_class',
						// 	id: '_watcher_lumi_tupliky_modal',
						// 	callback: function( data, i ){
						// 		if( data.vals[ 0 ].indexOf( 'modal-open' ) !== -1 ){
						// 			//Add media dialog open
						// 			$( 'body ').removeClass( 'lumi_dont-switch-to-gallery');
						// 			$( '.media-menu .media-menu-item:nth-child(5)' ).trigger( 'click' );
						// 		}
						// 		$('body').unwatch( '_watcher_lumi_tupliky_modal' ); //TODO: not registered for some reason
						// 	}
						// } );

						var frame = wp.media.frames.lumi_tupliky = wp.media( {
							frame: 'shortcode-ui',
							state: 'insert',
							multiple: false
						} );
						frame.open();

					}
               });
            //    ed.addCommand( 'lumi_tupliky_cmd', function() {
            //         var selected_text = ed.selection.getContent();
            //         var return_text = '';
            //         return_text = '<h1>' + selected_text + '</h1>';
            //         ed.execCommand('mceInsertContent', 0, return_text);
            //    });
          },
          createControl : function( n, cm ) {
               return null;
          },
     });
     /* Start the buttons */
     tinymce.PluginManager.add( 'lumi_tupliky_js', tinymce.plugins.lumi_tupliky );

} )( jQuery, tinymce, wp );
