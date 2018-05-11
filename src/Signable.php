<?php

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
	protected $signing_key;

	abstract public function get_signature_data();

	/**
	 * Set sigining key.
	 *
	 * @param $signing_key
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
