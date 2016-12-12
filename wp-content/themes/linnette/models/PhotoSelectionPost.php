<?php

namespace Linnette\Models;

use Timber\Post;

class PhotoSelectionPost extends Post {

	public $PostClass = self::class;

	/**
	 * Session lock TTL in minutes
	 *
	 * @var int
	 */
	private $sessionLockTTL = 2;

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

	/**
	 * Find out, if post is currently locked by session
	 *
	 * For user, which has set the lock, this will always return false (since it's supposed to be used in post validations)
	 * If last lock was set within TTL and it was set up by different user, return true
	 *
	 * @return bool
	 */
	public function isSessionLocked() {

		/** @var \DateTime|null $lock_timestamp */
		$lock_timestamp = $this->meta( '_session_lock_timestamp' );

		//Post has not been locked
		if( empty( $lock_timestamp ) ) return false;

		//Check, if lock is beyond TTL
		if( $lock_timestamp->add( new \DateInterval( 'PT' . $this->sessionLockTTL . 'M' ) ) > new \DateTime() ) {
			//Still in TTL, might be locked if we are not the ones, editing

			//If current user set the lock, we are returning unlocked
			$session_user = isset( $_SESSION[ 'session_lock_user' ] ) ? $_SESSION[ 'session_lock_user' ] : false;
			if( $session_user === $this->meta( '_session_lock_user' ) ) {
				return false;
			} else {
				return true;
			}

		} else {
			//Outside TTL
			return false;
		}

	}

	/**
	 * Catch valid access to Post and lock editing for current session
	 *
	 * Set only if no other user has set it, and it's active
	 */
	public function setSessionLock() {
		if( $this->isLocked() ) return; //Doesn't matter on final-lock
		if( $this->isSessionLocked() ) return; //Don't set lock, if somebody else has set it first and it's still within TTL

		$user_id = ( isset( $_SESSION[ 'session_lock_user' ] ) ) ? $_SESSION[ 'session_lock_user' ] : bin2hex( openssl_random_pseudo_bytes( 16 ) );
		$_SESSION[ 'session_lock_user' ] = $user_id;

		$this->update( '_session_lock_user', $user_id );
		$this->update( '_session_lock_timestamp', new \DateTime() );
	}

}