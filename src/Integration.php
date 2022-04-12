<?php
/**
 * Integration
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
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
class Integration extends AbstractGatewayIntegration {
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
				'id'            => 'rabobank-omnikassa-2',
				'name'          => 'Rabobank - OmniKassa 2.0',
				'api_url'       => 'https://betalen.rabobank.nl/omnikassa-api/',
				'product_url'   => 'https://www.rabobank.nl/bedrijven/betalen/geld-ontvangen/rabo-omnikassa/',
				'dashboard_url' => 'https://bankieren.rabobank.nl/omnikassa-dashboard/',
				'provider'      => 'rabobank',
				'supports'      => [
					'webhook',
					'webhook_log',
				],
				'manual_url'    => \__(
					'https://www.pronamic.eu/support/how-to-connect-rabo-omnikassa-2-0-with-wordpress-via-pronamic-pay/',
					'pronamic_ideal'
				),
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

		/**
		 * Admin notices.
		 *
		 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-admin/admin-header.php#L259-L264
		 * @var callable $admin_notices_function
		 */
		$admin_notices_function = [ $this, 'admin_notice_tld_test' ];

		if ( ! \has_action( 'admin_notices', $admin_notices_function ) ) {
			\add_action( 'admin_notices', $admin_notices_function );
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

		// Webhook controller.
		$webhook_controller = new WebhookController();

		$webhook_controller->setup();
	}

	/**
	 * Admin notice TLD .test.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-admin/admin-header.php#L259-L264
	 * @link https://developer.wordpress.org/reference/hooks/admin_notices/
	 * @link https://developer.wordpress.org/reference/functions/get_current_screen/
	 * @return void
	 */
	public function admin_notice_tld_test() {
		if ( \has_filter( 'pronamic_pay_omnikassa_2_merchant_return_url' ) ) {
			return;
		}

		$screen = \get_current_screen();

		if ( null === $screen ) {
			return;
		}

		if ( 'pronamic_gateway' !== $screen->id ) {
			return;
		}

		$host = \wp_parse_url( \home_url( '/' ), \PHP_URL_HOST );

		if ( ! \is_string( $host ) ) {
			return;
		}

		if ( '.test' !== \substr( $host, -5 ) ) {
			return;
		}

		$post_id = \get_the_ID();

		if ( empty( $post_id ) ) {
			return;
		}

		$gateway_id = \get_post_meta( $post_id, '_pronamic_gateway_id', true );

		if ( 'rabobank-omnikassa-2' !== $gateway_id ) {
			return;
		}

		$class   = 'notice notice-error';
		$message = \sprintf(
			/* translators: 1: Pronamic Pay, 2: Documentation link, 3: <code>.test</code> */
			\__(
				'%1$s — <a href="%2$s">OmniKassa 2 does not accept payments from %3$s environments</a>.',
				'pronamic_ideal'
			),
			\sprintf(
				'<strong>%s</strong>',
				\__( 'Pronamic Pay', 'pronamic_ideal' )
			),
			'https://github.com/wp-pay-gateways/omnikassa-2/tree/develop/documentation#merchantreturnurl-is-not-a-valid-web-address',
			'<code>.test</code>'
		);

		\printf(
			'<div class="%1$s"><p>%2$s</p></div>',
			\esc_attr( $class ),
			\wp_kses(
				$message,
				[
					'a'      => [
						'href' => true,
					],
					'code'   => [],
					'strong' => [],
				]
			)
		);
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
			'section'  => 'general',
			'filter'   => \FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_omnikassa_2_refresh_token',
			'title'    => \_x( 'Refresh Token', 'omnikassa', 'pronamic_ideal' ),
			'type'     => 'textarea',
			'classes'  => [ 'code' ],
		];

		// Signing Key.
		$fields[] = [
			'section'  => 'general',
			'filter'   => \FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_omnikassa_2_signing_key',
			'title'    => \_x( 'Signing Key', 'omnikassa', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => [ 'large-text', 'code' ],
		];

		// Purchase ID.
		$code_field = \sprintf( '<code>%s</code>', 'merchantOrderId' );

		$fields[] = [
			'section'     => 'advanced',
			'filter'      => \FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_omnikassa_2_order_id',
			'title'       => \__( 'Order ID', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => [ 'regular-text', 'code' ],
			'tooltip'     => \sprintf(
				/* translators: %s: <code>merchantOrderId</code> */
				\__( 'This setting defines the OmniKassa 2.0 %s field.', 'pronamic_ideal' ),
				$code_field
			),
			'description' => \sprintf(
				'%s<br />%s %s<br />%s',
				\sprintf(
					/* translators: %s: <code>merchantOrderId</code> */
					\__(
						'The OmniKassa 2.0 %s field must consist strictly of 24 alphanumeric characters, other characters, such as ".", "@", " " (space), etc. are not allowed.',
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
		];

		// Webhook.
		$fields[] = [
			'section'  => 'feedback',
			'title'    => \__( 'Webhook URL', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => [ 'large-text', 'code' ],
			'value'    => \rest_url( self::REST_ROUTE_NAMESPACE . '/webhook/' . (string) \get_the_ID() ),
			'readonly' => true,
			'tooltip'  => \__(
				'The Webhook URL as sent with each transaction to receive automatic payment status updates on.',
				'pronamic_ideal'
			),
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
