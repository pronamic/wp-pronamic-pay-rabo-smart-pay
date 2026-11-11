<?php
/**
 * Refund response
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Refund response class
 *
 * All properties of the `RefundInfo` schema in the Rabo Smart Pay OpenAPI
 * definition are optional, Rabo Smart Pay does not always return the
 * `refundTransactionId`, for example.
 *
 * @link https://docs.developer.rabobank.com/smartpay/reference/create-refund
 */
final class RefundResponse {
	/**
	 * Refund ID.
	 *
	 * @var string|null
	 */
	public ?string $id;

	/**
	 * Refund transaction ID.
	 *
	 * @var string|null
	 */
	public ?string $transaction_id;

	/**
	 * Status.
	 *
	 * `PENDING`, `SUCCEEDED`, `FAILED` or `UNKNOWN`.
	 *
	 * @var string|null
	 */
	public ?string $status;

	/**
	 * Construct refund response.
	 *
	 * @param string|null $refund_id      Refund ID.
	 * @param string|null $transaction_id Refund transaction ID.
	 * @param string|null $status         Status.
	 */
	public function __construct( ?string $refund_id, ?string $transaction_id, ?string $status = null ) {
		$this->id             = $refund_id;
		$this->transaction_id = $transaction_id;
		$this->status         = $status;
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
			$object_access->get_optional_string( 'refundId' ),
			$object_access->get_optional_string( 'refundTransactionId' ),
			$object_access->get_optional_string( 'status' )
		);
	}
}
