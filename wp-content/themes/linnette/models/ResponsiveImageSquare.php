<?php

namespace Linnette\Models;


use TimberImage;
use TimberImageHelper;

class ResponsiveImageSquare extends ResponsiveImage {

	protected function get_image_sizes() {

		$image = new TimberImage( $this->image_id );
		$image_src = $image->get_src();

		$widths = array( 600, 500, 400, 300, 200 );

		$final_sizes = array();
		foreach( $widths as $width ) {
			$final_sizes[] = array(
				'url' => TimberImageHelper::resize( $image_src, $width, $width ),
				'width' => $width,
				'height' => $width
			);
		}

		return $final_sizes;

	}

}