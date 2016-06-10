<?php
use Linnette\Models\CommentContext;

$data = ( new CommentContext() )->getContext();

Timber::render( '_comments.twig', $data );