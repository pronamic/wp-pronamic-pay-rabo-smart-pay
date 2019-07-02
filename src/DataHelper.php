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

		/**
		 * HTML tags are not allowed.
		 *
		 * @link https://stackoverflow.com/questions/5732758/detect-html-tags-in-a-string
		 */
		if ( wp_strip_all_tags( $value ) !== $value ) {
			throw new InvalidArgumentException(
				sprintf(
					'HTML tags are not allowed: `%s`.',
					$value
				)
			);
		}

		return true;
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
	 * Sanitize string to the specified length.
	 *
	 * @param string $string String.
	 * @param int    $length Length.
	 *
	 * @return string
	 */
	public static function sanitize_an( $string, $length ) {
		/**
		 * HTML tags are not allowed.
		 *
		 * @link https://stackoverflow.com/questions/5732758/detect-html-tags-in-a-string
		 */
		$sanitized = wp_strip_all_tags( $string );

		/**
		 * In version `2.1.6` of this library we used the `mb_strimwidth`
		 * function, unfortunately this function is not alwys  available.
		 * Therefor we now use the `mb_substr`, WordPress is shipped
		 * with a compat function.
		 *
		 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-includes/compat.php#L44-L217
		 */
		$sanitized = mb_substr( $sanitized, 0, $length );

		return $sanitized;
	}
}
