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
 * Config factory
 *
 * @author  Remco Tolsma
 * @version 2.0.2
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
		$config->mode                     = $this->get_meta( $post_id, 'mode' );
		$config->refresh_token            = $this->get_meta( $post_id, 'omnikassa_2_refresh_token' );
		$config->signing_key              = $this->get_meta( $post_id, 'omnikassa_2_signing_key' );
		$config->access_token             = $this->get_meta( $post_id, 'omnikassa_2_access_token' );
		$config->access_token_valid_until = $this->get_meta( $post_id, 'omnikassa_2_access_token_valid_until' );
		$config->order_id                 = $this->get_meta( $post_id, 'omnikassa_2_order_id' );

		return $config;
	}

	/**
	 * Get meta value.
	 *
	 * @param string|int $post_id Post ID.
	 * @param string     $key     Shortened meta key.
	 *
	 * @return string
	 */
	private function get_meta( $post_id, $key ) {
		if ( empty( $post_id ) ) {
			return '';
		}

		$post_id = intval( $post_id );

		$meta_key = sprintf( '_pronamic_gateway_%s', $key );

		// Get post meta.
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		if ( false === $meta_value ) {
			$meta_value = '';
		}

		return $meta_value;
	}
}
