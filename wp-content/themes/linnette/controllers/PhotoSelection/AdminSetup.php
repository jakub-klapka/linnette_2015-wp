<?php

namespace Linnette\Controllers\PhotoSelection;

use Linnette\Models\PhotoSelectionPost;
use Linnette\Traits\SingletonTrait;
use Timber\Timber;

class AdminSetup {
	use SingletonTrait;

	/**
	 * Remove Subscribe Reloaded column from Edit screen
	 *
	 * @wp-filter manage_photo_selection_posts_columns is_admin
	 * @param array $columns
	 * @return array
	 */
	public function removeSubscribeColumnFromEditScreen( $columns ) {
		if( isset( $columns[ 'subscribe-reloaded' ] ) ) {
			unset( $columns[ 'subscribe-reloaded' ] );
		}
		return $columns;
	}

	/**
	 * Adds Permalink control under title on post edit screen.
	 *
	 * (Because when CPT is public => false, this control is not there by default)
	 *
	 * @wp-action edit_form_before_permalink
	 * @param \WP_Post $post
	 */
	public function addPermalinkUnderTitle( $post ) {
		if( get_post_type( $post ) !== 'photo_selection' ) return;

		Timber::render( 'photo_selection/_post_edit_permalink.twig', [
			'sample_permalink_html' => get_sample_permalink_html( $post->ID )
		] );
	}

	/**
	 * Adds View link to Edit screen
	 *
	 * (As this one is also not visible, when post is not publicly queryable)
	 *
	 * @wp-filter post_row_actions
	 *
	 * @param array $actions
	 * @param \WP_Post $post
	 * @return array
	 */
	public function addViewToRowActions( $actions, $post ) {

		if( get_post_type( $post ) !== 'photo_selection' ) return $actions;

		if( isset( $actions[ 'view' ] ) ) return $actions;

		$actions['view'] = sprintf(
			'<a href="%s" rel="permalink" aria-label="%s">%s</a>',
			get_permalink( $post->ID ),
			/* translators: %s: post title */
			esc_attr( sprintf( __( 'View &#8220;%s&#8221;' ), $post->post_title ) ),
			__( 'View' )
		);

		return $actions;

	}

	/**
	 * Add photo selection settings page under CPTs edit.php parent
	 *
	 * @wp-action admin_init
	 */
	public function registerAcfSettingsPage() {

		if( function_exists('acf_add_options_page') ) {

			acf_add_options_page( [
				'page_title' 	=> 'Nastavení Výběrů fotek',
				'menu_title'    => 'Nastavení Výběrů fotek',
				'menu_slug'     => 'photo-selection-settings',
				'capability'    => 'edit_posts',
				'parent'        => 'edit.php?post_type=photo_selection'
			] );

		}

	}

	/**
	 * Register post edit metaboxes
	 *
	 * @wp-action add_meta_boxes
	 */
	public function registerSelectedImagesMetabox() {
		add_meta_box(
			'selected-photos',
			'Seznam vybraných fotek',
			[ $this, 'selectedImagesMetabox' ],
			'photo_selection',
			'normal',
			'high'
		);
	}

	/**
	 * Render selected photos metabox content
	 *
	 * @param \WP_Post $wp_post
	 */
	public function selectedImagesMetabox( $wp_post ) {
		$post = new PhotoSelectionPost( $wp_post->ID );
		echo $post->getSelectedPhotosHtml();
	}

	/**
	 * Register column on edit.php for post lock indication
	 *
	 * @wp-filter manage_photo_selection_posts_columns
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function registerLockColumn( $columns ) {
		$new_column = [ 'photo_selection_locked' => 'Výběr uzamčen' ];

		$date_index = array_search( 'date', array_keys( $columns ) );
		$date_index = ( $date_index === false ) ? count( $columns ) - 1 : $date_index;

		return array_merge( array_slice( $columns, 0, $date_index ), $new_column, array_slice( $columns, $date_index ) );
	}

	/**
	 * Add column to edit.php, which indicates, if post was already locked by user
	 *
	 * @wp-action manage_photo_selection_posts_custom_column
	 */
	public function lockColumn( $column, $post_id ) {
		if( $column !== 'photo_selection_locked' ) return;

		$post = new PhotoSelectionPost( $post_id );

		if( $post->isLocked() ) {
			echo '<span class="dashicons dashicons-lock" style="color: green;"></span>';
		} else {
			echo '<span class="dashicons dashicons-unlock" style="color: red;"></span>';
		}

	}

}