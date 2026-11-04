<?php
/**
 * Payment brands.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\PaymentMethods;

/**
 * Payment brands.
 *
 * @version 2.1.8
 * @since   1.0.0
 */
final class PaymentBrands {
	/**
	 * Payment brand 'IDEAL'.
	 */
	const string IDEAL = 'IDEAL';

	/**
	 * Payment brand 'PAYPAL'.
	 */
	const string PAYPAL = 'PAYPAL';

	/**
	 * Payment brand 'MASTERCARD'.
	 */
	const string MASTERCARD = 'MASTERCARD';

	/**
	 * Payment brand 'VISA'.
	 */
	const string VISA = 'VISA';

	/**
	 * Payment brand 'BANCONTACT'.
	 */
	const string BANCONTACT = 'BANCONTACT';

	/**
	 * Payment brand 'MAESTRO'.
	 */
	const string MAESTRO = 'MAESTRO';

	/**
	 * Payment brand 'SOFORT'.
	 */
	const string SOFORT = 'SOFORT';

	/**
	 * Payment brand 'V_PAY'.
	 */
	const string V_PAY = 'V_PAY';

	/**
	 * Payment brand 'CARDS'.
	 *
	 * The CARDS value ensures that the consumer can choose between payment methods:
	 * MASTERCARD, VISA, BANCONTACT, MAESTRO and V_PAY.
	 *
	 * Dutch: De waarde CARDS zorgt ervoor dat de consument kan kiezen uit de betaalmethoden:
	 * MASTERCARD, VISA, BANCONTACT, MAESTRO en V_PAY.
	 */
	const string CARDS = 'CARDS';

	/**
	 * Map payment methods to payment brands.
	 *
	 * @var array<string, string>
	 */
	private static $map = [
		PaymentMethods::BANCONTACT  => self::BANCONTACT,
		PaymentMethods::CREDIT_CARD => self::CARDS,
		PaymentMethods::IDEAL       => self::IDEAL,
		PaymentMethods::MAESTRO     => self::MAESTRO,
		PaymentMethods::MASTERCARD  => self::MASTERCARD,
		PaymentMethods::PAYPAL      => self::PAYPAL,
		PaymentMethods::SOFORT      => self::SOFORT,
		PaymentMethods::VISA        => self::VISA,
		PaymentMethods::V_PAY       => self::V_PAY,
	];

	/**
	 * Transform WordPress payment method to OmniKassa method.
	 *
	 * @since 1.0.0
	 * @param string|null $payment_method Payment method.
	 * @param string      $fallback       Fallback payment method.
	 * @return string|null
	 */
	public static function transform( $payment_method, $fallback = null ) {
		if ( ! \is_scalar( $payment_method ) ) {
			return null;
		}

		return self::$map[ $payment_method ] ?? $fallback;
	}

	/**
	 * Convert method from OmniKassa 2 indicator to a Pronamic indicator.
	 *
	 * @param string $payment_brand Method.
	 */
	public static function from_omnikassa_to_pronamic( string $payment_brand ): ?string {
		$key = \array_search( $payment_brand, self::$map, true );

		if ( false === $key ) {
			return null;
		}

		return $key;
	}
}
