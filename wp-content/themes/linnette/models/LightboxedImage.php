<?php


namespace Linnette\Models;


class LightboxedImage {

	public $responsive_image;
	public $caption;
	public $full_image;

	/**
	 * Holds TimberPost, which extends WP_Post
	 *
	 * @var \TimberPost|\WP_Post
	 */
	private $image_post;

	public function __construct( $image_id, $square = false ) {

		$this->image_post = new \TimberPost( $image_id );

		if( $square === false ) {
			$res_image = new ResponsiveImage( $image_id );
		} else {
			$res_image = new ResponsiveImageSquare( $image_id );
		}
		$full_image = wp_get_attachment_image_src( $image_id, 'full_image' );

		$this->responsive_image = $res_image->getImageData();
		$this->caption = $this->image_post->post_excerpt;
		$this->full_image = array(
				'url' => $full_image[0],
				'width' => $full_image[1],
				'height' => $full_image[2]
			);

	}

	public function __get( $name ) {
		return $this->image_post->$name;
	}

	public function __call( $name, $arguments ) {
		return call_user_func_array( [ $this->image_post, $name ], $arguments );
	}

}