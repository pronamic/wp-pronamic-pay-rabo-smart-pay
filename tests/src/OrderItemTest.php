<?php
/**
 * Order item test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Order item test
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   2.0.2
 */
class OrderItemTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test name.
	 */
	public function test_order_item() {
		$order_item = new OrderItem(
			'Jackie O Round Sunglasses',
			1,
			new Money( 'EUR', 22500 ),
			ProductCategories::PHYSICAL
		);

		$this->assertEquals( 'Jackie O Round Sunglasses', $order_item->get_name() );
	}
}
