<?php

namespace Linnette\Models;

class PhotoSelectionImage extends LightboxedImage {

	/**
	 * Holds information, if current photo is checked by user
	 *
	 * @var bool
	 */
	private $is_checked;

	/**
	 * PhotoSelectionImage constructor.
	 *
	 * @param int  $image_id
	 * @param bool $square
	 * @param bool $is_checked
	 */
	public function __construct( $image_id, $square, $is_checked = false ) {
		parent::__construct( $image_id, $square );
		$this->is_checked = $is_checked;
	}

	/**
	 * $this->is_checked getter
	 *
	 * @return bool
	 */
	public function is_checked() {
		return $this->is_checked;
	}

}