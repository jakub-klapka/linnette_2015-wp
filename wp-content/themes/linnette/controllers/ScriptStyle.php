<?php


namespace Linnette\Controllers;

/**
 * Singleton Class ScriptStyle
 * @package Linnette\Controllers
 */
class ScriptStyle {

	/**
	 * @var bool Has the lightbox been enqueued during current request?
	 */
	static $lightbox_enqueued = false;

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

		$theme_ver = wp_get_theme()->get( 'Version' );

		wp_register_style( 'open_sans', '//fonts.googleapis.com/css?family=Open+Sans:300italic,700italic,700,300&subset=latin,latin-ext', array(), $theme_ver );
		wp_register_style( 'layout', get_template_directory_uri() . '/assets/css/layout.css', array( 'open_sans' ), $theme_ver );
		wp_register_style( 'lightbox', get_template_directory_uri() . '/assets/css/lightbox.css', array( 'layout' ), $theme_ver );
		wp_register_style( 'comments', get_template_directory_uri() . '/assets/css/comments.css', array( 'layout' ), $theme_ver );

		wp_register_script( 'picturefill', get_template_directory_uri() . '/assets/js/libs/picturefill.js', array(), $theme_ver, true );
		wp_register_script( 'modernizr', get_template_directory_uri() . '/assets/js/libs/modernizr.js', array(), $theme_ver, true );

		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', get_template_directory_uri() . '/assets/js/libs/jquery-2.1.3.js', array(), $theme_ver, true );
		wp_register_script( 'velocity', get_template_directory_uri() . '/assets/js/libs/velocity.js', array( 'jquery' ), $theme_ver, true );
		wp_register_script( 'enquire', get_template_directory_uri() . '/assets/js/libs/enquire.js', array(), $theme_ver, true );
		wp_register_script( 'headroom_dep', get_template_directory_uri() . '/assets/js/libs/headroom.js', array(), $theme_ver, true );
		wp_register_script( 'headroom', get_template_directory_uri() . '/assets/js/libs/jQuery.headroom.js', array( 'jquery', 'headroom_dep' ), $theme_ver, true );

		wp_register_script( 'menu', get_template_directory_uri() . '/assets/js/menu.js', array( 'jquery', 'velocity', 'enquire', 'headroom' ), $theme_ver, true );
		wp_register_script( 'breadcrumbs', get_template_directory_uri() . '/assets/js/breadcrumbs.js', array( 'jquery' ), $theme_ver, true );

		wp_register_script( 'photoswipe_dep', get_template_directory_uri() . '/assets/js/libs/photoswipe.js', array(), $theme_ver, true );
		wp_register_script( 'photoswipe', get_template_directory_uri() . '/assets/js/libs/photoswipe-ui-default.js', array( 'photoswipe_dep' ), $theme_ver, true );
		wp_register_script( 'lightbox', get_template_directory_uri() . '/assets/js/lightbox.js', array( 'jquery', 'photoswipe' ), $theme_ver, true );

		wp_register_script( 'lazysizes', get_template_directory_uri() . '/assets/js/libs/lazysizes.js', array(), $theme_ver, true );

		wp_register_script( 'autosize', get_template_directory_uri() . '/assets/js/libs/autosize.js', array(), $theme_ver, true );
		wp_register_script( 'form', get_template_directory_uri() . '/assets/js/form.js', array( 'jquery', 'autosize' ), $theme_ver, true );

		wp_register_script( 'load_fb_share', get_template_directory_uri() . '/assets/js/load_fb_share.js', array( 'jquery' ), $theme_ver, true );

		wp_register_script( 'fitvids_lib', get_template_directory_uri() . '/assets/js/libs/jquery.fitvids.js', array( 'jquery' ), $theme_ver, true );
		wp_register_script( 'fitvids', get_template_directory_uri() . '/assets/js/fitvids.js', array( 'jquery', 'fitvids_lib' ), $theme_ver, true );

		wp_register_script( 'js_social_login', get_template_directory_uri() . '/assets/js/js_social_login.js', array( 'jquery', 'velocity' ), $theme_ver, true );

		wp_register_script( 'text_fit', get_template_directory_uri() . '/assets/js/libs/textFit.js', array(), $theme_ver, true );
		wp_register_script( 'image_with_text', get_template_directory_uri() . '/assets/js/image_with_text.js', array( 'jquery', 'text_fit', 'enquire' ), $theme_ver, true );

	}

	static function enqueuePicturefill() {
		wp_enqueue_script( 'picturefill' );
	}

	static function enqueueLightbox() {
		wp_enqueue_style( 'lightbox' );
		wp_enqueue_script( 'lightbox' );

		if( !self::$lightbox_enqueued ){
			add_action( 'wp_footer', function() {
				echo \Timber::compile( '_pswp_footer.twig' );
			} );
			self::$lightbox_enqueued = true;
		}

	}

	/**
	 * Adds function to twig to lazyload required frontend scripts
	 *
	 * @param $twig \Twig_Environment
	 *
	 * @return \Twig_Environment
	 */
	public function add_load_scripts_functions( $twig ) {
		$load_fb_like = new \Twig_SimpleFunction( 'enqueue_load_fb_like', function(){
			wp_enqueue_script( 'load_fb_share' );
		} );
		$twig->addFunction( $load_fb_like );

		$load_js_social_login = new \Twig_SimpleFunction( 'enqueue_js_social_login', function(){
			wp_enqueue_script( 'js_social_login' );
		} );
		$twig->addFunction( $load_js_social_login );

		$load_js_lazysizes = new \Twig_SimpleFunction( 'enqueue_js_lazysizes', function(){
			wp_enqueue_script( 'lazysizes' );
		} );
		$twig->addFunction( $load_js_lazysizes );

		return $twig;
	}

}
