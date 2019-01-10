<?php
/**
 * Data helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use InvalidArgumentException;

/**
 * Data helper
 *
 * @link    https://github.com/wp-pay-gateways/ideal-basic/blob/2.0.0/src/DataHelper.php
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class DataHelper {
	/**
	 * Validate AN..$max.
	 *
	 * @param string $value Value to validate.
	 * @param int    $max   Max length of value.
	 *
	 * @return bool
	 *
	 * @throws InvalidArgumentException Throws invalid argument exception when string is longer then max length.
	 */
	public static function validate_an( $value, $max ) {
		if ( mb_strlen( $value, 'UTF-8' ) > $max ) {
			throw new InvalidArgumentException(
				sprintf(
					'Value "%s" can not be longer then `%d`.',
					$value,
					$max
				)
			);
		}

		return true;
	}

	/**
	 * Validate AN..$max and take HTML special charachters in account.
	 *
	 * @param string $value Value to validate.
	 * @param int    $max   Max length of value.
	 *
	 * @return bool
	 *
	 * @throws InvalidArgumentException Throws invalid argument exception when string is longer then max length.
	 */
	public static function validate_an_html_special_chars( $value, $max ) {
		return self::validate_an( $value, self::length_html_special_chars( $value, $max ) );
	}

	/**
	 * Validate null or AN..$max.
	 *
	 * @param string|null $value Value to validate.
	 * @param int         $max   Max length of value.
	 *
	 * @return bool
	 *
	 * @throws InvalidArgumentException Throws invalid argument exception when value is not null and longer then max length.
	 */
	public static function validate_null_or_an( $value, $max ) {
		if ( null === $value ) {
			return true;
		}

		return self::validate_an( $value, $max );
	}

	/**
	 * Shorten string to the specified length.
	 *
	 * @param string $value  Value.
	 * @param int    $length Length.
	 *
	 * @return string
	 */
	public static function shorten( $string, $length ) {
		return mb_substr( $string, 0, $length, 'UTF-8' );
	}

	/**
	 * Shorten string to the specified length and take HTML special charachters in account.
	 *
	 * @param string $value  Value.
	 * @param int    $length Length.
	 *
	 * @return string
	 */
	public static function shorten_html_special_chars( $string, $length ) {
		return self::shorten( $string, self::length_html_special_chars( $string, $length ) );
	}

	/**
	 * Determine length of string and take HTML special charachters in account.
	 *
	 * @param string $value  Value.
	 * @param int    $length Length.
	 */
	private static function length_html_special_chars( $string, $length ) {
		$string_length = mb_strlen( $string, 'UTF-8' );
		$html_length   = mb_strlen( htmlspecialchars( $string, ENT_NOQUOTES ), 'UTF-8' );

		$length = $length - ( $html_length - $string_length );

		return $length;
	}
}
