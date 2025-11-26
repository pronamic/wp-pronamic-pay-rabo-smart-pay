<?php
/**
 * Gateway
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Money\Money;
use Pronamic\WordPress\Money\TaxedMoney;
use Pronamic\WordPress\Number\Number;
use Pronamic\WordPress\Pay\Core\Gateway as Core_Gateway;
use Pronamic\WordPress\Pay\Core\PaymentMethod;
use Pronamic\WordPress\Pay\Core\PaymentMethods;
use Pronamic\WordPress\Pay\Core\PaymentMethodsCollection;
use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Refunds\Refund;

/**
 * Gateway
 *
 * @author  Remco Tolsma
 * @version 2.3.4
 * @since   1.0.0
 */
final class Gateway extends Core_Gateway {
	/**
	 * Client.
	 *
	 * @var Client
	 */
	private $client;

	/**
	 * Config.
	 *
	 * @var Config
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
			'refunds',
			'webhook_log',
		];

		// Client.
		$this->client = new Client( $config );

		// Payment method iDEAL.
		$ideal_payment_method = new PaymentMethod( PaymentMethods::IDEAL );

		// Payment methods.
		$this->register_payment_method( new PaymentMethod( PaymentMethods::BANCONTACT ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::CREDIT_CARD ) );
		$this->register_payment_method( $ideal_payment_method );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::MAESTRO ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::MASTERCARD ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::PAYPAL ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::SOFORT ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::V_PAY ) );
		$this->register_payment_method( new PaymentMethod( PaymentMethods::VISA ) );
	}

	/**
	 * Get payment methods.
	 *
	 * @param array<mixed, mixed> $args Query arguments.
	 */
	public function get_payment_methods( array $args = [] ): PaymentMethodsCollection {
		try {
			$this->maybe_enrich_payment_methods();
		} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// No problem.
		}

