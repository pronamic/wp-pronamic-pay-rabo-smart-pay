<?php
/**
 * Webhook controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
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
 * @version 2.3.0
 * @since   2.3.0
 */
class WebhookController {
	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );

		\add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
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
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_omnikassa_2_webhook' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * REST API OmniKassa 2.0 webhook handler.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return object
	 */
	public function rest_api_omnikassa_2_webhook( \WP_REST_Request $request ) {
		// Input.
		$json = $request->get_body();

		try {
			$notification = Notification::from_json( $json );
		} catch ( \JsonSchema\Exception\ValidationException $e ) {
			// Invalid input data.
			return new \WP_Error(
				'rest_omnikassa_2_notification_invalid',
				\__( 'Invalid OmniKassa 2.0 notification.', 'pronamic_ideal ' ),
				array(
					'status'       => 400,
					'notification' => $json,
				)
			);
		}

		// Query.
		$query = new \WP_Query(
			array(
				'post_type'   => GatewayPostType::POST_TYPE,
				'post_status' => 'publish',
				'nopaging'    => true,
				'meta_query'  => array(
					array(
						'key'   => '_pronamic_gateway_id',
						'value' => 'rabobank-omnikassa-2',
					),
				),
			)
		);

		foreach ( $query->posts as $post ) {
			$gateway = Plugin::get_gateway( $post->ID );

			if ( $gateway instanceof Gateway ) {
				try {
					$gateway->handle_notification( $notification );
				} catch ( \Exception $e ) {
					continue;
				}
			}
		}

		// Response.
		$response = new \WP_REST_Response( array( 'success' => true ) );

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
