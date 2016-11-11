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
	 *
	 * @param string    $post_link Unfiltered post link
	 * @param \WP_Post  $post
	 * @param bool      $leavename Indicates, if we should leave %postname% placeholder in link
	 * @param bool      $sample
	 *
	 * @return string
	 */
	public function maybeModifyPermalink( $post_link, $post, $leavename, $sample ) {
		if( get_post_type( $post ) !== 'photo_selection' ) return $post_link;

		if( !current_user_can( 'publish_posts' ) ) return $post_link; //Don't display to anybody without proper caps

		$access_token = get_post_meta( $post->ID, '_access_token', true );

		if( empty( $access_token ) ) return $post_link;

		$postname = ( $leavename ) ? '%postname%' : $post->post_name;

		return trailingslashit( get_bloginfo( 'url' ) ) . 'fs/' . $postname . '/' . $access_token . '/';
	}

	/**
	 * Add rewrite tags for photo slection slug and access token
	 *
	 * @wp-action init
	 */
	public function addRewritetags() {
		add_rewrite_tag('%photo_selection%', '([^&]+)');
		add_rewrite_tag('%photo_selection_access_token%', '([^&]+)');
	}

	/**
	 * Add rewrite rule
	 *
	 * @wp-action init
	 */
	public function addRewriteRule() {
		add_rewrite_rule( '^fs/([^/]+)/([^/]+)/?', 'index.php?photo_selection=$matches[1]&photo_selection_access_token=$matches[2]', 'top' );
	}

	/**
	 * Catch main query parsing and check for existing photo selection query vars
	 *
	 * If so, modify main query to get single photo_selection post and modify is_ variables accordingly
	 *
	 * @wp-filter pre_get_posts
	 *
	 * @param \WP_Query $query
	 *
	 * @return \WP_Query
	 */
	public function catchFrontendAccess( $query ) {
		if( !$query->is_main_query() || is_admin() ) return $query;

		if( empty( $query->get( 'photo_selection' ) ) ) return $query;

		$query->set( 'post_type', 'photo_selection' );
		$query->set( 'name', $query->get( 'photo_selection' ) );

		$query->is_home = false;
		$query->is_singular = true;

		return $query;
	}

	/**
	 * Hook to routes deciding and if we are dealing with single photo_selection, check for valid access token
	 *
	 * On invalid or missing token, fire 404
	 *
	 * @param string $template Path to template
	 *
	 * @return string
	 */
	public function redirectToTemplate( $template ) {
		global $post;
		global $wp_query;

		if( $wp_query->is_singular( 'photo_selection' ) ) {

			$post_access_token = get_post_meta( $post->ID, '_access_token', true );

			if( $wp_query->get( 'photo_selection_access_token' ) !== $post_access_token ) {
				//Deny access
				$wp_query->set_404();
				status_header( 404 );
				return locate_template( '404.php' );
			} else {
				//Allow access
				return locate_template( 'single-photo_selection.php' );
			}

		}

		return $template;
	}

}