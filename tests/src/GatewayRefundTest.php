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

use Pronamic\WordPress\Http\Factory;
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

	/**
	 * Create a refund with a faked Rabo Smart Pay refund response.
	 *
	 * @param string $file Response file.
	 * @return Refund
	 */
	private function create_refund_with_response( $file ) {
		$http_factory = new Factory();

		$http_factory->fake(
			Client::URL_SANDBOX . 'order/server/api/v2/refund/transactions/22b36073-57a3-4c3d-9585-87f2e55275a5/refunds',
			$file
		);

		$config = new Config();

		$config->api_url                  = Client::URL_SANDBOX;
		$config->access_token             = 'valid-token';
		$config->access_token_valid_until = \gmdate( 'Y-m-d\TH:i:s.000O', \time() + \HOUR_IN_SECONDS );

		$gateway = new Gateway( $config );

		$payment = new Payment();

		$payment->set_transaction_id( '22b36073-57a3-4c3d-9585-87f2e55275a5' );

		$refund = new Refund( $payment, new Money( 1, 'EUR' ) );

		$gateway->create_refund( $refund );

		return $refund;
	}

	/**
	 * Test refund response with refund transaction ID.
	 *
	 * @return void
	 */
	public function test_refund() {
		$refund = $this->create_refund_with_response( __DIR__ . '/../http/refund-response.http' );

		$this->assertSame( 'd6b7a8f5-1e6b-4c5a-9d8f-0b3e2a1c4d5e', $refund->psp_id );
		$this->assertEquals(
			[
				'rabo_smart_pay_refund_id'             => 'd6b7a8f5-1e6b-4c5a-9d8f-0b3e2a1c4d5e',
				'rabo_smart_pay_refund_status'         => 'SUCCEEDED',
				'rabo_smart_pay_refund_transaction_id' => '7c1f0a2b-3d4e-5f60-7182-93a4b5c6d7e8',
			],
			$refund->meta
		);
	}

	/**
	 * Test refund response without refund transaction ID.
	 *
	 * @link https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/issues/55
	 * @return void
	 */
	public function test_refund_without_refund_transaction_id() {
		$refund = $this->create_refund_with_response(
			__DIR__ . '/../http/refund-response-without-refund-transaction-id.http'
		);

		$this->assertSame( 'a2b36073-57a3-4c3d-9585-87f2e55275aa', $refund->psp_id );
		$this->assertEquals(
			[
				'rabo_smart_pay_refund_id'     => 'a2b36073-57a3-4c3d-9585-87f2e55275aa',
				'rabo_smart_pay_refund_status' => 'PENDING',
			],
			$refund->meta
		);
	}
}
