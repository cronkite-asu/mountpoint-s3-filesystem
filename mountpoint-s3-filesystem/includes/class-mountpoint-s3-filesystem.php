<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://local.local
 * @since      1.0.0
 *
 * @package    Mountpoint_S3_Filesystem
 * @subpackage Mountpoint_S3_Filesystem/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mountpoint_S3_Filesystem
 * @subpackage Mountpoint_S3_Filesystem/includes
 * @author     Jeremy Leggat <jleggat@asu.edu>
 */
class Mountpoint_S3_Filesystem {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Mountpoint_S3_Filesystem_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Filesystem to use to override.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $filesystem_method    The filesystem to use.
	 */
	protected $filesystem_method = 'MountpointS3';

	/**
	 * Max length allowed for file paths in the Files Service.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      number    $max_file_path_length    The max length of file path.
	 */
	protected $max_file_path_length = 1024;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MOUNTPOINT_S3_FILESYSTEM_VERSION' ) ) {
			$this->version = MOUNTPOINT_S3_FILESYSTEM_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'mountpoint-s3-filesystem';

		$this->load_dependencies();
		$this->set_filesystem();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Mountpoint_S3_Filesystem_Loader. Orchestrates the hooks of the plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mountpoint-s3-filesystem-loader.php';

		/**
		 * WordPress's filesystem API class.
		 *
		 * We need to make sure this is loaded before we can load the custom api.
		 */
		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		/**
		 * WordPress's base filesystem API class.
		 *
		 * We need to make sure this is loaded before we can load the custom api.
		 */
		require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );

		/**
		 * WordPress's direct filesystem API class.
		 *
		 * We need to make sure this is loaded before we can load the custom api.
		 */
		require_once( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );

		/**
		 * The filesystem API shim that uses Mountpoint S3 filesystems.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-filesystem-mountpoints3.php';

		/**
		 * The filesystem API shim that uses Mountpoint S3 filesystems directly.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-filesystem-mountpoints3-uploads.php';

		$this->loader = new Mountpoint_S3_Filesystem_Loader();

	}

	/**
	 * Init the filesystem.
	 *
	 * Register the filesystem with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_filesystem() {
		error_log( __FUNCTION__ . ': ' . get_filesystem_method() );
		// Note: we're using `PHP_INT_MAX` for the priority because we want our `WP_Filesystem_MountpointS3` class to always take precedence.
		if ( ! defined( 'WP_RUN_CORE_TESTS' ) || ! WP_RUN_CORE_TESTS ) {
			$this->loader->add_filter( 'filesystem_method', $this, 'get_filesystem_method', PHP_INT_MAX, 4 );
		}

		$this->loader->add_filter( 'request_filesystem_credentials', $this, 'get_filesystem_credentials', PHP_INT_MAX, 3 );
		$this->loader->add_filter( 'sanitize_file_name', $this, 'sanitize_filename' );

		// ensure we always upload with year month folder layouts
		$this->loader->add_filter( 'pre_option_uploads_use_yearmonth_folders', $this, 'get_use_yearmonth_folders' );

		// Should't need this because we `require`-ed the class already.
		// But just in case :)
		$this->loader->add_filter( 'filesystem_method_file', $this, 'get_filesystem_method_file', PHP_INT_MAX, 2 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Mountpoint_S3_Filesystem_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Use to force uploads sorted in folders.
	 *
	 * @since     1.0.0
	 * @return    string    1
	 */
	public function get_use_yearmonth_folders() {
		return '1';
	}

	/**
	 * Make sure the returned filename is allowed on all file systems.
	 *
	 * @param string  $text
	 *
	 * @return string Transliterated filename
	 */
	public function sanitize_filename( $text ) {
		$text_encoding = mb_detect_encoding( $text );
		if ( 'ASCII' !== $text_encoding ) {
			// convert $text to ASCII
			$step1 = iconv( $text_encoding, 'ASCII//TRANSLIT', $text );
			// replace spaces and unknown characters with hyphens
			$step2 = sanitize_file_name( $step1 );
			$text = $step2;
		}

		return $text;
	}

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

	/**
	 * Retrieve the filesystem method of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The filesystem method.
	 */
	public function get_filesystem_method($method, $args, $context, $allow_relaxed_file_ownership) {
		error_log(__FUNCTION__ . ": filesystem_method = $method");
		return $this->filesystem_method; // The Mountpoint_S3 base class transparently handles using the direct filesystem as well as Mountpoint S3.
	}

	/**
	 * Retrieve the filesystem method file path.
	 *
	 * @since     1.0.0
	 * @return    array    The filesystem method file and method.
	 */
	public function get_filesystem_method_file( $file, $method ) {
		error_log(__FUNCTION__ . ": file = $file");
		error_log(__FUNCTION__ . ": method = $method");
		if ( $this->filesystem_method === $method ) {
			$file = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-filesystem-mountpoints3.php';
		}
		return $file;
	}

	/**
	 * Get file system credentials.
	 *
	 * @param array   $credentials
	 * @param string  $form_post
	 * @param string  $type
	 *
	 * @return array The filled credentials
	 */
	public function get_filesystem_credentials( $credentials, $form_post, $type ) {
		error_log(__FUNCTION__ . ": type = $type");
		// Handle the default `''` case which we'll override thanks to the `filesystem_method` filter.
		if ( '' === $type || $this->filesystem_method === $type ) {
			if ( defined( 'WORDPRESS_S3_FS' ) && true === WORDPRESS_S3_FS ) {
				$credentials = [
					new WP_Filesystem_MountpointS3_Uploads( null ),
					new WP_Filesystem_Direct( null ),
				];
			} else {
				// When not on Mountpoint S3 we'll pass direct to both. This means we'll still filter uploads folder but use direct regardless.
				$credentials = [
					new WP_Filesystem_Direct( null ),
					new WP_Filesystem_Direct( null ),
				];
			}
		}
		return $credentials;
	}

}
