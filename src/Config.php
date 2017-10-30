<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 config
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Config extends \Pronamic_WP_Pay_GatewayConfig {
	public $merchant_id;

	public $secret_key;

	public $key_version;

	public $order_id;

	public function get_gateway_class() {
		return 'Pronamic_WP_Pay_Gateways_OmniKassa_Gateway';
	}
}
