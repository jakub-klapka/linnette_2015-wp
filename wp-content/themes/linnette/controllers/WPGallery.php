<?php

namespace Linnette\Controllers;


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

	}

	public function gallery_shortcode( $content, $atts ) {

		ScriptStyle::enqueueLightbox();
		ScriptStyle::enqueuePicturefill();

		$ids = explode( ',', $atts[ 'ids' ] );
		$images = array();
		foreach( $ids as $image_id ) {
			$image_post = new \TimberPost( $image_id );
			$res_image = new ResponsiveImage( $image_id );
			$full_image = wp_get_attachment_image_src( $image_id, 'full_image' );
			$images[] = array(
				'responsive_image' => $res_image->getImageData(),
				'caption' => $image_post->post_excerpt,
				'full_image' => array(
					'url' => $full_image[0],
					'width' => $full_image[1],
					'height' => $full_image[2]
				)
			);
		}

		return \Timber::compile( '_wp_gallery.twig', array( 'images' => $images, 'cols' => $atts[ 'columns' ] ) );
	}

}