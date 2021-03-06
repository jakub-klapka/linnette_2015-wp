<?php

namespace Linnette\Controllers;


use Linnette\Models\LightboxedImage;

class HomePage {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	public function __construct() {

		if( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page( array(
				'page_title' => 'Home Page'
			) );
		}

		add_action( 'wp', array( $this, 'add_images_to_context' ) );

		add_action( 'wp', array( $this, 'enqueue_scripts' ) );

	}

	public function enqueue_scripts() {

		if( is_front_page() ){
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_cb' ) );
		}

	}

	public function enqueue_scripts_cb() {

		ScriptStyle::enqueuePicturefill();
		ScriptStyle::enqueueLightbox();

		wp_enqueue_script( 'lazysizes' );

	}

	public function add_images_to_context() {

		if( is_front_page() ) {
			add_filter( 'timber_context', array( $this, 'add_images_to_context_cb' ) );
		}

	}

	public function add_images_to_context_cb( $context ) {
		$gallery = get_field( 'home_images', 'option', false );

		$home_images = array();
		foreach( $gallery as $image ){
			$home_images[] = new LightboxedImage( $image, true );
		}

		$context[ 'home_images' ] = $home_images;

		return $context;
	}


}