<?php
/**
 * Abstract Option class which can be used as is, or extended.
 *
 * @package figuren-theater\ft-options
 */

namespace Figuren_Theater\Options\Abstracts;

use Figuren_Theater\Options\Interfaces as Options_Interfaces;

/**
 * Abstract Option class which can be used as is, or extended.
 *
 * @package Figuren_Theater\Options\Abstracts
 */
abstract class Option implements Options_Interfaces\Loadable, Options_Interfaces\Option {

	/**
	 * The name of the option.
	 *
	 * @var string
	 */
	public string $name;

	/**
	 * The value of the option.
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 * Origin this Option comes from, could be
	 * either 'core' or the usually the plugins basename,
	 * e.g. 'gutenberg/gutenberg.php'.
	 *
	 * @var string
	 */
	public string $origin = 'core';

	/**
	 * The type of option (in WordPress vocabulary).
	 * Could be either 'option' or 'site_option'.
	 *
	 * @var string
	 */
	public string $type = 'option';

	/**
	 * Identifier for this option, as it is used as the unique name inside the Collection.
	 *
	 * And for some ... reasons we often need this string like that.
	 *
	 * @var string
	 */
	public string $identifier;

	/**
	 * Filter hook to use
	 * to handle this special option
	 *
	 * Possible values are:
	 * - `pre_option_static`
	 * - `pre_site_option_static`
	 * - ..
	 *
	 * @var string
	 */
	public string $filter_hook;

	/**
	 * Callable function to return this options value.
	 *
	 * @var callable
	 */
	public $filter_callback;

	/**
	 * The Priority add which to load this filter
	 *
	 * @var int
	 */
	public int $filter_priority = 0;

	/**
	 * The number of arguments this filter can handle.
	 *
	 * Typically normal options take 3, site_options take 4 arguments.
	 *
	 * @var int
	 */
	public int $filter_arguments = 3;

	/**
	 * Whether or not the resource has been loaded already.
	 *
	 * @var bool
	 */
	protected bool $loaded = false;

	/**
	 * What to do with the option inside the DB.
	 *
	 * Could be one of:
	 * - 'un_autoload' (default)
	 * - 'autoload'
	 * - 'delete'
	 *
	 * @var string
	 */
	public string $db_strategy = 'un_autoload';

	/**
	 * Creator of each new, managed option - the construtor.
	 *
	 * @since   2.10
	 *
	 * @param   string $name   The name of the option to manage.
	 * @param   mixed  $value  Any option value.
	 * @param   string $origin Could be either 'core' or usually the plugins basename this options belongs to.
	 * @param   string $type   The type of option, could be either 'option' or 'site_option'.
	 */
	public function __construct( $name, $value, $origin = 'core', $type = 'option' ) {

		if (
			$this->set_name( $name ) &&
			$this->set_value( $value ) &&
			$this->set_origin( $origin ) &&
			$this->set_type( $type )
		) {
			// Define identifier,
			// we often need this string like that.
			$this->identifier = join( '_', [ $this->type, $this->name ] );

			// Set the filter hook, to which the filtered option will be returned.
			$this->filter_hook = join( '_', [ 'pre', $this->identifier ] );

			// Set action, to be used as callback for the pre-option-filter.
			$this->set_filter_callback();

			// We survived all error checking and
			// can safely add this option
			// to our collection.
			$this->add_to_collection();
		}
	}

	/**
	 * Defines the name of the option.
	 *
	 * The name may be or is already used as a *meta_key* in the `wp_options` or `wp_sitemeta` DB tables.
	 *
	 * @package    Figuren_Theater\Options\Interfaces
	 * @since      1.1
	 *
	 * @param      string $name Option name to set.
	 *
	 * @return     string The name of the option, without any prefixes, just the name.
	 */
	public function set_name( string $name ) : string {
		// Guard clauses are safer than type casting on the function|method calls,
		// because we avoid fatal PHP errors.
		if ( empty( $name ) ) {
			return '';
		}
		$this->name = $name;
		return $this->name;
	}

