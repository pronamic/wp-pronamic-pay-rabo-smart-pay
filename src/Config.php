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
	public $refresh_token;

	public $signing_key;

	public function get_gateway_class() {
		return __NAMESPACE__ . '\Gateway';
	}
}
