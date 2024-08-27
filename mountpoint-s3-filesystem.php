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

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MOUNTPOINT_S3_FILESYSTEM_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mountpoint-s3-filesystem-activator.php
 */
function activate_mountpoint_s3_filesystem() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mountpoint-s3-filesystem-activator.php';
	Mountpoint_S3_Filesystem_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mountpoint-s3-filesystem-deactivator.php
 */
function deactivate_mountpoint_s3_filesystem() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mountpoint-s3-filesystem-deactivator.php';
	Mountpoint_S3_Filesystem_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mountpoint_s3_filesystem' );
register_deactivation_hook( __FILE__, 'deactivate_mountpoint_s3_filesystem' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mountpoint-s3-filesystem.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mountpoint_s3_filesystem() {

	$plugin = new Mountpoint_S3_Filesystem();
	$plugin->run();

}
run_mountpoint_s3_filesystem();
