<?php
/**
 * Money
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Money
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   2.0.2
 */
final class Money implements \JsonSerializable {
	/**
	 * Currency.
	 *
	 * @var string
	 */
	private $currency;

	/**
	 * Amount.
	 *
	 * @var int
	 */
	private $amount;

	/**
	 * Construct Money.
	 *
	 * @param string $currency Currency.
	 * @param int    $amount   Amount in cents.
	 */
	public function __construct( $currency, $amount ) {
		$this->currency = $currency;
		$this->amount   = $amount;
	}

	/**
	 * Get currency.
	 *
	 * @return string
	 */
	public function get_currency() {
		return $this->currency;
	}

	/**
	 * Get amount.
	 *
	 * @return int
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return (object) [
			'amount'   => $this->get_amount(),
			'currency' => $this->get_currency(),
		];
	}

	/**
	 * Create money from object.
	 *
	 * @param object $data Object.
	 * @return Money
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		return new self(
			$object_access->get_string( 'currency' ),
			$object_access->get_int( 'amount' )
		);
	}

	/**
	 * Create money from JSON string.
	 *
	 * @param string $json JSON string.
	 * @return Money
	 * @throws \JsonSchema\Exception\ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_json( $json ) {
		$data = \json_decode( $json );

		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$data,
			(object) [
				'$ref' => 'file://' . \realpath( __DIR__ . '/../json-schemas/json-schema-money.json' ),
			],
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		return self::from_object( $data );
	}

	/**
	 * Get signature fields.
	 *
	 * @param array<string> $fields Fields.
	 * @return array<string>
	 */
	public function get_signature_fields( $fields = [] ) {
		$fields[] = $this->get_currency();
		$fields[] = \strval( $this->get_amount() );

		return $fields;
	}
}
