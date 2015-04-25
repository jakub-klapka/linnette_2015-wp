<?php


namespace Linnette\Controllers;


class Portfolio {

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

		add_action( 'wp', array( $this, 'check_for_portfolio_type' ) );

		add_action( 'wp', array( $this, 'add_portfolio_archive_cats' ) );

		add_action( 'wp', array( $this, 'modify_og_image' ) );

	}

	private function register_post_type() {

		$labels = array(
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
			'parent_item_colon'  => 'Nadřazená portfolia:',
			'not_found'          => 'Žádné portfolia nenalezena.',
			'not_found_in_trash' => 'Žádné portfolia nenalezena ani v koši.'
		);

		register_post_type( 'portfolio', array(
			'labels' => $labels,
			'public' => true,
			'supports' => array( 'title', 'editor', 'revisions' ),
			'has_archive' => true,
			'taxonomies' => array( 'portfolio_category' ),
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

		register_taxonomy( 'portfolio_category', 'portfolio', array(
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

	public function check_for_portfolio_type() {

		if( is_singular( 'portfolio' ) ) {

			add_filter( 'timber_context', array( $this, 'add_portfolio_breadcrumbs' ) );

		}

	}

	public function add_portfolio_breadcrumbs( $context ) {
		$post = new \TimberPost();
		$terms = $post->get_terms( 'portfolio_category' );

		if( empty( $terms ) ) return $context;

		//Get only last term for breadcrumbs
		$base_term = end( $terms );
		$terms_for_breadcb = array( $base_term );

		$iterating_term = $base_term;
		while( $iterating_term->parent !== 0 ) {
			$new_term = new \TimberTerm( $iterating_term->parent, 'portfolio_category' );
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
				'title' => 'Portfolio',
				'url' => get_post_type_archive_link( 'portfolio' )
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

	public function add_portfolio_archive_cats() {

		if( is_post_type_archive( 'portfolio' ) || is_tax( 'portfolio_category' ) ) {

			add_filter( 'timber_context', array( $this, 'add_portfolio_archive_cats_cb' ) );

		}

	}

	public function add_portfolio_archive_cats_cb( $context ) {
		$context[ 'cats' ] = \Timber::get_terms( 'portfolio_category', array(), '\Linnette\Models\PortfolioTerm' );

		return $context;
	}

	public function modify_og_image() {

		if( is_singular( 'portfolio' ) || is_singular( 'page' ) ){

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