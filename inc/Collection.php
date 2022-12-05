<?php
/**
 * Collection of all options and site_options managed by the plattform using a Static Proxy.
 * 
 * @package Figuren_Theater\Options
 */

declare(strict_types=1);

namespace Figuren_Theater\Options;

use Figuren_Theater\SiteParts;

/**
 * Collection of all options and site_options managed by the plattform using a Static Proxy.
 * 
 * @package Figuren_Theater\Options
 * @since   1.1
 */
final class Collection extends SiteParts\SitePartsCollectionAbstract {

	/**
	 * Retrieve the non-static proxied Collection
	 *
	 * @since   1.1
	 *
	 * @return  Proxied_Collection The one-and-only static instance of our collection.
	 */
	public static function get_collection() : SiteParts\SitePartsCollectionInterface {
		static $collection = null;

		if ( null === $collection ) {
			// You can have arbitrary logic in here to decide what
			// implementation to use.
			$collection = new Proxied_Collection();
		}

		return $collection;
	}
}

/**
 * Register the Collection to the API, for instant availability.
 * 
 * Later, call it via the API like so:
 * `\Figuren_Theater\API::get('Options')->get|add|remove()`
 * 
 * 
 * Or call the collection the 'normal' way:
 * ```
 * Collection::add( 'some option', 'value'),
 * Collection::get( 'some option' ),
 * Collection::remove( 'some option' ),
 * Collection::get( 'myname' ),
 * ```
 */
\Figuren_Theater\API::add( 'Options', __NAMESPACE__ . '\\Collection::get_collection' );
