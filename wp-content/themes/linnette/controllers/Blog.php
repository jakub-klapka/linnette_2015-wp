<?php

namespace Linnette\Controllers;

use Timber\Timber;

class Blog {

	/**
	 * Config array for Blog-like post types
	 *
	 * Adding new post type:
	 *  - Add to this config
	 *  - Add new post type to location on ACFs
	 *  - Add new post type to filter on related articles ACF
	 *
	 * @var array
	 */
	private static $post_types_config = [
		'blog' => [
			'labels' => [
				'name'               => 'Blog',
				'singular_name'      => 'Blog',
				'menu_name'          => 'Blog',
				'name_admin_bar'     => 'Blog',
				'add_new'            => 'Přidat',
				'add_new_item'       => 'Přidat blog',
				'new_item'           => 'Nový blog',
				'edit_item'          => 'Upravit blog',
				'view_item'          => 'Ukázat blog',
				'all_items'          => 'Všechny příspěvky',
				'search_items'       => 'Hledat příspěvky',
				'parent_item_colon'  => 'Nadřazený blog:',
				'not_found'          => 'Žádný blog nenalezen.',
				'not_found_in_trash' => 'Žádný blog nenalezen ani v koši.'
			],
			'slug' => 'blog',
			'taxonomy_name' => 'blog_category',
			'taxonomy_slug' => 'kategorie',
		],
		'life_documentary' => [
			'labels' => [
				'name'               => 'Životní dokument',
				'singular_name'      => 'Životní dokument',
				'menu_name'          => 'Životní dokument',
				'name_admin_bar'     => 'Životní dokument',
				'add_new'            => 'Přidat',
				'add_new_item'       => 'Přidat životní dokument',
				'new_item'           => 'Nový životní dokument',
				'edit_item'          => 'Upravit životní dokument',
				'view_item'          => 'Ukázat životní dokument',
				'all_items'          => 'Všechny příspěvky',
				'search_items'       => 'Hledat příspěvky',
				'parent_item_colon'  => 'Nadřazený životní dokument:',
				'not_found'          => 'Žádný životní dokument nenalezen.',
				'not_found_in_trash' => 'Žádný životní dokument nenalezen ani v koši.'
			],
			'slug' => 'zivotni-dokument',
			'taxonomy_name' => 'life_documentary_category',
			'taxonomy_slug' => 'kategorie-dokumentu',
		]
	];

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	private function __construct() {

		$this->register_post_type();

		add_action( 'wp', array( $this, 'check_for_blog_type' ) );

		add_action( 'wp', array( $this, 'check_for_blog_archive' ) );

		add_action( 'wp', array( $this, 'modify_og_image' ) );

		add_action( 'pre_get_posts', array( $this, 'remove_pass_protected_from_archive' ) );

	}

	private function register_post_type() {

		$tax_labels = array(
			'name'              => 'Kategorie',
			'singular_name'     => 'Kategorie',
			'search_items'      => 'Hledat kategorie',
			'all_items'         => 'Všechny kategorie',
			'parent_item'       => 'Nadřazená kategorie',
			'parent_item_colon' => 'Nadřazená kategorie:',
			'edit_item'         => 'Upravit kategorii',
			'update_item'       => 'Aktualizovat kategorii',
			'add_new_item'      => 'Přidat kategorii',
			'new_item_name'     => 'Název nové kategorie',
			'menu_name'         => 'Kategorie'
		);

		foreach( self::$post_types_config as $post_type_config ) {

			register_post_type( $post_type_config['slug'], array(
				'labels' => $post_type_config['labels'],
				'public' => true,
				'supports' => array( 'title', 'editor', 'revisions', 'comments' ),
				'has_archive' => true,
				'taxonomies' => array( $post_type_config['taxonomy_name'] ),
				'rewrite' => array(
					'pages' => false
				),
				'show_in_rest' => true
			) );

			register_taxonomy( $post_type_config['taxonomy_name'], $post_type_config['slug'], array(
				'labels' => $tax_labels,
				'show_tagcloud' => false,
				'show_admin_column' => true,
				'hierarchical' => true,
				'rewrite' => array(
					'slug' => $post_type_config['taxonomy_slug'],
					'hierarchical' => true
				)
			) );

		}

	}

	/**
	 * Get blog-like CPTs DB names (which are used almost nowhere)
	 *
	 * @return array
	 */
	public function getPostTypeNamesArray(): array {
		return array_keys( self::$post_types_config );
	}

	/**
	 * Get blog-like CPTs slugs (which are everywhere)
	 *
	 * @return array
	 */
	public static function getPostSlugsArray(): array {

		return array_map( function( $post_type_config ) {
			return $post_type_config['slug'];
		}, self::$post_types_config );

	}

	/**
	 * Get config for specific CPT by it's slug
	 *
	 * @param string $slug
	 *
	 * @return array
	 */
	public function getPostTypeConfigBySlug( string $slug ): array {
		return current( array_filter( self::$post_types_config, function( $post_type_config ) use ( $slug ) {
			return $post_type_config['slug'] === $slug;
		} ) );
	}

	/**
	 * Get all taxonomy names for all blog-like cpts
	 *
	 * @return array
	 */
	public function getPostTaxonomyNamesArray(): array {
		return array_map( function( $post_type_config ) {
			return $post_type_config['taxonomy_name'];
		}, self::$post_types_config );
	}

	public function check_for_blog_type() {

		if( is_singular( Blog::getPostSlugsArray() ) ) {

			add_filter( 'timber_context', array( $this, 'add_blog_breadcrumbs' ) );

		}

	}

