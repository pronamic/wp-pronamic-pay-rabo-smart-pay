<?php
/**
 * Client.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Http\Facades\Http;

/**
 * Client.
 *
 * @author  Remco Tolsma
 * @version 2.3.4
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
	 * @return void
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
	 * @return void
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
	 * @return void
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
		$args = [
			'method'  => $method,
			'headers' => [
				'Authorization' => 'Bearer ' . $token,
			],
			'timeout' => 30,
		];

		if ( null !== $object ) {
			$args['headers']['Content-Type'] = 'application/json';

			$args['body'] = \wp_json_encode( $object );
		}

		/**
		 * Filters the OmniKassa 2.0 remote request arguments.
		 *
		 * Developers who want to adjust the WordPress remote request arguments
		 * towards the OmniKassa servers can use this filter. For example, it
		 * can be used to increase the HTTP timeout to, for example, 3600. This
		 * is sometimes useful for testing AfterPay payments for example.
		 * Mainly because the OmniKassa servers sometimes respond slowly when
		 * starting AfterPay payments.
		 *
		 * @link https://github.com/wp-pay-gateways/omnikassa-2#pronamic_pay_omnikassa_2_request_args
		 * @param array $args WordPress remote request arguments.
		 */
		$args = \apply_filters( 'pronamic_pay_omnikassa_2_request_args', $args );

		/**
		 * Build cURL command for debug purposes.
		 *
		 * @link https://curl.haxx.se/
		 */

		// phpcs:disable SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable

		$curl = '';

		$tab = "\t";
		$eol = ' \\' . \PHP_EOL;

		$curl .= \sprintf( 'curl --request %s %s', $method, \escapeshellarg( $url ) ) . $eol;
		$curl .= $tab . \sprintf( '--header %s', \escapeshellarg( 'Authorization: Bearer ' . $token ) ) . $eol;
		$curl .= $tab . \sprintf( '--header %s', \escapeshellarg( 'Content-Type: application/json' ) ) . $eol;
		$curl .= $tab . \sprintf( '--data %s', \escapeshellarg( \strval( \wp_json_encode( $object ) ) ) ) . $eol;
		$curl .= $tab . \sprintf(
			'--user-agent %s',
			\escapeshellarg( 'WordPress/' . \get_bloginfo( 'version' ) . '; ' . \get_bloginfo( 'url' ) )
		) . $eol;
		$curl .= $tab . '--verbose';

		// phpcs:enable

		// Request.
		$response = Http::request( $url, $args );

		$data = $response->json();

		// Object.
		if ( ! \is_object( $data ) ) {
			throw new \Exception(
				\sprintf(
					'Could not JSON decode OmniKassa 2.0 response to an object, HTTP response: "%s %s", HTTP body length: "%d".',
					$response->status(),
					$response->message(),
					\strlen( $response->body() )
				),
				\intval( $response->status() )
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
		$result = $this->request( 'POST', 'order/server/api/v2/order', $config->access_token, $order );

		return OrderAnnounceResponse::from_object( $result );
	}

	/**
	 * Get order results by the notification token.
	 *
	 * @param string $notification_token Notification token.
	 * @return OrderResults
	 */
	public function get_order_results( $notification_token ) {
		$result = $this->request(
			'GET',
			'order/server/api/events/results/merchant.order.status.changed',
			$notification_token
		);

		return OrderResults::from_object( $result );
	}

	/**
	 * Get issuers.
	 *
	 * @link https://developer.rabobank.nl/product/8949/api/8826
	 * @param string $access_token Access token.
	 * @return array<string>
	 */
	public function get_issuers( $access_token ) {
		$result = $this->request( 'GET', 'ideal/server/api/v2/issuers', $access_token );

		$issuers = [];

		if ( \property_exists( $result, 'issuers' ) ) {
			foreach ( $result->issuers as $issuer ) {
				$issuers[ $issuer->id ] = $issuer->name;
			}
		}

		return $issuers;
	}
}
