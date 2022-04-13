<?php
/**
 * Webhook controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\GatewayPostType;
use Pronamic\WordPress\Pay\Plugin;

/**
 * Webhook controller
 *
 * @author  Remco Tolsma
 * @version 2.3.4
 * @since   2.3.0
 */
class WebhookController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );

		\add_action( 'wp_loaded', [ $this, 'wp_loaded' ] );
	}

	/**
	 * REST API init.
	 *
	 * @link https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
	 * @link https://developer.wordpress.org/reference/hooks/rest_api_init/
	 * @return void
	 */
	public function rest_api_init() {
		\register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/webhook',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'rest_api_omnikassa_2_webhook' ],
				'permission_callback' => '__return_true',
			]
		);

		\register_rest_route(
			Integration::REST_ROUTE_NAMESPACE,
			'/webhook/(?P<id>[\d]+)',
			[
				'args'                => [
					'id' => [
						'description' => \__( 'Unique identifier for the gateway configuration post.', 'pronamic_ideal' ),
						'type'        => 'integer',
					],
				],
				'methods'             => 'POST',
				'callback'            => [ $this, 'rest_api_omnikassa_2_webhook_item' ],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * REST API OmniKassa 2.0 webhook handler.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return object
	 * @throws \Exception Throws exception when something unexpected happens ;-).
	 */
	public function rest_api_omnikassa_2_webhook( \WP_REST_Request $request ) {
		// Query.
		$query = new \WP_Query(
			[
				'post_type'   => GatewayPostType::POST_TYPE,
				'post_status' => 'publish',
				'nopaging'    => true,
				'meta_query'  => [
					[
						'key'   => '_pronamic_gateway_id',
						'value' => 'rabobank-omnikassa-2',
					],
				],
			]
		);

		$data = [
			'success' => true,
			'results' => [],
		];

		foreach ( $query->posts as $post ) {
			$id = \get_post_field( 'ID', $post );

			$request->set_param( 'id', $id );

			$data['results'][] = $this->rest_api_omnikassa_2_webhook_item( $request );
		}

		// Response.
		$response = new \WP_REST_Response( $data );

		$response->add_link( 'self', \rest_url( $request->get_route() ) );

		return $response;
	}

	/**
	 * REST API OmniKassa 2.0 webhook handler.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return object
	 * @throws \Exception Throws exception when something unexpected happens ;-).
	 */
	public function rest_api_omnikassa_2_webhook_item( \WP_REST_Request $request ) {
		// Input.
		$json = $request->get_body();

		try {
			$notification = Notification::from_json( $json );
		} catch ( \JsonSchema\Exception\ValidationException $e ) {
			// Invalid input data.
			return new \WP_Error(
				'rest_omnikassa_2_notification_invalid',
				\__( 'Invalid OmniKassa 2.0 notification.', 'pronamic_ideal ' ),
				[
					'status'       => 400,
					'notification' => $json,
				]
			);
		}

		// Gateway configuration.
		$id = $request->get_param( 'id' );

		if ( ! \is_numeric( $id ) ) {
			return new \WP_Error(
				'rest_omnikassa_2_gateway_no_id',
				\__( 'No gateway ID given in `id` parameter.', 'pronamic_ideal' )
			);
		}

		$gateway = Plugin::get_gateway( (int) $id );

		if ( ! $gateway instanceof Gateway ) {
			// Invalid gateway.
			return new \WP_Error(
				'rest_omnikassa_2_gateway_invalid',
				\__( 'Invalid OmniKassa 2.0 gateway.', 'pronamic_ideal ' ),
				[
					'status' => 400,
					'id'     => $id,
				]
			);
		}

		/**
		 * Data.
		 */
		$data = [
			'success' => true,
		];

		try {
			$gateway->handle_notification( $notification );
			// @phpstan-ignore-next-line
		} catch ( \Pronamic\WordPress\Pay\Gateways\OmniKassa2\UnknownOrderIdsException $e ) {
			/**
			 * We don't return an error for unknown order IDs, since OmniKassa
			 * otherwise assumes that the notification could not be processed.
			 */
			$data['uknown_order_ids'] = true;
		} catch ( \Exception $e ) {
			return new \WP_Error(
				'rest_omnikassa_2_exception',
				$e->getMessage(),
				[
					'status'       => 400,
					'notification' => $json,
					'id'           => $id,
				]
			);
		}

		// Response.
		$response = new \WP_REST_Response( $data );

		$response->add_link( 'self', \rest_url( $request->get_route() ) );

		return $response;
	}

	/**
	 * WordPress loaded, check for deprecated webhook call.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.3/wp-includes/rest-api.php#L277-L309
	 * @return void
	 */
	public function wp_loaded() {
		if ( ! \filter_has_var( \INPUT_GET, 'omnikassa2_webhook' ) ) {
			return;
		}

		\rest_get_server()->serve_request( '/pronamic-pay/omnikassa-2/v1/webhook' );

		exit;
	}
}
