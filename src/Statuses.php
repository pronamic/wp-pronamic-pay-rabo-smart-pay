<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 statuses constants
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author Reüel van der Steege
 * @version 1.0.0
 * @since 1.0.0
 */
class Statuses {
	/**
	 * Cancelled
	 *
	 * @var string
	 */
	const CANCELLED = 'CANCELLED';

	/**
	 * Completed
	 *
	 * @var string
	 */
	const COMPLETED = 'COMPLETED';

	/**
	 * Expired
	 *
	 * @var string
	 */
	const EXPIRED = 'EXPIRED';
	/**
	 * In progress
	 *
	 * @var string
	 */
	const IN_PROGRESS = 'IN_PROGRESS';

	/////////////////////////////////////////////////

	/**
	 * Transform an OmniKassa 2.0 status to Pronamic Pay status.
	 *
	 * @param string $status
	 */
	public static function transform( $status ) {
		switch ( $status ) {
			case self::CANCELLED:
				return \Pronamic_WP_Pay_Statuses::CANCELLED;
			case self::COMPLETED:
				return \Pronamic_WP_Pay_Statuses::SUCCESS;
			case self::EXPIRED:
				return \Pronamic_WP_Pay_Statuses::EXPIRED;
			case self::IN_PROGRESS:
				return \Pronamic_WP_Pay_Statuses::OPEN;
			default:
				return null;
		}
	}
}
