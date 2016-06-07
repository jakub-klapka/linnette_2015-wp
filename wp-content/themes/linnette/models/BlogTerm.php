<?php

namespace Linnette\Models;


class BlogTerm extends \TimberTerm {

	/**
	 * @param int $tid
	 * @param string $tax
	 * @param bool $without_acf If true, we won't load meta from DB for this term
	 */
	public function __construct( $tid = null, $tax = '', $without_acf = false ) {
		if ( $tid === null ) {
			$tid = $this->get_term_from_query();
		}
		if ( strlen($tax) ) {
			$this->taxonomy = $tax;
		}
		$this->init( $tid, true );
	}

	public function current() {

		if( is_tax( $this->taxonomy, $this->term_id  ) ) return true;

		return false;

	}

	/**
	 * Acomodate cache reading
	 *
	 * @param $tid
	 * @param $taxonomy
	 *
	 * @return static
	 */
	public static function fromWithoutACF( $tid, $taxonomy ) {
		return new static( $tid, $taxonomy, true );
	}

	/**
	 * @internal
	 * @param int $tid
	 * @param bool $without_acf If true, don't read meta from DB
	 */
	protected function init( $tid, $without_acf = false ) {
		$term = $this->get_term($tid);
		if ( isset($term->id) ) {
			$term->ID = $term->id;
		} else if ( isset($term->term_id) ) {
			$term->ID = $term->term_id;
		} else if ( is_string($tid) ) {
			//echo 'bad call using '.$tid;
			//Helper::error_log(debug_backtrace());
		}
		if ( isset($term->ID) ) {
			$term->id = $term->ID;
			$this->import($term);

			//Modification here:
			if( !$without_acf ) {
				if ( isset($term->term_id) ) {
					$custom = $this->get_term_meta($term->term_id);
					$this->import($custom);
				}
			}

		}
	}

}