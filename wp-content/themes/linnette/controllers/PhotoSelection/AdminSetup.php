<?php

namespace Linnette\Controllers\PhotoSelection;

use Linnette\Traits\SingletonTrait;

class AdminSetup {
	use SingletonTrait;

	/**
	 * Remove Subscribe Reloaded column from Edit screen
	 *
	 * @wp-filter manage_photo_selection_posts_columns is_admin
	 * @param array $columns
	 * @return array
	 */
	public function removeSubscribeColumnFromEditScreen( $columns ) {
		if( isset( $columns[ 'subscribe-reloaded' ] ) ) {
			unset( $columns[ 'subscribe-reloaded' ] );
		}
		return $columns;
	}

}