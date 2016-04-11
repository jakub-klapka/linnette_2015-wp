<?php

namespace Linnette\Controllers;

class ShortcodeTupliky {

	public static function getInstance()
	{
		static $instance = null;
		if (null === $instance) {
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Register callbacks
	 *
	 * @wp-action init
	 * @return void
	 */
	public function __construct() {

		add_shortcode( 'tupliky', array( $this, 'render_tupliky' ) );
		add_action( 'register_shortcode_ui', array( $this, 'register_shortcode_ui' ) );

		if( current_user_can( 'edit_posts' ) ) {
			add_filter( 'mce_buttons_2', array( $this, 'add_tinymce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'register_sc_js' ) );
		}

	}

	/**
	 * Render HTML for shortcode [tupliky]
	 *
	 * @wp-shortcode tupliky
	 * @param array $atts Not used
	 * @param string $content Not used
	 * @return string HTML output
	 */
	public function render_tupliky( $atts = null, $content = null ) {

		return \Timber::compile( '_divider.twig', array( 'is_rendering_shortcake' => is_admin() ) );

	}

	/**
	 * Register [tupliky] for use in shortcake
	 *
	 * Won't be run, if shortcakes are not activated and functions does not exist
	 *
	 * @wp-action register_shortcode_ui
	 * @return void
	 */
	public function register_shortcode_ui() {

		shortcode_ui_register_for_shortcode( 'tupliky', array(
			'label' => 'Ťuplíky',
			'listItemImage' => 'dashicons-minus'
		) );

	}

	/**
	 * Add button to TinyMCE
	 *
	 * @wp-filter mce_buttons_2
	 * @param array $buttons All buttons in 2nd row
	 * @return array
	 */
	public function add_tinymce_button( $buttons ) {
		array_push( $buttons, 'lumi_tupliky' );
		return $buttons;
	}

	/**
	 * Register tinymce plugin JS
	 *
	 * @wp-filter mce_external_plugins
	 * @param array $scripts All tinymce scripts
	 * @return array
	 */
	public function register_sc_js( $scripts ) {
		global $lumi;
		wp_enqueue_style( 'lumi_tupliky_css', get_template_directory_uri() . '/admin-js/tinymce_lumi_tupliky.css', array(), $lumi['config']['static_assets_ver'] );
		$scripts[ 'lumi_tupliky_js' ] = get_template_directory_uri() . '/admin-js/tinymce_lumi_tupliky.js';
		return $scripts;
	}

}
