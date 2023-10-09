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
	 * Create transaction from object.
	 *
	 * @param object $object Object.
	 * @return Transaction
	 * @throws \InvalidArgumentException Throws invalid argument exception when object does not contains the required properties.
	 */
	public static function from_object( $object ) {
		if ( ! isset( $object->transactionId ) ) {
			throw new \InvalidArgumentException( 'Object must contain `transactionId` property.' );
		}

		if ( ! isset( $object->paymentBrand ) ) {
			throw new \InvalidArgumentException( 'Object must contain `paymentBrand` property.' );
		}

		if ( ! isset( $object->transactionType ) ) {
			throw new \InvalidArgumentException( 'Object must contain `transactionType` property.' );
		}

		if ( ! isset( $object->transactionStatus ) ) {
			throw new \InvalidArgumentException( 'Object must contain `transactionStatus` property.' );
		}

		return new self(
			$object->transactionId,
			$object->paymentBrand,
			$object->transactionType,
			$object->transactionStatus
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
