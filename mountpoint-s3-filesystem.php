<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://local.local
 * @since             1.0.0
 * @package           Mountpoint_S3_Filesystem
 *
 * @wordpress-plugin
 * Plugin Name:       Mountpoint S3 Filesystem
 * Plugin URI:        https://local.local
 * Description:       Write files to AWS S3 as a directly mounted filesystem.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Jeremy Leggat
 * Author URI:        https://local.local/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mountpoint-s3-filesystem
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/cronkite-asu/mountpoint-s3-filesystem
 * Primary Branch:    main
 */

/**
 * With the filters below, our override should load automatically when `WP_Filesystem()` is called.
 *
 * Here is sample code on how to use $wp_filesystem:
 *
 *      global $wp_filesystem;
 *      if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
 *          $creds = request_filesystem_credentials( site_url()
 *          wp_filesystem($creds);
 *      }
 *      $wp_filesystem->put_contents( wp_get_upload_dir()['basedir'] . '/test.txt', 'this is a test file');
 *
 */
// Note: we're using `PHP_INT_MAX` for the priority because we want our `WP_Filesystem_VIP` class to always take precedence.

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

require_once __DIR__ . '/mountpoint-s3-filesystem/class-wp-filesystem-mountpoints3.php';
require_once __DIR__ . '/mountpoint-s3-filesystem/class-wp-filesystem-mountpoints3-uploads.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MOUNTPOINT_S3_FILESYSTEM_VERSION', '1.0.0' );

define( 'MOUNTPOINT_S3_FILESYSTEM_METHOD', 'MountpointS3' );

/**
 * Enable S3 Filesystem.
 * To enable set to true in wp-config.php
 * Default to false for other environments, e.g. local development.
 */
if ( ! defined( 'WORDPRESS_S3_FS' ) ) {
	define( 'WORDPRESS_S3_FS', false );
}

if ( ! defined( 'WP_RUN_CORE_TESTS' ) || ! WP_RUN_CORE_TESTS ) {
	add_filter( 'filesystem_method', function () {
		return MOUNTPOINT_S3_FILESYSTEM_METHOD; // The Mountpoint S3 base class transparently handles using the direct filesystem as well as the Mountpoint S3 File API
	}, PHP_INT_MAX );
}

add_filter( 'request_filesystem_credentials', function ( $credentials, $form_post, $type ) {
	error_log(__FUNCTION__ . ": type = $type");
	// Handle the default `''` case which we'll override thanks to the `filesystem_method` filter.
	if ( '' === $type || MOUNTPOINT_S3_FILESYSTEM_METHOD === $type ) {
		if ( true === WORDPRESS_S3_FS ) {
			$credentials = [
				new WP_Filesystem_MountpointS3_Uploads( null ),
				new WP_Filesystem_Direct( null ),
			];
		} else {
			// When not using Mountpoint S3 we'll pass direct to both. This means we'll still get the errors thrown when writes are done outside the /tmp and the uploads folder
			$credentials = [
				new WP_Filesystem_Direct( null ),
				new WP_Filesystem_Direct( null ),
			];
		}
	}
	return $credentials;
}, PHP_INT_MAX, 3 );

// Should't need this because we `require`-ed the class already.
// But just in case :)
add_filter( 'filesystem_method_file', function ( $file, $method ) {
	error_log(__FUNCTION__ . ": file = $file");
	error_log(__FUNCTION__ . ": method = $method");
	if ( MOUNTPOINT_S3_FILESYSTEM_METHOD === $method ) {
		$file = __DIR__ . '/mountpoint-s3-filesystem/class-wp-filesystem-mountpoints3.php';
	}
	return $file;
}, PHP_INT_MAX, 2 );

// ensure filename works with S3 FS
add_filter( 'sanitize_file_name', function ( $text ) {
		$text_encoding = mb_detect_encoding( $text );
		if ( 'ASCII' !== $text_encoding ) {
			// convert $text to ASCII
			$step1 = iconv( $text_encoding, 'ASCII//TRANSLIT', $text );
			// replace spaces and unknown characters with hyphens
			$step2 = sanitize_file_name( $step1 );
			$text = $step2;
		}

		return $text;
}, 10, 1 );

// ensure we always upload with year month folder layouts
add_filter( 'pre_option_uploads_use_yearmonth_folders', function () {
	return '1';
} );

error_log( 'filesystem_method: ' . get_filesystem_method() );
