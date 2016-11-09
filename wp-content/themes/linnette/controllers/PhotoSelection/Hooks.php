<?php

namespace Linnette\Controllers\PhotoSelection;

use Linnette\Traits\SingletonTrait;

class Hooks {
	use SingletonTrait;

	/**
	 * Actions to register
	 *
	 * @var array
	 */
	private $actions;

	/**
	 * Filters to register
	 *
	 * @var array
	 */
	private $filters;

	/**
	 * Actions to register only if is_admin
	 *
	 * @var array
	 */
	private $adminActions;

	/**
	 * Filters to register only if is_admin
	 *
	 * @var array
	 */
	private $adminFilters;

	/**
	 * Set all required actions and filters here
	 *
	 */
	public function __construct() {

		$this->actions = [
			'init' => [
				[ CoreSetup::class, 'registerCustomPostType' ]
			],
			'acf/init' => [
				[ CoreSetup::class, 'addPostEditAcf' ]
			],
			'save_post_photo_selection' => [
				[ HandleFrontendAccess::class, 'createAccessToken' ]
			]
		];

		$this->filters = [
			'post_type_link' => [
				[ HandleFrontendAccess::class, 'maybeModifyPermalink', 10, 4 ]
			]
		];

		$this->adminActions = [];

		$this->adminFilters = [
			'manage_photo_selection_posts_columns' => [
				[ AdminSetup::class, 'removeSubscribeColumnFromEditScreen', 15 ]
			]
		];

	}

	/**
	 * Will actualy register all actions and filters
	 *
	 */
	private function processRegistration() {

		foreach ( $this->actions as $action_name => $items ) {
			foreach ( $items as $item ) {
				$instance = call_user_func( [ $item[0], 'getInstance' ] );
				add_action( $action_name, [ $instance, $item[1] ], ( isset( $item[2] ) ) ? $item[2] : 10, ( isset( $item[3] ) ) ? $item[3] : 1 );
			}
		}

		foreach ( $this->filters as $filter_name => $items ) {
			foreach ( $items as $item ) {
				$instance = call_user_func( [ $item[0], 'getInstance' ] );
				add_filter( $filter_name, [ $instance, $item[1] ], ( isset( $item[2] ) ) ? $item[2] : 10, ( isset( $item[3] ) ) ? $item[3] : 1 );
			}
		}

		if( is_admin() ) {

			foreach ( $this->adminActions as $action_name => $items ) {
				foreach ( $items as $item ) {
					$instance = call_user_func( [ $item[0], 'getInstance' ] );
					add_action( $action_name, [ $instance, $item[1] ], ( isset( $item[2] ) ) ? $item[2] : 10, ( isset( $item[3] ) ) ? $item[3] : 1 );
				}
			}

			foreach ( $this->adminFilters as $filter_name => $items ) {
				foreach ( $items as $item ) {
					$instance = call_user_func( [ $item[0], 'getInstance' ] );
					add_filter( $filter_name, [ $instance, $item[1] ], ( isset( $item[2] ) ) ? $item[2] : 10, ( isset( $item[3] ) ) ? $item[3] : 1 );
				}
			}

		}

	}

	/**
	 * Create this instance and run registration of all hooks
	 *
	 */
	public static function registerHooks() {

		$instance = static::getInstance();
		$instance->processRegistration();

	}

}