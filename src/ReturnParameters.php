<?php
/**
 * Return parameters
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use InvalidArgumentException;

/**
 * Return parameters
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class ReturnParameters extends ResponseMessage {
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
	 * @return array
	 */
	public function get_signature_fields() {
		return array(
			$this->get_order_id(),
			$this->get_status(),
		);
	}

	/**
	 * Check if data array contains return parameters.
	 *
	 * @param array $data Data array.
	 * @return bool True if array contains return parameters, false otherwise.
	 */
	public static function contains( array $data ) {
		return (
			array_key_exists( 'order_id', $data )
				&&
			array_key_exists( 'status', $data )
				&&
			array_key_exists( 'signature', $data )
		);
	}

	/**
	 * Get return parameters from the specifieid data array.
	 *
	 * @param array $data Data array.
	 * @return ReturnParameters
	 * @throws InvalidArgumentException Throws invalid argument exception when array does not contains the required keys.
	 */
	public static function from_array( array $data ) {
		if ( ! array_key_exists( 'order_id', $data ) ) {
			throw new InvalidArgumentException( 'Data array must contain `order_id` field.' );
		}

		if ( ! array_key_exists( 'status', $data ) ) {
			throw new InvalidArgumentException( 'Data array must contain `status` field.' );
		}

		if ( ! array_key_exists( 'signature', $data ) ) {
			throw new InvalidArgumentException( 'Data array must contain `signature` field.' );
		}

		return new self(
			$data['order_id'],
			$data['status'],
			$data['signature']
		);
	}
}
