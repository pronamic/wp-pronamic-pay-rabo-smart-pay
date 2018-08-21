<?php
/**
 * Config factory
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\GatewayConfigFactory;

/**
 * Title: OmniKassa 2.0 config factory
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class ConfigFactory extends GatewayConfigFactory {
	/**
	 * Get configuration by post ID.
	 *
	 * @param string $post_id Post ID.
	 * @return Config
	 */
	public function get_config( $post_id ) {
		$config = new Config();

		$config->post_id                  = $post_id;
		$config->mode                     = get_post_meta( $post_id, '_pronamic_gateway_mode', true );
		$config->refresh_token            = get_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_refresh_token', true );
		$config->signing_key              = get_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_signing_key', true );
		$config->access_token             = get_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_access_token', true );
		$config->access_token_valid_until = get_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_access_token_valid_until', true );
		$config->order_id                 = get_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_order_id', true );

		return $config;
	}
}
