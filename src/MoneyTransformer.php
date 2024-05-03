<?php
/**
 * Money transformer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Money\Money as PronamicMoney;

/**
 * Money transformer
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   2.0.2
 */
final class MoneyTransformer {
	/**
	 * Transform Pronamic money to OmniKassa 2.0 money.
	 *
	 * @param PronamicMoney $pronamic_money Pronamic money to convert.
	 * @return Money
	 * @throws \InvalidArgumentException Throws exception on invalid alphabetic currency code in given Pronamic money object.
	 */
	public static function transform( PronamicMoney $pronamic_money ) {
		$money = new Money(
			$pronamic_money->get_currency()->get_alphabetic_code(),
			$pronamic_money->get_minor_units()->to_int()
		);

		return $money;
	}
}
