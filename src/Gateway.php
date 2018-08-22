<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Gateway
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Gateway extends Core_Gateway {
	/**
	 * Constructs and initializes an OmniKassa 2.0 gateway.
	 *
	 * @param \Pronamic\WordPress\Pay\Gateways\OmniKassa2\Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->set_method( self::METHOD_HTTP_REDIRECT );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );

		// Client.
		$this->client = new Client();

		$url = Client::URL_PRODUCTION;

		if ( self::MODE_TEST === $config->mode ) {
			$url = Client::URL_SANDBOX;
		}

		$this->client->set_url( $url );
		$this->client->set_refresh_token( $config->refresh_token );
		$this->client->set_signing_key( $config->signing_key );
	}

	/**
	 * Get supported payment methods.
	 *
	 * @see \Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 * @return array
	 */
	public function get_supported_payment_methods() {
		return array(
			PaymentMethods::BANCONTACT,
			PaymentMethods::CREDIT_CARD,
			PaymentMethods::IDEAL,
			PaymentMethods::PAYPAL,
		);
	}

	/**
	 * Start.
	 *
	 * @see Core_Gateway::start()
	 *
	 * @param Payment $payment Payment.
	 */
	public function start( Payment $payment ) {
		$order = new Order();

		$order->timestamp           = date( DATE_ATOM );
		$order->merchant_order_id   = $payment->format_string( $this->config->order_id );
		$order->description         = $payment->get_description();
		$order->amount              = $payment->get_amount()->get_amount();
		$order->currency            = $payment->get_currency();
		$order->language            = $payment->get_language();
		$order->merchant_return_url = $payment->get_return_url();
		$order->payment_brand       = Methods::transform( $payment->get_method() );

		if ( null !== $order->payment_brand ) {
			// Payment brand force should only be set if payment brand is not empty.
			$order->payment_brand_force = PaymentBrandForce::FORCE_ONCE;
		}

		if ( ! $this->config->is_access_token_valid() ) {
			$data = $this->client->get_access_token_data();

			$error = $this->client->get_error();

			if ( is_wp_error( $error ) ) {
				$this->error = $error;

				return;
			}

			$this->config->access_token             = $data->token;
			$this->config->access_token_valid_until = $data->validUntil;

			update_post_meta( $this->config->post_id, '_pronamic_gateway_omnikassa_2_access_token', $data->token );
			update_post_meta( $this->config->post_id, '_pronamic_gateway_omnikassa_2_access_token_valid_until', $data->validUntil );
		}

		$result = $this->client->order_announce( $this->config, $order );

		$error = $this->client->get_error();

		if ( is_wp_error( $error ) ) {
			$this->error = $error;

			return;
		}

		if ( $result ) {
			$payment->set_action_url( $result->redirectUrl );
		}
	}

	/**
	 * Update status of the specified payment.
	 *
	 * @param Payment $payment Payment.
	 */
	public function update_status( Payment $payment ) {
		ReturnListener::listen( $payment );

		if ( ! ReturnParameters::contains( $_GET ) ) { // WPCS: CSRF ok.
			return;
		}

		$parameters = ReturnParameters::from_array( $_GET ); // WPCS: CSRF ok.

		if ( ! $parameters->is_valid( $this->config->signing_key ) ) {
			return;
		}

		$payment->set_status( Statuses::transform( $parameters->get_status() ) );
	}

	/**
	 * Handle notification.
	 *
	 * @param Notification $notification Notification.
	 */
	public function handle_notification( Notification $notification ) {
		if ( ! $notification->is_valid( $this->config->signing_key ) ) {
			return;
		}

		switch ( $notification->get_event_name() ) {
			case 'merchant.order.status.changed':
				return $this->handle_merchant_order_staus_changed( $notification );
		}
	}

	/**
	 * Handle `merchant.order.status.changed` event.
	 *
	 * @param Notification $notification Notification.
	 */
	private function handle_merchant_order_staus_changed( Notification $notification ) {
		do {
			$order_results = $this->client->get_order_results( $notification->get_authentication() );

			if ( ! $order_results->is_valid( $this->config->signing_key ) ) {
				return;
			}

			foreach ( $order_results as $order_result ) {
				$payment = get_pronamic_payment_by_meta( '_pronamic_payment_order_id', $order_result->get_merchant_order_id() );

				if ( empty( $payment ) ) {
					continue;
				}

				$payment->set_transaction_id( $order_result->get_omnikassa_order_id() );
				$payment->set_status( Statuses::transform( $order_result->get_order_status() ) );

				// Add note.
				$note = sprintf(
					/* translators: %s: OmniKassa 2.0 */
					__( 'Webhook requested by %s.', 'pronamic_ideal' ),
					__( 'OmniKassa 2.0', 'pronamic_ideal' )
				);

				$payment->add_note( $note );

				$payment->save();
			}
		} while ( $order_results->more_available() );
	}
}
