<?php

namespace Linnette\Controllers;


use Linnette\Models\LightboxedImage;
use Linnette\Models\ResponsiveImage;

class WPGallery {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	protected function __construct() {

		add_filter( 'post_gallery', array( $this, 'gallery_shortcode' ), 10, 2 );

		add_action( 'print_media_templates', array( $this, 'add_gallery_settings_template' ) );

	}

	public function gallery_shortcode( $content, $atts ) {

		ScriptStyle::enqueueLightbox();
		ScriptStyle::enqueuePicturefill();

		$ids = explode( ',', $atts[ 'ids' ] );
		$images = array();
		foreach( $ids as $image_id ) {
			$images[] = new LightboxedImage( $image_id );
		}

		return \Timber::compile( '_wp_gallery.twig', array( 'images' => $images, 'cols' => $atts[ 'columns' ] ) );
	}

	/**
	 * Based on wp-includes/media-template.php
	 */
	public function add_gallery_settings_template() {

		?>

		<script type="text/html" id="tmpl-lumi-gallery-settings">
			<h3><?php _e('Gallery Settings'); ?></h3>

			<label class="setting">
				<span><?php _e('Columns'); ?></span>
				<select class="columns" name="columns"
				        data-setting="columns">
					<?php for ( $i = 1; $i <= 2; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <#
						if ( <?php echo $i ?> == wp.media.galleryDefaults.columns ) { #>selected="selected"<# }
							#>>
							<?php echo esc_html( $i ); ?>
							</option>
					<?php endfor; ?>
					<option value="1_2">1+2</option>
					<option value="2_1">2+1</option>
				</select>
			</label>

			<label class="setting">
				<span><?php _e( 'Random Order' ); ?></span>
				<input type="checkbox" data-setting="_orderbyRandom" />
			</label>


		</script>

		<script type="text/html" id="tmpl-lumi-editor-gallery">
			<# if ( data.attachments.length ) { #>
				<div class="gallery gallery-columns-{{ data.columns }}">
					<# _.each( data.attachments, function( attachment, index ) { #>
						<dl class="gallery-item">
							<dt class="gallery-icon">
								<# if ( attachment.thumbnail ) { #>
									<img src="{{ attachment.thumbnail.url }}" width="{{ attachment.thumbnail.width }}" height="{{ attachment.thumbnail.height }}" />
									<# } else { #>
										<img src="{{ attachment.url }}" />
										<# } #>
							</dt>
							<# if ( attachment.caption ) { #>
								<dd class="wp-caption-text gallery-caption">
									{{ attachment.caption }}
								</dd>
								<# } #>
						</dl>
						<# if ( index % data.columns === data.columns - 1 ) { #>
							<br style="clear: both;">
							<# } #>
								<# } ); #>
				</div>
				<# } else { #>
					<div class="wpview-error">
						<div class="dashicons dashicons-format-gallery"></div><p><?php _e( 'No items found.' ); ?></p>
					</div>
					<# } #>
		</script>

		<?php

	}

}