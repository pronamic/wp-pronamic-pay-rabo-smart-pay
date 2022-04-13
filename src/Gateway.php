<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Money\TaxedMoney;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Payments\Payment;

/**
 * Gateway
 *
 * @author  Remco Tolsma
 * @version 2.3.4
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
	 * Config.
	 * 
	 * @var Config.
	 */
	private $config;

	/**
	 * Constructs and initializes an OmniKassa 2.0 gateway.
	 *
	 * @param Config $config Config.
	 */
	public function __construct( Config $config ) {
		parent::__construct();

		$this->config = $config;

		$this->set_method( self::METHOD_HTTP_REDIRECT );

		// Supported features.
		$this->supports = [
			'webhook_log',
		];

		// Client.
		$this->client = new Client();

		$this->client->set_url( $config->get_api_url() );
		$this->client->set_refresh_token( $config->refresh_token );
		$this->client->set_signing_key( $config->signing_key );
	}

	/**
	 * Get issuers
	 *
	 * @see Core_Gateway::get_issuers()
	 * @return array<int, array<string, array<string>>>
	 */
	public function get_issuers() {
		$groups = [];

		// Maybe update access token.
		$this->maybe_update_access_token();

		$result = $this->client->get_issuers( $this->config->access_token );

		$groups[] = [
			'options' => $result,
		];

		return $groups;
	}

	/**
	 * Get supported payment methods.
	 *
	 * @see \Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 * @return array<string>
	 */
	public function get_supported_payment_methods() {
		return [
			PaymentMethods::AFTERPAY_NL,
			PaymentMethods::BANCONTACT,
			PaymentMethods::CREDIT_CARD,
			PaymentMethods::IDEAL,
			PaymentMethods::MAESTRO,
			PaymentMethods::MASTERCARD,
			PaymentMethods::PAYPAL,
			PaymentMethods::V_PAY,
			PaymentMethods::VISA,
		];
	}

	/**
	 * Start.
	 *
	 * @param Payment $payment Payment.
	 * @return void
	 * @throws \Exception Throws exception when payment could not start at Rabobank OmniKassa 2.0.
	 * @see Core_Gateway::start()
	 */
	public function start( Payment $payment ) {
		// Merchant order ID.
		$merchant_order_id = $payment->format_string( $this->config->order_id );

		$payment->set_meta( 'omnikassa_2_merchant_order_id', $merchant_order_id );

		// New order.
		$merchant_return_url = $payment->get_return_url();

		/**
		 * Filters the OmniKassa 2.0 merchant return URL.
		 *
		 * OmniKassa 2 requires for each order announcement a merchant return URL.
		 * OmniKassa 2 does not allow all merchant return URL's. An order
		 * announcement with a merchant return URL's with the TLD `.test` will
		 * for example result in the following error:
		 *
		 * > merchantReturnURL is not a valid web address
		 *
		 * This can be very inconvenient for testing OmniKassa 2, therefor we
		 * introduced this filter.
		 *
		 * @link https://github.com/wp-pay-gateways/omnikassa-2#pronamic_pay_omnikassa_2_merchant_return_url
		 * @link https://github.com/wp-pay-gateways/omnikassa-2/tree/master/documentation#merchantreturnurl-is-not-a-valid-web-address
		 * @param string $merchant_return_url Merchant return URL.
		 */
		$merchant_return_url = \apply_filters( 'pronamic_pay_omnikassa_2_merchant_return_url', $merchant_return_url );

		$order = new Order(
			$merchant_order_id,
			MoneyTransformer::transform( $payment->get_total_amount() ),
			$merchant_return_url
		);

		// Shipping address.
		$order->set_shipping_detail( AddressTransformer::transform( $payment->get_shipping_address() ) );

		// Billing address.
		$order->set_billing_detail( AddressTransformer::transform( $payment->get_billing_address() ) );

		// Customer information.
		$customer = $payment->get_customer();

		if ( null !== $customer ) {
			// Language.
			$language = $customer->get_language();

			if ( null !== $language ) {
				$order->set_language( \strtoupper( $language ) );
			}

			// Customer information.
			$customer_information = new CustomerInformation();

			$customer_information->set_email_address( $customer->get_email() );
			$customer_information->set_date_of_birth( $customer->get_birth_date() );
			$customer_information->set_gender( Gender::transform( $customer->get_gender() ) );
			$customer_information->set_telephone_number( $customer->get_phone() );

			$name = $customer->get_name();

			if ( null !== $name ) {
				$customer_information->set_initials( $name->get_initials() );
			}

			$order->set_customer_information( $customer_information );
		}

		// Payment brand.
		$payment_brand = PaymentBrands::transform( $payment->get_payment_method() );

		$order->set_payment_brand( $payment_brand );

		if ( null !== $payment_brand ) {
			// Payment brand force should only be set if payment brand is not empty.
			$order->set_payment_brand_force( PaymentBrandForce::FORCE_ONCE );
		}

		// Issuer.
		if ( PaymentBrands::IDEAL === $payment_brand ) {
			$order->set_payment_brand_meta_data(
				(object) [
					'issuerId' => $payment->get_meta( 'issuer' ),
				] 
			);
		}

		// Description.
		$description = $payment->get_description();

		if ( null !== $description ) {
			$order->set_description( DataHelper::sanitize_an( $description, 35 ) );
		}

		// Lines.
		$lines = $payment->get_lines();

		if ( null !== $lines ) {
			$order_items = $order->new_items();

			$i = 1;

			foreach ( $lines as $line ) {
				$name = \sprintf(
					/* translators: %s: item index */
					\__( 'Item %s', 'pronamic_ideal' ),
					$i++
				);

				$line_name = $line->get_name();

				if ( null !== $line_name && '' !== $line_name ) {
					$name = $line_name;
				}

				$unit_price = $line->get_unit_price();

				if ( null === $unit_price ) {
					$unit_price = new Money();
				}

				$item = $order_items->new_item(
					DataHelper::sanitize_an( $name, 50 ),
					(int) $line->get_quantity(),
					// The amount in cents, including VAT, of the item each, see below for more details.
					MoneyTransformer::transform( $unit_price ),
					ProductCategories::transform( $line->get_type() )
				);

				$item->set_id( $line->get_id() );

				// Description.
				$description = $line->get_description();

				if ( empty( $description ) && PaymentBrands::AFTERPAY === $payment_brand ) {
					/*
					 * The `OrderItem.description` field is documented as `0..1` (optional),
					 * but for AfterPay payments it is required.
					 *
					 * @link https://github.com/wp-pay-gateways/omnikassa-2/tree/feature/post-pay/documentation#error-5024
					 */
					$description = $name;
				}

				if ( null !== $description ) {
					$description = DataHelper::sanitize_an( $description, 100 );
				}

				$item->set_description( $description );

				if ( $unit_price instanceof TaxedMoney ) {
					$tax_amount = $unit_price->get_tax_amount();

					if ( null !== $tax_amount ) {
						// The VAT of the item each, see below for more details.
						$item->set_tax( MoneyTransformer::transform( $tax_amount ) );
					}
				}
			}
		}

		// Maybe update access token.
		$this->maybe_update_access_token();

		// Announce order.
		$response = $this->client->order_announce( $this->config, $order );

		$payment->set_transaction_id( $response->get_omnikassa_order_id() );
		$payment->set_action_url( $response->get_redirect_url() );
	}

	/**
	 * Update status of the specified payment.
	 *
	 * @param Payment $payment Payment.
	 * @return void
	 */
	public function update_status( Payment $payment ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		if ( ! ReturnParameters::contains( $_GET ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended, SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
		$parameters = ReturnParameters::from_array( $_GET );

		// Note.
		$note_values = [
			'order_id'  => $parameters->get_order_id(),
			'status'    => $parameters->get_status(),
			'signature' => (string) $parameters->get_signature(),
			'valid'     => $parameters->is_valid( $this->config->signing_key ) ? 'true' : 'false',
		];

		$note = '';

		$note .= '<p>';
		$note .= \__( 'OmniKassa 2.0 return URL requested:', 'pronamic_ideal' );
		$note .= '</p>';

		$note .= '<dl>';

		foreach ( $note_values as $key => $value ) {
			$note .= \sprintf( '<dt>%s</dt>', \esc_html( $key ) );
			$note .= \sprintf( '<dd>%s</dd>', \esc_html( $value ) );
		}

		$note .= '</dl>';

		$payment->add_note( $note );

		// Validate.
		if ( ! $parameters->is_valid( $this->config->signing_key ) ) {
			return;
		}

		// Status.
		$pronamic_status = Statuses::transform( $parameters->get_status() );

		if ( null !== $pronamic_status ) {
			$payment->set_status( $pronamic_status );
		}
	}

	/**
	 * Handle notification.
	 *
	 * @param Notification $notification Notification.
	 * @return void
	 * @throws \Pronamic\WordPress\Pay\Gateways\OmniKassa2\InvalidSignatureException Throws invalid signature exception when notification message does not match gateway configuration signature.
	 */
	public function handle_notification( Notification $notification ) {
		if ( ! $notification->is_valid( $this->config->signing_key ) ) {
			throw new \Pronamic\WordPress\Pay\Gateways\OmniKassa2\InvalidSignatureException(
				\sprintf(
					'Signature on notification message does not match gateway configuration signature (%s).',
					\substr( $this->config->signing_key, 0, 7 )
				)
			);
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
	 * @return void
	 * @throws \Pronamic\WordPress\Pay\Gateways\OmniKassa2\InvalidSignatureException Throws invalid signature exception when order results message does not match gateway configuration signature.
	 * @throws \Pronamic\WordPress\Pay\Gateways\OmniKassa2\UnknownOrderIdsException Throws unknow order IDs exception when no payment could be find for on ore more OmniKassa order IDs.
	 */
	private function handle_merchant_order_status_changed( Notification $notification ) {
		$unknown_order_ids = [];

		do {
			$order_results = $this->client->get_order_results( $notification->get_authentication() );

			if ( ! $order_results->is_valid( $this->config->signing_key ) ) {
				throw new \Pronamic\WordPress\Pay\Gateways\OmniKassa2\InvalidSignatureException(
					\sprintf(
						'Signature on order results message does not match gateway configuration signature (%s).',
						\substr( $this->config->signing_key, 0, 7 )
					)
				);
			}

			foreach ( $order_results as $order_result ) {
				$omnikassa_order_id = $order_result->get_omnikassa_order_id();

				$payment = \get_pronamic_payment_by_transaction_id( $omnikassa_order_id );

				if ( empty( $payment ) ) {
					/**
					 * If there is no payment found with the OmniKassa order ID
					 * we will continue to check the other order results. It is
					 * possible that the payment has been deleted and can
					 * therefore no longer be updated. We keep track of this
					 * exception and throw it at the end of this function.
					 */
					$unknown_order_ids[] = $omnikassa_order_id;

					continue;
				}

				/**
				 * Webhook log payment.
				 *
				 * The `pronamic_pay_webhook_log_payment` action is triggered so the
				 * `wp-pay/core` library can hook into this and register the webhook
				 * call.
				 *
				 * @param Payment $payment Payment to log.
				 */
				\do_action( 'pronamic_pay_webhook_log_payment', $payment );

				$pronamic_status = Statuses::transform( $order_result->get_order_status() );

				if ( null !== $pronamic_status ) {
					$payment->set_status( $pronamic_status );
				}

				// Note.
				$note = \sprintf(
					'<p>%s</p><pre>%s</pre>',
					\__( 'OmniKassa 2.0 webhook URL requested:', 'pronamic_ideal' ),
					(string) \wp_json_encode( $order_result, \JSON_PRETTY_PRINT )
				);

				$payment->add_note( $note );

				$payment->save();
			}
		} while ( $order_results->more_available() );

		if ( \count( $unknown_order_ids ) > 0 ) {
			throw new \Pronamic\WordPress\Pay\Gateways\OmniKassa2\UnknownOrderIdsException(
				\sprintf(
					'Could not find payments for the following OmniKassa order IDs: %s.',
					\implode( ', ', $unknown_order_ids )
				)
			);
		}
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

		if ( isset( $data->token ) ) {
			$this->config->access_token = $data->token;

			\update_post_meta( $this->config->post_id, '_pronamic_gateway_omnikassa_2_access_token', $data->token );
		}

		/*
		 * @codingStandardsIgnoreStart
		 *
		 * Ignore coding standards because of sniff WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar
		 */
		if ( isset( $data->validUntil ) ) {
			$this->config->access_token_valid_until = $data->validUntil;

			\update_post_meta(
				$this->config->post_id,
				'_pronamic_gateway_omnikassa_2_access_token_valid_until',
				$data->validUntil
			);
		}
		// @codingStandardsIgnoreEnd
	}
}
