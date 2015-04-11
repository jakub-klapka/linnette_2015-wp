<?php


namespace Linnette\Controllers;

/**
 * Singleton Class Layout
 * @package Linnette\Controllers
 */
class Layout {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	protected function __construct()
	{

		ScriptStyle::getInstance();
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_global_scripts' ) );

		add_action( 'timber_context', array( $this, 'add_post_to_context' ) );

	}

	public function enqueue_global_scripts() {
		wp_enqueue_style( 'layout' );
		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'menu' );
	}

	public function add_post_to_context( $context ) {
		$context[ 'post' ] = new \TimberPost();
		return $context;
	}

}