<?php
/**
 * Normal option, merged from the DB and static values provied by this option.
 *
 * @package Figuren_Theater\Options;
 */

declare(strict_types=1);

namespace Figuren_Theater\Options;

use Figuren_Theater\Options\Abstracts;

/**
 * Normal option, merged from the DB and static values provied by this option.
 *
 * @package Figuren_Theater\Options;
 * @since   2.0
 * @since   2.10 Refactored from old 'SyncAndMerge' class which implemented the 'ArrayAccess' and 'SyncAndMerge__Interface'.
 */
class Merged_Option extends Abstracts\Option {

	/**
	 * Load something.
	 *
	 * @package Figuren_Theater\Network\Interfaces\Loadable
	 * @since   2.10
	 *
	 * @return bool TRUE on success, FALSE otherwise.
	 */
	public function load() : bool {
		
		if ( ! $this->should_load() )
			return false;

		// this should help saving
		$_this = $this;
		\add_filter( 
			"default_{$this->identifier}",
			static function() use ( $_this ) {
				return $_this->value;
			},
			$this->filter_priority,
			$this->filter_arguments,
		);

		parent::load();

		return $this->loaded;
	}

	/**
	 * Get merged Option value from DB and some static values.
	 *
	 * @package    Figuren_Theater\Options\Interfaces
	 * @since      2.10
	 *
	 * @return     mixed|null Returns any option saved with the same name
	 */
	public function get_value() : mixed {


		// remove filter to prevent infinite loop 
		// inside of get_option() (where we are right now ;)
		$this->unload();

		// get DB option from blog with given ID
		$_db_option = \get_option( $this->name );

		// re-add filter
		$this->load();

		if ( ! is_array( $_db_option ) || empty( $_db_option ) )
			return $this->value;

		// else ...
		return array_merge( $_db_option, $this->value );
	}

}
