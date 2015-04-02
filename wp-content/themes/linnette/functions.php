<?php

/**
 * Classes Autoloader
 */
spl_autoload_register( function( $name ){
	if( strpos( $name, 'Linnette\\Controllers\\' ) !== false ) {
		include_once( __DIR__ . '/controllers/' . str_replace( 'Linnette\\Controllers\\', '', $name ) . '.php' );
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
//	$lumi[ 'Controllers' ][ 'ScriptStyle' ] = \Linnette\Controllers\ScriptStyle::getInstance();
	$lumi[ 'Controllers' ][ 'Layout' ] = \Linnette\Controllers\Layout::getInstance();
	$lumi[ 'Controllers' ][ 'PluginsModifications' ] = \Linnette\Controllers\PluginsModifications::getInstance();
} );
