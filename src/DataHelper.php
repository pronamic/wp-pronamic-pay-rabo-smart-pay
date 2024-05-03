<?php
/**
 * Data helper
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Data helper
 *
 * @link    https://github.com/wp-pay-gateways/ideal-basic/blob/2.0.0/src/DataHelper.php
 * @author  Remco Tolsma
 * @version 2.1.9
 * @since   2.0.2
 */
final class DataHelper {
	/**
	 * Strictly alphanumerical (letters and numbers only).
	 *
	 * The OmniKassa 2.0 document is not very clear about spaces, these are not allowed in AN (Strictly).
	 * If a space is used in a AN (Strictly) field this will result in for example the following error:
	 * `merchantOrderId should only contain alphanumeric characters`.
	 *
	 * @var array<string>
	 */
	private static $characters_ans = [ 'A-Z', 'a-z', '0-9' ];

	/**
	 * Validate AN..$max.
	 *
	 * @param string $value Value to validate.
	 * @param int    $max   Max length of value.
	 * @param string $field Field name.
	 * @return true
	 * @throws \InvalidArgumentException Throws invalid argument exception when string is longer then max length.
	 */
	public static function validate_an( $value, $max, $field ) {
		if ( \mb_strlen( $value, 'UTF-8' ) > $max ) {
			throw new \InvalidArgumentException(
				\sprintf(
					'Field `%s` value "%s" can not be longer then `%d`.',
					\esc_html( $field ),
					\esc_html( $value ),
					\intval( $max )
				)
			);
		}

		/**
		 * HTML tags are not allowed.
		 *
		 * @link https://stackoverflow.com/questions/5732758/detect-html-tags-in-a-string
		 */

		// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- We don't want the `trim` in `wp_strip_all_tags`.
		if ( \strip_tags( $value ) !== $value ) {
			throw new \InvalidArgumentException(
				\sprintf(
					'Field `%s` cannot contain HTML tags: `%s`.',
					\esc_html( $field ),
					\esc_html( $value )
				)
			);
		}

		return true;
	}

	/**
	 * Validate AN(Strictly)..Max nn.
	 *
	 * @param string $value Value to validate.
	 * @param int    $max   Max length of value.
	 * @param string $field Field name.
	 * @return true
	 * @throws \InvalidArgumentException Throws invalid argument exception when string is not alphanumeric characters.
	 * @throws \Exception Throws exception when PCRE regex execution results in error.
	 */
	public static function validate_ans( $value, $max, $field ) {
		$pattern = '#[^' . \implode( self::$characters_ans ) . ']#';

		$result = \preg_match( $pattern, $value );

		if ( false === $result ) {
			throw new \Exception(
				'PCRE regex execution error.',
				\intval( \preg_last_error() )
			);
		}

		if ( 1 === $result ) {
			throw new \InvalidArgumentException(
				\sprintf(
					'Field `%s` must consists strictly of alphanumeric characters: `%s`.',
					\esc_html( $field ),
					\esc_html( $value )
				)
			);
		}

		return self::validate_an( $value, $max, $field );
	}

	/**
	 * Validate null or AN..$max.
	 *
	 * @param string|null $value Value to validate.
	 * @param int         $max   Max length of value.
	 * @param string      $field Field name.
	 * @return true
	 * @throws \InvalidArgumentException Throws invalid argument exception when value is not null and longer then max
	 * length.
	 */
	public static function validate_null_or_an( $value, $max, $field ) {
		if ( null === $value ) {
			return true;
		}

		return self::validate_an( $value, $max, $field );
	}

	/**
	 * Sanitize string to the specified length.
	 *
	 * @param string $value  String.
	 * @param int    $length Length.
	 * @return string
	 */
	public static function sanitize_an( $value, $length ) {
		/**
		 * HTML tags are not allowed.
		 *
		 * @link https://stackoverflow.com/questions/5732758/detect-html-tags-in-a-string
		 */

		// phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- We don't want the `trim` in `wp_strip_all_tags`.
		$sanitized = \strip_tags( $value );

		/**
		 * In version `2.1.6` of this library we used the `mb_strimwidth`
		 * function, unfortunately this function is not always available.
		 * Therefore we now use the `mb_substr`, WordPress is shipped
		 * with a compat function.
		 *
		 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-includes/compat.php#L44-L217
		 */
		$sanitized = \mb_substr( $sanitized, 0, $length );

		return $sanitized;
	}
}
