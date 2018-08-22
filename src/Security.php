<?php
/**
 * Security
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Security
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Security {
	/**
	 * Calculdate signature for specific data.
	 *
	 * @param Signable $signable    Signable object.
	 * @param string   $signing_key Signing Key.
	 * @return string|null
	 */
	public static function get_signature( Signable $signable, $signing_key ) {
		$data = $signable->get_signature_data();

		if ( empty( $data ) ) {
			return;
		}

		if ( empty( $signing_key ) ) {
			return;
		}

		$signature = hash_hmac(
			'sha512',
			implode( ',', $data ),
			base64_decode( $signing_key )
		);

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

		return ( 0 === strcasecmp( $signature_a, $signature_b ) );
	}
}
