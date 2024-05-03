<?php
/**
 * Config
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use JsonSerializable;
use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Config
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   1.0.0
 */
final class Config extends GatewayConfig implements JsonSerializable {
	/**
	 * Post ID.
	 *
	 * @var int
	 */
	public $post_id = 0;

	/**
	 * API URL.
	 *
	 * @var string
	 */
	public $api_url;

	/**
	 * Refresh token.
	 *
	 * @var string
	 */
	public $refresh_token = '';

	/**
	 * Signing key.
	 *
	 * @var string
	 */
	public $signing_key = '';

	/**
	 * Access token.
	 *
	 * @var string
	 */
	public $access_token = '';

	/**
	 * Access token valid until.
	 *
	 * @var string
	 */
	public $access_token_valid_until = '';

	/**
	 * Order ID.
	 *
	 * @var string
	 */
	public $order_id = '';

	/**
	 * Construct config.
	 */
	public function __construct() {
		$this->api_url = Client::URL_PRODUCTION;
	}

	/**
	 * Get API URL.
	 *
	 * @return string
	 */
	public function get_api_url() {
		return $this->api_url;
	}

	/**
	 * Set API URL.
	 *
	 * @param string $api_url API URL.
	 * @return void
	 */
	public function set_api_url( $api_url ) {
		$this->api_url = $api_url;
	}

	/**
	 * Check if access token is valid.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function is_access_token_valid() {
		if ( empty( $this->access_token ) ) {
			return false;
		}

		return \strtotime( $this->access_token_valid_until ) > \time();
	}

	/**
	 * Serialize to JSON.
	 *
	 * @return object
	 */
	public function jsonSerialize(): object {
		return (object) [
			'@type'         => self::class,
			'api_url'       => $this->api_url,
			'refresh_token' => $this->refresh_token,
			'signing_key'   => $this->signing_key,
		];
	}
}
