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
	if( strpos( $name, 'Linnette\\Traits\\' ) !== false ) {
		include_once( __DIR__ . '/traits/' . str_replace( 'Linnette\\Traits\\', '', $name ) . '.php' );
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
 * Disable Twig cache on DEV
 */
if( $_SERVER[ 'HTTP_HOST' ] !== 'localhost' ) {
	Timber::$cache = true;
}


/**
 * Setup controllers
 */
add_action( 'init', function(){
	\Linnette\Controllers\Layout::getInstance();
	\Linnette\Controllers\PluginsModifications::getInstance();
	\Linnette\Controllers\ImageSizes::getInstance();
	\Linnette\Controllers\TwigResponsiveImage::getInstance();
	\Linnette\Controllers\WPGallery::getInstance();
	\Linnette\Controllers\Blog::getInstance();
	\Linnette\Controllers\HomePage::getInstance();
	\Linnette\Controllers\ShortcodeZakodovatEmail::getInstance();
	\Linnette\Controllers\ClearTwigCache::getInstance();
	\Linnette\Controllers\OEmbedModifications::getInstance();
	\Linnette\Controllers\Comments::getInstance();
	\Linnette\Controllers\Portfolio::getInstance();
	\Linnette\Controllers\ShortcodeTupliky::getInstance();
	\Linnette\Controllers\ShortcodeCallToActionLink::getInstance();
	\Linnette\Controllers\ShortcodePhotoWithDescription::getInstance();
	\Linnette\Controllers\RelatedArticles::getInstance();

	if( is_admin() ) {
		\Linnette\Controllers\AdminModifications::getInstance();
		\Linnette\Controllers\EditorSEOSettings::getInstance();
	}
}, 5 );

//Brand new Controllers
\Linnette\Controllers\PhotoSelection\Hooks::registerHooks();


/*
 * Early actions
 */
add_filter( 'gt_default_glances', function() {
	$glances = array(
		'attachment' => array( 'icon' => 'f104', 'sort' => 3 ),
		'page' => array( 'icon' => 'f105', 'sort' => 1 ),
		'blog' => array( 'icon' => 'f306', 'sort' => 2 )
	);
	return $glances;
} );
