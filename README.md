# Mountpoint S3 Filesystem

A WordPress filesystem adapter for installations whose uploads directory is backed by [Mountpoint for Amazon S3](https://github.com/awslabs/mountpoint-s3).

The plugin routes WordPress filesystem operations inside `wp-content/uploads` to the mounted S3 filesystem and keeps operations elsewhere on the server's local filesystem. It is intended for controlled hosting environments, not as a general-purpose S3 media library plugin.

## How it works

- Registers a custom `WP_Filesystem` method.
- Uses the Mountpoint-backed uploads directory for media operations.
- Falls back to `WP_Filesystem_Direct` outside the uploads directory.
- Normalizes non-ASCII upload filenames and enforces year/month upload folders.

The plugin does **not** install Mountpoint, mount a bucket, or configure AWS credentials. Those tasks must be completed at the operating-system or infrastructure layer first.

## Requirements

- WordPress 5.2 or newer
- PHP 7.2 or newer with `mbstring` and `iconv`
- A Linux host with Mountpoint for Amazon S3 installed
- An S3 bucket mounted at the WordPress uploads path
- AWS credentials and IAM permissions appropriate for the required file operations

## Installation

1. Configure Mountpoint so the target bucket or prefix is mounted at `wp-content/uploads`.
2. Copy this repository into `wp-content/plugins/mountpoint-s3-filesystem`.
3. Activate **Mountpoint S3 Filesystem** in WordPress.
4. Enable the S3-backed adapter in `wp-config.php`:

   ```php
   define( 'WORDPRESS_S3_FS', true );
   ```

When `WORDPRESS_S3_FS` is absent or `false`, the adapter continues to use the direct local filesystem.

## Important limitations

Amazon S3 is not a POSIX filesystem, and Mountpoint intentionally does not implement every filesystem operation. Permission and ownership methods such as `chmod()`, `chown()`, `chgrp()`, and `touch()` are treated as unsupported for the uploads transport. Test media workflows, image processing, backup tools, and plugins that manipulate uploaded files before using this in production.

## Development

The distributable plugin consists of the root bootstrap file and the `mountpoint-s3-filesystem/` directory. Keep the WordPress.org-style `README.txt` in that directory aligned with release metadata when publishing a release.

## License

GPL-2.0-or-later. See [`mountpoint-s3-filesystem/LICENSE.txt`](mountpoint-s3-filesystem/LICENSE.txt).
