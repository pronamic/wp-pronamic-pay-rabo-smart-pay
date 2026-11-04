<?php
/**
 * Product categories.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Payments\PaymentLineType;

/**
 * Product categories.
 *
 * @version 2.1.8
 * @since   2.0.4
 */
final class ProductCategories {
	/**
	 * Product category 'PHYSICAL'.
	 */
	public const string PHYSICAL = 'PHYSICAL';

	/**
	 * Product category 'DIGITAL'.
	 */
	public const string DIGITAL = 'DIGITAL';

	/**
	 * Transform Pronamic payment line type to OmniKassa 2.0 product category.
	 *
	 * @param string $type Pronamic payment line type.
	 * @return string
	 */
	public static function transform( $type ) {
		return match ( $type ) {
			PaymentLineType::PHYSICAL => self::PHYSICAL,
			default => self::DIGITAL,
		};
	}
}
