<?php
/**
 * Message
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Message
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
abstract class Message implements Signable {
	/**
	 * Signature.
	 *
	 * @var string
	 */
	public $signature;

	/**
	 * Construct message.
	 *
	 * @param string $signature Signature.
	 */
	public function __construct( $signature ) {
		$this->signature = $signature;
	}

	/**
	 * Get signature.
	 *
	 * @return string
	 */
	public function get_signature() {
		return $this->signature;
	}

	/**
	 * Check if this message is valid.
	 *
	 * @param string $signing_key Signing key.
	 * @return bool True if valid, false otherwise.
	 */
	public function is_valid( $signing_key ) {
		$signature = Security::get_signature( $this, $signing_key );

		return Security::validate_signature( $signature, $this->get_signature() );
	}
}
