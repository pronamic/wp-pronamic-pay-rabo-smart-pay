<?php
/**
 * Order results
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Order results.
 *
 * @author  Remco Tolsma
 * @version 2.1.10
 * @since   1.0.0
 * @phpstan-implements \IteratorAggregate<int, OrderResult>
 */
class OrderResults extends ResponseMessage implements \IteratorAggregate {
	/**
	 * More order results available flag.
	 *
	 * @var bool
	 */
	private $more_available;

	/**
	 * Order results.
	 *
	 * @var array<OrderResult>
	 */
	private $order_results;

	/**
	 * Construct order results message.
	 *
	 * @param bool               $more_available True if more order results available, false otherwise.
	 * @param array<OrderResult> $order_results  Order results.
	 * @param string             $signature      Signature.
	 */
	public function __construct( $more_available, array $order_results, $signature ) {
		parent::__construct( $signature );

		$this->more_available = $more_available;
		$this->order_results  = $order_results;
	}

	/**
	 * More available.
	 *
	 * @return bool True if more order results available, false otherwise.
	 */
	public function more_available() {
		return $this->more_available;
	}

	/**
	 * Get signature data.
	 *
	 * @return array<string>
	 */
	public function get_signature_fields() {
		$fields = [];

		$fields[] = $this->more_available() ? 'true' : 'false';

		foreach ( $this->order_results as $order_result ) {
			$fields[] = $order_result->get_merchant_order_id();
			$fields[] = $order_result->get_omnikassa_order_id();
			$fields[] = \strval( $order_result->get_poi_id() );
			$fields[] = $order_result->get_order_status();
			$fields[] = $order_result->get_order_status_datetime();
			$fields[] = $order_result->get_error_code();

			$fields = $order_result->get_paid_amount()->get_signature_fields( $fields );
			$fields = $order_result->get_total_amount()->get_signature_fields( $fields );
		}

		return $fields;
	}

	/**
	 * Get iterator.
	 *
	 * @return \ArrayIterator<int, OrderResult>
	 */
	public function getIterator(): \Traversable {
		return new \ArrayIterator( $this->order_results );
	}

	/**
	 * Create order results from object.
	 *
	 * @param object $object Object.
	 * @return OrderResults
	 * @throws \InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->signature ) ) {
			throw new \InvalidArgumentException( 'Object must contain `signature` property.' );
		}

		if ( ! isset( $object->moreOrderResultsAvailable ) ) {
			throw new \InvalidArgumentException( 'Object must contain `moreOrderResultsAvailable` property.' );
		}

		if ( ! isset( $object->orderResults ) ) {
			throw new \InvalidArgumentException( 'Object must contain `orderResults` property.' );
		}

		if ( ! \is_array( $object->orderResults ) ) {
			throw new \InvalidArgumentException( 'The `orderResults` property must be an array.' );
		}

		$order_results = [];

		foreach ( $object->orderResults as $o ) {
			$order_results[] = OrderResult::from_object( $o );
		}

		return new self( $object->moreOrderResultsAvailable, $order_results, $object->signature );
	}

	/**
	 * Create notification from JSON string.
	 *
	 * @param string $json JSON string.
	 * @return OrderResults
	 * @throws \JsonSchema\Exception\ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_json( $json ) {
		$data = \json_decode( $json );

		$validator = new \JsonSchema\Validator();

		$validator->validate(
			$data,
			(object) [
				'$ref' => 'file://' . \realpath( __DIR__ . '/../json-schemas/order-results.json' ),
			],
			\JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
		);

		return self::from_object( $data );
	}
}
