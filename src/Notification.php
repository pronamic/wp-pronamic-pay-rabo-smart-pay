<?php
/**
 * Notification
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Notification
 *
 * @author  Remco Tolsma
 * @version 2.1.10
 * @since   2.0.2
 */
final class Notification extends ResponseMessage {
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
	 * Identification of the webshop (point of interaction), seen from ROK. This is relevant if several webshops
	 * use the same webhook URL.
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
	 * @return bool True if notice authentication token is expired, false otherwise.
	 */
	public function is_expired() {
		$timestamp = \strtotime( $this->get_expiry() );

		if ( false === $timestamp ) {
			return true;
		}

		return $timestamp > \time();
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
	 * @return array<string>
	 */
	public function get_signature_fields() {
		return [
			$this->get_authentication(),
			$this->get_expiry(),
			$this->get_event_name(),
			\strval( $this->get_poi_id() ),
		];
	}

	/**
	 * Create notification from object.
	 *
	 * @param \stdClass $data Object.
	 * @return Notification
	 */
	public static function from_object( \stdClass $data ) {
		$object_access = new ObjectAccess( $data );

		return new self(
			$object_access->get_string( 'authentication' ),
			$object_access->get_string( 'expiry' ),
			$object_access->get_string( 'eventName' ),
			$object_access->get_int( 'poiId' ),
			$object_access->get_string( 'signature' )
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
		$data = \json_decode( $json );

		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$data,
			(object) [
				'$ref' => 'file://' . \realpath( __DIR__ . '/../json-schemas/notification.json' ),
			],
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		return self::from_object( $data );
	}
}
