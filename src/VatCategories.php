<?php
/**
 * VAT categories.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * VAT categories.
 *
 * @version 2.1.8
 * @since   2.0.4
 */
final class VatCategories {
	/**
	 * Category 'Hoog'.
	 */
	const string HIGH = '1';

	/**
	 * Category 'Laag'.
	 */
	const string LOW = '2';

	/**
	 * Category 'Nul (0%)'.
	 */
	const string ZERO = '3';

	/**
	 * Category 'Geen (vrijgesteld van btw)'.
	 */
	const string EXEMPTED = '4';
}
