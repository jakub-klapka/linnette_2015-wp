<?php

namespace Linnette\Controllers;


class EditorSEOSettings {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	public function __construct() {

		/*
		 * Options page init
		 */
		if( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page( array(
				'page_title' => 'SEO nastavení'
			) );
		}
		
		/*
		 * Modify value loading
		 */
		add_filter( 'acf/load_value', array( $this, 'modifyValueLoading' ), 10, 3 );
		
		/*
		 * Modify values saving
		 */
		add_filter( 'acf/update_value', array( $this, 'modifyValueSaving' ), 10, 3 );

		/*
		 * Clear cache on save
		 */
		add_action( 'acf/save_post', array( $this, 'maybeClearCache' ) );

	}

	public function modifyValueLoading( $value, $post_id, $field ) {

		if( $post_id === 'options' ) {

			//Page title
			if( $field[ 'name' ] === 'seo_website_title' ) {
				$value = get_option( 'blogname' );
			}

			//Page desc
			if( $field[ 'name' ] === 'seo_website_desc' ) {
				$value = get_option( 'blogdescription' );
			}

			//Facebook site
			if( $field[ 'name' ] === 'seo_facebook_link' ) {

				$wpseo_social_options = get_option( 'wpseo_social' );
				$value = $wpseo_social_options[ 'facebook_site' ];

			}

		}

		return $value;
	}

	public function modifyValueSaving( $value, $post_id, $field ) {
		if( $post_id === 'options' ) {

			//Blog title
			if( $field[ 'name' ] === 'seo_website_title' ) {
				update_option( 'blogname', esc_attr( $value ) );
			}

			//Blog title
			if( $field[ 'name' ] === 'seo_website_desc' ) {
				update_option( 'blogdescription', esc_attr( $value ) );
			}

			//Facebook link
			if( $field[ 'name' ] === 'seo_facebook_link' && !empty( $value ) ) {

				$wpseo_social_options = get_option( 'wpseo_social' );
				$wpseo_social_options[ 'facebook_site' ] = esc_url( $value );
				update_option( 'wpseo_social', $wpseo_social_options );

			}

			//Facebook image
			if( $field[ 'name' ] === 'seo_facebook_image' && !empty( $value ) ) {

				$wpseo_social_options = get_option( 'wpseo_social' );

				$image_data = wp_get_attachment_image_src( intval( $value ), 'full' );
				if( isset( $image_data[0] ) && !empty( $image_data[0] ) ) {
					$wpseo_social_options[ 'og_default_image' ] = $image_data[0];
					update_option( 'wpseo_social', $wpseo_social_options );
				}


			}

		}

		return $value;
	}

	public function maybeClearCache( $post_id ) {
		if( $post_id === 'options' ) {
			//Clear whole cache
			if( function_exists( 'wp_cache_clear_cache' ) ) {
				wp_cache_clear_cache();
			}
		}
	}


}