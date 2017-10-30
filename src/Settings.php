<?php

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
class Pronamic_WP_Pay_Gateways_OmniKassa2_Settings extends Pronamic_WP_Pay_GatewaySettings {
	public function __construct() {
		add_filter( 'pronamic_pay_gateway_sections', array( $this, 'sections' ) );
		add_filter( 'pronamic_pay_gateway_fields', array( $this, 'fields' ) );
	}

	public function sections( array $sections ) {
		$sections['omnikassa-2'] = array(
			'title'   => __( 'OmniKassa 2.0', 'pronamic_ideal' ),
			'methods' => array( 'omnikassa-2' ),
		);

		return $sections;
	}

	public function fields( array $fields ) {
		// Refresh Token
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'omnikassa-2',
			'meta_key'    => '_pronamic_gateway_omnikassa_2_refresh_token',
			'title'       => _x( 'Refresh Token', 'omnikassa', 'pronamic_ideal' ),
			'type'        => 'textarea',
			'classes'     => array( 'code' ),
		);

		// Signing Key
		$fields[] = array(
			'filter'      => FILTER_SANITIZE_STRING,
			'section'     => 'omnikassa-2',
			'meta_key'    => '_pronamic_gateway_omnikassa_2_signing_key',
			'title'       => _x( 'Signing key', 'omnikassa', 'pronamic_ideal' ),
			'type'        => 'text',
			'classes'     => array( 'large-text', 'code' ),
		);

		return $fields;
	}
}
