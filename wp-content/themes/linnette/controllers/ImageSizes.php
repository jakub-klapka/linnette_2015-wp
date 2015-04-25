<?php

namespace Linnette\Controllers;


class ImageSizes {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	protected function __construct() {

		add_image_size( 'rwd_100', 100, 9999 );
		add_image_size( 'rwd_200', 200, 9999 );
		add_image_size( 'rwd_400', 400, 9999 );
		add_image_size( 'rwd_600', 600, 9999 );
		add_image_size( 'rwd_800', 800, 9999 );
		add_image_size( 'rwd_1000', 1000, 9999 );
		add_image_size( 'rwd_1200', 1200, 9999 );
		add_image_size( 'rwd_1600', 1600, 9999 );

		add_image_size( 'square_200', 200, 200, true );
		add_image_size( 'square_300', 300, 300, true );
		add_image_size( 'square_400', 400, 400, true );
		add_image_size( 'square_500', 500, 500, true );
		add_image_size( 'square_600', 600, 600, true );

		add_image_size( 'full_image', 1280, 1024 );

		$wide_widths = array( 100, 200, 400, 600, 800, 1000, 1200, 1600 );
		foreach( $wide_widths as $width ) {
			add_image_size( 'wide_' . $width, $width, round( ( $width / 16 ) * 7 ), true );
		}

	}

}