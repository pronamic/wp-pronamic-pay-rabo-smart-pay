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

/**
 * Methods
 *
 * @author  Remco Tolsma
 * @version 2.0.2
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
}
