<?php
/**
 * Order status
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
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
	 *
	 * @var string
	 */
	const COMPLETED = 'COMPLETED';

	/**
	 * The payment was cancelled.
	 *
	 * @var string
	 */
	const CANCELLED = 'CANCELLED';

	/**
	 * The consumer has not paid within the stipulated period.
	 *
	 * @var string
	 */
	const EXPIRED = 'EXPIRED';

	/**
	 * In progress.
	 * 
	 * The payment has not yet been completed. This can occur as a result of a
	 * breakdown or delay in the hinterland of payment processing. This is a
	 * possible outcome of an iDEAL or credit card payment.
	 *
	 * @link https://developer.rabobank.nl/rabo-smart-pay-online-payment-api#customer-returns-to-the-web-shop
	 * @var string
	 */
	const IN_PROGRESS = 'IN_PROGRESS';

	/**
	 * Transform Rabo Smart Pay order status to Pronamic Pay status.
	 *
	 * @param string $status OmniKassa 2.0 status.
	 * @return string|null
	 */
	public static function transform( $status ) {
		switch ( $status ) {
			case self::COMPLETED:
				return PaymentStatus::SUCCESS;
			case self::CANCELLED:
				return PaymentStatus::CANCELLED;
			case self::EXPIRED:
				return PaymentStatus::EXPIRED;
			case self::IN_PROGRESS:
				return PaymentStatus::OPEN;
			default:
				return null;
		}
	}
}
