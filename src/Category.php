<?php
/**
 * Category
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Payments\PaymentLineType;

/**
 * Category
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   1.0.0
 */
class Category {
	/**
	 * Physical.
	 *
	 * @var string
	 */
	const PHYSICAL = 'PHYSICAL';

	/**
	 * Digital.
	 *
	 * @var string
	 */
	const DIGITAL = 'DIGITAL';

	/**
	 * Transform an OmniKassa 2.0 status to Pronamic Pay status.
	 *
	 * @param string $status OmniKassa 2.0 status.
	 * @return string|null
	 */
	public static function transform( $type ) {
		switch ( $type ) {
			case PaymentLineType::DIGITAL:
				return self::DIGITAL;
			case PaymentLineType::DISCOUNT:
				return self::DIGITAL;
			case PaymentLineType::PHYSICAL:
				return self::PHYSICAL;
			case PaymentLineType::SHIPPING:
				return self::DIGITAL;
			default:
				return self::DIGITAL;
		}
	}
}
