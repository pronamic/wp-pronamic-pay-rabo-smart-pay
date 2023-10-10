<?php
/**
 * Transaction
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Transaction class
 */
final class Transaction {
	/**
	 * ID.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Payment brand.
	 *
	 * @var string
	 */
	private $payment_brand;

	/**
	 * Type.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Status.
	 *
	 * @var string
	 */
	private $status;

	/**
	 * Construct transaction.
	 *
	 * @param string $id            Transaction ID.
	 * @param string $payment_brand Payment brand.
	 * @param string $type          Transaction type.
	 * @param string $status        Transaction status.
	 */
	public function __construct( $id, $payment_brand, $type, $status ) {
		$this->id            = $id;
		$this->payment_brand = $payment_brand;
		$this->type          = $type;
		$this->status        = $status;
	}

	/**
	 * Get ID.
	 * 
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get status.
	 * 
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Create transaction from object.
	 *
	 * @param object $data Object.
	 * @return Transaction
	 * @throws \InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $data ) {
		if ( ! isset( $data->transactionId ) ) {
			throw new \InvalidArgumentException( 'Object must contain `transactionId` property.' );
		}

		if ( ! isset( $data->paymentBrand ) ) {
			throw new \InvalidArgumentException( 'Object must contain `paymentBrand` property.' );
		}

		if ( ! isset( $data->transactionType ) ) {
			throw new \InvalidArgumentException( 'Object must contain `transactionType` property.' );
		}

		if ( ! isset( $data->transactionStatus ) ) {
			throw new \InvalidArgumentException( 'Object must contain `transactionStatus` property.' );
		}

		return new self(
			$data->transactionId,
			$data->paymentBrand,
			$data->transactionType,
			$data->transactionStatus
		);
	}

	/**
	 * Create transaction from JSON string.
	 *
	 * @param string $json JSON string.
	 * @return Transaction
	 */
	public static function from_json( $json ) {
		$data = \json_decode( $json );

		return self::from_object( $data );
	}
}
