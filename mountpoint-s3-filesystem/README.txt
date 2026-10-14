=== Mountpoint S3 Filesystem ===
Contributors: onecooltaco
Tags: amazon s3, aws, filesystem, media, uploads
Requires at least: 5.2
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Routes WordPress upload filesystem operations through a preconfigured Mountpoint for Amazon S3 mount.

== Description ==

Mountpoint S3 Filesystem provides a custom WordPress filesystem adapter for hosting environments where `wp-content/uploads` is backed by Mountpoint for Amazon S3.

Operations inside the uploads directory use the Mountpoint-backed transport. Operations elsewhere continue to use WordPress's direct local filesystem transport.

This plugin does not install Mountpoint, mount an S3 bucket, configure AWS credentials, or provision IAM permissions. Configure those infrastructure components before activating the S3-backed adapter.

== Installation ==

1. Install and configure Mountpoint for Amazon S3 on the WordPress host.
2. Mount the intended S3 bucket or prefix at `wp-content/uploads`.
3. Upload the plugin files to `/wp-content/plugins/mountpoint-s3-filesystem`.
4. Activate the plugin in WordPress.
5. Add `define( 'WORDPRESS_S3_FS', true );` to `wp-config.php`.
6. Test uploads, image processing, file deletion, and backup workflows before production use.

== Frequently Asked Questions ==

= Does this plugin mount the bucket? =

No. Mountpoint installation, mounting, credentials, and IAM permissions are infrastructure responsibilities.

= What happens when WORDPRESS_S3_FS is false? =

The adapter uses the direct local filesystem for both uploads and other paths.

= Does it support every POSIX filesystem operation? =

No. S3 and Mountpoint do not provide all local-filesystem semantics. Permission, ownership, and timestamp methods are unsupported by the uploads transport.

== Changelog ==

= 1.0.0 =

* Initial release.
