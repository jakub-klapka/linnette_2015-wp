<?php

namespace Linnette\Models;
use Timber\Timber;

/**
 * Class CommentContext
 * 
 * Will populate some extra variables for comment handling
 * 
 * @package Linnette\Models
 */
class CommentContext {

	/**
	 * Hold Timber context with Comment additions
	 * @var array
	 */
	private $context;

	/**
	 * CommentContext constructor.
	 * Populate context variable on Model creation
	 */
	public function __construct() {

		$this->context = Timber::get_context();
		$this->context = $this->appendSpamFilteringData( $this->context );

	}

	/**
	 * Get Timber context with some extra variables
	 * 
	 * @return array
	 */
	public function getContext() {
		
		return $this->context;
		
	}

	/**
	 * Append Spam attributes to array
	 * Used to handle Akismet callbacks
	 * 
	 * @param $data array Timber context data
	 *
	 * @return array
	 */
	private function appendSpamFilteringData( $data ) {

		$status_map = array(
			'1' => 'approved',
			'2' => 'spam'
		);

		$author = ( isset( $_COOKIE[ 'comment_author_' . COOKIEHASH ] ) ) ? $_COOKIE[ 'comment_author_' . COOKIEHASH ] : false;
		$author_email = ( isset( $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] ) ) ? $_COOKIE[ 'comment_author_email_' . COOKIEHASH ] : false;

		$data[ 'comment' ] = array(
			'author' => $author,
			'author_email' => $author_email,
			'status' => ( isset( $_GET[ 'status' ] ) && isset( $status_map[ $_GET[ 'status' ] ] ) ) ? $status_map[ $_GET[ 'status' ] ] : false
		);
		if( $data[ 'comment' ][ 'status' ] === 'spam' && isset( $_GET[ 'comment_id' ] ) ) {
			$comment = get_comment( $_GET[ 'comment_id' ] );
			$data[ 'comment' ][ 'message' ] = $comment->comment_content;
		}
		
		return $data;
		
	}
	
}