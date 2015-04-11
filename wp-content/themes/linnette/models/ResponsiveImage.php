<?php

namespace Linnette\Models;


class ResponsiveImage {

	private $image_id;

	public function __construct( $image_id ) {

		$this->image_id = $image_id;

	}

	private function get_image_sizes() {

		$sizes = get_intermediate_image_sizes();
		$sizes = array_filter( $sizes, function( $item ){
			if( strpos( $item, 'rwd_' ) === 0 ) return true;
			return false;
		} );

		$image_sources = array();

		foreach( $sizes as $size ) {
			$image_data = wp_get_attachment_image_src( $this->image_id, $size );
			$image_sources[ $image_data[1] ] = array(
				'url' => $image_data[0],
				'width' => $image_data[1],
				'height' => $image_data[2]
			);
		}

		return $image_sources;

	}

	private function getSrcset() {
		$image_sizes = $this->get_image_sizes();
		$srcset_images_strings = array();
		foreach( $image_sizes as $size ) {
			$srcset_images_strings[] = $size[ 'url' ] . ' ' . $size[ 'width' ] . 'w';
		};
		return implode( ', ', $srcset_images_strings );
	}

	public function getImageData() {
		return array(
			'alt' => 'test',
			'srcset' => $this->getSrcset(),
			'width' => '',
			'height' => ''
		);
	}

}