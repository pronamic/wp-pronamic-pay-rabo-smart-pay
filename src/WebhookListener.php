<?php
/**
 * Webhook listener
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\GatewayPostType;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Webhook listener
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class WebhookListener {
	/**
	 * Listen to OmniKassa 2.0 webhook requests.
	 */
	public static function listen() {
		if ( ! filter_has_var( INPUT_GET, 'omnikassa2_webhook' ) ) {
			return;
		}

		$json = file_get_contents( 'php://input' );

		if ( empty( $json ) ) {
			return;
		}

		$notification = Notification::from_json( $json );

		$query = new \WP_Query(
			array(
				'post_type'   => GatewayPostType::POST_TYPE,
				'post_status' => 'publish',
				'nopaging'    => true,
				'meta_query'  => array(
					array(
						'key'   => '_pronamic_gateway_id',
						'value' => 'rabobank-omnikassa-2',
					),
				),
			)
		);

		foreach ( $query->posts as $post ) {
			$gateway = Plugin::get_gateway( $post->ID );

			if ( $gateway instanceof Gateway ) {
				$gateway->handle_notification( $notification );
			}
		}
	}
}
