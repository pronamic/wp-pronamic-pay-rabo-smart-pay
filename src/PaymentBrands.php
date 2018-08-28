<?php
/**
 * Payment brands
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Payment brands
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   1.0.0
 */
class PaymentBrands {
	/**
	 * Payment brand 'IDEAL'.
	 *
	 * @var string
	 */
	const IDEAL = 'IDEAL';

	/**
	 * Payment brand 'AFTERPAY'.
	 *
	 * @var string
	 */
	const AFTERPAY = 'AFTERPAY';

	/**
	 * Payment brand 'PAYPAL'.
	 *
	 * @var string
	 */
	const PAYPAL = 'PAYPAL';

	/**
	 * Payment brand 'MASTERCARD'.
	 *
	 * @var string
	 */
	const MASTERCARD = 'MASTERCARD';

	/**
	 * Payment brand 'VISA'.
	 *
	 * @var string
	 */
	const VISA = 'VISA';

	/**
	 * Payment brand 'BANCONTACT'.
	 *
	 * @var string
	 */
	const BANCONTACT = 'BANCONTACT';

	/**
	 * Payment brand 'MAESTRO'.
	 *
	 * @var string
	 */
	const MAESTRO = 'MAESTRO';

	/**
	 * Payment brand 'V_PAY'.
	 *
	 * @var string
	 */
	const V_PAY = 'V_PAY';

	/**
	 * Payment brand 'CARDS'.
	 *
	 * The CARDS value ensures that the consumer can choose between payment methods:
	 * MASTERCARD, VISA, BANCONTACT, MAESTRO and V_PAY.
	 *
	 * Dutch: De waarde CARDS zorgt ervoor dat de consument kan kiezen uit de betaalmethoden:
	 * MASTERCARD, VISA, BANCONTACT, MAESTRO en V_PAY.
	 *
	 * @var string
	 */
	const CARDS = 'CARDS';

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
				return self::CARDS;
			case PaymentMethods::IDEAL:
				return self::IDEAL;
			case PaymentMethods::PAYPAL:
				return self::PAYPAL;
			default:
				return null;
		}
	}
}
