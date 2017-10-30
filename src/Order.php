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

	public $merchantOrderId;

	public $description;

	public $amount;

	public $currency;

	public $language;

	public $merchantReturnURL;

	public $signature;

	public $orderItems;

	public $shippingDetail;

	public $paymentBrand;

	public $paymentBrandForce;

	public function get_json_string() {
		$object = (object) array(
			'timestamp'         => $this->timestamp,
			'merchantOrderId'   => $this->merchantOrderId,
			'description'       => $this->description,
			'amount'            => $this->amount,
			'currency'          => $this->currency,
			'language'          => $this->language,
			'merchantReturnURL' => $this->merchantReturnURL,
			'signature'         => $this->signature,
			'orderItems'        => $this->orderItems,
			'shippingDetail'    => $this->shippingDetail,
			'paymentBrand'      => $this->paymentBrand,
			'paymentBrandForce' => $this->paymentBrandForce,
		);

		return json_encode( $object );
	}
}
