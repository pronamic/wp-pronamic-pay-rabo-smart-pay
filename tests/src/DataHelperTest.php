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
	 * Test sanitize AN.
	 *
	 * @dataProvider sanitize_an_provider
	 *
	 * @param string $string   String.
	 * @param int    $length   Length.
	 * @param string $expected Expected.
	 */
	public function test_sanitize_an( $string, $length, $expected ) {
		$result = DataHelper::sanitize_an( $string, $length );

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Sanitize AN provider.
	 *
	 * @return array
	 */
	public function sanitize_an_provider() {
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
			// HTML tags.
			array( 'test <strong>abcd</strong> 1234', 20, 'test abcd 1234' ),
			array( '12345<strong>67890</strong>', 10, '1234567890' ),
			array( "line 1\r\nline 2\r\nline 3", 100, "line 1\r\nline 2\r\nline 3" ),
		);
	}
}
