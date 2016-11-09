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
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'menu_position'      => 28,
			'query_var'          => true,
			'menu_icon'          => 'dashicons-yes',
			'supports'           => [ 'title', 'editor', 'revisions' ],
			'rewrite'            => [
				'slug'  => 'fs',
				'pages' => false
			]
		] );

	}

	/**
	 * Register ACF Controls
	 *
	 * @wp-action acf/init
	 */
	public function addPostEditAcf() {

		acf_add_local_field_group(array (
			'key' => 'group_58238be61a6b5',
			'title' => 'Výběr fotek',
			'fields' => array (
				array (
					'key' => 'field_58238c1ebb2d8',
					'label' => 'Fotky',
					'name' => 'photos',
					'type' => 'gallery',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array (
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'min' => '',
					'max' => '',
					'preview_size' => 'thumbnail',
					'insert' => 'append',
					'library' => 'all',
					'min_width' => '',
					'min_height' => '',
					'min_size' => '',
					'max_width' => '',
					'max_height' => '',
					'max_size' => '',
					'mime_types' => '',
				),
			),
			'location' => array (
				array (
					array (
						'param' => 'post_type',
						'operator' => '==',
						'value' => 'photo_selection',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => 1,
			'description' => '',
		));

	}

}