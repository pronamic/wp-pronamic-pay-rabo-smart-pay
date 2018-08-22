<?php
/**
 * Error
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Exception;
use InvalidArgumentException;
use stdClass;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Error
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Error extends Exception {
	/**
	 * Code.
	 *
	 * @var string
	 */
	private $code;

	/**
	 * Message.
	 *
	 * @var string
	 */
	private $message;

	/**
	 * Consumer message.
	 *
	 * @var string
	 */
	private $consumer_message

	/**
	 * Construct error.
	 *
	 * @param string $code             Code.
	 * @param string $message          Message.
	 * @param string $consumer_message Consumer message.
	 */
	public function __construct( $code, $message, $consumer_message = null ) {
		parent::__construct( $message, $code );

		$this->code             = $code;
		$this->message          = $message;
		$this->consumer_message = $consumer_message;
	}

	/**
	 * Get codecode.
	 *
	 * @return string
	 */
	public function get_code() {
		return $this->code;
	}

	/**
	 * Get amount.
	 *
	 * @return int
	 */
	public function get_message() {
		return $this->message;
	}

	/**
	 * Create error from object.
	 *
	 * @param stdClass $object Object.
	 * @return Error
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( stdClass $object ) {
		if ( ! isset( $object->errorCode ) ) {
			throw new InvalidArgumentException( 'Object must contain `errorCode` property.' );
		}

		$message          = null;
		$consumer_message = null;

		if ( ! isset( $object->errorMessage ) ) {
			$message = $object->errorMessage;
		}

		if ( ! isset( $object->consumerMessage ) ) {
			$consumer_message = $object->consumerMessage;
		}

		return new self(
			$object->errorCode,
			$message,
			$consumer_message
		);
	}

	/**
	 * Create error from JSON string.
	 *
	 * @param string $json JSON string.
	 * @return Error
	 * @throws \JsonSchema\Exception\ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_json( $json ) {
		$data = json_decode( $json );

		$validator = new Validator();

		$validator->validate( $data, (object) array(
			'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/json-schema-error.json' ),
		), Constraint::CHECK_MODE_EXCEPTIONS );

		return self::from_object( $data );
	}
}
