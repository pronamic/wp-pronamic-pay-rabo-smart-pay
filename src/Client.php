<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 client
 * Description:
 * Copyright: Copyright (c) 2005 - 2018
 * Company: Pronamic
 *
 * @author  Remco Tolsma
 * @version 2.0.1
 * @since   1.0.0
 */
class Client {
	/**
	 * URL OmniKassa acceptance API.
	 *
	 * @var string
	 */
	const URL_ACCEPTANCE = 'https://betalen-acpt3.rabobank.nl/omnikassa-api/';

	/**
	 * URL OmniKassa API.
	 *
	 * @var string
	 */
	const URL_PRODUCTION = 'https://betalen.rabobank.nl/omnikassa-api/';

	/**
	 * URL OmniKassa sandbox API.
	 *
	 * @var string
	 */
	const URL_SANDBOX = 'https://betalen.rabobank.nl/omnikassa-api-sandbox/';

	/**
	 * Error
	 *
	 * @var WP_Error
	 */
	private $error;

	/**
	 * The URL.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Error
	 *
	 * @return WP_Error
	 */
	public function get_error() {
		return $this->error;
	}

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

	/**
	 * Get refresh token.
	 */
	public function get_refresh_token() {
		return $this->refresh_token;
	}

	/**
	 * Set refresh token.
	 */
	public function set_refresh_token( $refresh_token ) {
		$this->refresh_token = $refresh_token;
	}

	/**
	 * Get signing key.
	 */
	public function get_signing_key() {
		return $this->signing_key;
	}

	/**
	 * Set signing key.
	 */
	public function set_signing_key( $signing_key ) {
		$this->signing_key = $signing_key;
	}

	/**
	 * Get remote request arguments.
	 *
	 * @since 2.0.1
	 * @link https://github.com/WordPress/WordPress/blob/4.9.8/wp-includes/class-http.php#L176-L183
	 *
	 * @param array $args Arguments.
	 * @return array
	 */
	private function get_remote_request_args( $args = array() ) {
		$args = wp_parse_args( $args, array(
			// We send an empty User-Agent string so OmniKassa 2.0 servers can't block requests based on the User-Agent.
			'user-agent' => '',
		) );

		return $args;
	}

	/**
	 * Get access token.
	 */
	public function get_access_token_data() {
		$url = $this->get_url() . 'gatekeeper/refresh';

		$response = wp_remote_get( $url, $this->get_remote_request_args( array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->get_refresh_token(),
			),
		) ) );

		if ( is_wp_error( $response ) ) {
			$this->error = $response;

			$this->error->add( 'omnikassa_2_error', 'HTTP Request Failed' );

			return false;
		}

		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body );

		if ( is_object( $data ) && isset( $data->errorCode ) && isset( $data->errorMessage ) ) {
			$this->error = new \WP_Error( 'omnikassa_2_error', $data->errorMessage, $data );

			return false;
		}

		if ( is_object( $data ) && '200' != wp_remote_retrieve_response_code( $response ) ) { // WPCS: loose comparison ok.
			return false;
		}

		if ( ! is_object( $data ) ) {
			$this->error = new \WP_Error( 'omnikassa_2_error', 'Could not parse response.' );

			return false;
		}

		return $data;
	}

	public function order_announce( $config, Order $order ) {
		$url = $this->get_url() . 'order/server/api/order';

		$order->set_signing_key( $config->signing_key );

		$object            = $order->get_json();
		$object->signature = $order->get_signature();

		$response = wp_remote_post( $url, $this->get_remote_request_args( array(
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . $config->access_token,
			),
			'body'    => wp_json_encode( $object ),
		) ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body );

		if ( is_object( $data ) && isset( $data->errorCode ) && isset( $data->errorMessage ) ) {
			$this->error = new \WP_Error( 'omnikassa_2_error', $data->errorMessage, $data );
		}

		if ( is_object( $data ) && '201' != wp_remote_retrieve_response_code( $response ) ) { // WPCS: loose comparison ok.
			if ( isset( $data->consumerMessage ) ) {
				$message = $data->consumerMessage;
			} elseif ( isset( $data->errorMessage ) ) {
				$message = $data->errorMessage;
			} else {
				$message = 'Unknown error.';
			}

			$this->error = new \WP_Error( 'omnikassa_2_error', $message, $data );

			return false;
		}

		if ( ! is_object( $data ) ) {
			$this->error = new \WP_Error( 'omnikassa_2_error', 'Could not parse response.' );

			return false;
		}

		return $data;
	}

	public function retrieve_announcement( $announcement ) {
		if ( ! is_object( $announcement ) ) {
			return;
		}

		$url = $this->get_url() . 'order/server/api/events/results/' . $announcement->eventName;

		$response = wp_remote_get( $url, $this->get_remote_request_args( array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $announcement->authentication,
			),
		) ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );

		$data = json_decode( $body );

		if ( is_object( $data ) && isset( $data->errorCode ) && isset( $data->errorMessage ) ) {
			$this->error = new \WP_Error( 'omnikassa_2_error', $data->errorMessage, $data );
		}

		if ( is_object( $data ) && '200' != wp_remote_retrieve_response_code( $response ) ) { // WPCS: loose comparison ok.
			if ( isset( $data->consumerMessage ) ) {
				$message = $data->consumerMessage;
			} elseif ( isset( $data->errorMessage ) ) {
				$message = $data->errorMessage;
			} else {
				$message = 'Unknown error.';
			}

			$this->error = new \WP_Error( 'omnikassa_2_error', $message, $data );

			return false;
		}

		if ( ! is_object( $data ) ) {
			$this->error = new \WP_Error( 'omnikassa_2_error', 'Could not parse response.' );

			return false;
		}

		return $data;
	}
}
