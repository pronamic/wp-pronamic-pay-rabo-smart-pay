<?php
/**
 * Gateway refund test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Refunds\Refund;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Gateway refund test class
 */
class GatewayRefundTest extends TestCase {
	/**
	 * Get gateway.
	 *
	 * @return Gateway
	 */
	private function get_gateway() {
		$config = new Config();

		$config->api_url = Client::URL_SANDBOX;

		return new Gateway( $config );
	}

	/**
	 * Test refund without transaction ID.
	 *
	 * @return void
	 */
	public function test_refund_without_transaction_id() {
		$payment = new Payment();

		$refund = new Refund( $payment, new Money( 1, 'EUR' ) );

		$this->expectException( \Exception::class );
		$this->expectExceptionMessage( 'Cannot refund: the payment has no Rabo Smart Pay transaction ID.' );

		$this->get_gateway()->create_refund( $refund );
	}

	/**
	 * Test refund with an OmniKassa order ID as transaction ID.
	 *
	 * @return void
	 */
	public function test_refund_with_order_id_as_transaction_id() {
		$payment = new Payment();

		$payment->set_transaction_id( '1d0a95f4-2589-439b-9562-c50aa19f9caf' );
		$payment->set_meta( 'omnikassa_order_id', '1d0a95f4-2589-439b-9562-c50aa19f9caf' );

		$refund = new Refund( $payment, new Money( 1, 'EUR' ) );

		$this->expectException( \Exception::class );
		$this->expectExceptionMessage( 'Cannot refund: the payment transaction ID is a Rabo Smart Pay order ID' );

		$this->get_gateway()->create_refund( $refund );
	}
}
