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
	public static function get_order_signature( $order, $signing_key ) {
		$object = $order->get_json();

		$fields = array(
			$object->timestamp,
			$object->merchantOrderId,
			$object->currency,
			$object->amount,
			$object->language,
			$object->description,
			$object->merchantReturnURL,
		);

		$string = implode( ',', $fields );
		
		$signature = hash_hmac( 'sha512', $string, base64_decode( $signing_key ) );

		return $signature;
	}
}
