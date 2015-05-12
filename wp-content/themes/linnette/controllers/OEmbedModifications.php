<?php


namespace Linnette\Controllers;


class OEmbedModifications {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	private function __construct() {

		add_filter( 'embed_oembed_html', array( $this, 'add_html_wrap' ) );

		add_filter( 'embed_oembed_html', array( $this, 'add_fitvids_script' ), 10, 4 );

	}

	public function add_html_wrap( $html ) {
		return sprintf( '<div class="oembed_object">%s</div>', $html );
	}

	public function add_fitvids_script( $html, $cache, $url ) {
		if( strpos( $html, 'youtube' ) !== false || strpos( $html, 'vimeo' ) !== false ) {
			wp_enqueue_script( 'fitvids' );
		}

		return $html;
	}

}