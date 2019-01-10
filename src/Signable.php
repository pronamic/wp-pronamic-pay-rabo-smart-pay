<?php
/**
 * Signable
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Signable
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   1.0.0
 */
interface Signable {
	/**
	 * Get signature fields.
	 *
	 * @return array
	 */
	public function get_signature_fields();
}
