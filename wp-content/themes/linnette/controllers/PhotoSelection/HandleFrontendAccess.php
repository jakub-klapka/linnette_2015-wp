<?php

namespace Linnette\Controllers\PhotoSelection;

use Linnette\Traits\SingletonTrait;

class HandleFrontendAccess {
	use SingletonTrait;

	/**
	 * On post save, check if it has valid access token. If not, create one (probably first save)
	 *
	 * @wp-action save_post_photo_selection
	 * @param $post_id
	 */
	public function createAccessToken( $post_id ) {
		if( wp_is_post_revision( $post_id) || wp_is_post_autosave( $post_id) ) return;

		$current_token = get_post_meta( $post_id, '_access_token', true );
		
		if( empty( $current_token ) ) {
			
			$new_token = bin2hex( openssl_random_pseudo_bytes( 4 ) );

			remove_action( 'save_post_photo_selection', [ $this, 'createAccessToken' ] );
			update_post_meta( $post_id, '_access_token', $new_token );
			add_action( 'save_post_photo_selection', [ $this, 'createAccessToken' ] );

		}

	}

	/**
	 * Catch photo_selection CPT permalink creation and add access token to it
	 *
	 * Maybe, if current user don't have much caps, we shouldn't do that. (sitemap creators, etc.)
	 *
	 * @wp-filter post_type_link
	 */
	public function maybeModifyPermalink( $post_link, $post, $leavename, $sample ) {
		if( get_post_type( $post ) !== 'photo_selection' ) return $post_link;

		if( !current_user_can( 'publish_posts' ) ) return $post_link; //Don't display to anybody without proper caps

		$access_token = get_post_meta( $post->ID, '_access_token', true );

		if( empty( $access_token ) ) return $post_link;

		return $post_link . $access_token . '/';
	}

}