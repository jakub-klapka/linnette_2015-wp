<?php

namespace Linnette\Controllers\PhotoSelection;

use Linnette\Controllers\ScriptStyle;
use Linnette\Models\LightboxedImage;
use Linnette\Models\PhotoSelectionImage;
use Linnette\Models\PhotoSelectionPost;
use Linnette\Traits\SingletonTrait;
use Timber\Timber;

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

	/**
	 * Setup view variables
	 *
	 * @return array
	 */
	public static function setupView() {
		global $post;
		$post_locked = get_field( 'photo_selection_locked' );

		/*
		 * Do not cache this views with WP Super Cache
		 */
		if( !defined( 'DONOTCACHEPAGE' ) ) define( 'DONOTCACHEPAGE', true );

		/*
		 * Setup required JS
		 */
		add_action( 'wp_enqueue_scripts', function() use ( $post_locked ) {
			ScriptStyle::enqueueLightbox( 'photo_selection/_photo_selection_pswp_footer.twig', [ 'locked' => $post_locked ] );
			wp_enqueue_script( 'photo_selection' );
		}, 15 );

		/*
		 * Get Photos and checked status
		 */
		$photos = get_field( 'photos', false, false );
		if( $photos == false ) $photos = [];
		$checked_ids = get_post_meta( $post->ID, '_checked_photos', true );
		$checked_ids = ( is_array( $checked_ids ) ) ? $checked_ids : [];

		$data = [];
		$data[ 'images' ] = array_map( function( $image_id ) use ( $checked_ids ) {
			return new PhotoSelectionImage( $image_id, true, ( in_array( (int) $image_id, $checked_ids ) ) );
		}, $photos );
		$data[ 'checked_count' ] = count( $checked_ids );
		$data[ 'note' ] = get_field( 'photo_selection_note' );
		$data[ 'locked' ] = $post_locked;
		$data[ 'ajax_url' ] = admin_url( 'admin-ajax.php' );
		$data[ 'nonce' ] = wp_create_nonce( 'photo_selection_nonce_id_' . $post->ID );
		$data[ 'access_token' ] = get_post_meta( $post->ID, '_access_token', true );
		$data[ 'form_submitted' ] = ( get_query_var( 'photo_selection_submitted' ) == '1' ) ? get_field( 'photo_selection_success_message', 'option' ) : false;
		$data[ 'instructions' ] = get_field( 'photo_selection_instructions', 'option' );

		return $data;
	}

	/**
	 * @wp-filter query_vars
	 * @param $vars
	 *
	 * @return array
	 */
	public function registerSubmittedQueryVar( $vars ) {
		$vars[] = 'photo_selection_submitted';
		return $vars;
	}

	/**
	 * Handle requests to admin-ajax.php
	 *
	 * If request has photo_selection_action => save_selection, it is (auto)save request, which expects json response.
	 * If reqest have form_submit => 1, it is form submission, which should lock selection and return 302 redirect
	 *
	 * Expected json response: { "result": "saved" }
	 *
	 * @wp-action wp_ajax_nopriv_photo_selection, wp_ajax_photo_selection
	 */
	public function handleFormSubmission() {
		/*
		 * Var Normalization
		 */
		$req = [
			'post_id'      => ( isset( $_POST['post_id'] ) ) ? (int) $_POST['post_id'] : false,
			'_wp_nonce'    => ( isset( $_POST['_wp_nonce'] ) ) ? $_POST['_wp_nonce'] : false,
			'access_token' => ( isset( $_POST['access_token'] ) ) ? $_POST['access_token'] : false
		];

		/*
		 * Checks
		 */
		if( $req[ 'post_id' ] === false || $req[ '_wp_nonce' ] === false || $req[ 'access_token' ] === false ) wp_die( 'Ivalid access' );

		check_ajax_referer( 'photo_selection_nonce_id_' . $req[ 'post_id' ], '_wp_nonce' );

		$post_access_token = get_post_meta( $req[ 'post_id' ], '_access_token', true );
		if( $post_access_token !== $req[ 'access_token' ] ) wp_die( 'Invalid access' );

		if( get_field( 'photo_selection_locked', $req[ 'post_id' ] ) == true ) wp_die( 'Snažíte se upravit uzavřený výběr. To nejde...' );

		/*
		 * Saving
		 */
		$post = new PhotoSelectionPost( $req[ 'post_id' ] );
		$this->savePhotoSelection( $req );
		update_field( 'photo_selection_note', $_REQUEST[ 'zprava' ], $req[ 'post_id' ] ); //Is sanitized by wp_sanitize_meta

		/*
		 * Routing
		 */
		if( isset( $_REQUEST[ 'photo_selection_action' ] ) && $_REQUEST[ 'photo_selection_action' ] === 'save_selection' ) {
			//Autosave
			wp_send_json( [ "result" => "saved" ] );
		} elseif ( isset( $_REQUEST[ 'form_submit' ] ) ) {
			//Standard form submission
			$this->processSubmission( $post );
			wp_redirect( add_query_arg( 'photo_selection_submitted', '1', get_permalink( $req[ 'post_id' ] ) ) );
		} else {
			//Case, we are not aware of
			wp_die( 'Invalid form submission. Data saved.' );
		}

	}

	/**
	 * Saves current photo selection to DB
	 *
	 * Expecting, that request is validated and user has adequate privileges.
	 * This func checks only, if selected photos are in fact in current post
	 *
	 * @param array $req Normalized request variables
	 */
	private function savePhotoSelection( $req ) {

		// Get selected IDs
		$ids_of_selected = [];
		foreach( array_keys( $_REQUEST ) as $request_key ) {
			preg_match( '/^photo\_selection\_(\d+)$/', $request_key, $matches );
			if( isset( $matches[ 1 ] ) ) $ids_of_selected[] = (int)$matches[ 1 ];
		}

		//Check, if those IDs are really in current post
		$all_post_photos = get_field( 'photos', $req[ 'post_id' ], false );
		foreach ( $ids_of_selected as $photo_id ) {
			if( !in_array( $photo_id, $all_post_photos ) ) wp_die( 'Invalid photo selection.' );
		}

		//Save photo selection
		update_post_meta( $req[ 'post_id' ], '_checked_photos', $ids_of_selected );

	}

	/**
	 * Do actual post locking and send notification e-mail
	 *
	 * @param PhotoSelectionPost $post
	 */
	private function processSubmission( $post ) {
		//Set locked
		$post->update( 'photo_selection_locked', true );

		//Send mail
		$recipient_emails = get_field( 'photo_selection_notifications_email', 'option' );
		$recipient_emails = array_map( function( $repeater_item ) {
			return $repeater_item[ 'email' ];
		}, $recipient_emails );

		$mail_data = [
			'title'           => $post->title(),
			'link'            => $post->link(),
			'selected_photos' => $post->getSelectedPhotosHtml()
		];

		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];
		wp_mail(
			$recipient_emails,
			"Uzavřený výběr fotek: {$post->post_title}",
			Timber::compile( 'photo_selection/notification_email.twig', $mail_data ),
			$headers
		);

	}

}