<?php
/**
 * Order status test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Order status test class
 */
class OrderStatusTest extends TestCase {
	/**
	 * Test transform.
	 *
	 * @dataProvider status_matrix_provider
	 * @param string $rabo_smart_pay_status Rabo Smart Pay order status string.
	 * @param string $expected              Expected Pronamic Pay status string.
	 */
	public function test_transform( $rabo_smart_pay_status, $expected ) {
		$pronamic_pay_status = OrderStatus::transform( $rabo_smart_pay_status );

		$this->assertEquals( $expected, $pronamic_pay_status );
	}

	/**
	 * Status matrix provider.
	 *
	 * @return array<array<string|null>>
	 */
	public static function status_matrix_provider() {
		return [
			[ OrderStatus::CANCELLED, PaymentStatus::CANCELLED ],
			[ OrderStatus::COMPLETED, PaymentStatus::SUCCESS ],
			[ OrderStatus::EXPIRED, PaymentStatus::EXPIRED ],
			[ OrderStatus::IN_PROGRESS, PaymentStatus::OPEN ],
			[ 'not existing status', null ],
		];
	}
}
