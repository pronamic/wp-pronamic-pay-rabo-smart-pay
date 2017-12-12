<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 payment methods
 * Description:
 * Copyright: Copyright (c) 2005 - 2017
 * Company: Pronamic
 *
 * @author Reüel van der Steege
 * @version 1.0.0
 * @since 1.0.0
 */
class PaymentMethods {
	/**
	 * Constant for the Bancontact method.
	 *
	 * @var string
	 */
	const BANCONTACT = PaymentBrands::BANCONTACT;

	/**
	 * Constant for the CreditCard method.
	 *
	 * Dutch: De waarde CARDS zorgt ervoor dat de consument
	 * kan kiezen uit de betaalmethoden: MASTERCARD, VISA,
	 * BANCONTACT, MAESTRO en V_PAY.
	 *
	 * @var string
	 */
	const CREDIT_CARD = PaymentBrands::CARDS;

	/**
	 * Constant for the iDEAL payment method.
	 *
	 * @var string
	 */
	const IDEAL = PaymentBrands::IDEAL;

	/**
	 * Constant for the PayPal payment method.
	 *
	 * @var string
	 */
	const PAYPAL = PaymentBrands::PAYPAL;

	/////////////////////////////////////////////////

	/**
	 * Transform WordPress payment method to OmniKassa 2.0 method.
	 *
	 * @since 1.0.0
	 *
	 * @param $payment_method
	 *
	 * @return string
	 */
	public static function transform( $payment_method ) {
		switch ( $payment_method ) {
			case \Pronamic_WP_Pay_PaymentMethods::BANCONTACT:
				return self::BANCONTACT;
			case \Pronamic_WP_Pay_PaymentMethods::CREDIT_CARD:
				return self::CREDIT_CARD;
			case \Pronamic_WP_Pay_PaymentMethods::IDEAL:
				return self::IDEAL;
			case \Pronamic_WP_Pay_PaymentMethods::PAYPAL:
				return self::PAYPAL;
			default:
				return null;
		}
	}
}
