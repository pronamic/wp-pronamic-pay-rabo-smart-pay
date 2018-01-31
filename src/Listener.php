<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\GatewayPostType;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Core\Gateway;

/**
 * Title: OmniKassa 2.0 listener
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Reüel van der Steege
 * @version 1.0.0
 * @since 1.0.0
 */
class Listener {
	public static function listen() {
		if ( filter_has_var( INPUT_GET, 'omnikassa2_webhook' ) ) {
			/**
			 * Notification POST request body JSON sample:
			 *
			 * {
			 *  "authentication": "eyJraWQiOiJHS0wiLCJhbGciOiJFUzI1NiJ9.eyJwayMiOjUwMiwiY2lkIjoiYzhjYy1mMThjIiwiZXhwIjoxNDc5MTIyODc2fQ.MEUCIQC2Z5WUVTAKcBHISsOVMJIJE8PAbVe5x1ior4bgrTcgCwIgLNoVIWEmSbQekJTccM89sosAY-8JzN47DGjvdPGdF0w",
			 *  "expiry": "2016-11-25T09:53:46.765+01:00",
			 *  "eventName": "merchant.order.status.changed",
			 *  "poiId": "123"
			 * }
			 */
			$data = json_decode( file_get_contents( 'php://input' ) );

			if ( ! is_object( $data ) ) {
				return;
			}

			if ( 'merchant.order.status.changed' !== $data->eventName ) {
				return;
			}

			$query = new \WP_Query( array(
				'post_type'      => GatewayPostType::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					array(
						'key'   => '_pronamic_gateway_id',
						'value' => 'rabobank-omnikassa-2',
					),
				),
			) );

			foreach ( $query->posts as $post ) {
				$factory = new ConfigFactory();

				$config = $factory->get_config( $post->ID );

				if ( '' === $config->signing_key ) {
					continue;
				}

				// Client
				$client = new Client();

				$url = Client::URL_PRODUCTION;

				if ( Gateway::MODE_TEST === $config->mode ) {
					$url = Client::URL_SANDBOX;
				}

				$client->set_url( $url );
				$client->set_refresh_token( $config->refresh_token );
				$client->set_signing_key( $config->signing_key );

				// Retrieve and process announcements
				do {
					$response = $client->retrieve_announcement( $data );

					if ( is_wp_error( $response ) ) {
						continue;
					}

					$order_results = new OrderResults();

					$order_results->set_signing_key( $config->signing_key );

					$order_results->more_order_results_available = $response->moreOrderResultsAvailable;
					$order_results->order_results                = $response->orderResults;

					// Validate signature
					if ( ! Security::validate_signature( $response->signature, $order_results->get_signature() ) ) {
						// Invalid signature
						continue;
					}

					foreach ( $order_results->order_results as $order ) {
						$payment = null;

						if ( '{order_id}' === $config->order_id ) {
							$payment = get_pronamic_payment_by_meta( '_pronamic_payment_order_id', $order->merchantOrderId );
						}

						if ( ! $payment ) {
							$payment = get_pronamic_payment( $order->merchantOrderId );
						}

						$payment->set_meta( 'omnikassa_2_update_order_status', $order->orderStatus );

						Plugin::update_payment( $payment );
					}
				} while ( $response->moreOrderResultsAvailable );
			}
		}
	}
}
