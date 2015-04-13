<?php

/**
 * Classes Autoloader
 */
spl_autoload_register( function( $name ){
	if( strpos( $name, 'Linnette\\Controllers\\' ) !== false ) {
		include_once( __DIR__ . '/controllers/' . str_replace( 'Linnette\\Controllers\\', '', $name ) . '.php' );
		return;
	}
	if( strpos( $name, 'Linnette\\Models\\' ) !== false ) {
		include_once( __DIR__ . '/models/' . str_replace( 'Linnette\\Models\\', '', $name ) . '.php' );
		return;
	}
} );


/**
 * Global Var with all theme stuff
 */
global $lumi;


/**
 * Config
 */
$lumi[ 'config' ] = [
	'static_assets_ver' => 1
];


/**
 * Setup controllers
 */
add_action( 'wp_loaded', function(){
	\Linnette\Controllers\Layout::getInstance();
	\Linnette\Controllers\PluginsModifications::getInstance();
	\Linnette\Controllers\ImageSizes::getInstance();
	\Linnette\Controllers\TwigResponsiveImage::getInstance();
	\Linnette\Controllers\WPGallery::getInstance();
	\Linnette\Controllers\Portfolio::getInstance();

	if( is_admin() ) {
		\Linnette\Controllers\AdminModifications::getInstance();
	}
} );
