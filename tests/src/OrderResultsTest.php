<?php
/**
 * Order results test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use PHPUnit\Framework\TestCase;

/**
 * Order results test
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   2.0.2
 */
class OrderResultsTest extends TestCase {
	/**
	 * Test order results.
	 */
	public function test_order_results() {
		$signing_key = 'QAhFrajUoLsKowfRo15vFXIpdbCgmI2S82idk6xPiCk=';

		$json = \file_get_contents( __DIR__ . '/../json/merchant.order.status.changed-1.json', true );

		$order_results = OrderResults::from_json( $json );

		$items = \iterator_to_array( $order_results );

		$this->assertEquals( '9384f4387cd03b7d7e49a34a6037fe97ea901a830f466d2bc5e852388d1a95302278b229639de51e2f841dc35a4c282685fb2de9ad6d579a8d8efd950231e12f', $order_results->get_signature() );
		$this->assertFalse( $order_results->more_available() );
		$this->assertCount( 1, $order_results );

		$first = \reset( $items );

		$this->assertEquals( '73', $first->get_merchant_order_id() );
		$this->assertEquals( '2273c19c-d7b3-45d4-82cd-5612c4fd0957', $first->get_omnikassa_order_id() );
		$this->assertEquals( '5000', $first->get_poi_id() );
		$this->assertEquals( 'COMPLETED', $first->get_order_status() );
		$this->assertEquals( '2018-08-21T14:37:54.853+02:00', $first->get_order_status_datetime() );
		$this->assertEquals( '', $first->get_error_code() );
		$this->assertEquals( 'EUR', $first->get_paid_amount()->get_currency() );
		$this->assertEquals( '100', $first->get_paid_amount()->get_amount() );
		$this->assertEquals( 'EUR', $first->get_total_amount()->get_currency() );
		$this->assertEquals( '100', $first->get_total_amount()->get_amount() );

		$this->assertTrue( $order_results->is_valid( $signing_key ) );
	}
}
