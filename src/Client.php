<?php
/**
 * Client
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use WP_Error;

/**
 * Client
 *
 * @author  Remco Tolsma
 * @version 2.0.1
 * @since   1.0.0
 */
class Client {
	/**
	 * URL OmniKassa API.
	 *
	 * @var string
	 */
	const URL_PRODUCTION = 'https://betalen.rabobank.nl/omnikassa-api/';

	/**
	 * URL OmniKassa sandbox API.
	 *
	 * @var string
	 */
	const URL_SANDBOX = 'https://betalen.rabobank.nl/omnikassa-api-sandbox/';

	/**
	 * Error
	 *
	 * @var WP_Error
	 */
	private $error;

	/**
	 * The URL.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Error.
	 *
	 * @return WP_Error
	 */
	public function get_error() {
		return $this->error;
	}

	/**
	 * Get the URL.
	 *
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Set the action URL
	 *
	 * @param string $url URL.
	 */
	public function set_url( $url ) {
		$this->url = $url;
	}

	/**
	 * Get refresh token.
	 *
	 * @return string
	 */
	public function get_refresh_token() {
		return $this->refresh_token;
	}

	/**
	 * Set refresh token.
	 *
	 * @param string $refresh_token Refresh token.
	 */
	public function set_refresh_token( $refresh_token ) {
		$this->refresh_token = $refresh_token;
	}

	/**
	 * Get signing key.
	 *
	 * @return string
	 */
	public function get_signing_key() {
		return $this->signing_key;
	}

	/**
	 * Set signing key.
	 *
	 * @param string $signing_key Signing key.
	 */
	public function set_signing_key( $signing_key ) {
		$this->signing_key = $signing_key;
	}

	/**
	 * Request URL with specified method, token.
	 *
	 * @param string      $method   HTTP request method.
	 * @param string      $endpoint URL endpoint to request.
	 * @param string      $token    Authorization token.
	 * @param object|null $object   Object.
	 */
	private function request( $method, $endpoint, $token, $object = null ) {
		// URL.
		$url = $this->get_url() . $endpoint;

		// Arguments.
		$args = array(
			'method'     => $method,
			'headers'    => array(
				'Authorization' => 'Bearer ' . $token,
			),
		);

		if ( null !== $object ) {
			$args['headers']['Content-Type'] = 'application/json';

			$args['body'] = wp_json_encode( $object );
		}

		// Request.
		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			$this->error = $response;

			$this->error->add( 'omnikassa_2_error', 'HTTP Request Failed' );

			return false;
		}

		// Body.
		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body );

		if ( ! is_object( $data ) ) {
			$this->error = new \WP_Error( 'omnikassa_2_error', 'Could not parse response.', $data );

			return false;
		}

		// Error.
		if ( isset( $data->errorCode ) ) {
			$message = 'Unknown error.';

			if ( isset( $data->consumerMessage ) ) {
				$message = $data->consumerMessage;
			} elseif ( isset( $data->errorMessage ) ) {
				$message = $data->errorMessage;
			}

			$this->error = new \WP_Error( 'omnikassa_2_error', $message, $data );

			return false;
		}

		// Ok.
		return $data;
	}

	/**
	 * Get access token.
	 *
	 * @return string
	 */
	public function get_access_token_data() {
		return $this->request( 'GET', 'gatekeeper/refresh', $this->get_refresh_token() );
	}

	/**
	 * Order announce.
	 *
	 * @param Config $config Config.
	 * @param Order  $order  Order.
	 * @return object|bool
	 */
	public function order_announce( $config, Order $order ) {
		$object = $order->get_json();

		$object->signature = Security::get_signature( $order, $config->signing_key );

		return $this->request( 'POST', 'order/server/api/order', $config->access_token, $object );
	}

	/**
	 * Get order results by the notification token.
	 *
	 * @param string $notification_token Notification token.
	 * @return OrderResults
	 */
	public function get_order_results( $notification_token ) {
		$result = $this->request( 'GET', 'order/server/api/events/results/merchant.order.status.changed', $notification_token );

		return OrderResults::from_object( $result );
	}
}
