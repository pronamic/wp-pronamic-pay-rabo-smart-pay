<?php
/**
 * Order test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use DateTime;

/**
 * Order test
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   2.0.2
 */
class OrderTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test minimal order from documentation.
	 */
	public function test_documentation_minimal() {
		$merchant_order_id = 'order123';

		$amount = new Money( 'EUR', 4999 );

		$merchant_return_url = 'http://www.example.org';

		$order = new Order( $merchant_order_id, $amount, $merchant_return_url );

		$order->set_timestamp( new DateTime( '2017-02-06T08:32:51.759+01:00' ) );

		$fields = $order->get_signature_fields();

		$expected = '2017-02-06T08:32:51+01:00,order123,EUR,4999,,,http://www.example.org';

		$this->assertEquals( $expected, Security::get_signature_fields_combined( $fields ) );
	}

	/**
	 * Test complete order from documentation.
	 */
	public function test_documentation_complete() {
		$order = new Order( 'order123', new Money( 'EUR', 4999 ), 'http://www.example.org' );

		$order->set_timestamp( new DateTime( '2017-09-11T14:54:57+02:00' ) );
		$order->set_description( 'Aankoop mijn webwinkel ordernummer 123' );

		$order_items = $order->new_items();

		$order_item = $order_items->new_item(
			'Jackie O Round Sunglasses',
			1,
			new Money( 'EUR', 22500 ),
			Category::PHYSICAL
		);

		$order_item->set_id( 'A1000' );
		$order_item->set_description( 'These distinct, feminine frames balance a classic Jackie-O styling with a modern look.' );
		$order_item->set_vat_category( VatCategories::HIGH );

		$fields = $order->get_signature_fields();

		$expected = '2017-09-11T14:54:57+02:00,order123,EUR,22500,nl,Aankoop mijn webwinkel ordernummer 123,https://mijn.webwinkel.nl/betalingsresultaat,A1000,Jackie O Round Sunglasses,These distinct, feminine frames balance a classic Jackie-O styling with a modern look.,1,EUR,22500,EUR,4725,PHYSICAL,1,Jan,van,Jansen,Beukenlaan,12,a,1 234AA,Amsterdam,NL,IDEAL,FORCE_ONCE,jan@example.org,21-11- 1977,M,J.A.N.,+31204971111,Jan,van,Jansen,Kersenstraat,385,b,1234BB,Ha arlem,NL';

		$this->assertEquals( $expected, Security::get_signature_fields_combined( $fields ) );
	}
}
