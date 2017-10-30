<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 client
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Client {
	/**
	 * 
	 * @var string
	 */
	const URL_ACCEPTANCE = 'https://betalen-acpt3.rabobank.nl/';

	/**
	 * 
	 * @var string
	 */
	const URL_PRUDCTION = 'https://betalen.rabobank.nl/';

	//////////////////////////////////////////////////

	/**
	 * The URL.
	 *
	 * @var string
	 */
	private $url;

	//////////////////////////////////////////////////

	/**
	 * Get the URL
	 *
	 * @return the action URL
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Set the action URL
	 *
	 * @param string $url an URL
	 */
	public function set_url( $url ) {
		$this->url = $url;
	}

	//////////////////////////////////////////////////

	/**
	 *
	 */
	public function get_refresh_token() {
		return $this->refresh_token;
	}

	/**
	 *
	 */
	public function set_refresh_token( $refresh_token ) {
		$this->refresh_token = $refresh_token;
	}

	//////////////////////////////////////////////////

	/**
	 *
	 */
	public function get_signing_key() {
		return $this->signing_key;
	}

	/**
	 *
	 */
	public function set_signing_key( $signing_key ) {
		$this->signing_key = $signing_key;
	}

	//////////////////////////////////////////////////

	/**
	 * Get access token.
	 */
	public function get_access_token() {
		$url = $this->get_url() . 'omnikassa-api/gatekeeper/refresh';

		$response = wp_remote_get( $url, array(
			'headers' = array(
				'Authorization' => 'Bearer ' . $this->get_refresh_token(),
			),
		) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		if ( '200' != wp_remote_retrieve_response_code( $response ) ) { // WPCS: loose comparison ok.
			return false;
		}

		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body );

		if ( ! is_object( $data ) ) {
			return false;
		}

		return $data;
	}

	public function order_announce( $access_token, $order ) {
		$url = $this->get_url() . 'omnikassa-api/order/server/api/order';

		$response = wp_remote_get( $url, array(
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $access_token,
			),
			'body'    => $order->get_json_string(),
		) );


	}
}
