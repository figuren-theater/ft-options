<?php
declare(strict_types=1);

/**
 * Plugin Name:     figuren-theater | Options
 * Plugin URI:      https://github.com/figuren-theater/ft-options
 * Description:     Options Management for WordPress Multisite
 * Author:          Carsten Bach
 * Author URI:      https://carsten-bach.de
 * Text Domain:     ft-options
 * Domain Path:     /languages
 * Version:         1.1.1
 *
 * @package         Figuren_Theater\Options
 */

namespace Figuren_Theater\Options;

const DIRECTORY = __DIR__;

// add_action( 'Figuren_Theater\\Services\\init', __NAMESPACE__ . '\\register' );

\add_action( 
	'Figuren_Theater\init', 
	function ( $ft_site ) : void {

		if ( ! is_a( $ft_site, 'Figuren_Theater\ProxiedSite' ))
			return;

		// 3. Setup all Options as part of our Collection
		// 3.1. Create Collection 
		// It's important, to do that before ADDing post_types,
		// to properly instantiate our collection.
		$collection = Collection::get_collection();

		// 3.2. Add all Options to the collection
		//      
		// This is done from inside each /Figuren_Theater/Options/Option

		// 3.3. Setup SitePart Manager for 'Options'
		// with its personal RegistrationHandler and our 
		// prepared Collection
		$ft_site->set_Options_Manager( new Manager( 
			// with its Collection
			$collection
		) );
	},
	40
);
