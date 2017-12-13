<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 settings
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Settings extends \Pronamic_WP_Pay_GatewaySettings {
	public function __construct() {
		add_filter( 'pronamic_pay_gateway_sections', array( $this, 'sections' ) );
		add_filter( 'pronamic_pay_gateway_fields', array( $this, 'fields' ) );
	}

	public function sections( array $sections ) {
		$sections['omnikassa-2'] = array(
			'title'   => __( 'OmniKassa 2.0', 'pronamic_ideal' ),
			'methods' => array( 'omnikassa-2' ),
		);

		// Transaction eedback
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

	public function fields( array $fields ) {
		// Refresh Token
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'omnikassa-2',
			'meta_key' => '_pronamic_gateway_omnikassa_2_refresh_token',
			'title'    => _x( 'Refresh Token', 'omnikassa', 'pronamic_ideal' ),
			'type'     => 'textarea',
			'classes'  => array( 'code' ),
		);

		// Signing Key
		$fields[] = array(
			'filter'   => FILTER_SANITIZE_STRING,
			'section'  => 'omnikassa-2',
			'meta_key' => '_pronamic_gateway_omnikassa_2_signing_key',
			'title'    => _x( 'Signing Key', 'omnikassa', 'pronamic_ideal' ),
			'type'     => 'text',
			'classes'  => array( 'large-text', 'code' ),
		);

		// Transaction feedback
		$fields[] = array(
			'section' => 'omnikassa-2',
			'title'   => __( 'Transaction feedback', 'pronamic_ideal' ),
			'type'    => 'description',
			'html'    => sprintf(
				'<span class="dashicons dashicons-warning"></span> %s',
				__( 'Receiving payment status updates needs additional configuration, if not yet completed.', 'pronamic_ideal' )
			),
		);

		// Webhook
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

		return $fields;
	}
}
