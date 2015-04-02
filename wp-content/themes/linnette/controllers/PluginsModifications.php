<?php


namespace linnette\controllers;

/**
 * Singleton Class PluginsModifications
 * @package linnette\controllers
 */
class PluginsModifications {

	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

	protected function __construct() {

		/*
		 * Yoast
		 */
		add_filter('disable_wpseo_json_ld_search', '__return_true');

	}

}