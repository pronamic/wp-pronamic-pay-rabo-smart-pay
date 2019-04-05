<?php
/**
 * Data helper test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Data helper test
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   2.0.2
 */
class DataHelperTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test validate AN.
	 */
	public function test_validate_an() {
		$value = '1234567890';

		$result = DataHelper::validate_an( $value, 10 );

		$this->assertTrue( $result );
	}

	/**
	 * Test shorten.
	 *
	 * @dataProvider shorten_provider
	 *
	 * @param string $string   String.
	 * @param int    $length   Length.
	 * @param string $expected Expected.
	 */
	public function test_shorten( $string, $length, $expected ) {
		$result = DataHelper::shorten( $string, $length );

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Shorten provider.
	 *
	 * @return array
	 */
	public function shorten_provider() {
		return array(
			// Nothing to-do.
			array( '1234567890', 10, '1234567890' ),
			array( '1234567890', 100, '1234567890' ),
			// Shorten.
			array( '1234567890', 5, '12345' ),
			// UTF-8 test.
			array( 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ', 5, 'ÀÁÂÃÄ' ),
			// UTF-8 test Arabic (https://en.wikipedia.org/wiki/Arabic).
			array( 'كنت أريد أن أقرأ كتابا عن تاريخ المرأة في فرنسا', 10, 'كنت أريد أ' ),
		);
	}
}
