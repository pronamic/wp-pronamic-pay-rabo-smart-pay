<?php
/**
 * Data helper test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use PHPUnit\Framework\TestCase;

/**
 * Data helper test
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   2.0.2
 */
class DataHelperTest extends TestCase {
	/**
	 * Test validate AN.
	 *
	 * @dataProvider validate_an_provider
	 * @param string      $string   String.
	 * @param int         $length   Length.
	 * @param string|null $exception Expected.
	 */
	public function test_validate_an( $string, $length, $exception = null ) {
		if ( null !== $exception ) {
			$this->expectException( $exception );
		}

		$result = DataHelper::validate_an( $string, $length, 'PHPUnit test' );

		if ( null === $exception ) {
			$this->assertTrue( $result );
		}
	}

	/**
	 * Validate AN provider.
	 *
	 * @return array<array<string|int>>
	 */
	public function validate_an_provider() {
		return [
			// Valid.
			[ '1234567890', 50 ],
			/**
			 * In `DataHelper::validate_an` we have used `wp_strip_all_tags` which also uses `trim`.
			 * Therefor we test on white space left and right in string, this shold be allowed.
			 *
			 * @link https://github.com/WordPress/WordPress/blob/5.2/wp-includes/formatting.php#L5070-L5092
			 */
			[ ' 1234567890', 50 ],
			[ '1234567890 ', 50 ],
			[ ' 1234567890 ', 50 ],
			// Invalid.
			[ '<p>1234567890</p>', 50, \InvalidArgumentException::class ],
			[ '<script>1234567890</script>', 50, \InvalidArgumentException::class ],
		];
	}

	/**
	 * Test sanitize AN.
	 *
	 * @dataProvider sanitize_an_provider
	 * @param string $string   String.
	 * @param int    $length   Length.
	 * @param string $expected Expected.
	 */
	public function test_sanitize_an( $string, $length, $expected ) {
		$result = DataHelper::sanitize_an( $string, $length, 'PHPUnit test' );

		$this->assertEquals( $expected, $result );
	}

	/**
	 * Sanitize AN provider.
	 *
	 * @return array<array<string|int>>
	 */
	public function sanitize_an_provider() {
		return [
			// Nothing to-do.
			[ '1234567890', 10, '1234567890' ],
			[ '1234567890', 100, '1234567890' ],
			// Shorten.
			[ '1234567890', 5, '12345' ],
			// UTF-8 test.
			[ 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ', 5, 'ÀÁÂÃÄ' ],
			// UTF-8 test Arabic (https://en.wikipedia.org/wiki/Arabic).
			[ 'كنت أريد أن أقرأ كتابا عن تاريخ المرأة في فرنسا', 10, 'كنت أريد أ' ],
			// HTML tags.
			[ 'test <strong>abcd</strong> 1234', 20, 'test abcd 1234' ],
			[ '12345<strong>67890</strong>', 10, '1234567890' ],
			[ "line 1\r\nline 2\r\nline 3", 100, "line 1\r\nline 2\r\nline 3" ],
		];
	}
}
