<?php

namespace Linnette\Models;


class BlogTerm extends \TimberTerm {

	public function current() {

		if( is_tax( $this->taxonomy, $this->term_id  ) ) return true;

		return false;

	}

}