	public function add_blog_breadcrumbs( $context ) {

		$post_type_config = $this->getPostTypeConfigBySlug( get_post_type() );

		$taxonomy_name = $post_type_config['taxonomy_name'];

		$post = new \TimberPost();
		$terms = $post->get_terms( $taxonomy_name );

		if( empty( $terms ) ) return $context;

		//Get only last term for breadcrumbs
		$base_term = end( $terms );
		$terms_for_breadcb = array( $base_term );

		$iterating_term = $base_term;
		while( $iterating_term->parent !== 0 ) {
			$new_term = new \TimberTerm( $iterating_term->parent, $taxonomy_name );
			$terms_for_breadcb[] = $new_term;
			$iterating_term = $new_term;
		}

		//Make it from parent to children
		$terms_for_breadcb = array_reverse( $terms_for_breadcb );

		/*
		 * Build BC
		 */
		$breadcrumbs = array(
			(object)array(
				'title' => $post_type_config['labels']['name'],
				'url' => get_post_type_archive_link( get_post_type() )
			)
		);

		/** @var \TimberTerm $term */
		foreach( $terms_for_breadcb as $term ) {
			$breadcrumbs[] = (object)array(
				'title' => $term->name,
				'url' => $term->link()
			);
		}

		$breadcrumbs[] = (object)array(
			'title' => $post->title(),
			'url' => $post->link()
		);

		$context[ 'breadcrumbs' ] = $breadcrumbs;

		return $context;
	}

	/**
	 * Check if we are running blog-archive or blog taxonomy list
	 */
	public function check_for_blog_archive() {

		if( is_post_type_archive( Blog::getPostSlugsArray() ) || is_tax( $this->getPostTaxonomyNamesArray() ) ) {

			add_filter( 'timber_context', array( $this, 'add_blog_archive_cats_cb' ) );

			add_filter( 'timber_context', array( $this, 'change_timber_post_model' ) );

			$this->fillPostTermCache();

			$this->fillFeaturedImageMetaCache();

		}

	}

	public function add_blog_archive_cats_cb( $context ) {

		$current_tax_name = get_post_taxonomies()[0];

		$context[ 'cats' ] = \Timber::get_terms( $current_tax_name, array(), '\Linnette\Models\BlogTerm' );

		if( is_tax( $current_tax_name ) ) {
			//On any term page, but not on all items page
			$new_item = (object)array(
				'current' => false,
				'name' => 'Všechny',
				'link' => get_post_type_archive_link( get_post_type() )
			);

			if( is_array( $context[ 'cats' ] ) ) {
				array_unshift( $context[ 'cats' ], $new_item );
			} else {
				$context[ 'cats' ] = array( $new_item );
			}
		}

		return $context;
	}

	public function modify_og_image() {

		if( is_singular( Blog::getPostSlugsArray() ) || is_singular( 'page' ) ){

			if( get_field( 'featured_image' ) ) {

				add_filter( 'wpseo_opengraph_image', array( $this, 'modify_og_image_cb' ) );

			}

		}

	}

	public function modify_og_image_cb( $image ) {
		$image = new \TimberImage( get_field( 'featured_image' ) );
		return $image->src( 'full_image' );
	}

	public function remove_pass_protected_from_archive( $query ) {

		//Bail if not on blog archive page
		if( !$query->is_main_query() || is_admin() ) return;
		if( $query->is_post_type_archive( Blog::getPostSlugsArray() ) || $query->is_tax( $this->getPostTaxonomyNamesArray() ) ) {

			$query->set( 'has_password', false );

		};

	}

	/**
	 * Change TimberPost class on Blog archive
	 *
	 * @param \TimberContext $context
	 *
	 * @return \TimberContext
	 */
	public function change_timber_post_model( $context ) {

		$context[ 'posts' ] = \Timber::get_posts( false, 'Linnette\Models\LinnettePost' );

		return $context;

	}

	/**
	 * Will add term cache to wp_query global
	 * 
	 * @return void
	 */
	private function fillPostTermCache() {

		global $wp_query;

		//Bail on empty page
		if( !$wp_query->have_posts() ) return;

		$post_ids = array_map( function( $item ) {
			return $item->ID;
		}, $wp_query->posts );

		//Get all terms at once
		$current_tax_name = get_post_taxonomies()[0];
		$terms = wp_get_object_terms( $post_ids, $current_tax_name, array( 'fields' => 'all_with_object_id' ) );

		//Attach terms to posts
		foreach( $terms as $term ) {

			foreach( $wp_query->posts as $post_pointer => $post ) {

				if( $term->object_id === $post->ID ) {

					if( !isset( $wp_query->posts[ $post_pointer ]->blog_category_cache ) ) {
						$wp_query->posts[ $post_pointer ]->blog_category_cache = array();
					}

					$wp_query->posts[ $post_pointer ]->blog_category_cache[] = $term;

					//Since there is only one post for each term, we can bail on first find
					continue;

				}

			}

		}

	}

	/**
	 * Get featured images ids for all displayed posts and preload their meta to post cache
	 *
	 * TODO: extend this for almost all other pages
	 */
	private function fillFeaturedImageMetaCache() {

		global $wp_query;

		$featured_images_ids = array();

		foreach( $wp_query->posts as $post ) {
			$featured_images_ids[] = get_field( 'featured_image', $post->ID, false );
		}

		new \WP_Query( array(
			'post__in' => $featured_images_ids,
			'post_type' => 'any',
			'post_status' => 'any'
		) );

	}

}
