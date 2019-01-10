<?php
/**
 * Config factory
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\GatewayConfigFactory;

/**
 * Config factory
 *
 * @author  Remco Tolsma
 * @version 2.1.0
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

		$config->post_id                  = intval( $post_id );
		$config->mode                     = $this->get_meta( $post_id, 'mode' );
		$config->refresh_token            = $this->get_meta( $post_id, 'omnikassa_2_refresh_token' );
		$config->signing_key              = $this->get_meta( $post_id, 'omnikassa_2_signing_key' );
		$config->access_token             = $this->get_meta( $post_id, 'omnikassa_2_access_token' );
		$config->access_token_valid_until = $this->get_meta( $post_id, 'omnikassa_2_access_token_valid_until' );
		$config->order_id                 = $this->get_meta( $post_id, 'omnikassa_2_order_id' );

		return $config;
	}

	/**
	 * Delete access token meta for the specified post ID.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-includes/post.php#L3724-L3736
	 * @link https://codex.wordpress.org/Function_Reference/delete_post_meta
	 *
	 * @param int $post_id Post ID.
	 */
	public static function delete_access_token_meta( $post_id ) {
		delete_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_access_token' );
		delete_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_access_token_valid_until' );
	}
}
