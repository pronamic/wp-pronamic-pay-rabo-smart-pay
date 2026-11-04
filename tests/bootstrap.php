<?php
/**
 * Bootstrap tests
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

/**
 * Give access to tests_add_filter() function.
 *
 * @link https://github.com/wp-phpunit/example-plugin/blob/master/tests/bootstrap.php
 */
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

/**
 * SQLite database drop-in.
 *
 * Copies the `db.php` drop-in from the SQLite Database Integration plugin into
 * the WordPress content directory, so the tests run without a MySQL server.
 *
 * @link https://github.com/WordPress/sqlite-database-integration
 */
$sqlite_plugin_dir = dirname( __DIR__ ) . '/vendor/wp-plugin/sqlite-database-integration';

// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
$sqlite_dropin = file_get_contents( $sqlite_plugin_dir . '/db.copy' );

if ( false === $sqlite_dropin ) {
	echo 'SQLite Database Integration plugin not found, run `composer install`.' . PHP_EOL;

	exit( 1 );
}

$sqlite_dropin = str_replace(
	[
		'{SQLITE_IMPLEMENTATION_FOLDER_PATH}',
		'{SQLITE_PLUGIN}',
	],
	[
		$sqlite_plugin_dir,
		'sqlite-database-integration/load.php',
	],
	$sqlite_dropin
);

$wp_content_dir = dirname( __DIR__ ) . '/wordpress/wp-content';

if ( ! is_dir( $wp_content_dir ) ) {
	// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.directory_mkdir
	mkdir( $wp_content_dir );
}

// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_file_put_contents
file_put_contents( $wp_content_dir . '/db.php', $sqlite_dropin );

/**
 * Plugin.
 */
tests_add_filter(
	'muplugins_loaded',
	function () {
		require __DIR__ . '/../pronamic-pay-with-rabo-smart-pay.php';
	}
);

/**
 * Start up the WP testing environment.
 *
 * @link https://github.com/wp-phpunit/example-plugin/blob/master/tests/bootstrap.php
 */
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';
