<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Title: OmniKassa 2.0 gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Gateway extends Core_Gateway {
	/**
	 * Constructs and initializes an OmniKassa 2.0 gateway.
	 *
	 * @param \Pronamic\WordPress\Pay\Gateways\OmniKassa2\Config $config
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->set_method( Gateway::METHOD_HTTP_REDIRECT );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );

		// Client
		$this->client = new Client();

		$url = Client::URL_PRODUCTION;

		if ( Gateway::MODE_TEST === $config->mode ) {
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
	 * @param Payment $payment
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
	 * @param Payment $payment
	 */
	public function update_status( Payment $payment ) {
		$input_status = null;

		// Update status on customer return
		if ( filter_has_var( INPUT_GET, 'status' ) && filter_has_var( INPUT_GET, 'signature' ) ) {
			// Input data
			$input_status    = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_STRING );
			$input_signature = filter_input( INPUT_GET, 'signature', FILTER_SANITIZE_STRING );

			// Validate signature
			$merchant_order_id = $payment->get_id();

			if ( '{order_id}' === $this->config->order_id ) {
				$merchant_order_id = $payment->get_order_id();
			}

			$data = array( $merchant_order_id, $input_status );

			$signature = Security::calculate_signature( $data, $this->config->signing_key );

			if ( ! Security::validate_signature( $input_signature, $signature ) ) {
				// Invalid signature
				return;
			}
		}

		// Update status via webhook
		if ( isset( $payment->meta['omnikassa_2_update_order_status'] ) ) {
			$input_status = $payment->meta['omnikassa_2_update_order_status'];

			$payment->set_meta( 'omnikassa_2_update_order_status', null );
		}

		if ( ! $input_status ) {
			return;
		}

		// Update payment status
		$status = Statuses::transform( $input_status );

		$payment->set_status( $status );
	}
}
