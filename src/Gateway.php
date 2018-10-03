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

use Pronamic\WordPress\Pay\Core\Util as Core_Util;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Gateway
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   1.0.0
 */
class Gateway extends Core_Gateway {
	/**
	 * Client.
	 *
	 * @var Client
	 */
	private $client;

	/**
	 * Constructs and initializes an OmniKassa 2.0 gateway.
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->set_method( self::METHOD_HTTP_REDIRECT );

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
			PaymentMethods::AFTERPAY,
			PaymentMethods::BANCONTACT,
			PaymentMethods::CREDIT_CARD,
			PaymentMethods::IDEAL,
			PaymentMethods::MAESTRO,
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
		// Merchant order ID.
		$merchant_order_id = $payment->format_string( $this->config->order_id );

		$payment->set_meta( 'omnikassa_2_merchant_order_id', $merchant_order_id );

		// Shipping address.
		$shipping_address = $payment->get_shipping_address();

		if ( null !== $shipping_address ) {
			$shipping_detail = new Address();

			$name = $shipping_address->get_name();

			if ( null !== $name ) {
				$shipping_detail->set_first_name( $name->get_first_name() );
				$shipping_detail->set_middle_name( $name->get_middle_name() );
				$shipping_detail->set_last_name( $name->get_last_name() );
			}

			$shipping_detail->set_street( $shipping_address->get_street_name() );
			$shipping_detail->set_house_number( $shipping_address->get_house_number() );
			$shipping_detail->set_house_number_addition( $shipping_address->get_house_number_addition() );
			$shipping_detail->set_postal_code( $shipping_address->get_postal_code() );
			$shipping_detail->set_city( $shipping_address->get_city() );
			$shipping_detail->set_country_code( $shipping_address->get_country_code() );
		}

		// Billing address.
		$billing_address  = $payment->get_billing_address();

		if ( null !== $billing_address ) {
			$billing_detail = new Address();

			$name = $billing_address->get_name();

			if ( null !== $name ) {
				$billing_detail->set_first_name( $name->get_first_name() );
				$billing_detail->set_middle_name( $name->get_middle_name() );
				$billing_detail->set_last_name( $name->get_last_name() );
			}

			$billing_detail->set_street( $billing_address->get_street_name() );
			$billing_detail->set_house_number( $billing_address->get_house_number() );
			$billing_detail->set_house_number_addition( $billing_address->get_house_number_addition() );
			$billing_detail->set_postal_code( $billing_address->get_postal_code() );
			$billing_detail->set_city( $billing_address->get_city() );
			$billing_detail->set_country_code( $billing_address->get_country_code() );
		}

		// Customer information.
		$customer = $payment->get_customer();

		if ( null !== $customer ) {
			$customer_information = new CustomerInformation();
			
			$customer_information->set_email_address( $customer->get_email() );
			$customer_information->set_telephone_number( $customer->get_phone() );
		}

		// Payment brand.
		$payment_brand = PaymentBrands::transform( $payment->get_method() );

		// New order.
		$order = new Order(
			$merchant_order_id,
			new Money(
				$payment->get_currency(),
				Core_Util::amount_to_cents( $payment->get_amount()->get_amount() )
			),
			$payment->get_return_url()
		);

		$order->set_description( $payment->get_description() );
		$order->set_language( $payment->get_customer()->get_language() );
		$order->set_order_items( $payment->get_order_items() );
		$order->set_shipping_detail( $shipping_detail );
		$order->set_billing_detail( $billing_detail );
		$order->set_customer_information( $customer_information );
		$order->set_payment_brand( $payment_brand );

		if ( null !== $payment_brand ) {
			// Payment brand force should only be set if payment brand is not empty.
			$order->set_payment_brand_force( PaymentBrandForce::FORCE_ONCE );
		}

		// Maybe update access token.
		$this->maybe_update_access_token();

		// Handle errors.
		if ( $this->get_client_error() ) {
			return;
		}

		// Announce order.
		$result = $this->client->order_announce( $this->config, $order );

		// Handle errors.
		if ( $this->get_client_error() ) {
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
		if ( ! ReturnParameters::contains( $_GET ) ) { // WPCS: CSRF ok.
			return;
		}

		$parameters = ReturnParameters::from_array( $_GET ); // WPCS: CSRF ok.

		// Note.
		$note_values = array(
			'order_id'  => $parameters->get_order_id(),
			'status'    => $parameters->get_status(),
			'signature' => $parameters->get_signature(),
			'valid'     => $parameters->is_valid( $this->config->signing_key ) ? 'true' : 'false',
		);

		$note = '';

		$note .= '<p>';
		$note .= __( 'OmniKassa 2.0 return URL requested:', 'pronamic_ideal' );
		$note .= '</p>';

		$note .= '<dl>';

		foreach ( $note_values as $key => $value ) {
			$note .= sprintf( '<dt>%s</dt>', esc_html( $key ) );
			$note .= sprintf( '<dd>%s</dd>', esc_html( $value ) );
		}

		$note .= '</dl>';

		$payment->add_note( $note );

		// Validate.
		if ( ! $parameters->is_valid( $this->config->signing_key ) ) {
			return;
		}

		// Status.
		$payment->set_status( Statuses::transform( $parameters->get_status() ) );
	}

	/**
	 * Handle notification.
	 *
	 * @param Notification $notification Notification.
	 *
	 * @return void
	 */
	public function handle_notification( Notification $notification ) {
		if ( ! $notification->is_valid( $this->config->signing_key ) ) {
			return;
		}

		switch ( $notification->get_event_name() ) {
			case 'merchant.order.status.changed':
				$this->handle_merchant_order_status_changed( $notification );
		}
	}

