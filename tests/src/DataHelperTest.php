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
	 * Test replace HTML special characters.
	 *
	 * @dataProvider replace_html_special_chars_provider
	 *
	 * @param string $string   String.
	 * @param string $expected Expected.
	 */
	public function test_replace_html_special_chars( $string, $expected ) {
		$result = DataHelper::replace_html_special_chars( $string );

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Replace HTML special characters provider.
	 *
	 * @return array
	 */
	public function replace_html_special_chars_provider() {
		return array(
			array( '& Ampersand', '� Ampersand' ),
			array( '< Less-Than Sign', '� Less-Than Sign' ),
			array( '> Greater-Than Sign', '� Greater-Than Sign' ),
			array( 'Test & < > Test', 'Test � � � Test' ),
		);
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
			array( '1234567890', 5, '1234…' ),
			// UTF-8 test.
			array( 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ', 5, 'ÀÁÂÃ…' ),
			// UTF-8 test Arabic (https://en.wikipedia.org/wiki/Arabic).
			array( 'كنت أريد أن أقرأ كتابا عن تاريخ المرأة في فرنسا', 10, 'كنت أريد …' ),
		);
	}
}
