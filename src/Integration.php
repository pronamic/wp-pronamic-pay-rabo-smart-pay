<?php
/**
 * Integration
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2024 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\AbstractGatewayIntegration;

/**
 * Integration
 *
 * @author  Remco Tolsma
 * @version 2.3.4
 * @since   1.0.0
 */
final class Integration extends AbstractGatewayIntegration {
	/**
	 * REST route namespace.
	 *
	 * @var string
	 */
	const REST_ROUTE_NAMESPACE = 'pronamic-pay/omnikassa-2/v1';

	/**
	 * API URL.
	 *
	 * @var string
	 */
	private $api_url;

	/**
	 * Construct OmniKassa 2.0 integration.
	 *
	 * @param array<string, string|array<string>> $args Arguments.
	 */
	public function __construct( $args = [] ) {
		$args = \wp_parse_args(
			$args,
			[
				'api_url'       => 'https://betalen.rabobank.nl/omnikassa-api/',
				'dashboard_url' => 'https://bankieren.rabobank.nl/smartpay/dashboard/home',
				'id'            => 'rabobank-omnikassa-2',
				'manual_url'    => \__(
					'https://www.pronamicpay.com/en/manuals/how-to-connect-rabo-smart-pay-to-wordpress-with-pronamic-pay/',
					'pronamic_ideal'
				),
				'name'          => 'Rabobank - Rabo Smart Pay',
				'product_url'   => 'https://www.rabobank.nl/bedrijven/betalen/klanten-laten-betalen/rabo-smart-pay',
				'provider'      => 'rabobank',
				'supports'      => [
					'refunds',
					'webhook',
					'webhook_log',
				],
			]
		);

		parent::__construct( $args );

		$this->api_url = $args['api_url'];

		/**
		 * Save post.
		 *
		 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-includes/post.php#L3724-L3736
		 * @var callable $delete_access_token_meta_function
		 */
		$delete_access_token_meta_function = [ $this, 'delete_access_token_meta' ];

		if ( ! \has_action( 'save_post_pronamic_gateway', $delete_access_token_meta_function ) ) {
			\add_action( 'save_post_pronamic_gateway', $delete_access_token_meta_function );
		}
	}

	/**
	 * Setup gateway integration.
	 *
	 * @return void
	 */
	public function setup() {
		// Check if dependencies are met and integration is active.
		if ( ! $this->is_active() ) {
			return;
		}

		// Return controller.
		$return_controller = new ReturnController( $this );

		$return_controller->setup();

		// Webhook controller.
		$webhook_controller = new WebhookController();

		$webhook_controller->setup();
	}

	/**
	 * Get settings fields.
	 *
	 * @return array<int, array<string, callable|int|string|bool|array<int|string,int|string>>>
	 */
	public function get_settings_fields() {
		$fields = [];

		// Refresh Token.
		$fields[] = [
			'classes'  => [ 'code' ],
			'meta_key' => '_pronamic_gateway_omnikassa_2_refresh_token',
			'section'  => 'general',
			'title'    => \_x( 'Refresh Token', 'omnikassa', 'pronamic_ideal' ),
			'type'     => 'textarea',
		];

		// Signing Key.
		$fields[] = [
			'classes'  => [ 'large-text', 'code' ],
			'meta_key' => '_pronamic_gateway_omnikassa_2_signing_key',
			'section'  => 'general',
			'title'    => \_x( 'Signing Key', 'omnikassa', 'pronamic_ideal' ),
			'type'     => 'text',
		];

		// Purchase ID.
		$code_field = \sprintf( '<code>%s</code>', 'merchantOrderId' );

		$fields[] = [
			'classes'     => [ 'regular-text', 'code' ],
			'description' => \sprintf(
				'%s<br />%s %s<br />%s',
				\sprintf(
					/* translators: %s: <code>merchantOrderId</code> */
					\__(
						'The Rabo Smart Pay %s field must consist strictly of 24 alphanumeric characters, other characters, such as ".", "@", " " (space), etc. are not allowed.',
						'pronamic_ideal'
					),
					$code_field
				),
				\__( 'Available tags:', 'pronamic_ideal' ),
				\sprintf(
					'<code>%s</code> <code>%s</code>',
					'{order_id}',
					'{payment_id}'
				),
				\sprintf(
					/* translators: %s: default code */
					\__( 'Default: <code>%s</code>', 'pronamic_ideal' ),
					'{payment_id}'
				)
			),
			'meta_key'    => '_pronamic_gateway_omnikassa_2_order_id',
			'section'     => 'advanced',
			'title'       => \__( 'Order ID', 'pronamic_ideal' ),
			'tooltip'     => \sprintf(
				/* translators: %s: <code>merchantOrderId</code> */
				\__( 'This setting defines the Rabo Smart Pay %s field.', 'pronamic_ideal' ),
				$code_field
			),
			'type'        => 'text',
		];

		// Webhook.
		$fields[] = [
			'classes'  => [ 'large-text', 'code' ],
			'readonly' => true,
			'section'  => 'feedback',
			'title'    => \__( 'Webhook URL', 'pronamic_ideal' ),
			'tooltip'  => \sprintf(
				/* translators: %s: payment provider name */
				\__(
					'Copy the Webhook URL to the %s dashboard to receive automatic transaction status updates.',
					'pronamic_ideal'
				),
				\__( 'Rabo Smart Pay', 'pronamic_ideal' )
			),
			'type'     => 'text',
			'value'    => \rest_url( self::REST_ROUTE_NAMESPACE . '/webhook/' . (string) \get_the_ID() ),
		];

		return $fields;
	}

	/**
	 * Get configuration by post ID.
	 *
	 * @param int $post_id Post ID.
	 * @return Config
	 */
	public function get_config( $post_id ) {
		$config = new Config();

		$config->set_api_url( $this->api_url );

		$config->post_id                  = \intval( $post_id );
		$config->refresh_token            = $this->get_meta( $post_id, 'omnikassa_2_refresh_token' );
		$config->signing_key              = $this->get_meta( $post_id, 'omnikassa_2_signing_key' );
		$config->access_token             = $this->get_meta( $post_id, 'omnikassa_2_access_token' );
		$config->access_token_valid_until = $this->get_meta( $post_id, 'omnikassa_2_access_token_valid_until' );
		$config->order_id                 = $this->get_meta( $post_id, 'omnikassa_2_order_id' );

		return $config;
	}

	/**
	 * Delete access token meta for the specified post ID.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-includes/post.php#L3724-L3736
	 * @link https://codex.wordpress.org/Function_Reference/delete_post_meta
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public static function delete_access_token_meta( $post_id ) {
		\delete_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_access_token' );
		\delete_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_access_token_valid_until' );
	}

	/**
	 * Get gateway.
	 *
	 * @param int $post_id Post ID.
	 * @return Gateway
	 */
	public function get_gateway( $post_id ) {
		$gateway = new Gateway( $this->get_config( $post_id ) );

		$gateway->set_mode( $this->get_mode() );

		return $gateway;
	}
}
