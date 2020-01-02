<?php
/**
 * Client.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Client.
 *
 * @author  Remco Tolsma
 * @version 2.1.10
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
	 * @var \WP_Error
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
	 * @return \WP_Error
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
	 * @return object
	 * @throws \Exception Throws exception when Rabobank OmniKassa 2.0 response is not what we expect.
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

			$args['body'] = \wp_json_encode( $object );
		}

		$args = \apply_filters( 'pronamic_pay_omnikassa_2_request_args', $args );

		/**
		 * Build cURL command for debug purposes.
		 *
		 * @link https://curl.haxx.se/
		 */

		// phpcs:disable SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable

		$curl = '';

		$tab = "\t";
		$eol = ' \\' . \PHP_EOL;

		$curl .= \sprintf( 'curl --request %s %s', $method, \escapeshellarg( $url ) ) . $eol;
		$curl .= $tab . \sprintf( '--header %s', \escapeshellarg( 'Authorization: Bearer ' . $token ) ) . $eol;
		$curl .= $tab . \sprintf( '--header %s', \escapeshellarg( 'Content-Type: application/json' ) ) . $eol;
		$curl .= $tab . \sprintf( '--data %s', \escapeshellarg( \strval( \wp_json_encode( $object ) ) ) ) . $eol;
		$curl .= $tab . \sprintf( '--user-agent %s', \escapeshellarg( 'WordPress/' . \get_bloginfo( 'version' ) . '; ' . \get_bloginfo( 'url' ) ) ) . $eol;
		$curl .= $tab . '--verbose';

		// phpcs:enable

		// Request.
		$response = \wp_remote_request( $url, $args );

		if ( $response instanceof \WP_Error ) {
			throw new \Exception(
				\sprintf(
					'OmniKassa 2.0 HTTP request failed: %s.',
					$response->get_error_message()
				)
			);
		}

		// Body.
		$body = \wp_remote_retrieve_body( $response );

		// Response.
		$response_code    = \wp_remote_retrieve_response_code( $response );
		$response_message = \wp_remote_retrieve_response_message( $response );

		// Data.
		$data = \json_decode( $body );

		// JSON error.
		$json_error = \json_last_error();

		if ( \JSON_ERROR_NONE !== $json_error ) {
			throw new \Exception(
				\sprintf(
					'Could not JSON decode OmniKassa 2.0 response, HTTP response: "%s %s", HTTP body length: "%d", JSON error: "%s".',
					$response_code,
					$response_message,
					\strlen( $body ),
					\json_last_error_msg()
				),
				$json_error
			);
		}

		// Object.
		if ( ! \is_object( $data ) ) {
			throw new \Exception(
				\sprintf(
					'Could not JSON decode OmniKassa 2.0 response to an object, HTTP response: "%s %s", HTTP body length: "%d".',
					$response_code,
					$response_message,
					\strlen( $body )
				),
				\intval( $response_code )
			);
		}

		// Error.
		if ( isset( $data->errorCode ) ) {
			$error = Error::from_object( $data );

			throw $error;
		}

		// Ok.
		return $data;
	}

	/**
	 * Get access token.
	 *
	 * @return object
	 */
	public function get_access_token_data() {
		return $this->request( 'GET', 'gatekeeper/refresh', $this->get_refresh_token() );
	}

	/**
	 * Order announce.
	 *
	 * @param Config $config Config.
	 * @param Order  $order  Order.
	 * @return OrderAnnounceResponse
	 */
	public function order_announce( $config, Order $order ) {
		$order->sign( $config->signing_key );

		$object = $order->get_json();

		$result = $this->request( 'POST', 'order/server/api/order', $config->access_token, $object );

		return OrderAnnounceResponse::from_object( $result );
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
