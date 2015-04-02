<?php


namespace Linnette\Controllers;

/**
 * Singleton Class ScriptStyle
 * @package Linnette\Controllers
 */
class ScriptStyle {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	protected function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

	}

	public function register_scripts() {

		global $lumi;
		wp_register_style( 'open_sans', '//fonts.googleapis.com/css?family=Open+Sans:300italic,700italic,700,300&subset=latin,latin-ext', array(), $lumi[ 'config' ][ 'static_assets_ver' ] );
		wp_register_style( 'layout', get_template_directory_uri() . '/assets/css/layout.css', array( 'open_sans' ), $lumi[ 'config' ][ 'static_assets_ver' ] );
		wp_register_style( 'lightbox', get_template_directory_uri() . '/assets/css/lightbox.css', array( 'layout' ), $lumi[ 'config' ][ 'static_assets_ver' ] );

	}

}