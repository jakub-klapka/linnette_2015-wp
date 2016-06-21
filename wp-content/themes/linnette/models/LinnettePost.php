<?php

namespace Linnette\Models;


class LinnettePost extends \TimberPost {

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

		if( !empty( $this->blog_category_cache ) ) {

			$terms = $this->blog_category_cache;

			//Run through WP_Terms and create BlogTerms from them 
			$terms = array_map(function( $term ) {
				return call_user_func(array( BlogTerm::class, 'fromWithoutACF'), $term->term_id, 'blog_category');
			}, $terms);

			return $terms;
		}

		//On emty cache, use regular terms getter
		return parent::terms( $tax, $merge, $TermClass );

	}

	/**
	 * Get related articles as LinnettePosts
	 *
	 * @return array of LinnettePosts
	 */
	public function related_articles() {

		return array_map( function( $post ) {
			return new self( $post->ID );
		}, $this->getRelatedArcilesWPPosts() );

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
			return $primary_with_related;
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
			'post_type' => 'blog',
			'tax_query' => [
				[
					'taxonomy' => 'blog_category',
					'field' => 'term_id',
					'terms' => $terms_ids
				]
			],
			'post__not_in' => array_merge( $primary_with_related_ids, [ (string)$this->ID ] ) //Exclude self
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
			'post_type' => 'blog',
			'post__in' => $ids
		] );

		return $query->posts;

	}


}
