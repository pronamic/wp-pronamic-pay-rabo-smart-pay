<?php
/**
 * JSON Schema validation test.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

// These tests also run without WordPress.
// phpcs:disable WordPress.WP.AlternativeFunctions.json_encode_json_encode

/**
 * Exercise the validation API shared by JSON Schema 5 and 6.
 */
class JsonSchemaValidationTest extends TestCase {
	/**
	 * Test valid responses.
	 *
	 * @dataProvider valid_json_provider
	 * @param class-string $response_class Response class.
	 * @param string       $json           JSON response.
	 * @param string       $property       Required property.
	 */
	public function test_valid_json( $response_class, $json, $property ) {
		$this->assertInstanceOf( $response_class, $response_class::from_json( $json ) );
		$this->assertArrayHasKey( $property, \json_decode( $json, true ) );
	}

	/**
	 * Test invalid responses throw the exception caught by the webhook.
	 *
	 * @dataProvider invalid_json_provider
	 * @param class-string $response_class Response class.
	 * @param string       $json           JSON response.
	 */
	public function test_invalid_json( $response_class, $json ) {
		$this->expectException( \JsonSchema\Exception\ValidationException::class );

		$response_class::from_json( $json );
	}

	/**
	 * Valid JSON provider.
	 *
	 * @return array<string, array{class-string, string, string}>
	 */
	public static function valid_json_provider() {
		$order_results = \json_decode(
			\file_get_contents( __DIR__ . '/../json/merchant.order.status.changed-v2.json' ),
			true
		);

		return [
			'announcement'  => [
				OrderAnnounceResponse::class,
				\file_get_contents( __DIR__ . '/../json/order-announce-response.json' ),
				'redirectUrl',
			],
			'error'         => [
				Error::class,
				\file_get_contents( __DIR__ . '/../json/error-5001-full-authentication-required.json' ),
				'errorCode',
			],
			'money'         => [ Money::class, '{"currency":"EUR","amount":100}', 'currency' ],
			'money string'  => [ Money::class, '{"currency":"EUR","amount":"100"}', 'amount' ],
			'notification'  => [
				Notification::class,
				\file_get_contents( __DIR__ . '/../json/notification.json' ),
				'authentication',
			],
			'order result'  => [ OrderResult::class, \json_encode( $order_results['orderResults'][0] ), 'paidAmount' ],
			'order results' => [ OrderResults::class, \json_encode( $order_results ), 'orderResults' ],
		];
	}

	/**
	 * Invalid JSON provider.
	 *
	 * @return array<string, array{class-string, string}>
	 */
	public static function invalid_json_provider() {
		$cases = [];

		foreach ( self::valid_json_provider() as $name => [ $response_class, $json, $property ] ) {
			foreach ( [
				'array'        => '[]',
				'empty object' => '{}',
				'malformed'    => '{',
				'null'         => 'null',
			] as $invalid => $value ) {
				$cases[ $name . ' ' . $invalid ] = [ $response_class, $value ];
			}

			$data = \json_decode( $json );

			unset( $data->{$property} );

			$cases[ $name . ' missing property' ] = [ $response_class, \json_encode( $data ) ];

			$data = \json_decode( $json );

			$data->{$property} = false;

			$cases[ $name . ' wrong type' ] = [ $response_class, \json_encode( $data ) ];
		}

		$data = \json_decode( self::valid_json_provider()['order results'][1], true );

		unset( $data['orderResults'][0]['paidAmount']['currency'] );

		$cases['nested money reference'] = [ OrderResults::class, \json_encode( $data ) ];

		$data = \json_decode( self::valid_json_provider()['order result'][1], true );

		$data['orderStatusDateTime'] = 'not-a-date';

		$cases['date-time format'] = [ OrderResult::class, \json_encode( $data ) ];

		return $cases;
	}
}
