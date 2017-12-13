<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 security
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Security {
	public static function calculate_signature( $data, $signing_key ) {
		if ( ! is_array( $data ) ) {
			return;
		}

		if ( '' === $signing_key ) {
			return;
		}

		$signature = hash_hmac(
			'sha512',
			implode( ',', $data ),
			base64_decode( $signing_key )
		);

		return $signature;
	}

	public static function validate_signature( $signature_a, $signature_b ) {
		if ( empty( $signature_a ) || empty( $signature_b ) ) {
			// Empty signature string or null from calculation.

			return false;
		}

		if ( 0 === strcasecmp( $signature_a, $signature_b ) ) {
			// Valid signature
			return true;
		}

		return false;
	}
}
