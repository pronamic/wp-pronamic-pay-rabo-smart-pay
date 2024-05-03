<?php
/**
 * Refund request
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use JsonSerializable;

/**
 * Refund request class
 */
final class RefundRequest implements IdempotenceInterface, JsonSerializable {
	/**
	 * Transaction ID.
	 *
	 * @var string
	 */
	public string $transaction_id;

	/**
	 * Amount.
	 *
	 * @var Money
	 */
	public Money $amount;

	/**
	 * Description.
	 *
	 * Reason for refund request. This field is required for partial refunds of payment
	 * transactions with payment brand Riverty/AfterPay.
	 *
	 * @var string|null
	 */
	public ?string $description = null;

	/**
	 * VAT Category.
	 *
	 * The VAT category of the product. The values refer to the different rates used in
	 * the Netherlands. This field is required for partial refunds of payment transactions
	 * with payment brand Riverty/AfterPay.
	 *
	 * @var string|null
	 */
	public ?string $vat_category = null;

	/**
	 * Construct refund request.
	 *
	 * @param string $transaction_id Transaction ID.
	 * @param Money  $amount         Amount.
	 */
	public function __construct( string $transaction_id, Money $amount ) {
		$this->transaction_id = $transaction_id;
		$this->amount         = $amount;
	}

	/**
	 * Get JSON object.
	 *
	 * @return object
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		$properties = [
			'money' => $this->amount->jsonSerialize(),
		];

		if ( null !== $this->description ) {
			$properties['description'] = $this->description;
		}

		if ( null !== $this->vat_category ) {
			$properties['vatCategory'] = $this->vat_category;
		}

		return (object) $properties;
	}

	/**
	 * Get idempotence ID.
	 *
	 * @return string
	 */
	public function get_idempotence_id(): string {
		return \wp_unique_id();
	}
}
