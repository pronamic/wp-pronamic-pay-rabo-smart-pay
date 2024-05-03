<?php
/**
 * Gender.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Gender as Core_Gender;

/**
 * Gender.
 *
 * @author  Remco Tolsma
 * @since   2.1.0
 * @version 2.0.4
 */
final class Gender {
	/**
	 * Female.
	 *
	 * @var string
	 */
	const FEMALE = 'F';

	/**
	 * Male.
	 *
	 * @var string
	 */
	const MALE = 'M';

	/**
	 * Transform WordPress pay gender to OmniKassa gender.
	 *
	 * @param string|null $gender WordPress pay gender.
	 * @return string|null
	 */
	public static function transform( $gender ) {
		switch ( $gender ) {
			case Core_Gender::FEMALE:
				return self::FEMALE;
			case Core_Gender::MALE:
				return self::MALE;
			case Core_Gender::OTHER:
			default:
				return null;
		}
	}
}
