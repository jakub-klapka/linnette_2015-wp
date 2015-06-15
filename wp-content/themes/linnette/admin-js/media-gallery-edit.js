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
		Edit some options on gallery setting
		 */
		//TODO: click on edit button

		wp.media.galleryDefaults.columns = 1;

		// merge default gallery settings template with yours
		wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
			template: function(view){
				return wp.media.template('lumi-gallery-settings')(view);
			}
		});


		/**
		 * Edit media view in tinymce
		 * Code based on wp-includes/js/mce-view.js
		 */
		( function( window, views, $ ) {
			var postID = $( '#post_ID' ).val() || 0,
				media, gallery, av, embed;

			media = {
				state: [],

				edit: function( text, update ) {
					var media = wp.media[ this.type ],
						frame = media.edit( text );

					this.pausePlayers && this.pausePlayers();

					_.each( this.state, function( state ) {
						frame.state( state ).on( 'update', function( selection ) {
							update( media.shortcode( selection ).string() );
						} );
					} );

					frame.on( 'close', function() {
						frame.detach();
					} );

					frame.open();
				}
			};

			gallery = _.extend( {}, media, {
				state: [ 'gallery-edit' ],
				template: wp.media.template( 'lumi-editor-gallery' ),

				initialize: function() {
					var attachments = wp.media.gallery.attachments( this.shortcode, postID ),
						attrs = this.shortcode.attrs.named,
						self = this;

					attachments.more()
						.done( function() {
							attachments = attachments.toJSON();

							_.each( attachments, function( attachment ) {
								if ( attachment.sizes ) {
									if ( attrs.size && attachment.sizes[ attrs.size ] ) {
										attachment.thumbnail = attachment.sizes[ attrs.size ];
									} else if ( attachment.sizes.thumbnail ) {
										attachment.thumbnail = attachment.sizes.thumbnail;
									} else if ( attachment.sizes.full ) {
										attachment.thumbnail = attachment.sizes.full;
									}
								}
							} );

							/*MY EDIT*/
							if( attrs.columns === '1_2' || attrs.columns === '2_1' ) {
								attrs.columns = 3;
							}

							self.render( self.template( {
								attachments: attachments,
								/*MY EDIT*/
								columns: attrs.columns ? attrs.columns : wp.media.galleryDefaults.columns
							} ) );
						} )
						.fail( function( jqXHR, textStatus ) {
							self.setError( textStatus );
						} );
				}
			} );

			views.unregister( 'gallery' );
			views.register( 'gallery', _.extend( {}, gallery ) );

		} )( window, window.wp.mce.views, window.jQuery );


	} );

} )( jQuery );