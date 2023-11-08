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
	 * Type of the transaction.
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
	 * Amount.
	 * 
	 * The total order amount in cents, including VAT. The amount must be equal
	 * to the sum over all order items of the piece price (including VAT)
	 * multiplied by the quantity. Note: if the amount is not equal to the sum
	 * of the amounts of the order items then 1. the order items from the order
	 * announcement are filtered, and 2. Riverty/AfterPay is not possible as a
	 * payment method
	 *
	 * @var Money
	 */
	private $amount;

	/**
	 * Amount.
	 * 
	 * The amount that was confirmed by the external payment processor. This
	 * field is only filled when the transaction status is SUCCESS or ACCEPTED.
	 *
	 * @var Money|null
	 */
	private $confirmed_amount;

	/**
	 * Start time.
	 * 
	 * @var string
	 */
	private $start_time;

	/**
	 * Last update time.
	 * 
	 * @var string
	 */
	private $last_update_time;

	/**
	 * Construct transaction.
	 *
	 * @param string $id            Transaction ID.
	 * @param string $payment_brand Payment brand.
	 * @param string $type          Transaction type.
	 * @param string $status        Transaction status.
	 * @param Money  $amount        Amount.
	 * @param string $start_time       Start time.
	 * @param string $last_update_time Last update time.
	 */
	public function __construct( $id, $payment_brand, $type, $status, Money $amount, $start_time, $last_update_time ) {
		$this->id               = $id;
		$this->payment_brand    = $payment_brand;
		$this->type             = $type;
		$this->status           = $status;
		$this->amount           = $amount;
		$this->start_time       = $start_time;
		$this->last_update_time = $last_update_time;
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
	 * Get amount.
	 * 
	 * @return Money
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Get confirmed amount.
	 * 
	 * @return Money|null
	 */
	public function get_confirmed_amount() {
		return $this->confirmed_amount;
	}

	/**
	 * Get signature fields.
	 *
	 * @param array<string> $fields Fields.
	 * @return array<string>
	 */
	public function get_signature_fields( $fields = [] ) {
		$fields[] = $this->get_id();
		$fields[] = $this->get_payment_brand();
		$fields[] = $this->get_type();
		$fields[] = $this->get_status();

		$fields = $this->amount->get_signature_fields( $fields );

		if ( null !== $this->confirmed_amount ) {
			$fields = $this->confirmed_amount->get_signature_fields( $fields );
		}

		$fields[] = $this->start_time;
		$fields[] = $this->last_update_time;

		return $fields;
	}

	/**
	 * Create transaction from object.
	 *
	 * @param object $data Object.
	 * @return Transaction
	 */
	public static function from_object( $data ) {
		$object_access = new ObjectAccess( $data );

		$transaction = new self(
			$object_access->get_string( 'id' ),
			$object_access->get_string( 'paymentBrand' ),
			$object_access->get_string( 'type' ),
			$object_access->get_string( 'status' ),
			Money::from_object( $object_access->get_object( 'amount' ) ),
			$object_access->get_string( 'startTime' ),
			$object_access->get_string( 'lastUpdateTime' )
		);

		$object = $object_access->get_property( 'confirmedAmount' );

		if ( \is_object( $object ) ) {
			$transaction->confirmed_amount = Money::from_object( $object );
		}

		return $transaction;
	}
}
