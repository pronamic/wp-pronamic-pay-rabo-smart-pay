<?php
/**
 * Notification
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use InvalidArgumentException;
use stdClass;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Notification
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class Notification extends ResponseMessage {
	/**
	 * The token that can be used to do the status pull.
	 *
	 * @var string
	 */
	private $authentication;

	/**
	 * The validity period of the token, in the ISO-8601 format (yyyy- MM-ddTHH: mm: ss.SSSZZ).
	 *
	 * @var string
	 */
	private $expiry;

	/**
	 * The type of notification. For the time being this is always: `merchant.order.status.changed`.
	 *
	 * @var string
	 */
	private $event_name;

	/**
	 * Identification of the webshop (point of interaction), seen from ROK. This is relevant if several webshops use the same webhook URL.
	 *
	 * @var int|string
	 */
	private $poi_id;

	/**
	 * Construct notification message.
	 *
	 * @param string     $authentication Authentication.
	 * @param string     $expiry         Expiry.
	 * @param string     $event_name     Event name.
	 * @param int|string $poi_id         POI ID.
	 * @param string     $signature      Signature.
	 */
	public function __construct( $authentication, $expiry, $event_name, $poi_id, $signature ) {
		parent::__construct( $signature );

		$this->authentication = $authentication;
		$this->expiry         = $expiry;
		$this->event_name     = $event_name;
		$this->poi_id         = $poi_id;
	}

	/**
	 * Get authentication.
	 *
	 * @return string
	 */
	public function get_authentication() {
		return $this->authentication;
	}

	/**
	 * Get expiry.
	 *
	 * @return string
	 */
	public function get_expiry() {
		return $this->expiry;
	}

	/**
	 * Check if this notice (authentication token) is expired.
	 *
	 * @return bool True if notice authentication token is epxired, false otherwise.
	 */
	public function is_expired() {
		$timestamp = strtotime( $this->get_expiry() );

		if ( false === $timestamp ) {
			return true;
		}

		return $timestamp > time();
	}

	/**
	 * Get event name.
	 *
	 * @return string
	 */
	public function get_event_name() {
		return $this->event_name;
	}

	/**
	 * Get point of interaction ID.
	 *
	 * @return int|string
	 */
	public function get_poi_id() {
		return $this->poi_id;
	}

	/**
	 * Get signature fields.
	 *
	 * @return array
	 */
	public function get_signature_fields() {
		return array(
			$this->get_authentication(),
			$this->get_expiry(),
			$this->get_event_name(),
			$this->get_poi_id(),
		);
	}

	/**
	 * Create notification from object.
	 *
	 * @param stdClass $object Object.
	 * @return Notification
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( stdClass $object ) {
		if ( ! isset( $object->signature ) ) {
			throw new InvalidArgumentException( 'Object must contain `signature` property.' );
		}

		if ( ! isset( $object->authentication ) ) {
			throw new InvalidArgumentException( 'Object must contain `authentication` property.' );
		}

		if ( ! isset( $object->expiry ) ) {
			throw new InvalidArgumentException( 'Object must contain `expiry` property.' );
		}

		if ( ! isset( $object->eventName ) ) {
			throw new InvalidArgumentException( 'Object must contain `eventName` property.' );
		}

		if ( ! isset( $object->poiId ) ) {
			throw new InvalidArgumentException( 'Object must contain `poiId` property.' );
		}

		return new self(
			$object->authentication,
			$object->expiry,
			$object->eventName,
			$object->poiId,
			$object->signature
		);
	}

	/**
	 * Create notification from JSON string.
	 *
	 * @param string $json JSON string.
	 * @return Notification
	 * @throws \JsonSchema\Exception\ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_json( $json ) {
		$data = json_decode( $json );

		$validator = new Validator();

		$validator->validate(
			$data,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/notification.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		return self::from_object( $data );
	}
}
