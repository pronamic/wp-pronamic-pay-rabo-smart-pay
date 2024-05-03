<?php
/**
 * Transaction status
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
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
	 *
	 * @var string
	 */
	const SUCCESS = 'SUCCESS';

	/**
	 * Accepted.
	 *
	 * @var string
	 */
	const ACCEPTED = 'ACCEPTED';

	/**
	 * Cancelled.
	 *
	 * @var string
	 */
	const CANCELLED = 'CANCELLED';

	/**
	 * Expired.
	 *
	 * @var string
	 */
	const EXPIRED = 'EXPIRED';

	/**
	 * Failure.
	 *
	 * @var string
	 */
	const FAILURE = 'FAILURE';
}
