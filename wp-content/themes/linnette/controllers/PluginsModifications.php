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
		add_filter( 'wpseo_json_ld_output', array( $this, 'remove_json_ld' ), 10, 2 );
		add_filter( 'wpseo_metabox_prio', function() {return 'low';} );
		add_filter( 'wpseo_use_page_analysis', function() {return false;} );
		add_action( 'add_meta_boxes', array( $this, 'rename_yoast' ) );

		/*
		 * CF7
		 */
		add_filter( 'wpcf7_form_class_attr', array( $this, 'add_form_class' ) );
		add_action( 'wp', array( $this, 'add_wpcf7_scripts' ) );
		add_filter( 'wpcf7_load_js', '__return_false' );
		add_filter( 'wpcf7_load_css', '__return_false' );

		/**
		 * ACF cache delete
		 */
		add_filter( 'acf/save_post', function($post_id) {
			if( $post_id === 'options' ) {
				if( function_exists( 'wp_cache_clear_cache' ) ){
					wp_cache_clear_cache();
				}
			}
		} );

		/**
		 * Delete cache on save term
		 */
		add_action( 'edit_term', array( $this, 'delete_cache_on_save_post' ) );
		add_action( 'create_term', array( $this, 'delete_cache_on_save_post' ) );
		add_action( 'delete_term', array( $this, 'delete_cache_on_save_post' ) );

		/*
		* AIOWPS ServerSignature Off
		*/
		add_filter( 'aiowps_htaccess_rules_before_writing', array( $this, 'aiowps_disable_server_signature' ) );

		/*
		Update Nag
		*/
		add_action('admin_menu', function() {
			remove_action( 'admin_notices', 'update_nag', 3 );
		});

		/*
		 * post type support
		 */
		remove_post_type_support( 'page', 'custom-fields' );
		remove_post_type_support( 'page', 'author' );
		remove_post_type_support( 'page', 'comments' );

		/*
		 * Login logo
		 */
		add_action( 'login_enqueue_scripts', array( $this, 'my_login_logo' ) );


	}

	public function my_login_logo() { ?>
		<style type="text/css">
			.login h1 a {
				background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/assets/login-logo.png);
				background-size: 207px 130px;
				width: 207px;
				height: 130px;
			}
		</style>
	<?php }

	public function rename_yoast( $post_type ) {
		global $wp_meta_boxes;

		if( isset( $wp_meta_boxes[ 'page' ][ 'normal' ][ 'low' ][ 'wpseo_meta' ] ) ){
			$wp_meta_boxes[ 'page' ][ 'normal' ][ 'low' ][ 'wpseo_meta' ][ 'title' ] = 'SEO';
		}
		if( isset( $wp_meta_boxes[ 'post' ][ 'normal' ][ 'low' ][ 'wpseo_meta' ] ) ){
			$wp_meta_boxes[ 'post' ][ 'normal' ][ 'low' ][ 'wpseo_meta' ][ 'title' ] = 'SEO';
		}

	}


	public function aiowps_disable_server_signature( $rules ) {
		foreach( $rules as $key => $rule ) {
			if( $rule == 'ServerSignature Off' || $rule == 'LimitRequestBody 10240000' ) {
				unset( $rules[$key] );
			}
		}
		return $rules;
	}


	public function delete_cache_on_save_post( $post_id = null )
	{
		if( function_exists('wp_cache_clear_cache') ){
			wp_cache_clear_cache();
		}
	}

	public function remove_json_ld( $data, $context ) {
		if( $context === 'website' ) {
			return false;
		}
		return $data;
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