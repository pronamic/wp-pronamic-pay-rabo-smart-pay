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
	public const string SUCCESS = 'SUCCESS';

	/**
	 * Accepted.
	 */
	public const string ACCEPTED = 'ACCEPTED';

	/**
	 * Cancelled.
	 */
	public const string CANCELLED = 'CANCELLED';

	/**
	 * Expired.
	 */
	public const string EXPIRED = 'EXPIRED';

	/**
	 * Failure.
	 */
	public const string FAILURE = 'FAILURE';
}
