<?php
/**
 * Transaction status
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Transaction status class
 */
final class TransactionStatus {
	/**
	 * Success.
	 */
	const string SUCCESS = 'SUCCESS';

	/**
	 * Accepted.
	 */
	const string ACCEPTED = 'ACCEPTED';

	/**
	 * Cancelled.
	 */
	const string CANCELLED = 'CANCELLED';

	/**
	 * Expired.
	 */
	const string EXPIRED = 'EXPIRED';

	/**
	 * Failure.
	 */
	const string FAILURE = 'FAILURE';
}
