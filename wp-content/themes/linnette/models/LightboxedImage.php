<?php


namespace Linnette\Models;


class LightboxedImage {

	public $responsive_image;
	public $caption;
	public $full_image;

	public function __construct( $image_id, $square = false ) {

		$image_post = new \TimberPost( $image_id );
		if( $square === false ) {
			$res_image = new ResponsiveImage( $image_id );
		} else {
			$res_image = new ResponsiveImageSquare( $image_id );
		}
		$full_image = wp_get_attachment_image_src( $image_id, 'full_image' );

		$this->responsive_image = $res_image->getImageData();
		$this->caption = $image_post->post_excerpt;
		$this->full_image = array(
				'url' => $full_image[0],
				'width' => $full_image[1],
				'height' => $full_image[2]
			);

	}

}