<?php
/**
 * Address test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Address test
 *
 * @author  Remco Tolsma
 * @version 2.1.10
 * @since   2.0.2
 */
class AddressTest extends TestCase {
	/**
	 * Get test address.
	 *
	 * @return Address
	 */
	private function get_test_address() {
		$address = new Address( 'Jansen', 'Beukenlaan', '1234AA', 'Amsterdam', 'NL' );

		// Optional.
		$address->set_first_name( 'Jan' );
		$address->set_middle_name( 'van' );
		$address->set_house_number( '12' );
		$address->set_house_number_addition( 'a' );

		return $address;
	}

	/**
	 * Test address.
	 */
	public function test_address() {
		$address = $this->get_test_address();

		$this->assertEquals( 'Jansen', $address->get_last_name() );
		$this->assertEquals( 'Beukenlaan', $address->get_street() );
		$this->assertEquals( '1234AA', $address->get_postal_code() );
		$this->assertEquals( 'Amsterdam', $address->get_city() );
		$this->assertEquals( 'NL', $address->get_country_code() );

		// Optional.
		$this->assertEquals( 'Jan', $address->get_first_name() );
		$this->assertEquals( 'van', $address->get_middle_name() );
		$this->assertEquals( '12', $address->get_house_number() );
		$this->assertEquals( 'a', $address->get_house_number_addition() );
	}

	/**
	 * Test invalid last name.
	 */
	public function test_invalid_last_name() {
		$address = $this->get_test_address();

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Field `Address.lastName` value "123456789012345678901234567890123456789012345678901" can not be longer then `50`.'
		);

		$address->set_last_name( '123456789012345678901234567890123456789012345678901' );
	}

	/**
	 * Test invalid street.
	 */
	public function test_invalid_street() {
		$address = $this->get_test_address();

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Field `Address.street` value "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901" can not be longer then `100`.'
		);

		$address->set_street(
			'12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901'
		);
	}

	/**
	 * Test empty street.
	 *
	 * @todo Don't allow empty string since OmniKassa 2.0 will return 'the street is required'.
	 * @link https://basecamp.com/1810084/projects/10966871/todos/401356641
	 */
	public function test_empty_street() {
		$address = $this->get_test_address();

		$address->set_street( '' );

		$this->assertEquals( '', $address->get_street() );
	}

	/**
	 * Test invalid postal code.
	 */
	public function test_invalid_postal_code() {
		$address = $this->get_test_address();

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Field `Address.postalCode` value "12345678901" can not be longer then `10`.' );

		$address->set_postal_code( '12345678901' );
	}

	/**
	 * Test invalid city.
	 */
	public function test_invalid_city() {
		$address = $this->get_test_address();

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Field `Address.city` value "12345678901234567890123456789012345678901" can not be longer then `40`.'
		);

		$address->set_city( '12345678901234567890123456789012345678901' );
	}

	/**
	 * Test invalid country code.
	 */
	public function test_invalid_country_code() {
		$address = $this->get_test_address();

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( 'Field `Address.countryCode` value "ABC" can not be longer then `2`.' );

		$address->set_country_code( 'ABC' );
	}

	/**
	 * Test invalid first name.
	 */
	public function test_invalid_first_name() {
		$address = $this->get_test_address();

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Field `Address.firstName` value "123456789012345678901234567890123456789012345678901" can not be longer then `50`.'
		);

		$address->set_first_name( '123456789012345678901234567890123456789012345678901' );
	}

	/**
	 * Test invalid middle name.
	 */
	public function test_invalid_middle_name() {
		$address = $this->get_test_address();

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Field `Address.middleName` value "123456789012345678901" can not be longer then `20`.'
		);

		$address->set_middle_name( '123456789012345678901' );
	}

	/**
	 * Test invalid house number.
	 */
	public function test_invalid_house_number() {
		$address = $this->get_test_address();

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Field `Address.houseNumber` value "12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901" can not be longer then `100`.'
		);

		$address->set_house_number(
			'12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901'
		);
	}

	/**
	 * Test invalid house number addition.
	 */
	public function test_invalid_house_number_addition() {
		$address = $this->get_test_address();

		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage(
			'Field `Address.houseNumberAddition` value "1234567" can not be longer then `6`.'
		);

		$address->set_house_number_addition( '1234567' );
	}
}
