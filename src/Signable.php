<?php
/**
 * Signable
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 signable
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
abstract class Signable {
	/**
	 * Signing key.
	 *
	 * @var string
	 */
	protected $signing_key;

	/**
	 * Get signature data.
	 *
	 * @return array
	 */
	abstract public function get_signature_data();

	/**
	 * Set sigining key.
	 *
	 * @param string $signing_key Signing key.
	 */
	public function set_signing_key( $signing_key ) {
		$this->signing_key = $signing_key;
	}

	/**
	 * Get signature.
	 *
	 * @return string|void
	 */
	public function get_signature() {
		$data = $this->get_signature_data();

		return Security::calculate_signature( $data, $this->signing_key );
	}
}
