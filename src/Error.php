<?php
/**
 * Error
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Error
 *
 * @author  Remco Tolsma
 * @version 2.1.10
 * @since   2.0.2
 */
final class Error extends \Exception {
	/**
	 * Error code.
	 *
	 * @var int
	 */
	private $error_code;

	/**
	 * Error message.
	 *
	 * @var string|null
	 */
	private $error_message;

	/**
	 * Consumer message.
	 *
	 * @var string|null
	 */
	private $consumer_message;

	/**
	 * Construct error.
	 *
	 * @param int    $error_code Error code.
	 * @param string $message    Consumer or error message.
	 */
	public function __construct( $error_code, $message ) {
		parent::__construct( $message, $error_code );

		$this->error_code = $error_code;
	}

	/**
	 * Get error code.
	 *
	 * @return int
	 */
	public function get_error_code() {
		return $this->error_code;
	}

	/**
	 * Get error message.
	 *
	 * @return string|null
	 */
	public function get_error_message() {
		return $this->error_message;
	}

	/**
	 * Set error message.
	 *
	 * @param string|null $error_message Error message.
	 * @return void
	 */
	public function set_error_message( $error_message ) {
		$this->error_message = $error_message;
	}

	/**
	 * Get consumer message.
	 *
	 * @return string|null
	 */
	public function get_consumer_message() {
		return $this->consumer_message;
	}

	/**
	 * Set consumer message.
	 *
	 * @param string|null $consumer_message Consumer message.
	 * @return void
	 */
	public function set_consumer_message( $consumer_message ) {
		$this->consumer_message = $consumer_message;
	}

	/**
	 * Create error from object.
	 *
	 * @param object $data Object.
	 * @return Error
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		$error_code       = $object_access->get_int( 'errorCode' );
		$error_message    = null;
		$consumer_message = null;

		$message = \strval( $error_code );

		if ( $object_access->has_property( 'errorMessage' ) ) {
			$error_message = $object_access->get_string( 'errorMessage' );

			$message = $error_message;
		}

		if ( $object_access->has_property( 'consumerMessage' ) ) {
			$consumer_message = $object_access->get_string( 'consumerMessage' );

			$message = $consumer_message;
		}

		$error = new self( $error_code, $message );

		$error->set_error_message( $error_message );
		$error->set_consumer_message( $consumer_message );

		return $error;
	}

	/**
	 * Create error from JSON string.
	 *
	 * @param string $json JSON string.
	 * @return Error
	 * @throws \JsonSchema\Exception\ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_json( $json ) {
		$data = \json_decode( $json );

		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$data,
			(object) [
				'$ref' => 'file://' . \realpath( __DIR__ . '/../json-schemas/error.json' ),
			],
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		return self::from_object( $data );
	}
}
