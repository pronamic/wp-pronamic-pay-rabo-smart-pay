<?php
/**
 * Plugin Name: Pronamic Pay with Rabo Smart Pay
 * Plugin URI: https://www.pronamic.eu/plugins/pronamic-pay-with-rabo-smart-pay/
 * Description:
 *
 * Version: 4.7.1
 * Requires at least: 5.9
 * Requires PHP: 7.4
 *
 * Author: Pronamic
 * Author URI: https://www.pronamic.eu/
 *
 * Text Domain: pronamic-pay-rabo-smart-pay
 * Domain Path: /languages/
 *
 * License: GPL-3.0-or-later
 *
 * GitHub URI: https://github.com/wp-pay-gateways/omnikassa-2
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Autoload.
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

/**
 * Bootstrap.
 */
\Pronamic\WordPress\Pay\Plugin::instance(
	[
		'action_scheduler' => __DIR__ . '/vendor/woocommerce/action-scheduler/action-scheduler.php',
		'file'             => __FILE__,
	]
);

add_filter(
	'pronamic_pay_gateways',
	static function ( $gateways ) {
		$gateways[] = new \Pronamic\WordPress\Pay\Gateways\OmniKassa2\Integration(
			[
				'api_url' => 'https://betalen.rabobank.nl/omnikassa-api/',
				'id'      => 'rabobank-omnikassa-2',
				'mode'    => 'live',
				'name'    => 'Rabobank - Rabo Smart Pay',
			]
		);

		$gateways[] = new \Pronamic\WordPress\Pay\Gateways\OmniKassa2\Integration(
			[
				'api_url' => 'https://betalen.rabobank.nl/omnikassa-api-sandbox/',
				'id'      => 'rabobank-omnikassa-2-sandbox',
				'mode'    => 'test',
				'name'    => 'Rabobank - Rabo Smart Pay - Sandbox',
			]
		);

		return $gateways;
	}
);
