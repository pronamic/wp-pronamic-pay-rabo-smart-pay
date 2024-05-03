<?php
/**
 * Return parameters
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Return parameters
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   2.0.2
 */
final class ReturnParameters extends ResponseMessage {
	/**
	 * The "merchantOrderId" as used in the Order announce.
	 *
	 * @var string
	 */
	private $order_id;

	/**
	 * The status of the order, see below for more details.
	 *
	 * @var string
	 */
	private $status;

	/**
	 * Construct return parameters object.
	 *
	 * @param string $order_id  Order ID.
	 * @param string $status    Status.
	 * @param string $signature Signature.
	 */
	public function __construct( $order_id, $status, $signature ) {
		parent::__construct( $signature );

		$this->order_id = $order_id;
		$this->status   = $status;
	}

	/**
	 * Get order ID.
	 *
	 * @return string
	 */
	public function get_order_id() {
		return $this->order_id;
	}

	/**
	 * Get status.
	 *
	 * @return string
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Get signature data.
	 *
	 * The signature is calculated in the same way as other signatures. In this case, the two fields (in order: order_id, status) are used as input.
	 *
	 * @return array<string>
	 */
	public function get_signature_fields() {
		return [
			$this->get_order_id(),
			$this->get_status(),
		];
	}
}
