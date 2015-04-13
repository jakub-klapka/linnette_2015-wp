<?php

namespace Linnette\Controllers;


class AdminModifications {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	protected function __construct() {

		add_editor_style( 'assets/css/editor-style.css' );

//		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media_modifications' ) );

	}

	public function enqueue_media_modifications() {
		wp_enqueue_script( 'admin-media-modifs', get_template_directory_uri() . '/admin-js/media-gallery-edit.js', array( 'jquery' ), null, true );
	}

}