<?php
/**
 * Gender.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Gender as Core_Gender;

/**
 * Gender.
 *
 * @since   2.1.0
 * @version 2.0.4
 */
final class Gender {
	/**
	 * Female.
	 */
	public const string FEMALE = 'F';

	/**
	 * Male.
	 */
	public const string MALE = 'M';

	/**
	 * Transform WordPress pay gender to OmniKassa gender.
	 *
	 * @param string|null $gender WordPress pay gender.
	 * @return string|null
	 */
	public static function transform( $gender ) {
		return match ( $gender ) {
			Core_Gender::FEMALE => self::FEMALE,
			Core_Gender::MALE => self::MALE,
			default => null,
		};
	}
}
