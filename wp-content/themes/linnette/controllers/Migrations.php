<?php

namespace Linnette\Controllers;

use Linnette\Controllers\Migrations\Migration1ShortcodeConverter;
use Linnette\Traits\SingletonTrait;

class Migrations {
	use SingletonTrait;

	/**
	 * Register migrate route
	 */
	public function __construct() {

		\Routes::map( 'wp-admin/migrate', [ $this, 'maybeRunMigrations' ] );

	}

	/**
	 * Check for user privileges and run migrations, if he has 'manage_options'
	 */
	public function maybeRunMigrations() {

		if( current_user_can( 'manage_options' ) ) {

			$result = $this->runMigrations();
			$result .= $this->clearCaches();

			wp_die( $result );

		} else {
			wp_die( 'Nepovolený přístup' );
		}

	}

	private function clearCaches() {
		$out = '';

		update_option( 'rewrite_rules', '' );
		flush_rewrite_rules();
		$out .= 'Rewrite rules flushed.<br/>';

		if( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
			$out .= 'WP Super cache cleared.<br/>';
		}

		$cache_path = ABSPATH . 'wp-content/plugins/timber-library/cache/twig';
		if( is_dir( $cache_path ) ) {
			static::rrmdir( $cache_path );
			$out .= 'Twig cache cleared.<br/>';
		}

		if( class_exists( 'BWP_MINIFY' ) ) {
			$bwp = new \BWP_MINIFY();

			$deleted = 0;
			$cache_dir = !empty($cache_dir) ? $cache_dir : $bwp->get_cache_dir();
			$cache_dir = trailingslashit($cache_dir);

			if (is_dir($cache_dir))
			{
				if ($dh = opendir($cache_dir))
				{
					while (($file = readdir($dh)) !== false)
					{
						if (preg_match('/^minify_[a-z0-9\\.=_,]+(\.gz)?$/ui', $file)
						    || preg_match('/^minify-b\d+-[a-z0-9-_.]+(\.gz)?$/ui', $file)
						) {
							$deleted += true === @unlink($cache_dir . $file)
								? 1 : 0;
						}
					}
					closedir($dh);
				}
			}
			return $out .= 'Deleted: ' . $deleted . ' BWP minify files.<br/>';
		}

		return $out;
	}

	public static function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") self::rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}

	/**
	 * Run required migrations
	 *
	 * @return string
	 */
	public function runMigrations() {

		$result = '';
		$schema_version = get_option( 'theme_schema_version' );

		if( $schema_version == false || $schema_version < 1 ) {
			$result .= $this->migration_1();
			$schema_version = 1;
		};

//		update_option( 'theme_schema_version', $schema_version, false );
		return $result;

	}

	private function migration_1() {

//		require_once 'Migrations/Migration1ShortcodeConverter.php';



	}

}