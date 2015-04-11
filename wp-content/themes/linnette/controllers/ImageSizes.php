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

	}

}