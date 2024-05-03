<?php
/**
 * Payment brand force.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Payment brand force.
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   1.0.0
 */
final class PaymentBrandForce {
	/**
	 * In the case of FORCE_ONCE, the indicated paymentBrand is only enforced on the first
	 * transaction. If this fails, the consumer can still choose another payment method.
	 *
	 * @var string
	 */
	const FORCE_ONCE = 'FORCE_ONCE';

	/**
	 * When FORCE_ALWAYS is chosen, the consumer can not choose another payment method.
	 *
	 * @var string
	 */
	const FORCE_ALWAYS = 'FORCE_ALWAYS';
}
