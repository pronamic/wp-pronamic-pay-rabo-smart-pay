<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 order
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Order {
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
				'currency'      => $this->currency,
				'amount'        => \Pronamic_WP_Pay_Util::amount_to_cents( $this->amount ),
			),
			'language'          => $this->language,
			'merchantReturnURL' => $this->merchant_return_url,
			'orderItems'        => $this->order_items,
			'shippingDetail'    => $this->shipping_detail,
			'paymentBrand'      => $this->payment_brand,
			'paymentBrandForce' => $this->payment_brand_force,
		);
	}
}
