<?php
/**
 * Security test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use PHPUnit\Framework\TestCase;

/**
 * Security test
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   2.0.2
 */
class SecurityTest extends TestCase {
	/**
	 * Test signature.
	 */
	public function test_signature() {
		$signing_key = 'QAhFrajUoLsKowfRo15vFXIpdbCgmI2S82idk6xPiCk=';

		$get_data = array(
			'order_id'  => '77',
			'status'    => 'COMPLETED',
			'signature' => '045fdb9da232f1b4677921f9b14dcf739be130cf01a6620b1466b8c94a2df3ebaef92c86ef996d1a0685f2a2ec7d8c1fcb82976ec02f5af7b5bbf81bc1efd80c',
		);

		$return_parameters = ReturnParameters::from_array( $get_data );

		$signature = Security::get_signature( $return_parameters, $signing_key );

		$this->assertEquals( $return_parameters->get_signature(), $signature );
	}
}
