<?php
/**
 * Money transformer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Money\Money as PronamicMoney;

/**
 * Money transformer
 *
 * @author  Remco Tolsma
 * @version 2.0.2
 * @since   2.0.2
 */
class MoneyTransformer {
	/**
	 * Transform Pronamic address to OmniKassa 2.0 address.
	 *
	 * @param PronamicMoney|null $pronamic_money Pronamic money to convert.
	 * @return Money|null
	 */
	public static function transform( PronamicMoney $pronamic_money = null ) {
		if ( null === $pronamic_money ) {
			return null;
		}

		$money = new Money(
			$pronamic_money->get_currency()->get_alphabetic_code(),
			intval( $pronamic_money->get_cents() )
		);

		return $money;
	}
}
