<?php

namespace Linnette\Controllers\PhotoSelection;

use Linnette\Traits\SingletonTrait;

class CoreSetup {
	use SingletonTrait;

	/**
	 * Register CPT
	 *
	 * @wp-action init
	 */
	public function registerCustomPostType() {

		$labels = array(
			'name'               => 'Výběr fotek',
			'singular_name'      => 'Výběr fotek',
			'menu_name'          => 'Výběr fotek',
			'name_admin_bar'     => 'Výběr',
			'add_new'            => 'Přidat',
			'add_new_item'       => 'Přidat výběr',
			'new_item'           => 'Nový výběr',
			'edit_item'          => 'Upravit výběr',
			'view_item'          => 'Zobrazit výběr',
			'all_items'          => 'Všechny výběry fotek',
			'search_items'       => 'Hledat výběry',
			'parent_item_colon'  => 'Nadřazené výběry:',
			'not_found'          => 'Nenalezeno.',
			'not_found_in_trash' => 'Nenalezeno ani v koši.'
		);

		register_post_type( 'photo_selection', [
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'menu_position'      => 28,
			'query_var'          => true,
			'menu_icon'          => 'dashicons-yes',
			'supports'           => [ 'title', 'editor', 'revisions' ],
			'rewrite'            => false,
		] );

	}

}