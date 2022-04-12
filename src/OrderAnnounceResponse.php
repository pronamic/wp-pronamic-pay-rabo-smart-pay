<?php
/**
 * Order announce response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Order announce response
 *
 * @author  Remco Tolsma
 * @version 2.2.4
 * @since   2.0.2
 */
class OrderAnnounceResponse extends ResponseMessage {
	/**
	 * OmniKassa order ID.
	 *
	 * @var string
	 */
	private $omnikassa_order_id;

	/**
	 * Redirect URL.
	 *
	 * @var string
	 */
	private $redirect_url;

	/**
	 * Construct notification message.
	 *
	 * @param string $omnikassa_order_id OmniKassa order ID.
	 * @param string $redirect_url       Redirect URL.
	 */
	public function __construct( $omnikassa_order_id, $redirect_url ) {
		parent::__construct();

		$this->omnikassa_order_id = $omnikassa_order_id;
		$this->redirect_url       = $redirect_url;
	}

	/**
	 * Get OmniKassa order ID.
	 *
	 * @return string
	 */
	public function get_omnikassa_order_id() {
		return $this->omnikassa_order_id;
	}

	/**
	 * Get redirect URL.
	 *
	 * @return string
	 */
	public function get_redirect_url() {
		return $this->redirect_url;
	}

	/**
	 * Get signature fields.
	 *
	 * @return array<string>
	 */
	public function get_signature_fields() {
		return [];
	}

	/**
	 * Create notification from object.
	 *
	 * @param object $object Object.
	 * @return OrderAnnounceResponse
	 * @throws \InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->omnikassaOrderId ) ) {
			throw new \InvalidArgumentException( 'Object must contain `omnikassaOrderId` property.' );
		}

		if ( ! isset( $object->redirectUrl ) ) {
			throw new \InvalidArgumentException( 'Object must contain `redirectUrl` property.' );
		}

		return new self( $object->omnikassaOrderId, $object->redirectUrl );
	}

	/**
	 * Create order announce response from JSON string.
	 *
	 * @param string $json JSON string.
	 * @return OrderAnnounceResponse
	 * @throws \JsonSchema\Exception\ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_json( $json ) {
		$data = \json_decode( $json );

		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$data,
			(object) [
				'$ref' => 'file://' . \realpath( __DIR__ . '/../json-schemas/order-announce-response.json' ),
			],
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		return self::from_object( $data );
	}
}
