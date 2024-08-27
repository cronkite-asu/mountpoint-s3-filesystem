<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://local.local
 * @since      1.0.0
 *
 * @package    Mountpoint_S3_Filesystem
 * @subpackage Mountpoint_S3_Filesystem/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mountpoint_S3_Filesystem
 * @subpackage Mountpoint_S3_Filesystem/includes
 * @author     Jeremy Leggat <jleggat@asu.edu>
 */
class Mountpoint_S3_Filesystem_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mountpoint-s3-filesystem',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
