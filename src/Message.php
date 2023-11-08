<?php
/**
 * Message
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Message
 *
 * @author  Remco Tolsma
 * @version 2.2.4
 * @since   2.0.2
 */
abstract class Message implements Signable {
	/**
	 * Signature.
	 *
	 * @var string|null
	 */
	private $signature;

	/**
	 * Get signature.
	 *
	 * @return string|null
	 */
	public function get_signature() {
		return $this->signature;
	}

	/**
	 * Set signature.
	 *
	 * @param string|null $signature Signature.
	 * @return void
	 */
	protected function set_signature( $signature ) {
		$this->signature = $signature;
	}

	/**
	 * Sign this message with specified signing key.
	 *
	 * @param string $signing_key Signing key.
	 * @return void
	 */
	public function sign( $signing_key ) {
		$signature = Security::get_signature( $this, $signing_key );

		$this->set_signature( $signature );
	}

	/**
	 * Check if this message is valid.
	 *
	 * @param string $signing_key Signing key.
	 * @return bool True if valid, false otherwise.
	 */
	public function is_valid( $signing_key ) {
		try {
			$this->verify_signature( $signing_key );

			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Verify signature.
	 * 
	 * @param string $signing_key Signing key.
	 * @return void
	 * @throws \Pronamic\WordPress\Pay\Gateways\OmniKassa2\InvalidSignatureException Throws an exception when the signature cannot be verified.
	 */
	public function verify_signature( $signing_key ) {
		$signature_enclosed = (string) $this->get_signature();

		$signature_calculated = Security::get_signature( $this, $signing_key );

		$result = Security::validate_signature( $signature_enclosed, $signature_calculated );

		if ( false === $result ) {
			throw new \Pronamic\WordPress\Pay\Gateways\OmniKassa2\InvalidSignatureException(
				\sprintf(
					'Signature `%s` in message does not match signature `%s` calculated with signing key: `%s`.',
					\esc_html( $signature_enclosed ),
					\esc_html( $signature_calculated ),             
					\esc_html(
						\str_pad(
							\substr( $signing_key, 0, 7 ),
							\strlen( $signing_key ),
							'*'
						)
					)
				)
			);
		}
	}
}
