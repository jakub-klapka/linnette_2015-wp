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
		
		add_filter( 'get_twig', array( $this, 'add_load_scripts_functions' ) );

	}

	public function register_scripts() {

		global $lumi;
		wp_register_style( 'open_sans', '//fonts.googleapis.com/css?family=Open+Sans:300italic,700italic,700,300&subset=latin,latin-ext', array(), $lumi[ 'config' ][ 'static_assets_ver' ] );
		wp_register_style( 'layout', get_template_directory_uri() . '/assets/css/layout.css', array( 'open_sans' ), $lumi[ 'config' ][ 'static_assets_ver' ] );
		wp_register_style( 'lightbox', get_template_directory_uri() . '/assets/css/lightbox.css', array( 'layout' ), $lumi[ 'config' ][ 'static_assets_ver' ] );

		wp_register_script( 'picturefill', get_template_directory_uri() . '/assets/js/libs/picturefill.js', array(), $lumi[ 'config' ][ 'static_assets_ver' ], true );
		wp_register_script( 'modernizr', get_template_directory_uri() . '/assets/js/libs/modernizr.js', array(), $lumi[ 'config' ][ 'static_assets_ver' ], true );

		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', get_template_directory_uri() . '/assets/js/libs/jquery-2.1.3.js', array(), $lumi[ 'config' ][ 'static_assets_ver' ], true );
		wp_register_script( 'velocity', get_template_directory_uri() . '/assets/js/libs/velocity.js', array( 'jquery' ), $lumi[ 'config' ][ 'static_assets_ver' ], true );
		wp_register_script( 'enquire', get_template_directory_uri() . '/assets/js/libs/enquire.js', array(), $lumi[ 'config' ][ 'static_assets_ver' ], true );
		wp_register_script( 'headroom_dep', get_template_directory_uri() . '/assets/js/libs/headroom.js', array(), $lumi[ 'config' ][ 'static_assets_ver' ], true );
		wp_register_script( 'headroom', get_template_directory_uri() . '/assets/js/libs/jQuery.headroom.js', array( 'jquery', 'headroom_dep' ), $lumi[ 'config' ][ 'static_assets_ver' ], true );

		wp_register_script( 'menu', get_template_directory_uri() . '/assets/js/menu.js', array( 'jquery', 'velocity', 'enquire', 'headroom' ), $lumi[ 'config' ][ 'static_assets_ver' ], true );
		wp_register_script( 'breadcrumbs', get_template_directory_uri() . '/assets/js/breadcrumbs.js', array( 'jquery' ), $lumi[ 'config' ][ 'static_assets_ver' ], true );

		wp_register_script( 'photoswipe_dep', get_template_directory_uri() . '/assets/js/libs/photoswipe.js', array(), $lumi[ 'config' ][ 'static_assets_ver' ], true );
		wp_register_script( 'photoswipe', get_template_directory_uri() . '/assets/js/libs/photoswipe-ui-default.js', array( 'photoswipe_dep' ), $lumi[ 'config' ][ 'static_assets_ver' ], true );
		wp_register_script( 'lightbox', get_template_directory_uri() . '/assets/js/lightbox.js', array( 'jquery', 'photoswipe' ), $lumi[ 'config' ][ 'static_assets_ver' ], true );

		wp_register_script( 'lazysizes', get_template_directory_uri() . '/assets/js/libs/lazysizes.js', array(), $lumi[ 'config' ][ 'static_assets_ver' ], true );

		wp_register_script( 'autosize', get_template_directory_uri() . '/assets/js/libs/autosize.js', array(), $lumi[ 'config' ][ 'static_assets_ver' ], true );
		wp_register_script( 'form', get_template_directory_uri() . '/assets/js/form.js', array( 'jquery', 'autosize' ), $lumi[ 'config' ][ 'static_assets_ver' ], true );
		
		wp_register_script( 'load_fb_share', get_template_directory_uri() . '/assets/js/load_fb_share.js', array( 'jquery' ), $lumi[ 'config' ][ 'static_assets_ver' ], true );

	}

	static function enqueuePicturefill() {
		wp_enqueue_script( 'picturefill' );
	}

	static function enqueueLightbox() {
		wp_enqueue_style( 'lightbox' );
		wp_enqueue_script( 'lightbox' );

		global $lumi;

		if( !isset( $lumi[ 'lightbox_enqueued' ] ) ){
			add_action( 'wp_footer', function() {
				echo \Timber::compile( '_pswp_footer.twig' );
			} );
			$lumi[ 'lightbox_enqueued' ] = true;
		}

	}

	/**
	 * @param $twig \Twig_Environment
	 */
	public function add_load_scripts_functions( $twig ) {
		$load_fb_like = new \Twig_SimpleFunction( 'enqueue_load_fb_like', function(){
			wp_enqueue_script( 'load_fb_share' );
		} );
		$twig->addFunction( $load_fb_like );

		return $twig;
	}

}