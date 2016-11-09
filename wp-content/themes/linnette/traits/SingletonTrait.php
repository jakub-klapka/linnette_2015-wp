<?php

namespace Linnette\Traits;

trait SingletonTrait {

	/**
	 * Return the only one instance of class
	 *
	 * @return $this
	 */
	public static function getInstance()
	{
		static $instance = null;
		if ( null === $instance ) {
			$instance = new static();
		}
		return $instance;
	}

}