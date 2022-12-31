<?php
/**
 * Normal option, synced from a remote blog.
 *
 * @package Figuren_Theater\Options;
 */

declare(strict_types=1);

namespace Figuren_Theater\Options;

use Figuren_Theater\Options\Abstracts;

/**
 * Normal option, synced from a remote blog.
 *
 * @package Figuren_Theater\Options;
 * @since   2.0
 * @since   2.10 Refactored from old 'SyncFrom' class which implemented the 'SyncSource__Interface'.
 */
class Option_Synced extends Abstracts\Option {

	/**
	 * Blog ID where to retrieve this option from.
	 * 
	 * @var int
	 */
	protected int $remote_blog_id = 0;

	/**
	 * Define the value of this option.
	 *
	 * And prepare the ID of the remote blog,
	 * where to sync this option from.
	 *
	 * @package    Figuren_Theater\Options\Interfaces
	 * @since      2.10
	 *
	 * @param      mixed $value The options value.
	 *
	 * @return     bool Whether value is set or not.
	 */
	public function set_value( $value ) : bool {
		if ( parent::set_value( $value ) )
			$this->set_remote_blog_id();

		return (bool) $this->remote_blog_id;
	}

	/**
	 * Define the source blog, where to retrieve this option from.
	 *
	 * Defaults to get all synced option values from https://figuren.theater, 
	 * which can be filtered seperately per option using 
	 * 'Figuren_Theater\Options\Option_Synced\{$option_name}\remote_blog_id'
	 * or for all options at once using
	 * 'Figuren_Theater\Options\Option_Synced\remote_blog_id'.
	 *
	 * @since      2.10
	 * @since      2.12 Added filter to set remote_blog_id for all options at once.
	 *
	 * @param      int $remote_blog_id Blog ID where to retrieve this option from. Defaults to https://figuren.theater.
	 */
	public function set_remote_blog_id( int $remote_blog_id = 1 ) : void {
	

		// create a nice name for the filter hook 
		// a little complicated, but useful
		$_hook_name = join(
			'\\',
			[
				__NAMESPACE__,
				__CLASS__,
				$this->name,
				'remote_blog_id',
			]
		);

		/**
		 * Filters the remote blog, where to retrieve the option from.
		 *
		 * The dynamic portion of the hook name, `$this->name`, refers to the option name.
		 *
		 * @since 2.10
		 *
		 * @param int     $id    Blog ID.
		 * @param object  $this  This option object.
		 */
		$remote_blog_id = (int) \apply_filters( 
			$_hook_name, 
			$remote_blog_id, 
			$this
		);


		// create a nice name for the filter hook 
		// a little complicated, but useful
		$_hook_name = join(
			'\\',
			[
				__NAMESPACE__,
				__CLASS__,
				'remote_blog_id',
			]
		);

		/**
		 * Filters the remote blog, where to retrieve the option from.
		 *
		 * @since 2.12
		 *
		 * @param int     $id    Blog ID.
		 * @param object  $this  This option object.
		 */
		$remote_blog_id = (int) \apply_filters( 
			$_hook_name, 
			$remote_blog_id, 
			$this
		);

		// everything ok
		if ( ! empty( $remote_blog_id ) )
			$this->remote_blog_id = $remote_blog_id;
	}


	/**
	 * Get Option value from a remote blog.
	 *
	 * @package    Figuren_Theater\Options\Interfaces
	 * @since      2.10
	 *
	 * @return     mixed|null Returns any option saved with the same name from the remote blog.
	 */
	public function get_value() : mixed {

		if ( empty( $this->remote_blog_id ) )
			return null;

		// remove filter to prevent infinite loop 
		// inside of get_option() (where we are right now ;)
		$this->unload();

		// get DB option from blog with given ID
		$r = \get_blog_option( $this->remote_blog_id, $this->name );
		
		// re-add filter
		$this->load();

		return $r;
	}

	/**
	 * Whether this option should be filtered or not.
	 *
	 * Returns false if current site is the site to sync from.
	 *
	 * @since      2.10
	 *
	 * @return     bool       Allowed to sync this option.
	 */
	public function should_load() : bool {
		return ! empty( $this->filter_hook ) && \get_current_blog_id() !== $this->remote_blog_id;
	}
}
