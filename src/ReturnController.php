<?php
/**
 * Return controller
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Payments\Payment;
use WP_REST_Request;

/**
 * Return controller class
 */
class ReturnController {
	/**
	 * Integration.
	 *
	 * @var Integration
	 */
	private $integration;

	/**
	 * Construct return controller.
	 *
	 * @param Integration $integration Integration.
	 */
	public function __construct( Integration $integration ) {
		$this->integration = $integration;
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function setup() {
		\add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );
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
			'/return/(?P<payment_id>[\d]+)',
			[
				'args'                => [
					'order_id'   => [
						'description' => \__( 'OmniKassa order ID.', 'pronamic_ideal' ),
						'type'        => 'string',
					],
					'payment_id' => [
						'description' => \__( 'Unique identifier for the payment.', 'pronamic_ideal' ),
						'type'        => 'integer',
					],
					'signature'  => [
						'description' => \__( 'OmniKassa signature.', 'pronamic_ideal' ),
						'type'        => 'string',
					],
					'status'     => [
						'description' => \__( 'OmniKassa order status.', 'pronamic_ideal' ),
						'type'        => 'string',
					],
				],
				'callback'            => [ $this, 'rest_api_return' ],
				'methods'             => 'GET',
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Get return parameters.
	 *
	 * @param WP_REST_Request $request WordPress REST API request.
	 * @return ReturnParameters
	 * @throws \InvalidArgumentException Throws exception when REST request does not contain valid return parameter values.
	 */
	private function get_return_parameters( WP_REST_Request $request ) {
		$order_id  = $request->get_param( 'order_id' );
		$status    = $request->get_param( 'status' );
		$signature = $request->get_param( 'signature' );

		if ( ! \is_string( $order_id ) || ! \is_string( $status ) || ! \is_string( $signature ) ) {
			throw new \InvalidArgumentException(
				'WordPress REST API request does not contain valid return parameter values.'
			);
		}

		return new ReturnParameters( $order_id, $status, $signature );
	}

	/**
	 * Get payment.
	 *
	 * @param WP_REST_Request $request WordPress REST API request.
	 * @return Payment
	 * @throws \InvalidArgumentException Throws exception when REST request does not contain valid payment ID.
	 */
	private function get_payment( WP_REST_Request $request ): Payment {
		$payment_id = $request->get_param( 'payment_id' );

		if ( ! \is_int( $payment_id ) ) {
			throw new \InvalidArgumentException( 'WordPress REST API request does not contain valid payment ID values.' );
		}

		$payment = \get_pronamic_payment( $payment_id );

		if ( null === $payment ) {
			throw new \InvalidArgumentException(
				\sprintf(
					/* translators: %s: Payment ID. */
					\esc_html__( 'No payment found by `payment_id` variable: %s.', 'pronamic_ideal' ),
					\esc_html( (string) $payment_id )
				)
			);
		}

		return $payment;
	}

	/**
	 * REST API return handler.
	 *
	 * @param WP_REST_Request $request Request.
	 * @return object
	 * @throws \Exception Throws exception when something unexpected happens ;-).
	 */
	public function rest_api_return( WP_REST_Request $request ) {
		try {
			$payment    = $this->get_payment( $request );
			$parameters = $this->get_return_parameters( $request );

			$config = $this->integration->get_config( (int) $payment->config_id );

			// Note.
			$note_values = [
				'order_id'  => $parameters->get_order_id(),
				'signature' => (string) $parameters->get_signature(),
				'status'    => $parameters->get_status(),
				'valid'     => $parameters->is_valid( $config->signing_key ) ? 'true' : 'false',
			];

			$note = '';

			$note .= '<p>';
			$note .= \__( 'Rabo Smart Pay return URL requested:', 'pronamic_ideal' );
			$note .= '</p>';

			$note .= '<dl>';

			foreach ( $note_values as $key => $value ) {
				$note .= \sprintf( '<dt>%s</dt>', \esc_html( $key ) );
				$note .= \sprintf( '<dd>%s</dd>', \esc_html( $value ) );
			}

			$note .= '</dl>';

			$payment->add_note( $note );

			/**
			 * 303 See Other.
			 *
			 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/303
			 */
			$result = new \WP_REST_Response( null, 303, [ 'Location' => $payment->get_return_redirect_url() ] );

			// Validate.
			if ( ! $parameters->is_valid( $config->signing_key ) ) {
				return $result;
			}

			// Status.
			$pronamic_status = Statuses::transform( $parameters->get_status() );

			if ( null !== $pronamic_status ) {
				$payment->set_status( $pronamic_status );

				$result->header( 'Location', $payment->get_return_redirect_url() );
			}

			$payment->save();

			return $result;
		} catch ( \Exception $e ) {
			return new \WP_Error(
				'rest_omnikassa_2_exception',
				$e->getMessage()
			);
		}
	}
}
