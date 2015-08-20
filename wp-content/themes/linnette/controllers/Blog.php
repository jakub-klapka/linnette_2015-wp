<?php


namespace Linnette\Controllers;


class Blog {

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

		add_action( 'wp', array( $this, 'add_blog_archive_cats' ) );

		add_action( 'wp', array( $this, 'modify_og_image' ) );

	}

	private function register_post_type() {

		$labels = array(
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
		);

		register_post_type( 'blog', array(
			'labels' => $labels,
			'public' => true,
			'supports' => array( 'title', 'editor', 'revisions', 'comments' ),
			'has_archive' => true,
			'taxonomies' => array( 'blog_category' ),
			'rewrite' => array(
				'pages' => false
			)
		) );

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

		register_taxonomy( 'blog_category', 'blog', array(
			'labels' => $tax_labels,
			'show_tagcloud' => false,
			'show_admin_column' => true,
			'hierarchical' => true,
			'rewrite' => array(
				'slug' => 'kategorie',
				'hierarchical' => true
			)
		) );

	}

	public function check_for_blog_type() {

		if( is_singular( 'blog' ) ) {

			add_filter( 'timber_context', array( $this, 'add_blog_breadcrumbs' ) );

		}

	}

	public function add_blog_breadcrumbs( $context ) {
		$post = new \TimberPost();
		$terms = $post->get_terms( 'blog_category' );

		if( empty( $terms ) ) return $context;

		//Get only last term for breadcrumbs
		$base_term = end( $terms );
		$terms_for_breadcb = array( $base_term );

		$iterating_term = $base_term;
		while( $iterating_term->parent !== 0 ) {
			$new_term = new \TimberTerm( $iterating_term->parent, 'blog_category' );
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
				'title' => 'Blog',
				'url' => get_post_type_archive_link( 'blog' )
			)
		);

		/** @var \TimberTerm $term */
		foreach( $terms_for_breadcb as $term ) {
			$breadcrumbs[] = (object)array(
				'title' => $term->name,
				'url' => $term->get_url()
			);
		}

		$breadcrumbs[] = (object)array(
			'title' => $post->title(),
			'url' => $post->permalink()
		);

		$context[ 'breadcrumbs' ] = $breadcrumbs;

		return $context;
	}

	public function add_blog_archive_cats() {

		if( is_post_type_archive( 'blog' ) || is_tax( 'blog_category' ) ) {

			add_filter( 'timber_context', array( $this, 'add_blog_archive_cats_cb' ) );

		}

	}

	public function add_blog_archive_cats_cb( $context ) {
		$context[ 'cats' ] = \Timber::get_terms( 'blog_category', array(), '\Linnette\Models\BlogTerm' );

		if( is_tax( 'blog_category' ) ) {
			//On any term page, but not on all items page
			$new_item = (object)array(
				'current' => false,
				'name' => 'Všechny',
				'link' => get_post_type_archive_link( 'blog' )
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

		if( is_singular( 'blog' ) || is_singular( 'page' ) ){

			if( get_field( 'featured_image' ) ) {

				add_filter( 'wpseo_opengraph_image', array( $this, 'modify_og_image_cb' ) );

			}

		}

	}

	public function modify_og_image_cb( $image ) {
		$image = new \TimberImage( get_field( 'featured_image' ) );
		return $image->get_src( 'full_image' );
	}

}