<?php
/**
 * Order
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\Util as Core_Util;

/**
 * Title: OmniKassa 2.0 order
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Order extends Signable {
	/**
	 * Order timestamp.
	 *
	 * @var string
	 */
	public $timestamp;

	/**
	 * Merchant order ID.
	 *
	 * @var string
	 */
	public $merchant_order_id;

	/**
	 * Description.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Amount.
	 *
	 * @var string
	 */
	public $amount;

	/**
	 * Currency.
	 *
	 * @var string
	 */
	public $currency;

	/**
	 * Language.
	 *
	 * @var string
	 */
	public $language;

	/**
	 * Merchant return URL.
	 *
	 * @var string
	 */
	public $merchant_return_url;

	/**
	 * Signature.
	 *
	 * @var string
	 */
	public $signature;

	/**
	 * Order items.
	 *
	 * @var string
	 */
	public $order_items;

	/**
	 * Shipping detail.
	 *
	 * @var string
	 */
	public $shipping_detail;

	/**
	 * Payment brand.
	 *
	 * @var string
	 */
	public $payment_brand;

	/**
	 * Payment brand force.
	 *
	 * @var string
	 */
	public $payment_brand_force;

	/**
	 * Get JSON object.
	 *
	 * @return object
	 */
	public function get_json() {
		return (object) array(
			'timestamp'         => $this->timestamp,
			'merchantOrderId'   => $this->merchant_order_id,
			'description'       => $this->description,
			'amount'            => (object) array(
				'currency' => $this->currency,
				'amount'   => Core_Util::amount_to_cents( $this->amount ),
			),
			'language'          => $this->language,
			'merchantReturnURL' => $this->merchant_return_url,
			'orderItems'        => $this->order_items,
			'shippingDetail'    => $this->shipping_detail,
			'paymentBrand'      => $this->payment_brand,
			'paymentBrandForce' => $this->payment_brand_force,
		);
	}

	/**
	 * Get signature data.
	 *
	 * @return array
	 */
	public function get_signature_data() {
		// Required fields.
		$fields = array(
			$this->timestamp,
			$this->merchant_order_id,
			$this->currency,
			Core_Util::amount_to_cents( $this->amount ),
			$this->language,
			$this->description,
			$this->merchant_return_url,
		);

		// Optional fields; do not change field order!
		$optional = array(
			$this->payment_brand,
			$this->payment_brand_force,
		);

		// Remove empty optional fields.
		$optional = array_filter( $optional );

		return array_merge( $fields, $optional );
	}
}
