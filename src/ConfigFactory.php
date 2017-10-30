<?php

/**
 * Title: OmniKassa 2.0 config factory
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Pronamic_WP_Pay_Gateways_OmniKassa_ConfigFactory extends Pronamic_WP_Pay_GatewayConfigFactory {
	public function get_config( $post_id ) {
		$config = new Pronamic_WP_Pay_Gateways_OmniKassa2_Config();

		$config->refresh_token = get_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_refresh_token', true );
		$config->signing_key   = get_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_signing_key', true );

		return $config;
	}
}
