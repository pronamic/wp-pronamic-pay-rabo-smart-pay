<?php
/**
 * Client.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use WP_Error;

/**
 * Client.
 *
 * @author  Remco Tolsma
 * @version 2.1.0
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
	 * Refresh token.
	 *
	 * @var string
	 */
	private $refresh_token;

	/**
	 * Signing key.
	 *
	 * @var string
	 */
	private $signing_key;

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
	 *
	 * @return bool|object
	 */
	private function request( $method, $endpoint, $token, $object = null ) {
		// URL.
		$url = $this->get_url() . $endpoint;

		/*
		 * Arguments.
		 *
		 * The `timeout` argument is intentionally increased from the WordPress
		 * default `5` seconds to `30`. This is mainly important for AfterPay
		 * order announcements requests, but it can't hurt for other requests.
		 * It is probably better to wait a little longer for an answer than
		 * having timeout issues while starting a payment or requesting a
		 * payment status. The value can also be adjusted via the
		 * `pronamic_pay_omnikassa_2_request_args` filter.
		 */
		$args = array(
			'method'  => $method,
			'headers' => array(
				'Authorization' => 'Bearer ' . $token,
			),
			'timeout' => 30,
		);

		if ( null !== $object ) {
			$args['headers']['Content-Type'] = 'application/json';

			$args['body'] = wp_json_encode( $object );
		}

		$args = apply_filters( 'pronamic_pay_omnikassa_2_request_args', $args );

		// Request.
		$response = wp_remote_request( $url, $args );

		if ( $response instanceof WP_Error ) {
			$this->error = $response;

			$this->error->add( 'omnikassa_2_error', 'HTTP Request Failed' );

			return false;
		}

		// Body.
		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body );

		if ( ! is_object( $data ) ) {
			$message = implode(
				"\r\n",
				array(
					'Could not parse response.',
					sprintf( 'HTTP response status code: %s %s', wp_remote_retrieve_response_code( $response ), wp_remote_retrieve_response_message( $response ) ),
				)
			);

			$this->error = new WP_Error( 'omnikassa_2_error', $message, $data );

			return false;
		}

		// Error.
		// @codingStandardsIgnoreStart
		if ( isset( $data->errorCode ) ) {
			// @codingStandardsIgnoreEnd
			$message = 'Unknown error.';

			// @codingStandardsIgnoreStart
			if ( isset( $data->consumerMessage ) ) {
				$message = $data->consumerMessage;
			} elseif ( isset( $data->errorMessage ) ) {
				$message = $data->errorMessage;
			}
			// @codingStandardsIgnoreEnd

			$this->error = new WP_Error( 'omnikassa_2_error', $message, $data );

			return false;
		}

		// Ok.
		return $data;
	}

	/**
	 * Get access token.
	 *
	 * @return bool|object
	 */
	public function get_access_token_data() {
		return $this->request( 'GET', 'gatekeeper/refresh', $this->get_refresh_token() );
	}

	/**
	 * Order announce.
	 *
	 * @param Config $config Config.
	 * @param Order  $order  Order.
	 * @return OrderAnnounceResponse|false
	 */
	public function order_announce( $config, Order $order ) {
		$order->sign( $config->signing_key );

		$object = $order->get_json();

		$result = $this->request( 'POST', 'order/server/api/order', $config->access_token, $object );

		if ( ! is_object( $result ) ) {
			return false;
		}

		return OrderAnnounceResponse::from_object( $result );
	}

	/**
	 * Get order results by the notification token.
	 *
	 * @param string $notification_token Notification token.
	 *
	 * @return OrderResults|false
	 */
	public function get_order_results( $notification_token ) {
		$result = $this->request( 'GET', 'order/server/api/events/results/merchant.order.status.changed', $notification_token );

		if ( ! is_object( $result ) ) {
			return false;
		}

		return OrderResults::from_object( $result );
	}
}
