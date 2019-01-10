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
	 * Test validate AN.
	 */
	public function validate_an_html_special_chars() {
		$value = '1234567890123456789012345678901234567890123456789&';

		$result = DataHelper::validate_an_html_special_chars( $value, 50 );
	}
}
