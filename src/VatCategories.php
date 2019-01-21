<?php
/**
 * VAT categories.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * VAT categories.
 *
 * @author  Reüel van der Steege
 * @version 2.1.0
 * @since   2.0.4
 */
class VatCategories {
	/**
	 * Category 'Hoog'.
	 *
	 * @var string
	 */
	const HIGH = 1;

	/**
	 * Category 'Laag'.
	 *
	 * @var string
	 */
	const LOW = 2;

	/**
	 * Category 'Nul (0%)'.
	 *
	 * @var string
	 */
	const ZERO = 3;

	/**
	 * Category 'Geen (vrijgesteld van btw)'.
	 *
	 * @var int
	 */
	const EXEMPTED = 4;
}
