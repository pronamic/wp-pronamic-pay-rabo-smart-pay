<?php
/**
 * Money
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
use JsonSchema\Validator;

/**
 * Money
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class Money {
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
	public function get_json() {
		return (object) array(
			'currency' => $this->get_currency(),
			'amount'   => $this->get_amount(),
		);
	}

	/**
	 * Create money from object.
	 *
	 * @param stdClass $object Object.
	 * @return Money
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( stdClass $object ) {
		if ( ! isset( $object->currency ) ) {
			throw new InvalidArgumentException( 'Object must contain `currency` property.' );
		}

		if ( ! isset( $object->amount ) ) {
			throw new InvalidArgumentException( 'Object must contain `amount` property.' );
		}

		return new self(
			$object->currency,
			$object->amount
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
		$data = json_decode( $json );

		$validator = new Validator();

		$validator->validate(
			$data,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/json-schema-money.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		return self::from_object( $data );
	}

	/**
	 * Get signature fields.
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function get_signature_fields( $fields = array() ) {
		$fields[] = $this->get_currency();
		$fields[] = strval( $this->get_amount() );

		return $fields;
	}
}
