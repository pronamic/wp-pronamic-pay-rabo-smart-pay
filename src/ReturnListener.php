<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\GatewayPostType;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Core\Gateway;

/**
 * Title: OmniKassa 2.0 return listener
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class ReturnListener {
	public static function listen() {
		// Check if all the return URL parameters are set to make sure we deal with a OmniKassa 2.0 return request.
		if ( ! filter_has_var( INPUT_GET, 'order_id' ) ) {
			return;
		}

		if ( ! filter_has_var( INPUT_GET, 'status' ) ) {
			return;
		}

		if ( ! filter_has_var( INPUT_GET, 'signature' ) ) {
			return;
		}

		// Input data.
		$order_id  = filter_input( INPUT_GET, 'order_id', FILTER_SANITIZE_STRING );
		$status    = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_STRING );
		$signature = filter_input( INPUT_GET, 'signature', FILTER_SANITIZE_STRING );

		// Find payment.
		$payment = get_pronamic_payment_by_meta( '_pronamic_payment_order_id', $order_id );

		if ( empty( $payment ) ) {
			return;
		}

		// Check signature.
		$data = array(
			$order_id,
			$status,
		);

		// Add note.
		$payment->add_note( __( 'Return URL requested.', 'pronamic_ideal' ) );

		$payment->save();
	}
}
