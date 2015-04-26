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

		add_action( 'current_screen', array( $this, 'is_edit_page' ) );


	}

	public function is_edit_page( $screen ) {

		if( $screen->base === 'post' ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_media_modifications' ) );
		}

	}

	public function enqueue_media_modifications() {
		wp_register_script( 'arrive', get_template_directory_uri() . '/admin-js/arrive.min.js', array(), null, true );
		wp_register_script( 'watch', get_template_directory_uri() . '/admin-js/jquery-watch.min.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'admin-media-modifs', get_template_directory_uri() . '/admin-js/media-gallery-edit.js', array( 'jquery', 'arrive', 'watch' ), null, true );
	}

}