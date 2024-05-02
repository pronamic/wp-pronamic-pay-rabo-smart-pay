<?php
/**
 * Order results test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Order results test
 *
 * @author  Remco Tolsma
 * @version 2.2.4
 * @since   2.0.2
 */
class OrderAnnounceResponseTest extends TestCase {
	/**
	 * Test order results.
	 *
	 * @link https://developer.rabobank.nl/reference/omnikassa/1-0-1
	 */
	public function test_order_results() {
		$json = \file_get_contents( __DIR__ . '/../json/order-announce-response.json', true );

		$response = OrderAnnounceResponse::from_json( $json );

		$this->assertEquals( '1d0a95f4-2589-439b-9562-c50aa19f9caf', $response->get_omnikassa_order_id() );

		$this->assertEquals(
			'https://betalen.rabobank.nl/omnikassa-api/payment-brand?token=eyJraWQiOiJFTU8iLCJhbGciOiJFUzI1NiJ9.eyJlbW8iOiJhYWZhMDAxM',
			$response->get_redirect_url()
		);
	}
}
