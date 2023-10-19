<?php
/**
 * Plugin Name: Pronamic Pay with Rabo Smart Pay
 * Plugin URI: https://www.pronamic.eu/plugins/pronamic-pay-with-rabo-smart-pay/
 * Description:
 *
 * Version: 4.4.5
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
 * Requires Plugins: pronamic-ideal
 * Depends: wp-pay/core
 *
 * GitHub URI: https://github.com/wp-pay-gateways/omnikassa-2
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

/**
 * Autoload.
 */
require_once __DIR__ . '/vendor/autoload_packages.php';

/**
 * Bootstrap.
 */
\Pronamic\WordPress\Pay\Plugin::instance(
	[
		'file'             => __FILE__,
		'action_scheduler' => __DIR__ . '/packages/woocommerce/action-scheduler/action-scheduler.php',
	]
);

add_filter(
	'pronamic_pay_gateways',
	function( $gateways ) {
		$gateways[] = new \Pronamic\WordPress\Pay\Gateways\OmniKassa2\Integration(
			[
				'id'      => 'rabobank-omnikassa-2',
				'name'    => 'Rabobank - Rabo Smart Pay',
				'mode'    => 'live',
				'api_url' => 'https://betalen.rabobank.nl/omnikassa-api/',
			]
		);

		$gateways[] = new \Pronamic\WordPress\Pay\Gateways\OmniKassa2\Integration(
			[
				'id'      => 'rabobank-omnikassa-2-sandbox',
				'name'    => 'Rabobank - Rabo Smart Pay - Sandbox',
				'mode'    => 'test',
				'api_url' => 'https://betalen.rabobank.nl/omnikassa-api-sandbox/',
			]
		);

		return $gateways;
	}
);
