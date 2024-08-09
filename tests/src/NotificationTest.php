<?php
/**
 * Notification test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Notification test
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   2.0.2
 */
class NotificationTest extends TestCase {
	/**
	 * Test notification.
	 */
	public function test_notification() {
		$signing_key = 'QAhFrajUoLsKowfRo15vFXIpdbCgmI2S82idk6xPiCk=';

		$json = \file_get_contents( __DIR__ . '/../json/notification.json', true );

		$notification = Notification::from_json( $json );

		$this->assertEquals(
			'f709c6a8be921774ebe52af3ea194d3029e56b1256ec750a056cce6344b1795329f3869b3f4afa7c8e45a9814bbcf08c43f27a0a902b52c06abbb90e7ab1407e',
			$notification->get_signature()
		);
		$this->assertEquals( '2018-08-21T14:06:29.456+02:00', $notification->get_expiry() );
		$this->assertEquals( 'merchant.order.status.changed', $notification->get_event_name() );
		$this->assertEquals( 5000, $notification->get_poi_id() );

		$this->assertTrue( $notification->is_valid( $signing_key ) );
	}
}
