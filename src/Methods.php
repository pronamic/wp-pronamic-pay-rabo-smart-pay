<?php
/**
 * Methods
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Title: OmniKassa 2.0 payment methods
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class Methods {
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

	/**
	 * Transform WordPress payment method to OmniKassa 2.0 method.
	 *
	 * @since 1.0.0
	 *
	 * @param string $payment_method Payment method.
	 * @return string
	 */
	public static function transform( $payment_method ) {
		switch ( $payment_method ) {
			case PaymentMethods::BANCONTACT:
				return self::BANCONTACT;
			case PaymentMethods::CREDIT_CARD:
				return self::CREDIT_CARD;
			case PaymentMethods::IDEAL:
				return self::IDEAL;
			case PaymentMethods::PAYPAL:
				return self::PAYPAL;
			default:
				return null;
		}
	}
}
