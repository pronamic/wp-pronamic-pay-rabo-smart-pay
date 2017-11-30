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

			$payment = get_pronamic_payment( $payment_id );

			\Pronamic_WP_Pay_Plugin::update_payment( $payment );
		}
	}
}
