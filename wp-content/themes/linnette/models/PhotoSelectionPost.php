<?php

namespace Linnette\Models;

use Timber\Post;

class PhotoSelectionPost extends Post {

	public $PostClass = self::class;

	/**
	 * Return all selected photo titles in HTML (separated by <br/>)
	 *
	 * @return string
	 */
	public function getSelectedPhotosHtml() {

		$selected_photos_titles = $this->getSelectedPhotosTitles();

		$output = '';

		$count = count( $selected_photos_titles );
		foreach ( $selected_photos_titles as $i => $title ) {
			$output .= "$title";
			if( $i + 1 < $count ) $output .= "<br/>";
		}

		return $output;

	}

	/**
	 * Return all selected photo titles
	 *
	 * @return array
	 */
	private function getSelectedPhotosTitles() {

		$photo_ids = $this->meta( '_checked_photos' );
		if( $photo_ids == false ) return [];

		$photo_titles = [];
		foreach( $photo_ids as $photo_id ) {
			$post = new Post( $photo_id );
			$photo_titles[] = $post->post_title;
		}

		return $photo_titles;

	}

	/**
	 * Check, if current post has locked selection
	 *
	 * @return bool
	 */
	public function isLocked() {
		return (bool)get_field( 'photo_selection_locked', $this->ID );
	}

}