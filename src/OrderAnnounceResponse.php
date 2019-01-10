<?php
/**
 * Order announce response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use InvalidArgumentException;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Order announce response
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class OrderAnnounceResponse extends ResponseMessage {
	/**
	 * Redirect URL.
	 *
	 * @var string
	 */
	private $redirect_url;

	/**
	 * Construct notification message.
	 *
	 * @param string $redirect_url Redirect URL.
	 * @param string $signature    Signature.
	 */
	public function __construct( $redirect_url, $signature ) {
		parent::__construct( $signature );

		$this->redirect_url = $redirect_url;
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
	 * @return array
	 */
	public function get_signature_fields() {
		return array(
			$this->get_redirect_url(),
		);
	}

	/**
	 * Create notification from object.
	 *
	 * @param object $object Object.
	 * @return OrderAnnounceResponse
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->signature ) ) {
			throw new InvalidArgumentException( 'Object must contain `signature` property.' );
		}

		if ( ! isset( $object->redirectUrl ) ) {
			throw new InvalidArgumentException( 'Object must contain `redirectUrl` property.' );
		}

		return new self(
			$object->redirectUrl,
			$object->signature
		);
	}

	/**
	 * Create order announce response from JSON string.
	 *
	 * @param string $json JSON string.
	 * @return OrderAnnounceResponse
	 * @throws \JsonSchema\Exception\ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_json( $json ) {
		$data = json_decode( $json );

		$validator = new Validator();

		$validator->validate(
			$data,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/order-announce-response.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		return self::from_object( $data );
	}
}
