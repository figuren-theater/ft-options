<?php
/**
 * Manager for all options, site_options and their DB handling.
 *
 * @package figuren-theater\ft-options
 */

declare(strict_types=1);

namespace Figuren_Theater\Options;

use Figuren_Theater\SiteParts;

use function add_option;
use function apply_filters;
use function delete_option;
use function get_option;
use function wp_cache_delete;
use function wp_installing;
use function wp_list_filter;
use function wp_list_sort;
use function wp_next_scheduled;
use function wp_schedule_event;

/**
 * Manager for all options, site_options and their DB handling.
 *
 * Fundament of all SitePartManager classes.
 * SiteParts (in our situation) are
 * all the elements of our WordPress Site,
 * that we maybe want to change in certain situations.
 *
 * Theese SiteParts will be especially
 *  -- Plugins
 *  -- Options
 *  -- Taxonomies
 *  -- Post_Types
 *  -- RewriteRules
 *  -- UserRoles
 *  -- etc. ... (will be continued)
 *
 * @package Figuren_Theater\Options
 * @since  1.1
 */
class Manager extends SiteParts\SitePartsManagerAbstract {

	/**
	 * Returns an array of hooks that this subscriber wants to register with
	 * the WordPress plugin API.
	 *
	 * @since  1.1
	 *
	 * @return array<string, string|array<int, mixed>>
	 */
	public static function get_subscribed_events() : array {
		return [
			// Load early,
			// but not before the 'FeatureManager' enabled the basics
			// which happens at 'Figuren_Theater\loaded' 11
			// and the PluginsManager sent over all Plugin-Options
			// we should handle over here, which happens at 'Figuren_Theater\loaded' 12 .
			'Figuren_Theater\loaded'         => [ 'init', 13 ],

			// Register weekly action.
			'load-options-general.php' => 'register_cron_cleanup',
			// Hooked as sheduled action once a week.
			'ft_db_cleanup'            => 'run_cron_cleanup',
		];
	}

	/**
	 * Init our Manager onto WordPress
	 *
	 * This could mean 'register_something',
	 * 'add_filter_to_soemthing' or anything else,
	 * to do (probably on each SitePart inside the collection).
	 *
	 * This should be hooked into WP.
	 *
	 * @since  1.1
	 */
	public function init() : void {
		$_all_options = $this->collection->get();

		// sort managed options by type
		// to make sure
		// core-options are managed first
		//
		// because the definition of 'core' vs. 'PLUGIN-BASENAME' is stupid
		// so we've to get this in two rounds.

		// 1. core options
		$_core_options = wp_list_filter( $_all_options, [ 'origin' => 'core' ] );
		// 2. handle site_options before, options, so we need DESC
		$_core_options = wp_list_sort( $_core_options, 'type', 'DESC', true );
		// 3. get all other (aka plugin-) options
		$_other_options = wp_list_filter( $_all_options, [ 'origin' => 'core' ], 'NOT' );

		$_sorted_options = $_core_options + $_other_options;

		foreach ( $_sorted_options as $option ) {
			$option->load();
		}
	}

	/**
	 * Save all managed options to the DB,
	 * neither they will never be used because of our filtering-system.
	 *
	 * But some rough edge cases need the options to be in place in the DB,
	 * so we put them there on site-creation (e.g)
	 * and also on weekly maintenance.
	 *
	 * AND MORE IMPORTANT
	 * we unset 'autoload' to help overall performance on every site
	 *
	 * hevaily inspired by:
	 *
	 * @see https://kinsta.com/knowledgebase/wp-options-autoloaded-data/
	 * @see https://www.saotn.org/wordpress-wp-options-table-autoload-micro-optimization/
	 * @see https://wpshout.com/wp-option-autoload/
	 *
	 * @since  1.2
	 */
	public function new_set_and_cleanup_db() : void {

		// Make sure we start fresh.
		wp_cache_delete( 'alloptions', 'options' );

		// 1. get all options we handle
		// (2. array_diff against wp_load_alloptions() )
		foreach ( $this->collection->get() as $option ) {

			if ( ! $option->is_loaded() ) {
				continue;
			}

			switch ( $option->db_strategy ) {

				// v2 - hard version // !!
				case 'delete':
					// Make sure we start fresh,
					// just deleting the option entirely, without re-setting it.
					delete_option( $option->name );
					break;

				// v1 - soft version // !
				case 'un_autoload':
					// Remove filter to prevent infinite loop
					// when add_option() is called (in the next step)
					// inside of get_option() (where we are right now ;) !
					$option->unload();

					// Changing the autoload to "no" !
					$this->un_autoload_option( $option->name, $option->get_value() );

					$option->load();
					break;

				// Do nothing
				// if this should be autoloaded
				// or in case no $db_strategy is defined.
				case 'autoload':
				default:
					break;
			}
		}
	}

	/**
	 * Register cleanup cronjob within WordPress
	 *
	 * Save 'ft_db_cleanup' as weekly running job to the DB.
	 *
	 * @since     2.10
	 */
	public function register_cron_cleanup() : void {
		if ( ! wp_next_scheduled( 'ft_db_cleanup' ) && ! wp_installing() ) {
			wp_schedule_event( time(), 'weekly', 'ft_db_cleanup' );
		}
	}

