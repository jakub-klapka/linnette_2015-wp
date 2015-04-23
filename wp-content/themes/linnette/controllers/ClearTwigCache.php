<?php

namespace Linnette\Controllers;


class ClearTwigCache {

	public static function getInstance()
	{
		static $instance = null;
		if (null === $instance) {
			$instance = new static();
		}

		return $instance;
	}

	protected function __construct() {

		add_action( 'wp_before_admin_bar_render', array( $this, 'add_clear_twig_cache_to_menu' ) );
		
		add_action( 'admin_init', array( $this, 'check_for_clear_cache' ) );

	}

	public function add_clear_twig_cache_to_menu() {
		global $wp_admin_bar;
		if ( !current_user_can( 'manage_options' ) || !is_admin_bar_showing() )
			return;
		$wp_admin_bar->add_menu( array(
			'parent' => '',
			'id' => 'clear-twig-cache',
			'title' => 'Clear Twig Template Cache',
			'href' => wp_nonce_url( add_query_arg( 'clear_twig_cache', '1', admin_url() ), 'clear_twig_cache' )
		) );
	}

	public function check_for_clear_cache() {

		if( !isset( $_GET[ 'clear_twig_cache' ] ) ) return;

		if( !current_user_can( 'manage_options' ) ) wp_die( 'Nedostatečná práva' );

		if( !wp_verify_nonce( $_GET[ '_wpnonce' ], 'clear_twig_cache' ) ) wp_die( 'Nepovolený přístup' );

		$cache_path = WP_PLUGIN_DIR . '/timber-library/cache/twig' ;

		ClearTwigCache::rrmdir( $cache_path );

		add_action( 'admin_notices', function(){
			echo '<div class="updated"><p>Cache byla smazána</p></div>';
		} );

	}

	public static function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") self::rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

}