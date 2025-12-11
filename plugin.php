<?php
/**
 * Options Management for a WordPress multisite network like figuren.theater
 *
 * @package           figuren-theater/ft-options
 * @author            figuren.theater
 * @copyright         2023 figuren.theater
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       figuren.theater | Options
 * Plugin URI:        https://github.com/figuren-theater/ft-options
 * Description:       Options Management for a WordPress multisite network like figuren.theater
 * Version:           1.2.6
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            figuren.theater
 * Author URI:        https://figuren.theater
 * Text Domain:       figurentheater
 * Domain Path:       /languages
 * License:           GPL v3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Update URI:        https://github.com/figuren-theater/ft-options
 */

declare(strict_types=1);

namespace Figuren_Theater\Options;

const DIRECTORY = __DIR__;

/**
 * Setup all Options as part of our Collection.
 */
\add_action(
	'Figuren_Theater\init',
	// phpcs:ignore Universal.FunctionDeclarations.NoLongClosures.ExceedsRecommended
	function ( $ft_site ): void {

		if ( ! is_a( $ft_site, 'Figuren_Theater\ProxiedSite' ) ) {
			return;
		}

		// 1. Create Collection
		$collection = Collection::get_collection();

		// 2. Add all Options to the collection
		// This is done from inside each /Figuren_Theater/Options/Option

		// 3. Setup SitePart Manager for 'Options'
		// with its Collection.
		$ft_site->set_Options_Manager( new Manager( $collection ) );

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
	},
	40
);
