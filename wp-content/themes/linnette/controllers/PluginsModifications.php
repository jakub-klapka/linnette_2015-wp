<?php


namespace Linnette\Controllers;

/**
 * Singleton Class PluginsModifications
 * @package linnette\controllers
 */
class PluginsModifications {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	protected function __construct() {

		/*
		 * Yoast
		 */
		add_filter('disable_wpseo_json_ld_search', '__return_true');

		/*
		 * CF7
		 */
		add_filter( 'wpcf7_form_class_attr', array( $this, 'add_form_class' ) );
		add_action( 'wp', array( $this, 'add_wpcf7_scripts' ) );
		add_filter( 'wpcf7_load_js', '__return_false' );
		add_filter( 'wpcf7_load_css', '__return_false' );

	}

	public function add_form_class( $class_string ){
		return $class_string . ' form';
	}

	public function add_wpcf7_scripts() {
		global $post;
		if( isset( $post->post_content ) && strpos( $post->post_content, '[contact-form-7' ) !== false ) {

			add_action( 'wp_enqueue_scripts', array( $this, 'add_wpcf7_scripts_cb' ) );
			if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
				wpcf7_enqueue_scripts();
			}

			if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
				wpcf7_enqueue_styles();
			}

		}

	}

	public function add_wpcf7_scripts_cb() {
		wp_enqueue_script( 'form' );
	}

}