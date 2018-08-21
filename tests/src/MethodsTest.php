<?php
/**
 * Methods test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Methods test
 *
 * @author  Remco Tolsma
 * @version 2.0.3
 * @since   1.0.0
 */
class MethodsTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test transform.
	 *
	 * @param string $pronamic_pay_method Pronamic Pay method.
	 * @param string $expected            Expected OmniKassa 2.0 payment method string.
	 *
	 * @dataProvider status_matrix_provider
	 */
	public function test_transform( $pronamic_pay_method, $expected ) {
		$omnikassa_2_method = Methods::transform( $pronamic_pay_method );

		$this->assertEquals( $expected, $omnikassa_2_method );
	}

	/**
	 * Status matrix provider.
	 *
	 * @return array
	 */
	public function status_matrix_provider() {
		return array(
			array( PaymentMethods::BANCONTACT,  PaymentBrands::BANCONTACT ),
			array( PaymentMethods::CREDIT_CARD, PaymentBrands::CARDS ),
			array( PaymentMethods::IDEAL,       PaymentBrands::IDEAL ),
			array( PaymentMethods::PAYPAL,      PaymentBrands::PAYPAL ),
			array( 'not existing status', null ),
		);
	}
}
