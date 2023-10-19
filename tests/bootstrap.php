<?php
/**
 * Bootstrap tests
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

require_once __DIR__ . '/../vendor/autoload.php';

require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

tests_add_filter(
	'muplugins_loaded',
	function() {
		require __DIR__ . '/../pronamic-pay-with-rabo-smart-pay.php';
	}
);

require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';
