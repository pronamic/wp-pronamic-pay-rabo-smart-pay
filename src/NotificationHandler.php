<?php
/**
 * Notification handler
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
 * Notification handler
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class NotificationHandler {
	/**
	 * Handle notification.
	 *
	 * @param string $notification JSON notification message.
	 * @return bool True if handled, false otherwise.
	 */
	public static function handle_notification( $notification ) {
		$data = json_decode( $notification );

		if ( ! is_object( $data ) ) {
			return false;
		}

		// Validate JSON.
		$validator = new Validator();

		$validator->validate( $data, (object) array(
			'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/json-schema-notification.json' ),
		) );

		if ( ! $validator->isValid() ) {
			return false;
		}

		// For the time being this is always: merchant.order.status.changed.
		if ( 'merchant.order.status.changed' !== $data->eventName ) {
			return false;
		}

		

		// Return result.
		return true;
	}
}
