<?php

global $lumi_is_comment;
$lumi_is_comment = true;
$data = Timber::get_context();
$lumi_is_comment = false;

Timber::render( '_comments.twig', $data );