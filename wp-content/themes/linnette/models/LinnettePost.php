<?php

namespace Linnette\Models;


use Linnette\Controllers\Blog;
use Timber\Post;
use Timber\Timber;

class LinnettePost extends Post {

	public $PostClass = self::class;

	protected $blog_category_cache;
	private $related_articles_count = 3;


	/**
	 * @param int $count
	 * @param string $order
	 * @param string $type
	 * @param string $status
	 * @param string $CommentClass
	 *
	 * @return mixed
	 */
	public function comments( $count = 0, $order = 'wp', $type = 'comment', $status = 'approve', $CommentClass = 'TimberComment' ) {
		return parent::comments( 0, 'wp', 'comment', 'approve', '\Linnette\Models\LinnetteComment' );
	}

	/**
	 * Modify terms getter to read from post cache first
	 *
	 * @param string $tax
	 * @param bool $merge
	 * @param string $TermClass
	 *
	 * @return array of BlogTerms
	 */
	public function terms( $tax = '', $merge = true, $TermClass = '' ) {

		$current_tax_name = get_post_taxonomies()[0];

		if( !empty( $this->blog_category_cache ) ) {

			$terms = $this->blog_category_cache;

			//Run through WP_Terms and create BlogTerms from them 
			$terms = array_map(function( $term ) use( $current_tax_name ) {
				return call_user_func(array( BlogTerm::class, 'fromWithoutACF'), $term->term_id, $current_tax_name );
			}, $terms);

			return $terms;
		}

		//On emty cache, use regular terms getter
		return parent::terms( $tax, $merge, $TermClass );

	}

	/**
	 * Get related articles as LinnettePosts
	 *
	 * For blog posts, always use special logic
	 * For pages, use those, selected in admin
	 *
	 * @return self[]
	 */
	public function related_articles(): array {

		switch( $this->post_type ) {
			case \in_array( $this->post_type, Blog::getPostSlugsArray(), true ):
				return $this->getBlogRelatedArticles();
			case 'page':
				return $this->getPageRelatedArticles();
			default:
				return [];
		}

	}

	/**
	 * Get related articles by blog post logic
	 *
	 * @return self[]
	 */
	private function getBlogRelatedArticles(): array {

		return array_map( function( $post ) {
			return new self( $post->ID );
		}, $this->getRelatedArcilesWPPosts() );

	}

	/**
	 * Page related articles are simply set (or not) in admin acf box
	 *
	 * @return self[]
	 */
	private function getPageRelatedArticles(): array {

		$related_post_ids = get_field( 'selected_related_articles', $this->ID );

		if( $related_post_ids === null || $related_post_ids === '' ) {
			return [];
		}

		return array_map( function( $post_id ) {
			return new self( $post_id );
		}, \array_slice( $related_post_ids, 0, 3 ) );

	}

	/**
	 * Get related articles as WP_Posts
	 *
	 * At first, get primary articles in selected order, than fill rest with random from
	 * related articles and if we still have space left, fill it with random from same
	 * categories.
	 *
	 * @return array of WP_Posts
	 */
	private function getRelatedArcilesWPPosts() {

		$primary_articles_ids = get_field( 'primary_related_articles', $this->ID, false );
		$primary_articles_ids = ( is_array( $primary_articles_ids ) ) ? $primary_articles_ids : [];
		$primary_articles = $this->getArticlesByIds( $primary_articles_ids );

		//If we have enough primary articles
		if( count( $primary_articles ) >= $this->related_articles_count )
			return array_slice( $primary_articles, 0, $this->related_articles_count );


		$related_articles_ids = get_field( 'related_articles', $this->ID, false );
		$related_articles_ids = ( is_array( $related_articles_ids ) ) ? $related_articles_ids : [];

		//Clear the ones, which are already in primary
		$related_articles_ids = array_diff( $related_articles_ids, $primary_articles_ids );

		$related_articles = $this->getArticlesByIds( $related_articles_ids );
		$count_to_add = $this->related_articles_count - count( $primary_articles );

		shuffle( $related_articles );
		$primary_with_related = array_merge( $primary_articles, $related_articles );

		if( count( $related_articles ) >= $count_to_add ) {
			//We have enought articles in related, return them in random
			return array_slice( $primary_with_related, 0, $this->related_articles_count );
		}

		//Fill rest with random from same category
		$count_to_add = $this->related_articles_count - count( $primary_with_related );

		$current_terms = $this->terms();
		$terms_ids = array_map( function( $term ) {
			return $term->term_id;
		}, $current_terms );
		$primary_with_related_ids = array_map( function( $post ) {
			return $post->ID;
		}, $primary_with_related );

		$posts_in_same_terms = new \WP_Query( [
			'orderby' => 'rand',
			'posts_per_page' => $count_to_add,
			'post_type' => $this->post_type,
			'tax_query' => [
				[
					'taxonomy' => get_post_taxonomies()[0],
					'field' => 'term_id',
					'terms' => $terms_ids
				]
			],
			'post__not_in' => array_merge( $primary_with_related_ids, [ $this->ID ] ) //Exclude self
		] );

		return array_merge( $primary_with_related, $posts_in_same_terms->posts );

	}

	/**
	 * Get blog articles by IDs
	 *
	 * @param array $ids IDs of articles to fetch
	 *
	 * @return array of WP_Posts
	 */
	private function getArticlesByIds( $ids ) {

		if( empty( $ids ) ) return [];

		$query = new \WP_Query( [
			'nopaging' => true,
			'post_type' => Blog::getPostSlugsArray(),
			'post__in' => $ids
		] );

		return $query->posts;

	}

	/**
	 * Get Soundcloud link from admin and construct player
	 *
	 * Used in template
	 *
	 * @return bool|string
	 */
	public function soundcloudMusicPlayer() {

		if( !get_field( 'add_music', $this->id ) ) return false;

		$url = get_field( 'soundcloud_link', $this->id, false );
		if( !preg_match( '/https?\:\/\/(:?www\.)?soundcloud\.com/i', $url ) ) return false;

		return Timber::compile( '_soundcloud_player.twig', [ 'url' => htmlspecialchars( $url ) ] );

	}

}
