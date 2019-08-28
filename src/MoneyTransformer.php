<?php
/**
 * Money transformer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Money\Money as PronamicMoney;

/**
 * Money transformer
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class MoneyTransformer {
	/**
	 * Transform Pronamic money to OmniKassa 2.0 money.
	 *
	 * @param PronamicMoney $pronamic_money Pronamic money to convert.
	 * @return Money
	 */
	public static function transform( PronamicMoney $pronamic_money ) {
		$money = new Money(
			strval( $pronamic_money->get_currency()->get_alphabetic_code() ),
			intval( $pronamic_money->get_cents() )
		);

		return $money;
	}
}
