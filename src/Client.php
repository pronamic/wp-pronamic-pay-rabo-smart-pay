<?php
/**
 * Client.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
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
final class Client {
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
	 * Config.
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Construct client.
	 * 
	 * @param Config $config Configuration object.
	 */
	public function __construct( $config ) {
		$this->config = $config;
	}

	/**
	 * Request URL with specified method, token.
	 *
	 * @param string      $method   HTTP request method.
	 * @param string      $endpoint URL endpoint to request.
	 * @param string      $token    Authorization token.
	 * @param object|null $data     Data.
	 * @return object
	 * @throws \Exception Throws exception when Rabobank OmniKassa 2.0 response is not what we expect.
	 */
	private function request( $method, $endpoint, $token, $data = null ) {
		$url = $this->config->api_url . $endpoint;

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
			'headers' => [
				'Authorization'    => 'Bearer ' . $token,
				'X-Api-User-Agent' => '(pr: RSP-PRON-0001)',
			],
			'method'  => $method,
			'timeout' => 30,
		];

		if ( null !== $data ) {
			$args['headers']['Content-Type'] = 'application/json';

			if ( $data instanceof IdempotenceInterface ) {
				$args['headers']['Request-ID'] = $data->get_idempotence_id();
			}

			$args['body'] = \wp_json_encode( $data );
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

		$response = Http::request( $url, $args );

		$data = $response->json();

		if ( ! \is_object( $data ) ) {
			throw new \Exception(
				\sprintf(
					'Could not JSON decode Rabo Smart Pay response to an object, HTTP response: "%s %s", HTTP body length: "%d".',
					\esc_html( (string) $response->status() ),
					\esc_html( $response->message() ),
					\intval( \strlen( $response->body() ) )
				),
				\intval( $response->status() )
			);
		}

		$object_access = new ObjectAccess( $data );

		if ( $object_access->has_property( 'errorCode' ) ) {
			$error = Error::from_object( $data );

			throw $error;
		}

		return $data;
	}

	/**
	 * Get access token.
	 *
	 * @return object
	 */
	public function get_access_token_data() {
		return $this->request( 'GET', 'gatekeeper/refresh', $this->config->refresh_token );
	}

	/**
	 * Order announce.
	 *
	 * @param Order $order  Order.
	 * @return OrderAnnounceResponse
	 */
	public function order_announce( Order $order ) {
		$result = $this->request( 'POST', 'order/server/api/v2/order', $this->config->access_token, $order );

		return OrderAnnounceResponse::from_object( $result );
	}

	/**
	 * Refund.
	 *
	 * @param RefundRequest $refund Refund request.
	 * @return RefundResponse
	 */
	public function refund( RefundRequest $refund ) {
		$result = $this->request(
			'POST',
			\strtr(
				'order/server/api/v2/refund/transactions/{transaction_id}/refunds',
				[
					'{transaction_id}' => $refund->transaction_id,
				]
			),
			$this->config->access_token,
			$refund
		);

		return RefundResponse::from_object( $result );
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
			'order/server/api/v2/events/results/merchant.order.status.changed',
			$notification_token
		);

		return OrderResults::from_object( $result );
	}

	/**
	 * Get payment brands.
	 *
	 * @link https://developer.rabobank.nl/product/9619/api/9355#/RaboOmniKassaOnlinePaymentAPI_1010/operation/%2Forder%2Fserver%2Fapi%2Fpayment-brands/get
	 * @param string $access_token Access token.
	 * @return array<string>
	 */
	public function get_payment_brands( $access_token ) {
		$result = $this->request( 'GET', 'order/server/api/payment-brands', $access_token );

		$object_access = new ObjectAccess( $result );

		$data = $object_access->get_optional( 'paymentBrands' );

		if ( ! \is_array( $data ) ) {
			return [];
		}

		$payment_brands = [];

		foreach ( $data as $brand ) {
			$payment_brands[ $brand->name ] = $brand->status;
		}

		return $payment_brands;
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
