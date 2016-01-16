<?php


namespace Linnette\Controllers;
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

		add_filter( 'the_content', array( $this, 'replace_hr_with_divider' ) );


	}

	public function enqueue_global_scripts() {
		wp_enqueue_style( 'layout' );
		wp_enqueue_script( 'modernizr' );
		wp_enqueue_script( 'menu' );
	}

	public function add_post_to_context( $context ) {
		$context[ 'post' ] = new LinnettePost();
		$context[ 'main_menu' ] = new \TimberMenu();

		$wpseo_options = \WPSEO_Options::get_instance();
		$wpseo_options = $wpseo_options->get_all();
		$context[ 'facebook_link' ] = $wpseo_options[ 'facebook_site' ];
		$context[ 'instagram_link' ] = $wpseo_options[ 'instagram_url' ];

		$wpseo_front = \WPSEO_Frontend::get_instance();
		$context[ 'canonical_url' ] = $wpseo_front->canonical( false );
		$context[ 'seo_description' ] = $wpseo_front->metadesc( false );

		$context[ 'is_user_logged_in' ] = is_user_logged_in();
		$context[ 'user' ] = new \TimberUser();
		return $context;
	}

	public function replace_hr_with_divider( $content ) {

		if( stripos( $content, '<hr' ) !== false ) {

			$divider = \Timber::compile( '_divider.twig' );
			$patterns = array( '<hr>', '<hr >', '<hr/>', '<hr />' );
			foreach( $patterns as $pattern ) {
				$content = str_ireplace( $pattern, '<hr style="display: none;" />' . $divider, $content );
			}

		}

		return $content;

	}

}
