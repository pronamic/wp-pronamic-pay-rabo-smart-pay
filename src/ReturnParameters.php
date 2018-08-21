<?php
/**
 * Return parameters
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\GatewayPostType;
use Pronamic\WordPress\Pay\Plugin;
use Pronamic\WordPress\Pay\Core\Gateway;

/**
 * Return parameters
 *
 * @author  Remco Tolsma
 * @version 2.0.0
 * @since   1.0.0
 */
class ReturnParameters extends Message implements Signable {
	/**
	 * The "merchantOrderId" as used in the Order announce.
	 *
	 * @var string
	 */
	public $order_id;

	/**
	 * The status of the order, see below for more details.
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Get signature data.
	 *
	 * The signature is calculated in the same way as other signatures. In this case, the two fields (in order: order_id, status) are used as input.
	 *
	 * @return array
	 */
	public function get_signature_data() {
		return array(
			$this->order_id,
			$this->status,
		);
	}

	/**
	 * Get return parameters from the specifieid data array.
	 *
	 * @param array $data Data array.
	 * @return ReturnParameters
	 */
	public static function from_array( $data ) {
		$object = new self();

		if ( array_key_exists( 'order_id', $data ) ) {
			$object->order_id = $data['order_id'];
		}

		if ( array_key_exists( 'status', $data ) ) {
			$object->status = $data['status'];
		}

		if ( array_key_exists( 'signature', $data ) ) {
			$object->signature = $data['signature'];
		}

		return $object;
	}
}
