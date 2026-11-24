<?php
/**
 * Return controller test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Payments\Payment;
use Pronamic\WordPress\Pay\Payments\PaymentStatus;
use WP_REST_Request;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Return controller test class
 */
class ReturnControllerTest extends TestCase {
	/**
	 * Signing key.
	 *
	 * @var string
	 */
	private $signing_key = 'QAhFrajUoLsKowfRo15vFXIpdbCgmI2S82idk6xPiCk=';

	/**
	 * Config ID.
	 *
	 * @var int
	 */
	private $config_id;

	/**
	 * Setup.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$controller = new ReturnController(
			new Integration(
				[
					'id' => 'rabobank-omnikassa-2-sandbox',
				]
			)
		);

		$controller->setup();

		\do_action( 'rest_api_init' );

		$config_id = \wp_insert_post(
			[
				'meta_input'  => [
					'_pronamic_gateway_id' => 'rabobank-omnikassa-2-sandbox',
					'_pronamic_gateway_omnikassa_2_refresh_token' => '',
					'_pronamic_gateway_omnikassa_2_signing_key' => $this->signing_key,
				],
				'post_status' => 'publish',
				'post_title'  => 'Rabo Smart Pay - Test',
				'post_type'   => 'pronamic_gateway',
			]
		);

		$this->assertIsInt( $config_id );

		$this->config_id = $config_id;
	}

	/**
	 * Tear down.
	 *
	 * @return void
	 */
	public function tear_down() {
		\wp_delete_post( $this->config_id, true );

		parent::tear_down();
	}

	/**
	 * Create payment.
	 *
	 * @param string|null $merchant_order_id Merchant order ID.
	 * @return Payment
	 */
	private function create_payment( $merchant_order_id ) {
		$payment = new Payment();

		$payment->set_config_id( $this->config_id );
		$payment->set_status( PaymentStatus::OPEN );

		if ( null !== $merchant_order_id ) {
			$payment->set_meta( 'omnikassa_2_merchant_order_id', $merchant_order_id );
		}

		$payment->save();

		return $payment;
	}

	/**
	 * Request return URL.
	 *
	 * @param Payment $payment   Payment.
	 * @param string  $order_id  Order ID.
	 * @param string  $status    Status.
	 * @param string  $signature Signature, `null` to sign with the signing key.
	 * @return \WP_REST_Response
	 */
	private function request_return( Payment $payment, $order_id, $status, $signature = null ) {
		if ( null === $signature ) {
			$signature = Security::get_signature( new ReturnParameters( $order_id, $status, '' ), $this->signing_key );
		}

		$request = new WP_REST_Request( 'GET', '/pronamic-pay/omnikassa-2/v1/return/' . $payment->get_id() );

		$request->set_query_params(
			[
				'order_id'  => $order_id,
				'signature' => $signature,
				'status'    => $status,
			]
		);

		return \rest_do_request( $request );
	}

	/**
	 * Get payment from database.
	 *
	 * @param Payment $payment Payment.
	 * @return Payment
	 */
	private function reload_payment( Payment $payment ) {
		$result = \get_pronamic_payment( (int) $payment->get_id() );

		$this->assertInstanceOf( Payment::class, $result );

		return $result;
	}

	/**
	 * Count return URL notes.
	 *
	 * @param Payment $payment Payment.
	 * @return int
	 */
	private function count_return_notes( Payment $payment ) {
		$comments = \get_comments(
			[
				'post_id' => $payment->get_id(),
				'search'  => 'Rabo Smart Pay return URL requested',
				'type'    => 'payment_note',
			]
		);

		return \count( $comments );
	}

	/**
	 * Test return with valid signature and matching order ID.
	 *
	 * @return void
	 */
	public function test_return_valid() {
		$payment = $this->create_payment( '1001' );

		$response = $this->request_return( $payment, '1001', 'COMPLETED' );

		$this->assertEquals( 303, $response->get_status() );
		$this->assertEquals( PaymentStatus::SUCCESS, $this->reload_payment( $payment )->get_status() );
		$this->assertEquals( 1, $this->count_return_notes( $payment ) );
	}

	/**
	 * Test return with valid signature for the order ID of another payment.
	 *
	 * @return void
	 */
	public function test_return_order_id_of_other_payment() {
		$payment_a = $this->create_payment( '1001' );
		$payment_b = $this->create_payment( '1002' );

		$response = $this->request_return( $payment_b, '1001', 'COMPLETED' );

		$this->assertEquals( 303, $response->get_status() );
		$this->assertEquals( PaymentStatus::OPEN, $this->reload_payment( $payment_b )->get_status() );
		$this->assertEquals( PaymentStatus::OPEN, $this->reload_payment( $payment_a )->get_status() );
		$this->assertEquals( 0, $this->count_return_notes( $payment_b ) );
	}

	/**
	 * Test return with invalid signature.
	 *
	 * @return void
	 */
	public function test_return_invalid_signature() {
		$payment = $this->create_payment( '1001' );

		$response = $this->request_return( $payment, '1001', 'COMPLETED', \str_repeat( '0', 128 ) );

		$this->assertEquals( 303, $response->get_status() );
		$this->assertEquals( PaymentStatus::OPEN, $this->reload_payment( $payment )->get_status() );
		$this->assertEquals( 0, $this->count_return_notes( $payment ) );
	}

	/**
	 * Test return for payment without merchant order ID.
	 *
	 * @return void
	 */
	public function test_return_without_merchant_order_id() {
		$payment = $this->create_payment( null );

		$response = $this->request_return( $payment, '1001', 'COMPLETED' );

		$this->assertEquals( 303, $response->get_status() );
		$this->assertEquals( PaymentStatus::OPEN, $this->reload_payment( $payment )->get_status() );
		$this->assertEquals( 0, $this->count_return_notes( $payment ) );
	}
}
