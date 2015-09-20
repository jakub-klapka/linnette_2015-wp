<?php

namespace Linnette\Models;


class LinnettePost extends \TimberPost {

	/**
	 * @return mixed
	 */
	public function comments() {
		return $this->get_comments( 0, 'wp', 'comment', 'approve', '\Linnette\Models\LinnetteComment' );
	}

}