<?php

namespace Linnette\Controllers;


use Linnette\Models\ResponsiveImage;

class FeaturedImage {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	protected function __construct() {

		add_action( 'wp', array( $this, 'check_for_valid_posttypes' ) );

	}

	public function check_for_valid_posttypes() {

		if( is_page() ) {

			add_filter( 'timber_context', array( $this, 'add_featured_image' ) );

		}

	}

	public function add_featured_image( $context ) {
		$featured_image_id = get_field( 'featured_image' );
		if( $featured_image_id ) {
			ScriptStyle::enqueuePicturefill();
			$featured_image = new ResponsiveImage( $featured_image_id );
			$context[ 'featured_image' ] = $featured_image->getImageData();
			$context[ 'featured_image_caption_1' ] = get_field( 'featured_image_caption_1' );
			$context[ 'featured_image_caption_2' ] = get_field( 'featured_image_caption_2' );
		}

		return $context;
	}

}