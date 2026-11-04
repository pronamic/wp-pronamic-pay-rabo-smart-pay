<?php
/**
 * Refund request test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Refund request test
 */
class RefundRequestTest extends TestCase {
	/**
	 * Test that the idempotence ID is a UUID that stays the same for one request.
	 */
	public function test_idempotence_id() {
		$refund_request = new RefundRequest( 'transaction-1', new Money( 'EUR', 100 ) );

		$idempotence_id = $refund_request->get_idempotence_id();

		$this->assertTrue( \wp_is_uuid( $idempotence_id, 4 ) );
		$this->assertSame( $idempotence_id, $refund_request->get_idempotence_id() );
	}

	/**
	 * Test that different refund requests get different idempotence IDs.
	 */
	public function test_idempotence_id_unique() {
		$refund_request_1 = new RefundRequest( 'transaction-1', new Money( 'EUR', 100 ) );
		$refund_request_2 = new RefundRequest( 'transaction-1', new Money( 'EUR', 100 ) );

		$this->assertNotSame( $refund_request_1->get_idempotence_id(), $refund_request_2->get_idempotence_id() );
	}
}
