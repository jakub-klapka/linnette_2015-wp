<?php

namespace Linnette\Controllers;


use Linnette\Models\LightboxedImage;

class Portfolio {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	public function __construct() {

		$this->register_cpt();

		add_action( 'wp', array( $this, 'conditional_loads' ) );

		add_action( 'pre_get_posts', array( $this, 'modify_archive_query' ) );

	}

	private function register_cpt() {

		$labels = [
			'name'               => 'Portfolio',
			'singular_name'      => 'Portfolio',
			'menu_name'          => 'Portfolio',
			'name_admin_bar'     => 'Portfolio',
			'add_new'            => 'Přidat',
			'add_new_item'       => 'Přidat portfolio',
			'new_item'           => 'Nové portfolio',
			'edit_item'          => 'Upravit portfolio',
			'view_item'          => 'Ukázat portfolio',
			'all_items'          => 'Všechna portfolia',
			'search_items'       => 'Hledat portfolia',
			'parent_item_colon'  => 'Nadřazené portfolio:',
			'not_found'          => 'Žádná portfolia nenalezena.',
			'not_found_in_trash' => 'Žádná portfolia nenalezena ani v koši.'
		];

		register_post_type( 'portfolio', [

			'labels' => $labels,
			'public' => true,
			'supports' => [ 'title', 'page-attributes' ],
			'has_archive' => true,
			'rewrite' => [
				'slug' => 'portfolio'
			]

		] );

	}

	public function conditional_loads() {

		if( is_singular( 'portfolio' ) ) {

			add_action( 'timber_context', array( $this, 'add_data_to_context' ) );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_cb' ) );

		}

	}

	public function enqueue_scripts_cb() {

		ScriptStyle::enqueuePicturefill();
		ScriptStyle::enqueueLightbox();

		wp_enqueue_script( 'lazysizes' );

	}

	public function add_data_to_context( $context ) {

		/*
		 * Images
		 */
		$gallery = get_field( 'images', get_the_ID(), false );

		$home_images = array();
		foreach( $gallery as $image ){
			$home_images[] = new LightboxedImage( $image, true );
		}

		$context[ 'home_images' ] = $home_images;

		/*
		 * Categories
		 */
		$posts = new \WP_Query( [
			'post_type' => 'portfolio',
			'nopaging' => true,
			'orderby' => 'menu_order',
			'order' => 'asc'
		] );

		$cats = [];
		/** @var \WP_Post $post */
		foreach( $posts->posts as $post ) {
			$cats[] = (object)[
				'current' => ( get_the_ID() === $post->ID ) ? true : false,
				'link' => get_permalink( $post ),
				'name' => $post->post_title
			];
		}

		$context[ 'cats' ] = $cats;

		/*
		 * Call to action button
		 */
		$context[ 'call_to_action_button_enabled' ] = get_field( 'call_to_action_enabled' );
		$context[ 'call_to_action_button_link' ] = get_field( 'call_to_action_button_link' );
		$context[ 'call_to_action_button_text' ] = get_field( 'call_to_action_button_text' );


		return $context;

	}


	/**
	 * @param \WP_Query $query
	 */
	public function modify_archive_query( $query ) {
		if( $query->is_main_query() && $query->is_post_type_archive( 'portfolio' ) ) {
			$query->set( 'nopaging', true );
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'asc' );
		}
	}

}
