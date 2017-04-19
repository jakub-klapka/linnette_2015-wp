<?php

namespace Linnette\Models;

/**
 * @property string _menu_item_type [
 *      @type string 'post_type_archive' Indicates Menu Item with post type archive as target
 *      @type string 'post_type' Targets single post
 * ]
 * @property string _menu_item_object Basicaly post type of archive or single post
 * @property string object_id ID of target post as string
 */
class LinnetteMenuItem extends \Timber\MenuItem {

	/**
	 * Should current MenuItem be marked as active
	 *
	 * @var bool
	 */
	protected $active = false;

	public function __construct( $data ) {
		parent::__construct( $data );

		$this->findOutIfActive();
	}

	/**
	 * Fill in $this->active property based on menu_item_type
	 */
	protected function findOutIfActive() {

		$active = false;

		$method_name = 'findOutIfActive_' . $this->_menu_item_type;
		if( method_exists( $this, $method_name ) ) {
			$active =  call_user_func( [ $this, $method_name ] );
		}

		$this->active = $active;

	}

	/**
	 * For post type archives
	 *
	 * Called poly in $this->findOutIfActive()
	 *
	 * @return bool
	 */
	private function findOutIfActive_post_type_archive() {

		$post_type = $this->_menu_item_object;
		$post_type_taxes = get_taxonomies( [ 'object_type' => [ $post_type ] ] );

		// Have to do this first, as is_tax( [] ) will return true on any tax
		if( !empty( $post_type_taxes ) && is_tax( $post_type_taxes ) ) {
			return true;
		}

		if( is_singular( $post_type ) || is_post_type_archive( $post_type ) ) {
			return true;
		}

		return false;

	}

	/**
	 * For singular post pages
	 *
	 * Called poly in $this->findOutIfActive()
	 *
	 * @return bool
	 */
	private function findOutIfActive_post_type() {

		$post = get_post();
		if( $post instanceof \WP_Post ) {
			return get_post()->ID === (int)$this->object_id;
		}

		return false;

	}

	/**
	 * Is current menu item active
	 *
	 * @return bool
	 */
	public function isActive() {
		return $this->active;
	}

}