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
	 * Validate HTML special chars.
	 *
	 * @param string $value Value to validate.
	 *
	 * @return bool
	 *
	 * @throws InvalidArgumentException Throws invalid argument exception when value contains HTML special chars.
	 */
	public static function validate_html_special_chars( $value ) {
		$html_special_chars = self::get_html_special_chars();

		foreach ( $html_special_chars as $char ) {
			if ( false !== mb_strpos( $value, $char ) ) {
				throw new InvalidArgumentException(
					sprintf(
						'Value "%s" can not contain the special HTML character `%s`.',
						$value,
						$char
					)
				);
			}
		}

		return true;
	}

	/**
	 * Shorten string to the specified length.
	 *
	 * @param string $string String.
	 * @param int    $length Length.
	 *
	 * @return string
	 */
	public static function shorten( $string, $length ) {
		return mb_strimwidth( $string, 0, $length, '…', 'UTF-8' );
	}

	/**
	 * Get HTML special chars.
	 *
	 * @return array
	 */
	private static function get_html_special_chars() {
		return array(
			// Ampersand.
			'&', // Ampersand (https://unicode-table.com/en/0026/).
			'＆', // Fullwidth Ampersand (https://unicode-table.com/en/FF06/).
			// Less-Than.
			'<', // Less-Than Sign (https://unicode-table.com/en/003C/).
			'﹤', // Small Less-Than Sign (https://unicode-table.com/en/FE64/).
			'＜', // Fullwidth Less-Than Sign (https://unicode-table.com/en/FF1C/).
			// Greater-Than.
			'>', // Greater-Than Sign (https://unicode-table.com/en/003E/).
			'﹥', // Small Greater-Than Sign (https://unicode-table.com/en/FE65/).
			'＞', // Fullwidth Greater-Than Sign (https://unicode-table.com/en/FF1E/).
		);
	}

	/**
	 * Replace HTML special chars with fullwidth Unicode characters.
	 *
	 * @param string $string String.
	 *
	 * @return string
	 */
	public static function replace_html_special_chars( $string ) {
		$replacement = '�'; // Replacement Character » https://unicode-table.com/en/FFFD/.

		return str_replace( self::get_html_special_chars(), $replacement, $string );
	}
}
