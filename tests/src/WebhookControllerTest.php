<?php
/**
 * Webhook controller test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Http\Factory;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use Pronamic\WordPress\Pay\Plugin;
use WP_REST_Request;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Webhook controller test class
 */
class WebhookControllerTest extends TestCase {
	/**
	 * REST server.
	 *
	 * @var WP_REST_Server
	 */
	private $rest_server;

	/**
	 * Controller.
	 *
	 * @var WebhookController
	 */
	private $controller;

	/**
	 * HTTP factory.
	 *
	 * @var Factory
	 */
	private $http_factory;

	/**
	 * Setup.
	 *
	 * @link https://github.com/WordPress/phpunit-test-reporter/blob/master/tests/test-restapi.php
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->rest_server = \rest_get_server();

		$this->controller = new WebhookController();
		$this->controller->setup();

		$this->http_factory = new Factory();

		// REST API init.
		\do_action( 'rest_api_init' );
	}

	/**
	 * Test webhook.
	 *
	 * @dataProvider webhook_test_provider
	 * @param string $response_file  Response file.
	 * @param string $payment_status Payment status.
	 * @param string $transaction_id Transaction ID.
	 * @return void
	 */
	public function test_webhook( $response_file, $payment_status, $transaction_id ) {
		/**
		 * Setup gateway.
		 */
		$config_id = \wp_insert_post(
			[
				'meta_input'  => [
					'_pronamic_gateway_id' => 'rabobank-omnikassa-2-sandbox',
					'_pronamic_gateway_omnikassa_2_refresh_token' => '',
					'_pronamic_gateway_omnikassa_2_signing_key' => 'QAhFrajUoLsKowfRo15vFXIpdbCgmI2S82idk6xPiCk',
				],
				'post_status' => 'publish',
				'post_title'  => 'Rabo Smart Pay - Test',
				'post_type'   => 'pronamic_gateway',
			]
		);

		$this->assertIsInt( $config_id );

		/**
		 * The start payment routine will trigger a
		 * `gatekeeper/refresh` request.
		 */
		$this->http_factory->fake(
			'https://betalen.rabobank.nl/omnikassa-api-sandbox/gatekeeper/refresh',
			__DIR__ . '/../http/gatekeeper-refresh.http'
		);

		/**
		 * The start payment routine will trigger a
		 * `order/server/api/v2/order` request.
		 */
		$this->http_factory->fake(
			'https://betalen.rabobank.nl/omnikassa-api-sandbox/order/server/api/v2/order',
			__DIR__ . '/../http/announce-order-response.http'
		);

		/**
		 * Setup payment.
		 */
		$payment = new Payment();

		$payment->set_config_id( $config_id );

		Plugin::start_payment( $payment );

		$this->assertEquals( 'rabo-smart-pay-order-1d0a95f4-2589-439b-9562-c50aa19f9caf', $payment->get_slug() );

		/**
		 * The webhook request from the Rabobak will trigger a
		 * `order/server/api/v2/events/results/merchant.order.status.changed`
		 * request. Before we simulate the webhook request from the Rabobank we
		 * prepare a fake HTTP response to mock the API response.
		 */
		$this->http_factory->fake(
			'https://betalen.rabobank.nl/omnikassa-api-sandbox/order/server/api/v2/events/results/merchant.order.status.changed',
			$response_file
		);

		/**
		 * Simulate webhook request from Rabobank.
		 */
		$json = \file_get_contents( __DIR__ . '/../json/notification.json', true );

		$request = new WP_REST_Request( 'POST', '/pronamic-pay/omnikassa-2/v1/webhook/' . $config_id );

		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_header( 'User-Agent', 'Java/1.8.0' );
		$request->set_body( $json );

		$response = \rest_do_request( $request );

		$error = $response->as_error();

		$this->assertEquals( 200, $response->get_status(), null === $error ? '' : $error->get_error_message() );

		/**
		 * Assert payment.
		 */
		$this->assertEquals( $payment_status, $payment->get_status() );
		$this->assertEquals( $transaction_id, $payment->get_transaction_id() );

		/**
		 * Cleanup.
		 */
		\wp_delete_post( $payment->get_id(), true );
	}

	/**
	 * Webhook test provider.
	 *
	 * @return array<string>
	 */
	public static function webhook_test_provider() {
		return [
			[
				__DIR__ . '/../http/merchant.order.status.changed.http',
				PaymentStatus::SUCCESS,
				'22b36073-57a3-4c3d-9585-87f2e55275a5',
			],
			[
				__DIR__ . '/../http/merchant.order.status.changed-cancelled.http',
				PaymentStatus::CANCELLED,
				'22b36073-57a3-4c3d-9585-87f2e55275a5',
			],
		];
	}
}