	/**
	 * Defines the value of this option.
	 *
	 * @package    Figuren_Theater\Options\Interfaces
	 * @since      1.1
	 *
	 * @param      mixed $value The options value to set.
	 *
	 * @return     bool Whether value is set or not.
	 */
	public function set_value( $value ) : bool {
		// Check only against type of FALSE
		// to keep the possibility for the value to be '0' (zero).
		if ( false !== $value ) {
			$this->value = $value;
			// Do not return the value,
			// in case it is zero.
			return true;
		}
		return false;
	}

	/**
	 * Defines where this Option comes from, could be
	 * either 'core' or the usually the plugins basename,
	 * e.g. 'gutenberg/gutenberg.php'.
	 *
	 * @since  1.1
	 *
	 * @param  string $origin Origin of this option, 'core' or '{$plugin_basename}'.
	 *
	 * @return string Could only be `'core'` or some `{$plugin_basename}`.
	 */
	public function set_origin( string $origin ) : string {
		if ( empty( $origin ) ) {
			return '';
		}
		$this->origin = $origin;
		return $this->origin;
	}

	/**
	 * Defines the type of option (in WordPress vocabulary).
	 *
	 * @since  1.1
	 *
	 * @param  string $type Allowed values are only: `'option'` or `'site_option'`.
	 *
	 * @return string Could only be `'option'` or `'site_option'`.
	 */
	public function set_type( $type ) : string {
		if ( ! in_array( $type, [ 'option', 'site_option' ], true ) ) {
			return '';
		}
		$this->type = $type;
		// Unset db_strategy for site_options.
		$this->db_strategy = 'option' === $type ? $this->db_strategy : '';

		return $this->type;
	}

	/**
	 * Define which method to use, when filtering this option.
	 *
	 * @since   2.10
	 *
	 * @param   callable $callback Callable function or method to use as action, to hook onto this options filter.
	 */
	public function set_filter_callback( $callback = null ) : void {

		// This is the default.
		$this->filter_callback = [ $this, 'get_value' ];

		if ( is_callable( $callback ) ) {
			$this->filter_callback = $callback;
		}
	}

	/**
	 * Get Option value.
	 *
	 * @package    Figuren_Theater\Options\Interfaces
	 * @since      1.1
	 * @since      2.10 Duplicated existing fn into the Interface.
	 *
	 * @return     mixed|null Returns any option with the same name.
	 */
	public function get_value() : mixed {
		return $this->value;
	}

	/**
	 * Adds this Option to the global Collection.
	 *
	 * @since   1.1
	 */
	protected function add_to_collection() : void {
		\Figuren_Theater\API::get( 'Options' )->add( $this->identifier, $this );
	}

	/**
	 * Load something.
	 *
	 * @package Figuren_Theater\Core\Loadable
	 * @since   2.10
	 *
	 * @return bool TRUE on success, FALSE otherwise.
	 */
	public function load() : bool {
		if ( $this->should_load() ) {
			// this could be a little tricky because
			// add_filter always returns true!
			$this->loaded = \add_filter(
				$this->filter_hook,
				$this->filter_callback,
				$this->filter_priority,
				$this->filter_arguments
			);
		}
		return $this->loaded;
	}

	/**
	 * Unload.
	 *
	 * @package Figuren_Theater\Core\Loadable
	 * @since   2.10
	 *
	 * @return bool TRUE on success, FALSE otherwise.
	 */
	public function unload() : bool {

		// Remove filter to prevent infinite loop
		// inside of get_option() (where we are right now ;) !
		$_removed = \remove_filter(
			$this->filter_hook,
			$this->filter_callback,
			$this->filter_priority
		);

		$this->loaded = ! $_removed;
		return $_removed;
	}

	/**
	 * Whether or not the resource has been loaded already.
	 *
	 * @package Figuren_Theater\Core\Loadable
	 * @since   2.10
	 *
	 * @return bool
	 */
	public function is_loaded() : bool {
		return $this->loaded;
	}

	/**
	 * Whether this option should be filtered or not.
	 *
	 * @since      2.10
	 *
	 * @return     bool       Allowed to filter.
	 */
	public function should_load() : bool {
		return ! empty( $this->filter_hook );
	}
}
