<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\Util as Core_Util;

/**
 * Title: OmniKassa 2.0 order
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Order extends Signable {
	public $timestamp;

	public $merchant_order_id;

	public $description;

	public $amount;

	public $currency;

	public $language;

	public $merchant_return_url;

	public $signature;

	public $order_items;

	public $shipping_detail;

	public $payment_brand;

	public $payment_brand_force;

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

	public function get_signature_data() {
		// Required fields
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

		// Remove empty optional fields
		$optional = array_filter( $optional );

		return array_merge( $fields, $optional );
	}
}
