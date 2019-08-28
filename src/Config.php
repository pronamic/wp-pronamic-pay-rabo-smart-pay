<?php
/**
 * Config
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\GatewayConfig;

/**
 * Config
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   1.0.0
 */
class Config extends GatewayConfig {
	/**
	 * Post ID.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Refresh token.
	 *
	 * @var string
	 */
	public $refresh_token;

	/**
	 * Signing key.
	 *
	 * @var string
	 */
	public $signing_key;

	/**
	 * Access token.
	 *
	 * @var string
	 */
	public $access_token;

	/**
	 * Access token valid until.
	 *
	 * @var string
	 */
	public $access_token_valid_until;

	/**
	 * Order ID.
	 *
	 * @var string
	 */
	public $order_id;

	/**
	 * Check if access token is valid.
	 *
	 * @return bool True if valid, false otherwise.
	 */
	public function is_access_token_valid() {
		if ( empty( $this->access_token ) ) {
			return false;
		}

		return strtotime( $this->access_token_valid_until ) > time();
	}
}
