<?php
Timber::render( 'single-photo_selection.twig', array_merge( Timber::get_context(), \Linnette\Controllers\PhotoSelection\HandleFrontendAccess::setupView() ) );