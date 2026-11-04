<?php
/**
 * Customer information test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Customer information test
 */
class CustomerInformationTest extends TestCase {
	/**
	 * Test full name in JSON.
	 */
	public function test_full_name() {
		$customer_information = new CustomerInformation();

		$customer_information->set_initials( 'J.' );
		$customer_information->set_full_name( 'Jan van Veen' );

		$this->assertJsonStringEqualsJsonString(
			'{"initials":"J.","fullName":"Jan van Veen"}',
			\wp_json_encode( $customer_information )
		);
	}

	/**
	 * Test that full name is omitted when not set.
	 */
	public function test_full_name_not_set() {
		$customer_information = new CustomerInformation();

		$customer_information->set_initials( 'J.' );

		$this->assertJsonStringEqualsJsonString(
			'{"initials":"J."}',
			\wp_json_encode( $customer_information )
		);
	}
}
