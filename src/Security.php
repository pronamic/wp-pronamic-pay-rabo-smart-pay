<?php
/**
 * Security
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Security
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   1.0.0
 */
final class Security {
	/**
	 * Get signature fields combined.
	 *
	 * @param array<string> $fields Fields.
	 * @return string
	 */
	public static function get_signature_fields_combined( $fields ) {
		return \implode( ',', $fields );
	}

	/**
	 * Calculate signature for specific data.
	 *
	 * @param Signable $signable    Signable object.
	 * @param string   $signing_key Signing Key.
	 * @return string
	 * @throws \InvalidArgumentException Signing key is invalid.
	 */
	public static function get_signature( Signable $signable, $signing_key ) {
		if ( empty( $signing_key ) ) {
			throw new \InvalidArgumentException(
				\sprintf(
					'Signing key "%s" is empty.',
					\esc_html( $signing_key )
				)
			);
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$decoded_signing_key = \base64_decode( $signing_key, true );

		if ( false === $decoded_signing_key ) {
			throw new \InvalidArgumentException(
				\sprintf(
					'Signing key "%s" contains character from outside the base64 alphabet.',
					\esc_html( $signing_key )
				)
			);
		}

		$fields = $signable->get_signature_fields();

		$combined = self::get_signature_fields_combined( $fields );

		$signature = \hash_hmac( 'sha512', $combined, $decoded_signing_key );

		return $signature;
	}

	/**
	 * Validate signature.
	 *
	 * @param string $signature_a Signature A.
	 * @param string $signature_b Signature B.
	 * @return bool True if valid, false otherwise.
	 */
	public static function validate_signature( $signature_a, $signature_b ) {
		if ( empty( $signature_a ) || empty( $signature_b ) ) {
			// Empty signature string or null from calculation.
			return false;
		}

		$result = ( 0 === \strcasecmp( $signature_a, $signature_b ) );

		return $result;
	}
}
