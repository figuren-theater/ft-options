<?php
/**
 * Interface to load and unload things.
 *
 * @package figuren-theater\ft-options
 */

namespace Figuren_Theater\Options\Interfaces;

/**
 * Interface to load and unload things.
 *
 * Details are left to the constructor of the implementing classes.
 *
 * @since   2.10
 *
 * @author  toscho
 * @see     https://github.com/thefuxia/t5-libraries/blob/master/Core/Resources/Loadable.php
 * @version 2013.12.25
 * @license MIT
 */
interface Loadable {
	/**
	 * Load something.
	 *
	 * @package figuren-theater\ft-options
	 * @since   2.10
	 *
	 * @return bool TRUE on success, FALSE otherwise.
	 */
	public function load(): bool;

	/**
	 * Unload.
	 *
	 * @package figuren-theater\ft-options
	 * @since   2.10
	 *
	 * @return bool TRUE on success, FALSE otherwise.
	 */
	public function unload(): bool;

	/**
	 * Whether or not the resource has been loaded already.
	 *
	 * @package figuren-theater\ft-options
	 * @since   2.10
	 *
	 * @return bool
	 */
	public function is_loaded(): bool;
}
