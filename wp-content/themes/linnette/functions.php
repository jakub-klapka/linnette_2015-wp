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
 * Load Plugins translations
 * Actually fix textdomains, where plugin don't have same textdomain as pluginname
 */
$plugins_textdomain_fix = array(
	'acf' => 'acf-options-page',
	'baweic' => 'baw-invitation-codes',
	'ga-dash' => 'google-analytics-dashboard-for-wp'
);
foreach( $plugins_textdomain_fix as $textdomain => $file_name ) {
	$file = WP_LANG_DIR . '/plugins/' . $file_name . '-' . get_locale() . '.mo';
	if( file_exists( $file ) ) {
		load_textdomain( $textdomain, $file );
	}
}

/**
 * Global Var with all theme stuff
 */
global $lumi;


/**
 * Config
 */
$lumi[ 'config' ] = [
	'static_assets_ver' => 2
];
Timber::$cache = true;


/**
 * Setup controllers
 */
add_action( 'init', function(){
	\Linnette\Controllers\Layout::getInstance();
	\Linnette\Controllers\PluginsModifications::getInstance();
	\Linnette\Controllers\ImageSizes::getInstance();
	\Linnette\Controllers\TwigResponsiveImage::getInstance();
	\Linnette\Controllers\WPGallery::getInstance();
	\Linnette\Controllers\Portfolio::getInstance();
	\Linnette\Controllers\HomePage::getInstance();
	\Linnette\Controllers\ShortcodeZakodovatEmail::getInstance();
	\Linnette\Controllers\ClearTwigCache::getInstance();

	if( is_admin() ) {
		\Linnette\Controllers\AdminModifications::getInstance();
		\Linnette\Controllers\EditorSEOSettings::getInstance();
	}
} );



/*
 * Early actions
 */
add_filter( 'gt_default_glances', function() {
	$glances = array(
		'attachment' => array( 'icon' => 'f104', 'sort' => 3 ),
		'page' => array( 'icon' => 'f105', 'sort' => 1 ),
		'portfolio' => array( 'icon' => 'f306', 'sort' => 2 )
	);
	return $glances;
} );