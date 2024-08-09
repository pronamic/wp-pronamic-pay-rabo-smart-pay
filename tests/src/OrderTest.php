<?php
/**
 * Order test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use DateTime;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Order test
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   2.0.2
 */
class OrderTest extends TestCase {
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
		$order = new Order( 'order123', new Money( 'EUR', 22500 ), 'https://mijn.webwinkel.nl/betalingsresultaat' );

		$order->set_timestamp( new DateTime( '2017-09-11T14:54:57+02:00' ) );
		$order->set_description( 'Aankoop webwinkel ordernummer 123' );

		// Order items.
		$order_items = $order->new_items();

		$order_item = $order_items->new_item(
			'Jackie O Round Sunglasses',
			1,
			new Money( 'EUR', 22500 ),
			ProductCategories::PHYSICAL
		);

		$order_item->set_id( 'A1000' );
		$order_item->set_description(
			'These distinct, feminine frames balance a classic Jackie-O styling with a modern look.'
		);
		$order_item->set_tax( new Money( 'EUR', 4725 ) );
		$order_item->set_vat_category( VatCategories::HIGH );

		// Shipping detail.
		$shipping_detail = new Address( 'Jansen', 'Beukenlaan', '1234AA', 'Amsterdam', 'NL' );

		$shipping_detail->set_first_name( 'Jan' );
		$shipping_detail->set_middle_name( 'van' );
		$shipping_detail->set_house_number( '12' );
		$shipping_detail->set_house_number_addition( 'a' );

		$order->set_shipping_detail( $shipping_detail );

		// Billing detail.
		$billing_detail = new Address( 'Jansen', 'Kersenstraat', '1234BB', 'Haarlem', 'NL' );

		$billing_detail->set_first_name( 'Jan' );
		$billing_detail->set_middle_name( 'van' );
		$billing_detail->set_house_number( '385' );
		$billing_detail->set_house_number_addition( 'b' );

		$order->set_billing_detail( $billing_detail );

		// Customer information.
		$customer_information = new CustomerInformation();

		$customer_information->set_email_address( 'jan@example.org' );
		$customer_information->set_date_of_birth( new DateTime( '21-11-1977' ) );
		$customer_information->set_gender( 'M' );
		$customer_information->set_initials( 'J.A.N.' );
		$customer_information->set_telephone_number( '+31204971111' );

		$order->set_customer_information( $customer_information );

		// Language.
		$order->set_language( 'nl' );

		// Payment brand.
		$order->set_payment_brand( PaymentBrands::IDEAL );
		$order->set_payment_brand_force( PaymentBrandForce::FORCE_ONCE );

		// Fields.
		$fields = $order->get_signature_fields();

		$expected = '2017-09-11T14:54:57+02:00,order123,EUR,22500,nl,Aankoop webwinkel ordernummer 123,https://mijn.webwinkel.nl/betalingsresultaat,A1000,Jackie O Round Sunglasses,These distinct, feminine frames balance a classic Jackie-O styling with a modern look.,1,EUR,22500,EUR,4725,PHYSICAL,1,Jan,van,Jansen,Beukenlaan,12,a,1234AA,Amsterdam,NL,IDEAL,FORCE_ONCE,jan@example.org,21-11-1977,M,J.A.N.,+31204971111,Jan,van,Jansen,Kersenstraat,385,b,1234BB,Haarlem,NL';

		$this->assertEquals( $expected, Security::get_signature_fields_combined( $fields ) );
	}

	/**
	 * Test merchant order ID.
	 */
	public function test_merchant_order_id() {
		// Normal.
		$merchant_order_id = 'order123';

		$order = new Order(
			$merchant_order_id,
			new Money( 'EUR', 22500 ),
			'https://mijn.webwinkel.nl/betalingsresultaat'
		);

		$this->assertEquals( $merchant_order_id, $order->get_merchant_order_id() );

		// Max length.
		$merchant_order_id = '123456789012345678901234';

		$order->set_merchant_order_id( $merchant_order_id );

		$this->assertEquals( $merchant_order_id, $order->get_merchant_order_id() );
	}

	/**
	 * Test merchant order ID too long.
	 */
	public function test_merchant_order_id_too_long() {
		$this->expectException( \InvalidArgumentException::class );

		new Order(
			'123456789012345678901234567890',
			new Money( 'EUR', 22500 ),
			'https://mijn.webwinkel.nl/betalingsresultaat'
		);
	}

	/**
	 * Test merchant order ID strictly.
	 */
	public function test_merchant_order_id_strictly() {
		$this->expectException( \InvalidArgumentException::class );

		new Order( '12345 @ 67890 .', new Money( 'EUR', 22500 ), 'https://mijn.webwinkel.nl/betalingsresultaat' );
	}
}
