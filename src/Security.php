<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 security
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Security {
	public static function get_order_signature( Order $order, $signing_key ) {
		$object = $order->get_json();

		$fields = array(
			$object->timestamp,
			$object->merchantOrderId,
			$object->amount->currency,
			$object->amount->amount,
			$object->language,
			$object->description,
			$object->merchantReturnURL,
		);

		$optional_fields = array();

		if ( $object->orderItems ) {
			// Add order items
		}

		if ( $object->shippingDetail ) {
			// Add shipping detail
		}

		$optional_fields[] = $object->paymentBrand;
		$optional_fields[] = $object->paymentBrandForce;

		// Do not include empty optional fields in signature calculation.
		$optional_fields = array_filter( $optional_fields );

		$fields = array_merge( $fields, $optional_fields );

		$string = implode( ',', $fields );
		
		$signature = hash_hmac( 'sha512', $string, base64_decode( $signing_key ) );

		return $signature;
	}
}
