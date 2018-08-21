<?php
/**
 * Order results
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 order
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class OrderResults extends Signable {
	/**
	 * More order results available flag.
	 *
	 * @var bool
	 */
	public $more_order_results_available;

	/**
	 * Order results.
	 *
	 * @var array
	 */
	public $order_results;

	/**
	 * Get signature data.
	 *
	 * @return array
	 */
	public function get_signature_data() {
		$more_results_available = $this->more_order_results_available ? 'true' : 'false';

		$data = array(
			$more_results_available,
		);

		if ( ! $this->order_results ) {
			return $data;
		}

		foreach ( $this->order_results as $order ) {
			$order_data = array(
				$order->merchantOrderId,
				$order->omnikassaOrderId,
				$order->poiId,
				$order->orderStatus,
				$order->orderStatusDateTime,
				$order->errorCode,
				$order->paidAmount->currency,
				$order->paidAmount->amount,
				$order->totalAmount->currency,
				$order->totalAmount->amount,
			);

			$data = array_merge( $data, $order_data );
		}

		return $data;
	}
}
