<?php
/**
 * Idempotence
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Idempotence interface
 *
 * @author  Remco Tolsma
 * @since   2.1.0
 * @version 2.0.4
 */
interface IdempotenceInterface {
	/**
	 * Get idempotence ID.
	 *
	 * @return string
	 */
	public function get_idempotence_id(): string;
}
