<?php

namespace Linnette\Controllers;

class ShortcodeCallToActionLink {

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

		add_shortcode( 'call_to_action_link', array( $this, 'render_call_to_action_link' ) );
		add_action( 'register_shortcode_ui', array( $this, 'register_shortcode_ui' ) );

	}

	/**
	 * Render HTML for shortcode [call_to_action_link]
	 *
	 * @wp-shortcode call_to_action_link
	 * @param array $atts Not used
	 * @param string $content Not used
	 * @return string HTML output
	 */
	public function render_call_to_action_link( $atts = null, $content = null ) {

		$atts = shortcode_atts( array(
			'url' => '',
			'post' => ''
		), $atts, 'call_to_action_link' );

		$url = ( empty( $atts[ 'url' ] ) ) ? get_permalink( $atts[ 'post' ] ) : esc_url( $atts[ 'url' ] );

		return \Timber::compile( '_call_to_action_link.twig', array( 'url' => $url, 'text' => esc_textarea( $content ) ) );

	}

	/**
	 * Register [call_to_action_link] for use in shortcake
	 *
	 * Won't be run, if shortcakes are not activated and functions does not exist
	 *
	 * @wp-action register_shortcode_ui
	 * @return void
	 */
	public function register_shortcode_ui() {

		shortcode_ui_register_for_shortcode( 'call_to_action_link', array(
			'label' => 'Tlačítko s odkazem',
			'listItemImage' => 'dashicons-align-center',
			'inner_content' => array(
				'label' => 'Text na tlačítku'
			),
			'attrs' => array(
				array(
					'label' => 'Odkaz na stránku',
					'type' => 'post_select',
					'attr' => 'post',
					'query' => array( 'post_type' => array( 'page', 'blog' ) )
				),
				array(
					'label' => 'Vlastní odkaz',
					'type' => 'url',
					'attr' => 'url',
					'description' => 'Nech prázdné, pokud chceš jen odkaz na stránku/blog...'
				)
			)
		) );

	}

}
