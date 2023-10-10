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
	 * Get payment brand.
	 * 
	 * @return string
	 */
	public function get_payment_brand() {
		return $this->payment_brand;
	}

	/**
	 * Get type.
	 * 
	 * @return string
	 */
	public function get_type() {
		return $this->type;
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
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		return new self(
			$object_access->get_string( 'transactionId' ),
			$object_access->get_string( 'paymentBrand' ),
			$object_access->get_string( 'transactionType' ),
			$object_access->get_string( 'transactionStatus' )
		);
	}
}
