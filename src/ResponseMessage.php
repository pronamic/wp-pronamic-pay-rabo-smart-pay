<?php
/**
 * Response message
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Response message
 *
 * @author  Remco Tolsma
 * @version 2.2.4
 * @since   2.0.2
 */
abstract class ResponseMessage extends Message {
	/**
	 * Construct message.
	 *
	 * @param string|null $signature Signature.
	 */
	public function __construct( $signature = null ) {
		$this->set_signature( $signature );
	}
}
