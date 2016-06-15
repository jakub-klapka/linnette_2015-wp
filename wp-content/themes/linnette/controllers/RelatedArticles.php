<?php

namespace Linnette\Controllers;

/**
 * Class RelatedArticles
 *
 * @init at init WP action
 * @package Linnette\Controllers
 */
class RelatedArticles {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	/**
	 * RelatedArticles constructor.
	 *
	 * Hook filters and actions
	 */
	public function __construct() {
		
		add_action( 'acf/save_post', array( $this, 'linkArticles' ), 20 );

	}

	/**
	 * On every post save, check for related articles changes and apply them recursive to other articles
	 *
	 * For blog post type only for now
	 *
	 * @param string $post_id Current post ID
	 * @wp-action acf/save_post
	 */
	public function linkArticles( $post_id ) {
		if( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) return;
		if( get_post_type( $post_id ) !== 'blog' ) return;

		/*
		 * Add this article to related ones
		 */
		$related_articles_ids = get_field( 'related_articles', $post_id, false );

		$related_articles_query = new \WP_Query( array(
			'post__in' => $related_articles_ids,
			'post_type' => 'any',
			'post_status' => 'any',
			'nopaging' => true
		) );

		$related_articles = $related_articles_query->get_posts();

		/** @var \WP_Post $article */
		foreach( $related_articles as $article ) {

			$related_articles_of_this_post = get_field( 'related_articles', $article->ID, false );

			if( !is_array( $related_articles_of_this_post )
			    || !in_array( $post_id, $related_articles_of_this_post ) ) {

				$this->addArticleToRelated( $article->ID, $post_id );

			}

		}

		/*
		 * Unlink removed
		 */
		$all_with_current_as_related_query = new \WP_Query( array(
			'post_type' => 'any',
			'post_status' => 'any',
			'nopaging' => true,
			'meta_key' => 'related_articles',
			'meta_value' => '"' . $post_id . '"',
			'meta_compare' => 'LIKE'
		) );

		$all_with_current_as_related = $all_with_current_as_related_query->get_posts();

		/** @var \WP_Post $item */
		foreach ( $all_with_current_as_related as $item ) {

			if( !in_array( $item->ID, $related_articles_ids ) ) {

				$this->removeRelatedArticle( $item, $post_id );

			}

		}

	}

	/**
	 * Add one article as related in other
	 *
	 * @param string $id_of_current_post ID of post, to which we are adding related article
	 * @param string $id_to_add ID of post, which would be added
	 */
	private function addArticleToRelated( $id_of_current_post, $id_to_add ) {

		$related_articles = get_field( 'related_articles', $id_of_current_post, false );
		$related_articles = ( is_array( $related_articles ) ) ? $related_articles : array();
		$related_articles[] = (string)$id_to_add; // linkArticles() depends on this value to be serialized as string in DB

		update_field( 'related_articles', $related_articles, $id_of_current_post );

	}

	/**
	 * Remove related article from artilce
	 *
	 * @param string $item_from_which_to_remove ID of post, from which we are removing
	 * @param string $id_to_remove ID of post to remove
	 */
	private function removeRelatedArticle( $item_from_which_to_remove, $id_to_remove ) {

		$related_articles = get_field( 'related_articles', $item_from_which_to_remove, false );

		$related_articles = array_filter( $related_articles, function( $item ) use ( $id_to_remove ){
			return ( $item == $id_to_remove ) ? false : true;
		} );

		update_field( 'related_articles', $related_articles, $item_from_which_to_remove );

	}

}