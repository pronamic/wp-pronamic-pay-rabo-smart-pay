<?php
/**
 * Transaction test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Transaction test class
 */
class TransactionTest extends TestCase {
	/**
	 * Get transaction data.
	 *
	 * @return array<string, mixed>
	 */
	private function get_data() {
		return [
			'amount'         => (object) [
				'amount'   => '10997',
				'currency' => 'EUR',
			],
			'id'             => '22b36073-57a3-4c3d-9585-87f2e55275a5',
			'lastUpdateTime' => '2018-03-20T09:12:28Z',
			'paymentBrand'   => 'IDEAL',
			'startTime'      => '2018-03-20T09:12:28Z',
			'status'         => 'CANCELLED',
			'type'           => 'PAYMENT',
		];
	}

	/**
	 * Test transaction with confirmed amount.
	 *
	 * @return void
	 */
	public function test_from_object_with_confirmed_amount() {
		$data = $this->get_data();

		$data['confirmedAmount'] = (object) [
			'amount'   => '10997',
			'currency' => 'EUR',
		];

		$transaction = Transaction::from_object( (object) $data );

		$this->assertInstanceOf( Money::class, $transaction->get_confirmed_amount() );
		$this->assertSame( 10997, $transaction->get_confirmed_amount()->get_amount() );
	}

	/**
	 * Test transaction without confirmed amount.
	 *
	 * The `confirmedAmount` property is optional, it is only filled when the
	 * transaction status is `SUCCESS` or `ACCEPTED`.
	 *
	 * @link https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/issues/55
	 * @return void
	 */
	public function test_from_object_without_confirmed_amount() {
		$transaction = Transaction::from_object( (object) $this->get_data() );

		$this->assertNull( $transaction->get_confirmed_amount() );
		$this->assertSame(
			[
				'22b36073-57a3-4c3d-9585-87f2e55275a5',
				'IDEAL',
				'PAYMENT',
				'CANCELLED',
				'EUR',
				'10997',
				'2018-03-20T09:12:28Z',
				'2018-03-20T09:12:28Z',
			],
			$transaction->get_signature_fields()
		);
	}
}
