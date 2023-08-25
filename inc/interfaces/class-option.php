<?php
/**
 * Basic contract for options.
 *
 * @package figuren-theater\ft-options
 */

namespace Figuren_Theater\Options\Interfaces;

/**
 * Basic contract for options.
 *
 * @package figuren-theater\ft-options
 */
interface Option {

	/**
	 * Defines the name of the option.
	 *
	 * The name may be or is already used as a *meta_key* in the `wp_options` or `wp_sitemeta` DB tables.
	 *
	 * @package    figuren-theater\ft-options
	 * @since      1.1
	 *
	 * @param      string $name Option name to set.
	 *
	 * @return     string The name of the option, without any prefixes, just the name.
	 */
	public function set_name( $name ) : string;

	/**
	 * Defines the value of this option.
	 *
	 * @package    figuren-theater\ft-options
	 * @since      1.1
	 *
	 * @param      mixed $value The options value to set.
	 *
	 * @return     bool Whether value is set or not.
	 */
	public function set_value( $value ) : bool;

	/**
	 * Get Option value.
	 *
	 * @package    figuren-theater\ft-options
	 * @since      2.10
	 *
	 * @return     mixed|null Returns any option with the same name.
	 */
	public function get_value() : mixed;
}
