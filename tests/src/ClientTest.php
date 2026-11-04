<?php
/**
 * Client test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Http\Factory;
use Pronamic\WordPress\Http\Response;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Client test class
 */
class ClientTest extends TestCase {
	/**
	 * HTTP factory.
	 *
	 * @var Factory
	 */
	private $http_factory;

	/**
	 * Requested URLs.
	 *
	 * @var array<string>
	 */
	private $requests = [];

	/**
	 * Configs passed to the access token refreshed action.
	 *
	 * @var array<Config>
	 */
	private $refreshed_configs = [];

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->http_factory = new Factory();

		$this->requests = [];

		$this->refreshed_configs = [];
	}

	/**
	 * Fake an URL with a response file and record the request.
	 *
	 * @param string $url  URL.
	 * @param string $file Response file.
	 * @return void
	 */
	private function fake( $url, $file ) {
		$this->http_factory->fake(
			$url,
			function () use ( $url, $file ) {
				$this->requests[] = $url;

				return Response::array_from_file( $file );
			}
		);
	}

	/**
	 * Get config.
	 *
	 * @return Config
	 */
	private function get_config() {
		$config = new Config();

		$config->api_url       = Client::URL_SANDBOX;
		$config->refresh_token = 'refresh-token';

		return $config;
	}

	/**
	 * Test that the client refreshes an expired access token before a refund.
	 *
	 * @return void
	 */
	public function test_refund_refreshes_expired_access_token() {
		$this->fake( Client::URL_SANDBOX . 'gatekeeper/refresh', __DIR__ . '/../http/gatekeeper-refresh.http' );
		$this->fake(
			Client::URL_SANDBOX . 'order/server/api/v2/refund/transactions/transaction-1/refunds',
			__DIR__ . '/../http/refund-response.http'
		);

		$config = $this->get_config();

		$config->access_token             = 'expired-token';
		$config->access_token_valid_until = '2000-01-01T00:00:00.000+0000';

		\add_action(
			'pronamic_pay_omnikassa_2_access_token_refreshed',
			function ( Config $config ) {
				$this->refreshed_configs[] = clone $config;
			}
		);

		$client = new Client( $config );

		$response = $client->refund( new RefundRequest( 'transaction-1', new Money( 'EUR', 100 ) ) );

		$this->assertSame( 'd6b7a8f5-1e6b-4c5a-9d8f-0b3e2a1c4d5e', $response->id );

		$this->assertSame(
			[
				Client::URL_SANDBOX . 'gatekeeper/refresh',
				Client::URL_SANDBOX . 'order/server/api/v2/refund/transactions/transaction-1/refunds',
			],
			$this->requests
		);

		$this->assertCount( 1, $this->refreshed_configs );
		$this->assertStringStartsWith( 'eyJraWQiOiJHS0wi', $this->refreshed_configs[0]->access_token );
		$this->assertSame( '2018-11-24T16:54:51.216+0000', $this->refreshed_configs[0]->access_token_valid_until );
	}

	/**
	 * Test that the client does not refresh a valid access token.
	 *
	 * @return void
	 */
	public function test_refund_uses_valid_access_token() {
		$this->fake( Client::URL_SANDBOX . 'gatekeeper/refresh', __DIR__ . '/../http/gatekeeper-refresh.http' );
		$this->fake(
			Client::URL_SANDBOX . 'order/server/api/v2/refund/transactions/transaction-1/refunds',
			__DIR__ . '/../http/refund-response.http'
		);

		$config = $this->get_config();

		$config->access_token             = 'valid-token';
		$config->access_token_valid_until = \gmdate( 'Y-m-d\TH:i:s.000O', \time() + \HOUR_IN_SECONDS );

		$actions_before = \did_action( 'pronamic_pay_omnikassa_2_access_token_refreshed' );

		$client = new Client( $config );

		$client->refund( new RefundRequest( 'transaction-1', new Money( 'EUR', 100 ) ) );

		$this->assertSame(
			[
				Client::URL_SANDBOX . 'order/server/api/v2/refund/transactions/transaction-1/refunds',
			],
			$this->requests
		);

		$this->assertSame( $actions_before, \did_action( 'pronamic_pay_omnikassa_2_access_token_refreshed' ) );
	}

	/**
	 * Test that an empty 404 response results in a clear exception.
	 *
	 * @return void
	 */
	public function test_empty_not_found_response() {
		$this->fake(
			Client::URL_SANDBOX . 'order/server/api/v2/refund/transactions/transaction-1/refunds',
			__DIR__ . '/../http/not-found-empty.http'
		);

		$config = $this->get_config();

		$config->access_token             = 'valid-token';
		$config->access_token_valid_until = \gmdate( 'Y-m-d\TH:i:s.000O', \time() + \HOUR_IN_SECONDS );

		$client = new Client( $config );

		$this->expectException( \Exception::class );
		$this->expectExceptionCode( 404 );
		$this->expectExceptionMessage(
			'Rabo Smart Pay request failed: POST order/server/api/v2/refund/transactions/transaction-1/refunds, HTTP response: "404 Not Found", empty body.'
		);

		$client->refund( new RefundRequest( 'transaction-1', new Money( 'EUR', 100 ) ) );
	}
}
