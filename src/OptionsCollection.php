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
final class OptionsCollection extends SiteParts\SitePartsCollectionAbstract {

	/**
	 * Retrieve the non-static proxied OptionsCollection
	 *
	 * @since   1.1
	 *
	 * @return  ProxiedOptionsCollection The one-and-only static instance of our collection.
	 */
	public static function get_collection() : SiteParts\SitePartsCollectionInterface {
		static $collection = null;

		if ( null === $collection ) {
			// You can have arbitrary logic in here to decide what
			// implementation to use.
			$collection = new ProxiedOptionsCollection();
		}

		return $collection;
	}
}

/**
 * Register the OptionsCollection to the API, for instant availability.
 * 
 * Later, call it via the API like so:
 * `\Figuren_Theater\API::get('Options')->get|add|remove()`
 * 
 * 
 * Or call the collection the 'normal' way:
 * ```
 * OptionsCollection::add( 'some option', 'value'),
 * OptionsCollection::get( 'some option' ),
 * OptionsCollection::remove( 'some option' ),
 * OptionsCollection::get( 'myname' ),
 * ```
 */
\Figuren_Theater\API::add( 'Options', __NAMESPACE__ . '\\OptionsCollection::get_collection' );
