<?php
/**
 * Statuses
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\Statuses as Core_Statuses;

/**
 * Statuses
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   1.0.0
 */
class Statuses {
	/**
	 * Cancelled.
	 *
	 * @var string
	 */
	const CANCELLED = 'CANCELLED';

	/**
	 * Completed.
	 *
	 * @var string
	 */
	const COMPLETED = 'COMPLETED';

	/**
	 * Expired.
	 *
	 * @var string
	 */
	const EXPIRED = 'EXPIRED';

	/**
	 * In progress.
	 *
	 * @var string
	 */
	const IN_PROGRESS = 'IN_PROGRESS';

	/**
	 * Transform an OmniKassa 2.0 status to Pronamic Pay status.
	 *
	 * @param string $status OmniKassa 2.0 status.
	 * @return string|null
	 */
	public static function transform( $status ) {
		switch ( $status ) {
			case self::CANCELLED:
				return Core_Statuses::CANCELLED;
			case self::COMPLETED:
				return Core_Statuses::SUCCESS;
			case self::EXPIRED:
				return Core_Statuses::EXPIRED;
			case self::IN_PROGRESS:
				return Core_Statuses::OPEN;
			default:
				return null;
		}
	}
}