	/**
	 * Handle `merchant.order.status.changed` event.
	 *
	 * @param Notification $notification Notification.
	 *
	 * @return void
	 */
	private function handle_merchant_order_status_changed( Notification $notification ) {
		do {
			$order_results = $this->client->get_order_results( $notification->get_authentication() );

			if ( ! $order_results || $order_results->is_valid( $this->config->signing_key ) ) {
				return;
			}

			foreach ( $order_results as $order_result ) {
				$payment = get_pronamic_payment_by_meta( '_pronamic_payment_omnikassa_2_merchant_order_id', $order_result->get_merchant_order_id() );

				if ( empty( $payment ) ) {
					continue;
				}

				$payment->set_transaction_id( $order_result->get_omnikassa_order_id() );
				$payment->set_status( Statuses::transform( $order_result->get_order_status() ) );

				// Note.
				$note = '';

				$note .= '<p>';
				$note .= __( 'OmniKassa 2.0 webhook URL requested:', 'pronamic_ideal' );
				$note .= '</p>';
				$note .= '<pre>';
				$note .= wp_json_encode( $order_result->get_json(), JSON_PRETTY_PRINT );
				$note .= '</pre>';

				$payment->add_note( $note );

				$payment->save();
			}
		} while ( $order_results->more_available() );
	}

	/**
	 * Maybe update access token.
	 *
	 * @return void
	 */
	private function maybe_update_access_token() {
		if ( $this->config->is_access_token_valid() ) {
			return;
		}

		$data = $this->client->get_access_token_data();

		if ( ! is_object( $data ) ) {
			return;
		}

		$this->config->access_token             = $data->token;
		$this->config->access_token_valid_until = $data->validUntil;

		update_post_meta( $this->config->post_id, '_pronamic_gateway_omnikassa_2_access_token', $data->token );
		update_post_meta(
			$this->config->post_id,
			'_pronamic_gateway_omnikassa_2_access_token_valid_until',
			$data->validUntil
		);
	}

	/**
	 * Get client error.
	 *
	 * @return \WP_Error|bool
	 */
	private function get_client_error() {
		$error = $this->client->get_error();

		if ( is_wp_error( $error ) ) {
			$this->error = $error;

			return $error;
		}

		return false;
	}
}
