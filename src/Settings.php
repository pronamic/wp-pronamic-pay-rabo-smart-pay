<?php
/**
 * Settings
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Core\GatewaySettings;
use Pronamic\WordPress\Pay\WebhookManager;

/**
 * Settings
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   1.0.0
 */
class Settings extends GatewaySettings {
	/**
	 * Constructs and initialize settings.
	 */
	public function __construct() {
		add_filter( 'pronamic_pay_gateway_sections', array( $this, 'sections' ) );
		add_filter( 'pronamic_pay_gateway_fields', array( $this, 'fields' ) );
	}

	/**
	 * Sections.
	 *
	 * @param array $sections Sections.
	 * @return array
	 */
	public function sections( array $sections ) {
		$sections['omnikassa-2'] = array(
			'title'   => __( 'OmniKassa 2.0', 'pronamic_ideal' ),
			'methods' => array( 'omnikassa-2' ),
		);

		// Advanced.
		$sections['omnikassa-2_advanced'] = array(
			'title'   => __( 'Advanced', 'pronamic_ideal' ),
			'methods' => array( 'omnikassa-2' ),
		);

		// Transaction feedback.
		$sections['omnikassa-2_feedback'] = array(
			'title'       => __( 'Transaction feedback', 'pronamic_ideal' ),
			'methods'     => array( 'omnikassa-2' ),
			'description' => sprintf(
				/* translators: %s: OmniKassa 2 */
				__( 'Set the Webhook URL in the %s dashboard to receive automatic transaction status updates.', 'pronamic_ideal' ),
				__( 'OmniKassa 2.0', 'pronamic_ideal' )
			),
		);

		return $sections;
	}

	/**
	 * Fields.
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function fields( array $fields ) {
		// Refresh Token.
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'omnikassa-2',
			'meta_key' => '_pronamic_gateway_omnikassa_2_refresh_token',
			'title'    => _x( 'Refresh Token', 'omnikassa', 'pronamic_ideal' ),
			'type'     => 'textarea',
			'classes'  => array( 'code' ),
		);

		// Signing Key.
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'omnikassa-2',
			'meta_key' => '_pronamic_gateway_omnikassa_2_signing_key',
			'title'    => _x( 'Signing Key', 'omnikassa', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'large-text', 'code' ),
		);

		// Transaction feedback.
		$fields[] = array(
			'section'  => 'omnikassa-2',
			'methods'  => array( 'omnikassa-2' ),
			'title'    => __( 'Transaction feedback', 'pronamic_ideal' ),
			'type'     => 'description',
			'html'     => __( 'Receiving payment status updates needs additional configuration.', 'pronamic_ideal' ),
			'features' => Gateway::get_supported_features(),
		);

		// Purchase ID.
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'omnikassa-2_advanced',
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
			'section'  => 'omnikassa-2_feedback',
			'title'    => __( 'Webhook URL', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'large-text', 'code' ),
			'value'    => add_query_arg( 'omnikassa2_webhook', '', home_url( '/' ) ),
			'readonly' => true,
			'methods'  => array( 'omnikassa-2' ),
			'tooltip'  => __( 'The Webhook URL as sent with each transaction to receive automatic payment status updates on.', 'pronamic_ideal' ),
		);

		// Webhook status.
		$fields[] = array(
			'section'  => 'omnikassa-2_feedback',
			'methods'  => array( 'omnikassa-2' ),
			'title'    => __( 'Status', 'pronamic_ideal' ),
			'type'     => 'description',
			'callback' => array( $this, 'feedback_status' ),
		);

		return $fields;
	}

	/**
	 * Transaction feedback status.
	 *
	 * @param array $field Settings field.
	 */
	public function feedback_status( $field ) {
		$features = Gateway::get_supported_features();

		WebhookManager::settings_status( $field, $features );
	}
}