	/**
	 * Multiple DB cleanup actions to run on a regurlar bases.
	 *
	 * 1. Autoload, Un-Autoload or Delete options managed by the Manager.
	 * 2. Un-Autoload Options, not handled directly by the Manager.
	 * 3. Delete Options, not handled directly by the Manager.
	 *
	 * @since     2.10
	 */
	public function run_cron_cleanup() : void {

		// 1. set autoload to "no" or delete all statically handled options
		$this->new_set_and_cleanup_db();

		// 2.
		// set autoload="no" for some un-managed options,
		// that are being filtered somehow later
		//
		//
		$this->un_autoload_options();

		// 3.
		// only delete options,
		// w/o adding a pre_option_ filter
		//
		// theese options are either 100% filtered or really old and deprecated
		// so they can be removed
		//
		// this was tooo hard for normal WP, got a fatal during install
		$this->delete_options();

	}

	/**
	 * Set the autoload field in the `wp_options` table to 'no' for multiple options at once.
	 *
	 * @since   2.10
	 *
	 * @param   string[]|array<string, mixed> $options Array of option-names, or array with option-names as indexes and option-values to set.
	 *
	 * @return void
	 */
	protected function un_autoload_options( array $options = [] ) : void {

		$options = ! empty( $options ) ? $options : [
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
			// 'active_plugins', // Totally needed, this should not be touched!
			// 'uninstall_plugins', // 'no' by default ??..
			// 'activate_plugins', // Totally needed, do not touch!
			'isc_storage',
		];

		// Create a nice name for the filter hook
		// (a little complicated, but useful).
		$_hook_name = join(
			'\\',
			[
				__NAMESPACE__,
				__CLASS__,
				__FUNCTION__,
			]
		);

		// Allow to use '$this' in an anonymous function, later on.
		$options_manager = $this;

		/**
		 * Filters the options before their autoload value will be set to 'no'.
		 *
		 * @since 2.10
		 *
		 * @param array   $options Array with all options to un-autoload.
		 * @param object  $options_manager The Options-Manager object.
		 */
		$options = apply_filters(
			$_hook_name,
			$options,
			$options_manager
		);

		array_walk(
			$options,
			function( string $option ) use ( $options_manager ) : void {
				$options_manager->un_autoload_option( $option );
			}
		);
	}

	/**
	 * Set the autoload field in the `wp_options` table to 'no' for one option.
	 * A value for the option can be provided or will be retrieved via `get_option()`.
	 *
	 * @since   2.10
	 *
	 * @param   string $name  Option to un-autoload.
	 * @param   mixed  $value Option value to save into the DB.
	 *
	 * @return  bool True when option was newly added with autoload='no', false on failure.
	 */
	protected function un_autoload_option( string $name, $value = null ) : bool {

		// Get a $value, if none.
		if ( null === $value ) {
			$value = get_option( $name, null );
		}
		// bail, if it's still NULL.
		if ( null === $value ) {
			return false;
		}

		// Otherwise do the work.

		// 'update_option' doesn't work
		// because of its $old_value==$new_value-comparison
		// \update_option( $name, $value, 'no' )
		//
		// so we go for delete_ and add_option()
		delete_option( $name );
		return add_option( $name, $value, '', 'no' );
	}

	/**
	 * Delete multiple options from the DB at once, by a list of option-names.
	 *
	 * @since  2.10
	 *
	 * @param  string[] $options Array of strings with all option-names to delete.
	 *
	 * @return void
	 */
	protected function delete_options( array $options = [] ) : void {

		$options = ! empty( $options ) ? $options : [

			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
			// 'widget_archives',
			// 'widget_block',
			// 'widget_calendar',
			// 'widget_categories',
			// 'widget_custom_html',
			// 'widget_koko-analytics-most-viewed-posts',
			// 'widget_media_audio',
			// 'widget_media_gallery',
			// 'widget_media_image',
			// 'widget_media_video',
			// 'widget_meta',
			// 'widget_nav_menu',
			// 'widget_pages',
			// 'widget_recent-posts',
			// 'widget_rss',
			// 'widget_search',
			// 'widget_tag_cloud',
			// 'widget_text',

			// 'hack_file',

			'mailserver_url',
			'mailserver_login',
			'mailserver_pass',
			'mailserver_port',
		];

		// Create a nice name for the filter hook
		// (a little complicated, but useful).
		$_hook_name = join(
			'\\',
			[
				__NAMESPACE__,
				__CLASS__,
				__FUNCTION__,
			]
		);

		// Allow to use '$this' in an anonymous function, later on.
		$options_manager = $this;

		/**
		 * Filters the options before they are deleted from the DB.
		 *
		 * @since  2.10
		 *
		 * @param  string[]  $options            Array with all options to delete.
		 * @param  object    $options_manager    This Manager object.
		 *
		 * @return string[]
		 */
		$options = apply_filters(
			$_hook_name,
			$options,
			$options_manager
		);

		\array_walk( $options, 'delete_option' );
	}

}



