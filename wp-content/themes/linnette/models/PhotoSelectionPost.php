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
			if( $i + 1 < $count ) $output .= "</br>";
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

		$photo_titles = [];
		foreach( $photo_ids as $photo_id ) {
			$post = new Post( $photo_id );
			$photo_titles[] = $post->post_title;
		}

		return $photo_titles;

	}

}