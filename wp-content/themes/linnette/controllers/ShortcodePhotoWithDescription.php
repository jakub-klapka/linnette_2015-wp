<?php

namespace Linnette\Controllers;

use Linnette\Models\LightboxedImage;

class ShortcodePhotoWithDescription {

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

		add_shortcode( 'photo_with_description', array( $this, 'render_photo_with_description' ) );
		add_action( 'register_shortcode_ui', array( $this, 'register_shortcode_ui' ) );

	}

	/**
	 * Render HTML for shortcode [photo_with_description]
	 *
	 * @wp-shortcode photo_with_description
	 * @param array $atts Not used
	 * @param string $content Not used
	 * @return string HTML output
	 */
	public function render_photo_with_description( $atts = null, $content = null ) {

		$atts = shortcode_atts( array(
			'attachment' => '',
			'signature' => '',
			'type' => 'normal',
			'is_review' => 'false'
		), $atts, 'photo_with_description' );

		//Normalize is_review
		$atts[ 'is_review' ] = ( $atts[ 'is_review' ] === 'true' ) ? true : false; //Cast from text

		//Check for image and responsive the ..it out of it
		//TODO: add check for not-existing attachment (to the Model itself)
		$atts[ 'image' ] = new LightboxedImage( (int)$atts[ 'attachment' ] );

		//TODO: enqueue JS (also picturefill)

		return \Timber::compile( '_photo_with_description.twig', array_merge( $atts, array( 'text' => esc_textarea( $content ) ) ) );

	}

	/**
	 * Register [photo_with_description] for use in shortcake
	 *
	 * Won't be run, if shortcakes are not activated and functions does not exist
	 *
	 * @wp-action register_shortcode_ui
	 * @return void
	 */
	public function register_shortcode_ui() {

		shortcode_ui_register_for_shortcode( 'photo_with_description', array(
			'label' => 'Fotka s dlouhým popisem',
			'listItemImage' => 'dashicons-analytics',
			'inner_content' => array(
				'label' => 'Text vedle fotky'
			),
			'attrs' => array(
				array(
					'label' => 'Fotka',
					'type' => 'attachment',
					'attr' => 'attachment',
					'libraryType' => array( 'image' ),
					'addButton'   => 'Vložit',
					'frameTitle'  => 'Zvol fotku',
				),
				array(
					'label' => 'Podpis pod ťuplíky',
					'attr' => 'signature',
					'type' => 'text',
					'description' => 'Nech prázdné, pokud nechceš žádné ťuplíky'
				),
				array(
					'label' => 'Umísťění',
					'attr' => 'type',
					'type' => 'radio',
					'options' => array( 'normal' => 'Fotka vlevo, text napravo', 'reverse' => 'Naopak' )
				),
				array(
					'label' => 'Je to reference',
					'attr' => 'is_review',
					'type' => 'checkbox',
					'description' => '(Aby google věděl, o co jde...)'
				)
			)
		) );

	}

}
