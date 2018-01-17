<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 gateway
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Gateway extends \Pronamic_WP_Pay_Gateway {
	/**
	 * The OmniKassa 2 client object.
	 *
	 * @var \Pronamic\WordPress\Pay\Gateways\OmniKassa2\Client
	 */
	private $client;

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an OmniKassa 2.0 gateway.
	 *
	 * @param \Pronamic\WordPress\Pay\Gateways\OmniKassa2\Config $config
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->set_method( \Pronamic_WP_Pay_Gateway::METHOD_HTTP_REDIRECT );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );

		// Client
		$this->client = new Client();

		$url = Client::URL_PRODUCTION;

		if ( \Pronamic_IDeal_IDeal::MODE_TEST === $config->mode ) {
			$url = Client::URL_SANDBOX;
		}

		$this->client->set_url( $url );
		$this->client->set_refresh_token( $config->refresh_token );
		$this->client->set_signing_key( $config->signing_key );
	}

	/////////////////////////////////////////////////

	/**
	 * Get supported payment methods.
	 *
	 * @see \Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			\Pronamic_WP_Pay_PaymentMethods::BANCONTACT,
			\Pronamic_WP_Pay_PaymentMethods::CREDIT_CARD,
			\Pronamic_WP_Pay_PaymentMethods::IDEAL,
			\Pronamic_WP_Pay_PaymentMethods::PAYPAL,
		);
	}

	/////////////////////////////////////////////////

	/**
	 * Start.
	 *
	 * @see \Pronamic_WP_Pay_Gateway::start()
	 *
	 * @param \Pronamic_Pay_Payment $payment
	 */
	public function start( \Pronamic_Pay_Payment $payment ) {
		$order = new Order();

		$order->timestamp           = date( DATE_ATOM );
		$order->merchant_order_id   = $payment->get_id();
		$order->description         = $payment->get_description();
		$order->amount              = $payment->get_amount();
		$order->currency            = $payment->get_currency();
		$order->language            = $payment->get_language();
		$order->merchant_return_url = $payment->get_return_url();
		$order->payment_brand       = PaymentMethods::transform( $payment->get_method() );

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

	/////////////////////////////////////////////////

	/**
	 * Get the output HTML.
	 *
	 * @since 1.1.2
	 * @see \Pronamic_WP_Pay_Gateway::get_output_html()
	 */
	public function get_output_fields() {
		return $this->client->get_fields();
	}

	/////////////////////////////////////////////////

	/**
	 * Update status of the specified payment.
	 *
	 * @param \Pronamic_Pay_Payment $payment
	 */
	public function update_status( \Pronamic_Pay_Payment $payment ) {
		$input_status = null;

		// Update status on customer return
		if ( filter_has_var( INPUT_GET, 'status' ) && filter_has_var( INPUT_GET, 'signature' ) ) {
			// Input data
			$input_status    = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_STRING );
			$input_signature = filter_input( INPUT_GET, 'signature', FILTER_SANITIZE_STRING );

			// Validate signature
			$data = array( $payment->get_id(), $input_status );

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
