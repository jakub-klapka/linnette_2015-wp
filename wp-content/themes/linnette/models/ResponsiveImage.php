<?php

namespace Linnette\Models;


class ResponsiveImage {

	protected $image_id;
	private $image_sizes;
	protected $square = false;

	public function __construct( $image_id ) {

		$this->image_id = $image_id;
		$this->image_sizes = $this->get_image_sizes();

	}

	protected function get_image_sizes() {

		$sizes = get_intermediate_image_sizes();
		$sizes = array_filter( $sizes, function( $item ){
			$prefix = ( $this->square ) ? 'square_' : 'rwd_';
			if( strpos( $item, $prefix ) === 0 ) return true;
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
		$srcset_images_strings = array();
		foreach( $this->image_sizes as $size ) {
			$srcset_images_strings[] = $size[ 'url' ] . ' ' . $size[ 'width' ] . 'w';
		};
		return implode( ', ', $srcset_images_strings );
	}

	public function getImageData() {
		$last_image_size = end( $this->image_sizes );
		return array(
			'alt' => get_post_meta( $this->image_id, '_wp_attachment_image_alt', true ),
			'srcset' => $this->getSrcset(),
			'width' => $last_image_size[ 'width' ],
			'height' => $last_image_size[ 'height' ]
		);
	}

}