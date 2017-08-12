<?php


namespace Linnette\Controllers;
use Linnette\Models\LinnetteMenu;
use Linnette\Models\LinnettePost;

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

		register_nav_menu( 'main_menu', 'Hlavní menu' );

	}

	public function enqueue_global_scripts() {
		wp_enqueue_style( 'layout' );
		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'menu' );
		wp_enqueue_script( 'webfontloader' );
	}

	public function add_post_to_context( $context ) {
		$context[ 'post' ] = new LinnettePost();
		$context[ 'main_menu' ] = new LinnetteMenu();

		$wpseo_options = \WPSEO_Options::get_instance();
		$wpseo_options = $wpseo_options->get_all();
		$context[ 'facebook_link' ] = $wpseo_options[ 'facebook_site' ];
		$context[ 'instagram_link' ] = $wpseo_options[ 'instagram_url' ];
		$context[ 'slusna_firma_link' ] = get_field( 'slusna_firma_link', 'option' );

		$wpseo_front = \WPSEO_Frontend::get_instance();
		$context[ 'canonical_url' ] = $wpseo_front->canonical( false );
		$context[ 'seo_description' ] = $wpseo_front->metadesc( false );

		$context[ 'is_user_logged_in' ] = is_user_logged_in();
		$context[ 'user' ] = new \TimberUser();
		$context[ 'theme_version' ] = $theme_ver = wp_get_theme()->get( 'Version' );
		return $context;
	}

}
