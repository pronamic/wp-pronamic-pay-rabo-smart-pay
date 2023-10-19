<?php
/**
 * Webhook controller test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Http\Factory;
use WP_REST_Request;
use WP_REST_Server;
use WP_UnitTestCase;

/**
 * Webhook controller test class
 */
class WebhookControllerTest extends WP_UnitTestCase {
	/**
	 * REST server.
	 * 
	 * @var WP_REST_Server;
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
	 * @return void
	 */
	public function test_webhook() {
		/**
		 * Setup gateway configuration.
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
		 * The webhook request from the Rabobak will trigger a
		 * `order/server/api/v2/events/results/merchant.order.status.changed`
		 * request.
		 */
		$this->http_factory->fake(
			'https://betalen.rabobank.nl/omnikassa-api-sandbox/order/server/api/v2/events/results/merchant.order.status.changed',
			__DIR__ . '/../http/merchant.order.status.changed.http'
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

		$this->assertEquals( 200, $response->get_status() );

		$this->assertEquals(
			(object) [
				'notificationResponse' => '[accepted]',
			],
			$response->get_data()
		);
	}
}
