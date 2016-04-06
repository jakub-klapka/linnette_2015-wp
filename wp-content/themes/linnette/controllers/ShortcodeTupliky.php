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

}
