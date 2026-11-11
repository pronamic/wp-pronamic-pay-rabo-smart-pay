<?php
/**
 * Refund response test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Refund response test class
 */
class RefundResponseTest extends TestCase {
	/**
	 * Test refund response with all properties.
	 *
	 * @return void
	 */
	public function test_from_object() {
		$response = RefundResponse::from_object(
			(object) [
				'refundId'            => 'd6b7a8f5-1e6b-4c5a-9d8f-0b3e2a1c4d5e',
				'refundTransactionId' => '7c1f0a2b-3d4e-5f60-7182-93a4b5c6d7e8',
				'status'              => 'SUCCEEDED',
			]
		);

		$this->assertSame( 'd6b7a8f5-1e6b-4c5a-9d8f-0b3e2a1c4d5e', $response->id );
		$this->assertSame( '7c1f0a2b-3d4e-5f60-7182-93a4b5c6d7e8', $response->transaction_id );
		$this->assertSame( 'SUCCEEDED', $response->status );
	}

	/**
	 * Test refund response without refund transaction ID.
	 *
	 * @link https://github.com/pronamic/wp-pronamic-pay-rabo-smart-pay/issues/55
	 * @return void
	 */
	public function test_from_object_without_refund_transaction_id() {
		$response = RefundResponse::from_object(
			(object) [
				'refundId' => 'a2b36073-57a3-4c3d-9585-87f2e55275aa',
				'status'   => 'PENDING',
			]
		);

		$this->assertSame( 'a2b36073-57a3-4c3d-9585-87f2e55275aa', $response->id );
		$this->assertNull( $response->transaction_id );
		$this->assertSame( 'PENDING', $response->status );
	}

	/**
	 * Test refund response without any properties.
	 *
	 * @return void
	 */
	public function test_from_empty_object() {
		$response = RefundResponse::from_object( (object) [] );

		$this->assertNull( $response->id );
		$this->assertNull( $response->transaction_id );
		$this->assertNull( $response->status );
	}

	/**
	 * Test refund response with a property that is not a string.
	 *
	 * @return void
	 */
	public function test_from_object_with_invalid_refund_id() {
		$this->expectException( \Exception::class );
		$this->expectExceptionMessage( 'Property `refundId` must be a string.' );

		RefundResponse::from_object(
			(object) [
				'refundId' => 123,
			]
		);
	}
}
