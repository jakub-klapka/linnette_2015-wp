<?php
$data = Timber::get_context();

$status_map = array(
	'1' => 'approved',
	'2' => 'spam'
);

$data[ 'comment' ] = array(
	'author' => $comment_author,
	'author_email' => $comment_author_email,
	'status' => ( isset( $_GET[ 'status' ] ) && isset( $status_map[ $_GET[ 'status' ] ] ) ) ? $status_map[ $_GET[ 'status' ] ] : false
);
if( $data[ 'comment' ][ 'status' ] === 'spam' && isset( $_GET[ 'comment_id' ] ) ) {
	$comment = get_comment( $_GET[ 'comment_id' ] );
	$data[ 'comment' ][ 'message' ] = $comment->comment_content;
}
Timber::render( '_comments.twig', $data );