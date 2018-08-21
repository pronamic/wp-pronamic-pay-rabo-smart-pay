<?php
/**
 * Notification handler test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Notification handler test
 *
 * @author  Remco Tolsma
 * @version 2.0.3
 * @since   1.0.0
 */
class NotificationHandlerTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Test notification handler.
	 */
	public function test_notification_handler() {
		$result = NotificationHandler::handle_notification( file_get_contents( __DIR__ . '/../json/notification.json' ) );

		var_dump( $result );
	}
}
