<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Title: OmniKassa 2.0 config
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Config extends GatewayConfig {
	public $refresh_token;

	public $signing_key;

	public $access_token;

	public $access_token_valid_until;

	public $order_id;

	public function is_access_token_valid() {
		if ( empty( $this->access_token ) ) {
			return false;
		}

		return strtotime( $this->access_token_valid_until ) > time();
	}

	public function get_gateway_class() {
		return __NAMESPACE__ . '\Gateway';
	}
}
