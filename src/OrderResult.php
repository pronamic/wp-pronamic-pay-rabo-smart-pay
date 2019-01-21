<?php
/**
 * Order result
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use stdClass;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Order result
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class OrderResult {
	/**
	 * OrderId as delivered during the Order Announce.
	 *
	 * @var string
	 */
	private $merchant_order_id;

	/**
	 * The unique id that the omnikassa has assigned to this order.
	 *
	 * @var string
	 */
	private $omnikassa_order_id;

	/**
	 * Unique identification of the webshop (point of interaction), seen from ROK.
	 * This is relevant if several webshops use the same webhook URL.
	 *
	 * @var int|string
	 */
	private $poi_id;

	/**
	 * The status of the order. See chapter "Consumer returns at the webshop" for an overview of the possible statuses.
	 *
	 * @var string
	 */
	private $order_status;

	/**
	 * The moment this status is reached.
	 *
	 * @var string
	 */
	private $order_status_datetime;

	/**
	 * Future field, for now: always empty.
	 *
	 * @var string
	 */
	private $error_code;

	/**
	 * Paid amount.
	 *
	 * @var Money
	 */
	private $paid_amount;

	/**
	 * Total amount.
	 *
	 * @var Money
	 */
	private $total_amount;

	/**
	 * Construct order result.
	 *
	 * @param string     $merchant_order_id     Merchant order ID.
	 * @param string     $omnikassa_order_id    OmniKassa order ID.
	 * @param int|string $poi_id                Point of interaction ID.
	 * @param string     $order_status          Order status.
	 * @param string     $order_status_datetime Order status datetime.
	 * @param string     $error_code            Error code.
	 * @param Money      $paid_amount           Paid amount.
	 * @param Money      $total_amount          Total amount.
	 */
	public function __construct( $merchant_order_id, $omnikassa_order_id, $poi_id, $order_status, $order_status_datetime, $error_code, Money $paid_amount, Money $total_amount ) {
		$this->merchant_order_id     = $merchant_order_id;
		$this->omnikassa_order_id    = $omnikassa_order_id;
		$this->poi_id                = $poi_id;
		$this->order_status          = $order_status;
		$this->order_status_datetime = $order_status_datetime;
		$this->error_code            = $error_code;
		$this->paid_amount           = $paid_amount;
		$this->total_amount          = $total_amount;
	}

	/**
	 * Get merchant order ID.
	 *
	 * @return string
	 */
	public function get_merchant_order_id() {
		return $this->merchant_order_id;
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
	 * Get point of interaction ID.
	 *
	 * @return int|string
	 */
	public function get_poi_id() {
		return $this->poi_id;
	}

	/**
	 * Get order status.
	 *
	 * @return string
	 */
	public function get_order_status() {
		return $this->order_status;
	}

	/**
	 * Get order status datetime.
	 *
	 * @return string
	 */
	public function get_order_status_datetime() {
		return $this->order_status_datetime;
	}

	/**
	 * Get error code.
	 *
	 * @return string
	 */
	public function get_error_code() {
		return $this->error_code;
	}

	/**
	 * Get paid amount.
	 *
	 * @return Money
	 */
	public function get_paid_amount() {
		return $this->paid_amount;
	}

	/**
	 * Get total amount.
	 *
	 * @return Money
	 */
	public function get_total_amount() {
		return $this->total_amount;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		return (object) array(
			'merchantOrderId'     => $this->get_merchant_order_id(),
			'omnikassaOrderId'    => $this->get_omnikassa_order_id(),
			'poiId'               => $this->get_poi_id(),
			'orderStatus'         => $this->get_order_status(),
			'orderStatusDateTime' => $this->get_order_status_datetime(),
			'errorCode'           => $this->get_error_code(),
			'paidAmount'          => $this->get_paid_amount()->get_json(),
			'totalAmount'         => $this->get_total_amount()->get_json(),
		);
	}

	/**
	 * Create order result from object.
	 *
	 * @param stdClass $object Object.
	 * @return OrderResult
	 * @throws InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( stdClass $object ) {
		if ( ! isset( $object->merchantOrderId ) ) {
			throw new InvalidArgumentException( 'Object must contain `merchantOrderId` property.' );
		}

		if ( ! isset( $object->omnikassaOrderId ) ) {
			throw new InvalidArgumentException( 'Object must contain `omnikassaOrderId` property.' );
		}

		if ( ! isset( $object->poiId ) ) {
			throw new InvalidArgumentException( 'Object must contain `poiId` property.' );
		}

		if ( ! isset( $object->orderStatus ) ) {
			throw new InvalidArgumentException( 'Object must contain `orderStatus` property.' );
		}

		if ( ! isset( $object->orderStatusDateTime ) ) {
			throw new InvalidArgumentException( 'Object must contain `orderStatusDateTime` property.' );
		}

		if ( ! isset( $object->errorCode ) ) {
			throw new InvalidArgumentException( 'Object must contain `errorCode` property.' );
		}

		if ( ! isset( $object->paidAmount ) ) {
			throw new InvalidArgumentException( 'Object must contain `paidAmount` property.' );
		}

		if ( ! isset( $object->totalAmount ) ) {
			throw new InvalidArgumentException( 'Object must contain `totalAmount` property.' );
		}

		return new self(
			$object->merchantOrderId,
			$object->omnikassaOrderId,
			$object->poiId,
			$object->orderStatus,
			$object->orderStatusDateTime,
			$object->errorCode,
			Money::from_object( $object->paidAmount ),
			Money::from_object( $object->totalAmount )
		);
	}

	/**
	 * Create notification from JSON string.
	 *
	 * @param string $json JSON string.
	 * @return OrderResult
	 * @throws \JsonSchema\Exception\ValidationException Throws JSON schema validation exception when JSON is invalid.
	 */
	public static function from_json( $json ) {
		$data = json_decode( $json );

		$validator = new Validator();

		$validator->validate(
			$data,
			(object) array(
				'$ref' => 'file://' . realpath( __DIR__ . '/../json-schemas/json-schema-order-result.json' ),
			),
			Constraint::CHECK_MODE_EXCEPTIONS
		);

		return self::from_object( $data );
	}
}
