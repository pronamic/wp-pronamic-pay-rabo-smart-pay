<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 listener
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Reüel van der Steege
 * @version 1.0.0
 * @since 1.0.0
 */
class Listener implements \Pronamic_Pay_Gateways_ListenerInterface {
	public static function listen() {
		if (
			filter_has_var( INPUT_GET, 'order_id' )
				&&
			filter_has_var( INPUT_GET, 'status' )
				&&
			filter_has_var( INPUT_GET, 'signature' )
		) {
			// This is the request when customer returns from OmniKassa, NOT the webhook.

			$payment_id = filter_input( INPUT_GET, 'order_id', FILTER_SANITIZE_STRING );

			// Validate signature
			$config_id = get_post_meta( $payment_id, '_pronamic_payment_config_id', true );

			$signing_key = get_post_meta( $config_id, '_pronamic_gateway_omnikassa_2_signing_key', true );

			if ( '' === $signing_key ) {
				return;
			}

			$data = array(
				$payment_id,
				filter_input( INPUT_GET, 'status', FILTER_SANITIZE_STRING ),
			);

			$input_signature = filter_input( INPUT_GET, 'signature', FILTER_SANITIZE_STRING );

			$signature = Security::calculate_signature( $data, $signing_key );

			if ( 0 !== strcasecmp( $input_signature, $signature ) ) {
				// Invalid signature

				return;
			}

			$payment = get_pronamic_payment( $payment_id );

			\Pronamic_WP_Pay_Plugin::update_payment( $payment );
		}
	}
}
