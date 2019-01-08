<?php

namespace Linnette\Controllers;

use Linnette\Models\LightboxedImage;
use Linnette\Traits\SingletonTrait;

class Gutenberg {
	use SingletonTrait;

	public function __construct() {

		/*
		 * Theme setup
		 */
		add_theme_support('editor-styles');
		add_editor_style( 'assets/css/editor-style.css' );

		/*
		 * Actions
		 */
		add_action( 'enqueue_block_assets', [ $this, 'dequeueDefaultGutenbergFrontendStyle' ] );
		$this->registerAcfBlocks();

		/*
		 * Admin actions
		 */
		if( is_admin() ) {

			add_action( 'admin_enqueue_scripts', [ $this, 'maybeEnqueueAdminScripts' ] );
			add_filter( 'block_editor_settings', [ $this, 'dequeueDefaultEditorStyles' ] );

		}

	}

	/**
	 * Dequeue default frontend style (since we have own)
	 *
	 * @wp-action enqueue_block_assets
	 */
	public function dequeueDefaultGutenbergFrontendStyle() {
		wp_dequeue_style( 'wp-block-library' );
	}

	/**
	 * Enqueue scripts and styles for custom Gutenberg behavior
	 *
	 * @wp-action admin_enqueue_scripts
	 */
	public function maybeEnqueueAdminScripts() {

		if( \get_current_screen()->is_block_editor ) {

			wp_enqueue_style( 'editor-style', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/css/editor-style.css' );

			/*
			 * Skeleton, not using it now:
			 */
//			wp_register_script(
//				'linn-gutenberg-editor-bundle',
//				trailingslashit(get_stylesheet_directory_uri()) . 'admin-js/gutenberg.js',
//				array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-api-fetch' )
//			);
//
//			register_block_type( 'linnette/gallery', array(
//				'editor_script' => 'linn-gutenberg-editor-bundle',
//			) );

		}

	}

	/**
	 * Dequeue editor styles, which overwrite our custom fonts
	 *
	 * @wp-filter block_editor_settings
	 *
	 * @param $editor_settings
	 *
	 * @return mixed
	 */
	public function dequeueDefaultEditorStyles( $editor_settings ) {

		$styles = $editor_settings['styles'];

		foreach ( $styles as $style_key => $style ) {

			if ( stripos( $style['css'], 'Noto Serif' ) !== false ) {
				unset( $styles[ $style_key ] );
			}

		}

		$editor_settings['styles'] = array_values( $styles );

		return $editor_settings;

	}

	/**
	 * Handle registering of ACF Guten blocks
	 */
	public function registerAcfBlocks() {

		// check function exists
		if( \function_exists('acf_register_block') ) {

			acf_register_block(array(
				'name'				=> 'linn-gallery',
				'title'				=> 'Galerie',
				'render_callback'	=> [ $this, 'linnGalleryRender' ],
				'category'			=> 'layout',
				'icon'				=> 'format-gallery',
				'supports' => [
					'align' => false,
					'html' => true
				]
			));
		}

	}

	/**
	 * Render linn-gallery ACF block
	 *
	 * @param $block_data
	 */
	public function linnGalleryRender( $block_data ) {

		ScriptStyle::enqueueLightbox();
		ScriptStyle::enqueuePicturefill();

		$images = array();
		foreach( get_field( 'images' ) as $image ) {
			$images[] = new LightboxedImage( $image['ID'] );
		}

		echo \Timber::compile( '_wp_gallery.twig', array( 'images' => $images, 'cols' => get_field( 'column_count' ) ) );

	}

}