		return parent::get_payment_methods( $args );
	}

	/**
	 * Get credit card payment methods.
	 *
	 * @return array<string>
	 */
	private function get_credit_card_payment_methods() {
		return [
			PaymentMethods::MAESTRO,
			PaymentMethods::MASTERCARD,
			PaymentMethods::V_PAY,
			PaymentMethods::VISA,
		];
	}

	/**
	 * Maybe enrich payment methods.
	 *
	 * @return void
	 * @throws \Exception Throws an exception if OmniKassa payment brands cannot be requested.
	 */
	private function maybe_enrich_payment_methods() {
		$cache_key = 'pronamic_pay_omnikassa_2_payment_brands_' . \md5( (string) \wp_json_encode( $this->config ) );

		$omnikassa_payment_brands = \get_transient( $cache_key );

		if ( false === $omnikassa_payment_brands ) {
			$this->maybe_update_access_token();

			$omnikassa_payment_brands = $this->client->get_payment_brands( $this->config->access_token );

			\set_transient( $cache_key, $omnikassa_payment_brands, \DAY_IN_SECONDS );
		}

		if ( ! \is_array( $omnikassa_payment_brands ) ) {
			throw new \Exception( 'OmniKassa payment brands invalid.' );
		}

		foreach ( $this->payment_methods as $payment_method ) {
			$payment_method->set_status( 'inactive' );

			$omnikassa_payment_brand = (string) PaymentBrands::transform( $payment_method->get_id() );

			if ( \array_key_exists( $omnikassa_payment_brand, $omnikassa_payment_brands ) ) {
				$status = $omnikassa_payment_brands[ $omnikassa_payment_brand ];

				if ( 'Active' !== $status ) {
					continue;
				}

				$payment_method->set_status( 'active' );
			}
		}

		/**
		 * Credit card.
		 */
		$credit_card_payment_methods = parent::get_payment_methods(
			[
				'id'     => $this->get_credit_card_payment_methods(),
				'status' => [ '', 'active' ],
			]
		);

		if ( \count( $credit_card_payment_methods ) > 0 ) {
			$payment_method = $this->get_payment_method( PaymentMethods::CREDIT_CARD );

			if ( null !== $payment_method ) {
				$payment_method->set_status( 'active' );
			}
		}
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

		if ( '' === $merchant_order_id ) {
			$merchant_order_id = (string) $payment->get_id();
		}

		$payment->set_meta( 'omnikassa_2_merchant_order_id', $merchant_order_id );

		// New order.
		$merchant_return_url = \rest_url( Integration::REST_ROUTE_NAMESPACE . '/return/' . $payment->get_id() );

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

				$quantity = $line->get_quantity();

				if ( null === $quantity ) {
					throw new \InvalidArgumentException( 'Payment line quantity is required.' );
				}

				$description = $line->get_description();

				// Handle decimal quantities.
				if ( ! $quantity->is_whole_number() ) {
					$description = \sprintf(
						'%s × %s',
						$quantity->format_i18n_non_trailing_zeros(),
						(string) $description
					);

					$quantity = new Number( 1 );
				}

				$item = $order_items->new_item(
					DataHelper::sanitize_an( $name, 50 ),
					$quantity->to_int(),
					// The amount in cents, including VAT, of the item each, see below for more details.
					MoneyTransformer::transform( $unit_price ),
					ProductCategories::transform( $line->get_type() )
				);

				$item->set_id( $line->get_id() );

				// Description.
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

		// Skip hosted result page.
		$order->set_skip_hosted_result_page( $this->config->skip_hosted_result_page );

		// Maybe update access token.
		$this->maybe_update_access_token();

		// Announce order.
		$response = $this->client->order_announce( $order );

		$payment->set_slug( $this->get_payment_slug_for_omnikassa_order_id( $response->get_omnikassa_order_id() ) );

		$payment->set_meta( 'omnikassa_order_id', $response->get_omnikassa_order_id() );

		$payment->set_action_url( $response->get_redirect_url() );
	}

	/**
	 * Create refund.
	 *
	 * @param Refund $refund Refund.
	 * @return void
	 * @throws \Exception Throws exception on unknown resource type.
	 */
	public function create_refund( Refund $refund ) {
		$payment = $refund->get_payment();

		$transaction_id = (string) $payment->get_transaction_id();

		$amount = MoneyTransformer::transform( $refund->get_amount() );

		$refund_request = new RefundRequest( $transaction_id, $amount );

		$description = $refund->get_description();

		if ( '' !== $description ) {
			$refund_request->description = $description;
		}

		$refund_response = $this->client->refund( $refund_request );

		$refund->psp_id = $refund_response->id;

		$refund->meta['rabo_smart_pay_refund_id']             = $refund_response->id;
		$refund->meta['rabo_smart_pay_refund_transaction_id'] = $refund_response->transaction_id;
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
					\esc_html( \substr( $this->config->signing_key, 0, 7 ) )
				)
			);
		}

		switch ( $notification->get_event_name() ) {
			case 'merchant.order.status.changed':
				$this->handle_merchant_order_status_changed( $notification );
		}
	}

	/**
	 * Get slug.
	 *
	 * @param string $omnikassa_order_id OmniKassa order ID.
	 * @return string
	 */
	private function get_payment_slug_for_omnikassa_order_id( $omnikassa_order_id ) {
		return 'rabo-smart-pay-order-' . $omnikassa_order_id;
	}

	/**
	 * Get Pronamic payment by OmniKassa order ID.
	 *
	 * @param string $omnikassa_order_id OmniKassa order ID.
	 * @return Payment|null
	 */
	private function get_payment_by_omnikassa_order_id( $omnikassa_order_id ) {
		/**
		 * Slug.
		 *
		 * Since version 4.5 of this library, we store the OmniKassa order ID
		 * in the slug of the payment so that the payment can be requested
		 * efficiently.
		 *
		 * @link https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/issues/21
		 * @link https://github.com/pronamic/wp-pay-core/issues/146
		 */
		$slug = $this->get_payment_slug_for_omnikassa_order_id( $omnikassa_order_id );

		$payment = \get_pronamic_payment_by_meta(
			'',
			'',
			[
				/**
				 * During development we also used the `name` argument here,
				 * this caused the `WP_Query->is_single` flag to be set to `true`.
				 * For single post queries WordPress will check if the post status
				 * of the post is public. If the post status is not public,
				 * WordPress will not return the post for users with
				 * insufficient permissions. Therefore, we use the
				 * `post_name__in` parameter here to bypass this behavior.
				 *
				 * @link https://github.com/WordPress/wordpress-develop/blob/6.3/src/wp-includes/class-wp-query.php#L3394-L3412
				 * @link https://developer.wordpress.org/reference/classes/wp_query/#post-page-parameters
				 */
				'post_name__in' => [ $slug ],
			]
		);

		if ( null !== $payment ) {
			return $payment;
		}

		/**
		 * Order ID - transaction ID.
		 *
		 * In older versions of this library we use the OmniKassa order ID as
		 * the transaction ID. This piece of code is still in this library for
		 * backward compatibility and may be removed in the future.
		 *
		 * @link https://github.com/pronamic/wp-pronamic-pay-omnikassa-2/issues/21
		 */
		$payment = \get_pronamic_payment_by_transaction_id( $omnikassa_order_id );

		return $payment;
	}

	/**
	 * Handle `merchant.order.status.changed` event.
	 *
	 * @param Notification $notification Notification.
	 * @return void
	 * @throws \Pronamic\WordPress\Pay\Gateways\OmniKassa2\UnknownOrderIdsException Throws unknown order IDs exception when no payment could be found for on ore more OmniKassa order IDs.
	 */
	private function handle_merchant_order_status_changed( Notification $notification ) {
		$unknown_order_ids = [];

		do {
			$order_results = $this->client->get_order_results( $notification->get_authentication() );

			$order_results->verify_signature( $this->config->signing_key );

			foreach ( $order_results as $order_result ) {
				$omnikassa_order_id = $order_result->get_omnikassa_order_id();

				$payment = $this->get_payment_by_omnikassa_order_id( $omnikassa_order_id );

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

				$pronamic_status = OrderStatus::transform( $order_result->get_order_status() );

				if ( null !== $pronamic_status ) {
					$payment->set_status( $pronamic_status );
				}

				$this->update_payment_transaction_id_from_order_result( $payment, $order_result );

				// Note.
				$note = \sprintf(
					'<p>%s</p><pre>%s</pre>',
					\__( 'Rabo Smart Pay webhook URL requested:', 'pronamic_ideal' ),
					(string) \wp_json_encode( $order_result, \JSON_PRETTY_PRINT )
				);

				$payment->add_note( $note );

				$payment->save();
			}
		} while ( $order_results->more_available() );

		if ( \count( $unknown_order_ids ) > 0 ) {
			throw new \Pronamic\WordPress\Pay\Gateways\OmniKassa2\UnknownOrderIdsException(
				\sprintf(
					'Could not find payments for the following Rabo Smart Pay order IDs: %s.',
					\esc_html( \implode( ', ', $unknown_order_ids ) )
				)
			);
		}
	}

	/**
	 * Update payment transaction ID from order result.
	 *
	 * @param Payment     $payment      Payment.
	 * @param OrderResult $order_result Order result.
	 * @return void
	 */
	private function update_payment_transaction_id_from_order_result( $payment, $order_result ) {
		$transaction_statuses = [
			TransactionStatus::SUCCESS,
			TransactionStatus::ACCEPTED,
			TransactionStatus::CANCELLED,
			TransactionStatus::EXPIRED,
			TransactionStatus::FAILURE,
		];

		switch ( $order_result->get_order_status() ) {
			case OrderStatus::COMPLETED:
				$transaction_statuses = [
					TransactionStatus::SUCCESS,
					TransactionStatus::ACCEPTED,
				];

				break;
			case OrderStatus::CANCELLED:
				$transaction_statuses = [
					TransactionStatus::CANCELLED,
				];

				break;
			case OrderStatus::EXPIRED:
				$transaction_statuses = [
					TransactionStatus::EXPIRED,
				];

				break;
		}

		$transactions = \array_filter(
			$order_result->get_transactions(),
			static function ( $transaction ) use ( $transaction_statuses ) {
				return \in_array( $transaction->get_status(), $transaction_statuses, true );
			}
		);

		$transaction = \array_shift( $transactions );

		if ( null === $transaction ) {
			return;
		}

		$payment->set_transaction_id( $transaction->get_id() );
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

		$object_access = new ObjectAccess( $data );

		if ( $object_access->has_property( 'token' ) ) {
			$this->config->access_token = $object_access->get_string( 'token' );

			\update_post_meta(
				$this->config->post_id,
				'_pronamic_gateway_omnikassa_2_access_token',
				$this->config->access_token
			);
		}

		if ( $object_access->has_property( 'validUntil' ) ) {
			$this->config->access_token_valid_until = $object_access->get_string( 'validUntil' );

			\update_post_meta(
				$this->config->post_id,
				'_pronamic_gateway_omnikassa_2_access_token_valid_until',
				$this->config->access_token_valid_until
			);
		}
	}
}
