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

}