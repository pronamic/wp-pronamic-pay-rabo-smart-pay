<?php
/**
 * Error test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Error test
 *
 * @author  Remco Tolsma
 * @version 2.1.10
 * @since   2.0.2
 */
class ErrorTest extends \PHPUnit\Framework\TestCase {
	/**
	 * Get test error.
	 *
	 * @return Error
	 */
	private function get_test_error() {
		$error = new Error( 5001, 'Full authentication is required to access this resource' );

		return $error;
	}

	/**
	 * Test error.
	 */
	public function test_error() {
		$error = $this->get_test_error();

		$this->assertEquals( 5001, $error->get_error_code() );
		$this->assertEquals( 'Full authentication is required to access this resource', $error->getMessage() );
	}

	/**
	 * Test JSON.
	 *
	 * @dataProvider json_test_provider
	 * @param string $file             JSON source file.
	 * @param int    $error_code       Error code.
	 * @param string $error_message    Error message.
	 * @param string $consumer_message Consumer message.
	 */
	public function test_json( $file, $error_code, $error_message, $consumer_message ) {
		$json = \file_get_contents( __DIR__ . '/../json/' . $file, true );

		$error = Error::from_json( $json );

		$this->assertEquals( $error_code, $error->get_error_code() );
		$this->assertEquals( $error_message, $error->get_error_message() );
		$this->assertEquals( $consumer_message, $error->get_consumer_message() );
	}

	/**
	 * Status matrix provider.
	 *
	 * @return array<array<string|int|null>>
	 */
	public static function json_test_provider() {
		return [
			[ 'error-5001-full-authentication-required.json', 5001, 'Full authentication is required to access this resource', null ],
			[ 'error-5001-invalid-or-missing-signature.json', 5001, null, 'Invalid or missing signature' ],
			[ 'error-5001-timestamp-invalid.json', 5001, null, 'The timestamp of the order announcement is invalid' ],
			[ 'error-5017-currency-required.json', 5017, null, 'currency required and should be one of: [AUD, CAD, CHF, DKK, EUR, GBP, JPY, NOK, SEK, USD]' ],
			[ 'error-5017-merchant-order-id-required.json', 5017, null, 'merchantOrderId is required' ],
			[ 'error-5017-merchant-return-url-required.json', 5017, null, 'merchantReturnURL is required' ],
			[ 'error-5017-order-amount.json', 5017, null, 'order amount must be greater than zero' ],
		];
	}
}
