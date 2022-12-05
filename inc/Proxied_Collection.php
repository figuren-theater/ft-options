<?php
/**
 * Collection of WordPress Options
 * managed by the Plattform.
 *
 * @package Figuren_Theater\Options
 */

declare(strict_types=1);

namespace Figuren_Theater\Options;

use Figuren_Theater\Options\Interfaces;
use Figuren_Theater\SiteParts;

/**
 * Collection of WordPress Options
 * managed by the Plattform.
 *
 * @package Figuren_Theater\Options
 * @since   1.1
 */
final class Proxied_Collection extends SiteParts\ProxiedSitePartsCollectionAbstract {

	/**
	 * Checks wether it is allowed to add something to our collection.
	 * This should typically check for the implementation of needed interfaces,
	 * not do any checking on its values.
	 *
	 * @since  1.1
	 * 
	 * @param  mixed $input Could be anything,
	 *                      but are typically our SiteParts.
	 *
	 * @return bool
	 */
	protected function validate( $input ) : bool {
		return $input instanceof Interfaces\Option;
	}
}
