<?php
/**
 * Return parameters test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use PHPUnit\Framework\TestCase;

/**
 * Return parameters test
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   2.0.2
 */
class ReturnParametersTest extends TestCase {
	/**
	 * Test return parameters.
	 */
	public function test_return_parameters() {
		$signing_key = 'QAhFrajUoLsKowfRo15vFXIpdbCgmI2S82idk6xPiCk=';

		$get = [
			'order_id'  => '77',
			'signature' => '045fdb9da232f1b4677921f9b14dcf739be130cf01a6620b1466b8c94a2df3ebaef92c86ef996d1a0685f2a2ec7d8c1fcb82976ec02f5af7b5bbf81bc1efd80c',
			'status'    => 'COMPLETED',
		];

		$this->assertTrue( ReturnParameters::contains( $get ) );

		$parameters = ReturnParameters::from_array( $get );

		$this->assertEquals( '77', $parameters->get_order_id() );
		$this->assertEquals( 'COMPLETED', $parameters->get_status() );
		$this->assertEquals(
			'045fdb9da232f1b4677921f9b14dcf739be130cf01a6620b1466b8c94a2df3ebaef92c86ef996d1a0685f2a2ec7d8c1fcb82976ec02f5af7b5bbf81bc1efd80c',
			$parameters->get_signature()
		);

		$this->assertTrue( $parameters->is_valid( $signing_key ) );
	}
}
