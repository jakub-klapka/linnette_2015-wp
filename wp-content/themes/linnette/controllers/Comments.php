<?php


namespace Linnette\Controllers;


class Comments {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	public function __construct() {

		add_filter( 'get_twig', array( $this, 'addEnqueueCommentsFunction' ) );
		
		add_filter( 'comment_form_default_fields', array( $this, 'modifyDefaultFields' ) );

		add_filter( 'comment_post_redirect', array( $this, 'handleSentCommentURL' ), 10, 2 );

	}

	/**
	 * @param \Twig_Environment $twig
	 *
	 * @return mixed
	 */
	public function addEnqueueCommentsFunction( $twig ) {
		$func = new \Twig_SimpleFunction( 'enqueue_comments_scripts', array( $this, 'addEnqueueCommentsFunctionCb' ) );
		$twig->addFunction( $func );
		return $twig;
	}

	public function addEnqueueCommentsFunctionCb() {

		wp_enqueue_style( 'comments' );
		wp_enqueue_script( 'form' );

	}

	public function modifyDefaultFields( $fields ) {
		unset( $fields[ 'url' ] );
		return $fields;
	}

	/**
	 * Add arguments to URL after comment send
	 *
	 * @param string $location
	 * @param \stdClass $comment
	 *
	 * @return string
	 *
	 * ?status=1 -> comment approved
	 * ?status=2 -> comment blocked by akismet
	 */
	public function handleSentCommentURL( $location, $comment ) {

		if( $comment->comment_approved === "1" ) {
			return add_query_arg( array( 'status' => '1' ), $location );
		}

		if( $comment->comment_approved === "spam" ) {
			return add_query_arg( array( 'status' => '2', 'comment_id' => $comment->comment_ID ), get_permalink( $comment->comment_post_ID ) . '#comments' );
		}

		return $location;
	}

}