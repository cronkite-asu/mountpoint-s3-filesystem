<?php

# copied from https://github.com/Automattic/vip-go-mu-plugins/blob/develop/files/class-wp-filesystem-vip-uploads.php

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

class WP_Filesystem_MountpointS3_Uploads extends WP_Filesystem_Direct {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $arg Not used.
	 */
	public function __construct( $arg ) {
		$this->method = 'mountpoints3-upload';
		$this->errors = new WP_Error();
	}

	/**
	 * Unimplemented - Set the access and modification times of a file.
	 *
	 * Note: If $file doesn't exist, it will be created.
	 *
	 * @param string $file Path to file.
	 * @param int $time Optional. Modified time to set for file.
	 *                      Default 0.
	 * @param int $atime Optional. Access time to set for file.
	 *                      Default 0.
	 *
	 * @return bool Whether operation was successful or not.
	 */
	public function touch( $file, $time = 0, $atime = 0 ) {
		return $this->handle_unimplemented_method( __METHOD__, true );
	}

	/**
	 * Unimplemented - Changes file group
	 *
	 * @param string $file Path to the file.
	 * @param mixed $group A group name or number.
	 * @param bool $recursive Optional. If set True changes file group recursively. Default false.
	 *
	 * @return bool Returns true on success or false on failure.
	 */
	public function chgrp( $file, $group, $recursive = false ) {
		return $this->handle_unimplemented_method( __METHOD__, true );
	}

	/**
	 * Unimplemented - Changes filesystem permissions
	 *
	 * @param string $file Path to the file.
	 * @param int $mode Optional. The permissions as octal number, usually 0644 for files,
	 *                          0755 for dirs. Default false.
	 * @param bool $recursive Optional. If set True changes file group recursively. Default false.
	 *
	 * @return bool Returns true on success or false on failure.
	 */
	public function chmod( $file, $mode = false, $recursive = false ) {
		return $this->handle_unimplemented_method( __METHOD__, true );
	}

	/**
	 * Unimplemented - Changes file owner
	 *
	 * @param string $file Path to the file.
	 * @param mixed $owner A user name or number.
	 * @param bool $recursive Optional. If set True changes file owner recursively.
	 *                          Default false.
	 *
	 * @return bool Returns true on success or false on failure.
	 */
	public function chown( $file, $owner, $recursive = false ) {
		return $this->handle_unimplemented_method( __METHOD__, true );
	}

	protected function handle_unimplemented_method( $method, $return_value = false ) {
		/* Translators: unsupported method name */
		$error_msg = sprintf( __( 'The `%s` method is not implemented and/or not supported.' ), $method );

		$this->errors->add( 'unimplemented-method', $error_msg );

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error, WordPress.Security.EscapeOutput.OutputNotEscaped
		trigger_error( $error_msg, E_USER_WARNING );

		return $return_value;
	}
}
