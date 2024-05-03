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
final class RefundResponse {
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
	public function __construct( string $refund_id, string $transaction_id ) {
		$this->id             = $refund_id;
		$this->transaction_id = $transaction_id;
	}

	/**
	 * Create refund response from object.
	 *
	 * @param object $data Object.
	 * @return self
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		return new self(
			$object_access->get_string( 'refundId' ),
			$object_access->get_string( 'refundTransactionId' )
		);
	}
}
