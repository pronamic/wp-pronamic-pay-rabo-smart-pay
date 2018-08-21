<?php
/**
 * Return handler test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Return handler test
 *
 * @author  Remco Tolsma
 * @version 2.0.3
 * @since   1.0.0
 */
class ReturnHandlerTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test return handler.
	 */
	public function test_return_handler() {
		$result = ReturnHandler::handle_return( array(
			'order_id'  => '77',
			'status'    => 'COMPLETED',
			'signature' => '045fdb9da232f1b4677921f9b14dcf739be130cf01a6620b1466b8c94a2df3ebaef92c86ef996d1a0685f2a2ec7d8c1fcb82976ec02f5af7b5bbf81bc1efd80c',
		) );

		
	}
}
