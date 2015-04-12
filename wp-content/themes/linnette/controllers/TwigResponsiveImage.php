<?php

namespace Linnette\Controllers;


use Linnette\Models\ResponsiveImage;

class TwigResponsiveImage {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	protected function __construct() {
		
		add_filter( 'get_twig', array( $this, 'add_responsive_image_fnc' ) );
		
	}

	/**
	 * @param $twig \Twig_Environment
	 *
	 * @return \Twig_Environment
	 */
	public function add_responsive_image_fnc( $twig ) {

		$function = new \Twig_SimpleFunction( 'responsive_image', array( $this, 'twig_responsive_image' ) );
		$twig->addFunction( $function );

		return $twig;
	}


	public function twig_responsive_image( $image_id ) {
		ScriptStyle::enqueuePicturefill();
		$responsive_image = new ResponsiveImage( $image_id );
		return $responsive_image->getImageData();
	}

}