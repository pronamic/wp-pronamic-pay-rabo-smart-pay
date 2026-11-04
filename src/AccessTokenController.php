<?php
/**
 * Access token controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Access token controller
 *
 * The integration is constructed multiple times (live and sandbox), this
 * singleton makes sure the access token hooks are only added once.
 */
final class AccessTokenController {
	/**
	 * Instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Construct access token controller.
	 */
	private function __construct() {
		/**
		 * Save post.
		 *
		 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-includes/post.php#L3724-L3736
		 */
		\add_action( 'save_post_pronamic_gateway', $this->delete_access_token_meta( ... ) );

		/**
		 * Save the access token after the client refreshed it.
		 */
		\add_action( 'pronamic_pay_omnikassa_2_access_token_refreshed', $this->save_access_token_meta( ... ) );
	}

	/**
	 * Instance.
	 */
	public static function instance(): self {
		self::$instance ??= new self();

		return self::$instance;
	}

	/**
	 * Delete access token meta for the specified post ID.
	 *
	 * @link https://codex.wordpress.org/Function_Reference/delete_post_meta
	 * @param int $post_id Post ID.
	 * @return void
	 */
	private function delete_access_token_meta( $post_id ) {
		\delete_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_access_token' );
		\delete_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_access_token_valid_until' );
	}

	/**
	 * Save access token meta for the config post.
	 *
	 * @param Config $config Config.
	 * @return void
	 */
	private function save_access_token_meta( Config $config ) {
		if ( 0 === $config->post_id ) {
			return;
		}

		\update_post_meta( $config->post_id, '_pronamic_gateway_omnikassa_2_access_token', $config->access_token );

		\update_post_meta(
			$config->post_id,
			'_pronamic_gateway_omnikassa_2_access_token_valid_until',
			$config->access_token_valid_until
		);
	}
}
