<?php
/**
 * Integration
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Gateways\Common\AbstractIntegration;

/**
 * Integration
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   1.0.0
 */
class Integration extends AbstractIntegration {
	/**
	 * Construct and initialize integration.
	 */
	public function __construct() {
		$this->id            = 'rabobank-omnikassa-2';
		$this->name          = 'Rabobank - OmniKassa 2.0';
		$this->product_url   = 'https://www.rabobank.nl/bedrijven/betalen/geld-ontvangen/rabo-omnikassa/';
		$this->dashboard_url = 'https://bankieren.rabobank.nl/omnikassa-dashboard/';
		$this->provider      = 'rabobank';
		$this->supports      = array(
			'webhook',
			'webhook_log',
		);

		/**
		 * Webhook listener function.
		 *
		 * @var callable $webhook_listener_function
		 */
		$webhook_listener_function = array( __NAMESPACE__ . '\WebhookListener', 'listen' );

		if ( ! has_action( 'wp_loaded', $webhook_listener_function ) ) {
			add_action( 'wp_loaded', $webhook_listener_function );
		}

		/**
		 * Save post.
		 *
		 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-includes/post.php#L3724-L3736
		 *
		 * @var callable $delete_access_token_meta_function
		 */
		$delete_access_token_meta_function = array( $this, 'delete_access_token_meta' );

		if ( ! has_action( 'save_post_pronamic_gateway', $delete_access_token_meta_function ) ) {
			add_action( 'save_post_pronamic_gateway', $delete_access_token_meta_function );
		}

		/**
		 * Admin notices.
		 *
		 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-admin/admin-header.php#L259-L264
		 *
		 * @var callable $admin_notices_function
		 */
		$admin_notices_function = array( $this, 'admin_notice_tld_test' );

		if ( ! has_action( 'admin_notices', $admin_notices_function ) ) {
			add_action( 'admin_notices', $admin_notices_function );
		}
	}

	/**
	 * Admin notice TLD .test.
	 *
	 * @link https://github.com/WordPress/WordPress/blob/5.0/wp-admin/admin-header.php#L259-L264
	 * @link https://developer.wordpress.org/reference/hooks/admin_notices/
	 * @link https://developer.wordpress.org/reference/functions/get_current_screen/
	 */
	public function admin_notice_tld_test() {
		if ( has_filter( 'pronamic_pay_omnikassa_2_merchant_return_url' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( null === $screen ) {
			return;
		}

		if ( 'pronamic_gateway' !== $screen->id ) {
			return;
		}

		$host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

		if ( is_array( $host ) ) {
			return;
		}

		if ( '.test' !== substr( $host, -5 ) ) {
			return;
		}

		$post_id = get_the_ID();

		if ( empty( $post_id ) ) {
			return;
		}

		$gateway_id = get_post_meta( $post_id, '_pronamic_gateway_id', true );

		if ( 'rabobank-omnikassa-2' !== $gateway_id ) {
			return;
		}

		$class   = 'notice notice-error';
		$message = sprintf(
			/* translators: 1: Pronamic Pay, 2: Documentation link, 3: <code>.test</code> */
			__( '%1$s — <a href="%2$s">OmniKassa 2 does not accept payments from %3$s environments</a>.', 'pronamic_ideal' ),
			sprintf(
				'<strong>%s</strong>',
				__( 'Pronamic Pay', 'pronamic_ideal' )
			),
			'https://github.com/wp-pay-gateways/omnikassa-2/tree/develop/documentation#merchantreturnurl-is-not-a-valid-web-address',
			'<code>.test</code>'
		);

		printf(
			'<div class="%1$s"><p>%2$s</p></div>',
			esc_attr( $class ),
			wp_kses(
				$message,
				array(
					'a'      => array(
						'href' => true,
					),
					'code'   => array(),
					'strong' => array(),
				)
			)
		);
	}

	/**
	 * Get settings fields.
	 *
	 * @return array
	 */
	public function get_settings_fields() {
		$fields = array();

		// Refresh Token.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_omnikassa_2_refresh_token',
			'title'    => _x( 'Refresh Token', 'omnikassa', 'pronamic_ideal' ),
			'type'     => 'textarea',
			'classes'  => array( 'code' ),
		);

		// Signing Key.
		$fields[] = array(
			'section'  => 'general',
			'filter'   => FILTER_SANITIZE_STRING,
			'meta_key' => '_pronamic_gateway_omnikassa_2_signing_key',
			'title'    => _x( 'Signing Key', 'omnikassa', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'large-text', 'code' ),
		);

		// Purchase ID.
		$fields[] = array(
			'section'     => 'advanced',
			'filter'      => FILTER_SANITIZE_STRING,
			'meta_key'    => '_pronamic_gateway_omnikassa_2_order_id',
			'title'       => __( 'Order ID', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'regular-text', 'code' ),
			'tooltip'     => sprintf(
				/* translators: %s: Parameter */
				__( 'The OmniKassa %s parameter.', 'pronamic_ideal' ),
				sprintf( '<code>%s</code>', 'orderId' )
			),
			'description' => sprintf(
				'%s %s<br />%s',
				__( 'Available tags:', 'pronamic_ideal' ),
				sprintf(
					'<code>%s</code> <code>%s</code>',
					'{order_id}',
					'{payment_id}'
				),
				sprintf(
					/* translators: %s: {payment_id} */
					__( 'Default: <code>%s</code>', 'pronamic_ideal' ),
					'{payment_id}'
				)
			),
		);

		// Webhook.
		$fields[] = array(
			'section'  => 'feedback',
			'title'    => __( 'Webhook URL', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'large-text', 'code' ),
			'value'    => add_query_arg( 'omnikassa2_webhook', '', home_url( '/' ) ),
			'readonly' => true,
			'tooltip'  => __( 'The Webhook URL as sent with each transaction to receive automatic payment status updates on.', 'pronamic_ideal' ),
		);

		return $fields;
	}

	/**
	 * Get configuration by post ID.
	 *
	 * @param string $post_id Post ID.
	 * @return Config
	 */
	public function get_config( $post_id ) {
		$config = new Config();

		$config->post_id                  = intval( $post_id );
		$config->mode                     = $this->get_meta( $post_id, 'mode' );
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
	 *
	 * @param int $post_id Post ID.
	 */
	public static function delete_access_token_meta( $post_id ) {
		delete_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_access_token' );
		delete_post_meta( $post_id, '_pronamic_gateway_omnikassa_2_access_token_valid_until' );
	}

	/**
	 * Get gateway.
	 *
	 * @param int $post_id Post ID.
	 * @return Gateway
	 */
	public function get_gateway( $post_id ) {
		return new Gateway( $this->get_config( $post_id ) );
	}
}
