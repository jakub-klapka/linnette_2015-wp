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


}