<?php

namespace Linnette\Models;


class LinnettePost extends \TimberPost {

	protected $blog_category_cache;

	/**
	 * @return mixed
	 */
	public function comments() {
		return parent::comments( 0, 'wp', 'comment', 'approve', '\Linnette\Models\LinnetteComment' );
	}

	/**
	 * Modify terms getter to read from post cache first
	 * 
	 * @return array of BlogTerms
	 */
	public function terms() {

		if( !empty( $this->blog_category_cache ) ) {

			$terms = $this->blog_category_cache;

			//Run through WP_Terms and create BlogTerms from them 
			$terms = array_map(function( $term ) {
				return call_user_func(array( BlogTerm::class, 'fromWithoutACF'), $term->term_id, 'blog_category');
			}, $terms);

			return $terms;
		}

		//On emty cache, use regular terms getter
		return parent::terms();

	}

}
