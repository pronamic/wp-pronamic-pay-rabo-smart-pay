<?php
/**
 * Order results test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Order results test
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   2.0.2
 */
class OrderAnnounceResponseTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test order results.
	 */
	public function test_order_results() {
		$json = file_get_contents( __DIR__ . '/../json/order-announce-response.json' );

		$response = OrderAnnounceResponse::from_json( $json );

		$this->assertEquals( 'd3dd97b48752f3d4d4c5a914bf9e935956546887c7c8fd020a0702cd4462fbd8c60d2b7b0e0c4fc160005c71a1f7c504ef7ca8bbfb82cf0a6564b1bfeb0a4f7f', $response->get_signature() );
		$this->assertEquals( 'https://betalen.rabobank.nl/omnikassa-api/payment-brand?token=eyJraWQiOiJFTU8iLCJhbGciOiJFUzI1NiJ9.eyJlbW8iOiJhYWZhMDAxMy1lYmNiLTQ1ZjQtYTRmYi01OGNjMmQ5MDM2MDIiLCJjaWQiOiIxOTQwLTBkNTgiLCJleHAiOjE0ODAxNTA0Mjd9.MEQCIHJLZjlcNYShX7YzVFvghfwmvH7WTV2Lj5IQIejFyjH7AiBKmvahL29DgiA5vMhGLOHoHaT3SjQKgR4RVxJetG7Fdw&lang=nl', $response->get_redirect_url() );
	}
}
