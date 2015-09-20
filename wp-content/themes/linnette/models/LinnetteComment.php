<?php

namespace Linnette\Models;


class LinnetteComment extends \TimberComment {

	/**
	 * @param int $size
	 * @param string $default
	 * @return bool|mixed|string
	 */
	public function avatar( $size = 92, $default = '' ) {

		$social_provider = get_user_meta( $this->user_id, 'oa_social_login_identity_provider' );

		if( is_array( $social_provider ) && in_array( 'Facebook', $social_provider ) ) {
			//This is facebook login
			$thumbnail = get_user_meta( $this->user_id, 'oa_social_login_user_thumbnail', true );

			if( !empty( $thumbnail ) ) return $thumbnail;
		}

		return parent::avatar( $size, $default );

	}

}