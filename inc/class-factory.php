<?php
/**
 * Factory to instantiate multiple Options at once.
 *
 * @package    figuren-theater\ft-options
 */

declare(strict_types=1);

namespace Figuren_Theater\Options;

/**
 * Factory to instantiate multiple Options at once.
 *
 * @package    Figuren_Theater\Options
 * @since      1.1
 * @since      2.10 Renamed from former 'Option__Factory'.
 */
class Factory {

	/**
	 * Factory to instantiate multiple Options at once.
	 *
	 * @since 1.1
	 *
	 * @param array<string, mixed> $options     Array of Options with names as indexes => and option values; Example: [ 'hack_file' => 1 ].
	 * @param string               $new_class   The name of the Option-Class to create, which defaults to a normal 'Option'.
	 *                                          Note that $class must be a full-path, valid address of class,
	 *                                          for example: `$class = 'app\models\MyClass'`.
	 * @param string               $origin      Where do this Option come from, either `'core'` or the usually the plugins basename, e.g. `'gutenberg/gutenberg.php'`.
	 * @param string               $type        The type of option (*in WordPress vocabulary*). Could be either `'option'` or `'site_option'`.
	 */
	public function __construct( array $options, $new_class = __NAMESPACE__ . '\\Option', $origin = 'core', $type = 'option' ) {
		if ( ! empty( $options ) ) {
			foreach ( $options as $key => $value ) {
				new $new_class( $key, $value, $origin, $type );
			}
		}
	}
}
