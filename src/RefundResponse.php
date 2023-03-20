<?php
/**
 * Refund response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Refund response class
 */
class RefundResponse {
	/**
	 * Refund ID.
	 *
	 * @var string
	 */
	public string $id;

	/**
	 * Transaction ID.
	 *
	 * @var string
	 */
	public string $transaction_id;

	/**
	 * Construct refund response.
	 *
	 * @param string $refund_id      Refund ID.
	 * @param string $transaction_id Transaction ID.
	 */
	public function __construct( string, $refund_id, string $transaction_id ) {
		$this->id             = $refund_id;
		$this->transaction_id = $transaction_id;
	}

	/**
	 * Create refund response from object.
	 *
	 * @param object $object Object.
	 * @return self
	 * @throws \InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->refundId ) ) {
			throw new \InvalidArgumentException( 'Object must contain `refundId` property.' );
		}

		if ( ! isset( $object->refundTransactionId ) ) {
			throw new \InvalidArgumentException( 'Object must contain `refundTransactionId` property.' );
		}

		return new self( $object->refundId, $object->refundTransactionId );
	}
}
