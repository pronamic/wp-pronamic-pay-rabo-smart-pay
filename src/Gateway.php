<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 gateway
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Gateway extends \Pronamic_WP_Pay_Gateway {
	/**
	 * The OmniKassa client object
	 *
	 * @var Pronamic_WP_Pay_Gateways_OmniKassa_Client
	 */
	private $client;

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an OmniKassa gateway
	 *
	 * @param Pronamic_WP_Pay_Gateways_OmniKassa_Config $config
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->set_method( \Pronamic_WP_Pay_Gateway::METHOD_HTML_FORM );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );

		// Client
		$this->client = new Client();

		$action_url = Client::ACTION_URL_PRUDCTION;
		if ( \Pronamic_IDeal_IDeal::MODE_TEST === $config->mode ) {
			$action_url = Client::ACTION_URL_TEST;
		}

		$this->client->set_action_url( $action_url );
		$this->client->set_merchant_id( $config->merchant_id );
		$this->client->set_key_version( $config->key_version );
		$this->client->set_secret_key( $config->secret_key );
	}

	/////////////////////////////////////////////////

	/**
	 * Get supported payment methods
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			\Pronamic_WP_Pay_PaymentMethods::IDEAL,
			\Pronamic_WP_Pay_PaymentMethods::CREDIT_CARD,
			\Pronamic_WP_Pay_PaymentMethods::DIRECT_DEBIT,
			\Pronamic_WP_Pay_PaymentMethods::BANCONTACT,
		);
	}

	/////////////////////////////////////////////////

	/**
	 * Start
	 *
	 * @see Pronamic_WP_Pay_Gateway::start()
	 * @param Pronamic_Pay_PaymentDataInterface $data
	 */
	public function start( \Pronamic_Pay_Payment $payment ) {
		$transaction_reference = $payment->get_meta( 'omnikassa_transaction_reference' );

		if ( empty( $transaction_reference ) ) {
			$transaction_reference = md5( uniqid( '', true ) );

			$payment->set_meta( 'omnikassa_transaction_reference', $transaction_reference );
		}

		$payment->set_transaction_id( $transaction_reference );
		$payment->set_action_url( $this->client->get_action_url() );

		$this->client->set_customer_language( Pronamic_WP_Pay_Gateways_OmniKassa_LocaleHelper::transform( $payment->get_language() ) );
		$this->client->set_currency_numeric_code( $payment->get_currency_numeric_code() );
		$this->client->set_order_id( $payment->format_string( $this->config->order_id ) );
		$this->client->set_normal_return_url( home_url( '/' ) );
		$this->client->set_automatic_response_url( home_url( '/' ) );
		$this->client->set_amount( $payment->get_amount() );
		$this->client->set_transaction_reference( $transaction_reference );

		switch ( $payment->get_method() ) {
			/*
			 * If this field is not supplied in the payment request, then
			 * by default the customer will be redirected to the Rabo
			 * OmniKassa payment page. On the payment page, the
			 * customer can choose from the payment methods
			 * offered by the Rabo OmniKassa. These are the payment
			 * methods: IDEAL, VISA, MASTERCARD,
			 * MAESTRO, V PAY and BCMC.
			 *
			 * Exception: the register services INCASSO (direct debit),
			 * ACCEPTGIRO (giro collection form) and REMBOURS
			 * (cash on delivery) are not displayed on the Rabo
			 * OmniKassa payment page by default.
			 * If you wish to offer these register services to the
			 * customer on the payment page, then you need to
			 * always populate the paymentMeanBrandList field with
			 * all the payment methods you wish to offer (provided
			 * these have been requested and activated): IDEAL,
			 * VISA, MASTERCARD, MAESTRO, VPAY, BCMC,
			 * INCASSO, ACCEPTGIRO, REMBOURS.
			 *
			 * If you let the customer choose the payment method
			 * while still in your webshop, then you must populate
			 * this field of the payment request with only the selected
			 * payment method. Populating this field with only one
			 * payment method will instruct the Rabo OmniKassa to
			 * redirect the customer directly to the payment page for
			 * this payment method.
			 */
			case \Pronamic_WP_Pay_PaymentMethods::BANCONTACT :
			case \Pronamic_WP_Pay_PaymentMethods::MISTER_CASH :
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::BCMC );

				break;
			case \Pronamic_WP_Pay_PaymentMethods::CREDIT_CARD :
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::MAESTRO );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::MASTERCARD );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::VISA );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::VPAY );

				break;
			case \Pronamic_WP_Pay_PaymentMethods::DIRECT_DEBIT :
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::INCASSO );

				break;
			case \Pronamic_WP_Pay_PaymentMethods::MAESTRO :
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::MAESTRO );

				break;
			case \Pronamic_WP_Pay_PaymentMethods::IDEAL :
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::IDEAL );

				break;
			default :
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::IDEAL );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::VISA );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::MASTERCARD );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::MAESTRO );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::VPAY );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::BCMC );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::INCASSO );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::ACCEPTGIRO );
				$this->client->add_payment_mean_brand( \Pronamic_WP_Pay_Gateways_OmniKassa_PaymentMethods::REMBOURS );

				break;
		}
	}

	/////////////////////////////////////////////////

	/**
	 * Get the output HTML
	 *
	 * @since 1.1.2
	 * @see Pronamic_WP_Pay_Gateway::get_output_html()
	 */
	public function get_output_fields() {
		return $this->client->get_fields();
	}

	/////////////////////////////////////////////////

	/**
	 * Update status of the specified payment
	 *
	 * @param Pronamic_Pay_Payment $payment
	 */
	public function update_status( \Pronamic_Pay_Payment $payment ) {
		
	}
}
