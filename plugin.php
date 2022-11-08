<?php
declare(strict_types=1);

/**
 * Plugin Name:     figuren.theater | Options
 * Plugin URI:      https://github.com/figuren-theater/ft-options
 * Description:     Options Management for a WordPress Multisite like figuren.theater
 * Author:          Carsten Bach
 * Author URI:      https://figuren.theater
 * Text Domain:     ft-options
 * Domain Path:     /languages
 * Version:         1.1.3
 *
 * @package         Figuren_Theater\Options
 */

namespace Figuren_Theater\Options;

const DIRECTORY = __DIR__;

\add_action( 
	'Figuren_Theater\init', 
	function ( $ft_site ) : void {

		if ( ! is_a( $ft_site, 'Figuren_Theater\ProxiedSite' ))
			return;

		// Setup all Options as part of our Collection
		// 
		// 1. Create Collection 
		$collection = Collection::get_collection();

		// 2. Add all Options to the collection
		// This is done from inside each /Figuren_Theater/Options/Option

		// 3. Setup SitePart Manager for 'Options'
		$ft_site->set_Options_Manager( 
			// with its Collection
			new Manager( $collection)
		);
	},
	40
);
