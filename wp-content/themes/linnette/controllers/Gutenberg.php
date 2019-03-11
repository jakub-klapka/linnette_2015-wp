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
		add_theme_support( 'disable-custom-colors' );
		add_theme_support( 'editor-color-palette' );
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
			wp_enqueue_script( 'image_with_text' );

			wp_enqueue_script(
				'gutenberg-settings',
				trailingslashit( get_stylesheet_directory_uri() ) . 'admin-js/gutenberg-settings.js',
				array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' )
			);

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

		if ( \function_exists( 'acf_register_block' ) ) {

			acf_register_block( [
				'name'            => 'linn-gallery',
				'title'           => 'Galerie',
				'render_callback' => [ $this, 'linnGalleryRender' ],
				'category'        => 'layout',
				'icon'            => 'format-gallery',
				'supports'        => [
					'align' => false,
					'html'  => true
				]
			] );

			acf_register_block( [
				'name'            => 'linn-tupliky',
				'title'           => 'Ťuplíky',
				'render_callback' => [ $this, 'linnTuplikyRender' ],
				'category'        => 'layout',
				'icon'            => 'format-status',
				'supports'        => [
					'align' => false,
					'html'  => true
				]
			] );

			acf_register_block( [
				'name'            => 'linn-call_to_action_button',
				'title'           => 'Tlačítko',
				'render_callback' => [ $this, 'linnCallToActionButtonRender' ],
				'category'        => 'layout',
				'icon'            => 'align-center',
				'supports'        => [
					'align' => false,
					'html'  => true
				]
			] );

			acf_register_block( [
				'name'            => 'linn-photo_with_description',
				'title'           => 'Fotka s popisem',
				'render_callback' => [ $this, 'linnPhotoWithDescriptionRender' ],
				'category'        => 'layout',
				'icon'            => 'analytics',
				'supports'        => [
					'align' => false,
					'html'  => true
				]
			] );

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

	/**
	 * Render linn-tupliky ACF Block
	 */
	public function linnTuplikyRender() {

		echo \Timber::compile( '_divider.twig', array( 'is_rendering_shortcake' => is_admin() ) );

	}

	/**
	 * Render linn-call_to_action_button Block
	 */
	public function linnCallToActionButtonRender() {

		if( ! get_field( 'custom_link' ) ) {
			$post_url = get_field( 'post' );
		} else {
			$post_url = esc_url( get_field( 'custom_link' ) );
		}

		$url = is_admin() ? '#' : $post_url;
		$content = get_field( 'text' );

		echo \Timber::compile( '_call_to_action_link.twig', array( 'url' => $url, 'text' => $content ) );

	}

	/**
	 * Render linn-photo_with_description Block
	 */
	public function linnPhotoWithDescriptionRender() {

		//Enqueue Scripts
		ScriptStyle::enqueueLightbox();
		ScriptStyle::enqueuePicturefill();
		wp_enqueue_script( 'image_with_text' );

		$data = [
			'image'                  => new LightboxedImage( (int) get_field( 'attachment' ) ),
			'signature'              => get_field( 'signature' ),
			'type'                   => get_field( 'type' ),
			'is_review'              => get_field( 'is_review' ),
			'text'                   => get_field( 'text' ),
			'is_rendering_shortcake' => is_admin()
		];

		echo \Timber::compile( '_photo_with_description.twig', $data );
		if( is_admin() ) {
			echo '<script type="text/javascript">window.linnette.imageWithText.refreshAllImages()</script>';
		}

	}

}