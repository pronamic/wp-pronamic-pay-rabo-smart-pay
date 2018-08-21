<?php
/**
 * Return handler
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use JsonSchema\Validator;
use Pronamic\WordPress\Pay\GatewayPostType;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Core\Gateway;

/**
 * Return handler
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class ReturnHandler {
	/**
	 * Handle return.
	 *
	 * @param array $input Input data.
	 * @return bool True if handled, false otherwise.
	 */
	public static function handle_return( $input ) {
		
	}
}
