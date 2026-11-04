<?php
/**
 * Order status
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Payments\PaymentStatus;

/**
 * Order status class
 */
final class OrderStatus {
	/**
	 * Successful payment of the order.
	 */
	const string COMPLETED = 'COMPLETED';

	/**
	 * The payment was cancelled.
	 */
	const string CANCELLED = 'CANCELLED';

	/**
	 * The consumer has not paid within the stipulated period.
	 */
	const string EXPIRED = 'EXPIRED';

	/**
	 * In progress.
	 *
	 * The payment has not yet been completed. This can occur as a result of a
	 * breakdown or delay in the hinterland of payment processing. This is a
	 * possible outcome of an iDEAL or credit card payment.
	 *
	 * @link https://developer.rabobank.nl/rabo-smart-pay-online-payment-api#customer-returns-to-the-web-shop
	 */
	const string IN_PROGRESS = 'IN_PROGRESS';

	/**
	 * Transform Rabo Smart Pay order status to Pronamic Pay status.
	 *
	 * @param string $status OmniKassa 2.0 status.
	 * @return string|null
	 */
	public static function transform( $status ) {
		return match ( $status ) {
			self::COMPLETED => PaymentStatus::SUCCESS,
			self::CANCELLED => PaymentStatus::CANCELLED,
			self::EXPIRED => PaymentStatus::EXPIRED,
			self::IN_PROGRESS => PaymentStatus::OPEN,
			default => null,
		};
	}
}
