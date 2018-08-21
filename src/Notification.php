<?php
/**
 * Notification
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Notification
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Notification extends Message {
	public static function from_object( stdClass $object ) {
		$notification = new self();

		if ( isset( $object->signature ) ) {
			$notification->signature = $object->signature;
		}

		if ( isset( $object->authentication ) ) {
			$notification->authentication = $object->authentication;
		}

		if ( isset( $object->expiry ) ) {
			$notification->expiry = $object->expiry;
		}

		if ( isset( $object->eventName ) ) {
			$notification->event_name = $object->eventName;
		}

		if ( isset( $object->poiId ) ) {
			$notification->poi_id = $object->poiId;
		}

		return $notification;
	}
}
