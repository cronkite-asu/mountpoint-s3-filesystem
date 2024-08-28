<?php
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
// Note: we're using `PHP_INT_MAX` for the priority because we want our `WP_Filesystem_Mountpoint_S3` class to always take precedence.

define( 'MOUNTPOINT_S3_FILESYSTEM_METHOD', 'Mountpoint_S3' );

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';

require_once __DIR__ . '/class-wp-filesystem-mountpoint-s3.php';

if ( ! defined( 'WP_RUN_CORE_TESTS' ) || ! WP_RUN_CORE_TESTS ) {
	add_filter( 'filesystem_method', function () {
		return MOUNTPOINT_S3_FILESYSTEM_METHOD; // The Mountpoint_S3 base class transparently handles using the direct filesystem as well as Mountpoint S3.
	}, PHP_INT_MAX );
}

add_filter( 'request_filesystem_credentials', function ( $credentials, $form_post, $type ) {
	// Handle the default `''` case which we'll override thanks to the `filesystem_method` filter.
	if ( '' === $type || MOUNTPOINT_S3_FILESYSTEM_METHOD === $type ) {
		if ( true === WPCOM_IS_VIP_ENV ) {
			$credentials = [
				new WP_Filesystem_Mountpoint_S3_Uploads( null ),
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
}, PHP_INT_MAX, 3 );

// Should't need this because we `require`-ed the class already.
// But just in case :)
add_filter( 'filesystem_method_file', function ( $file, $method ) {
	if ( 'Mountpoint_S3' === $method ) {
		$file = __DIR__ . '/class-wp-filesystem-mountpoint-s3.php';
	}
	return $file;
}, PHP_INT_MAX, 2 );
