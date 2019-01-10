<?php
/**
 * Payment brands.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Payment brands.
 *
 * @author  Remco Tolsma
 * @version 2.1.0
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
	 * Map payment methods to payment brands.
	 *
	 * @var array
	 */
	private static $map = array(
		PaymentMethods::AFTERPAY    => self::AFTERPAY,
		PaymentMethods::BANCONTACT  => self::BANCONTACT,
		PaymentMethods::CREDIT_CARD => self::CARDS,
		PaymentMethods::IDEAL       => self::IDEAL,
		PaymentMethods::MAESTRO     => self::MAESTRO,
		PaymentMethods::PAYPAL      => self::PAYPAL,
	);

	/**
	 * Transform WordPress payment method to OmniKassa method.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $payment_method Payment method.
	 * @param mixed       $default        Default payment method.
	 *
	 * @return string|null
	 */
	public static function transform( $payment_method, $default = null ) {
		if ( ! is_scalar( $payment_method ) ) {
			return null;
		}

		if ( isset( self::$map[ $payment_method ] ) ) {
			return self::$map[ $payment_method ];
		}

		return $default;
	}
}
