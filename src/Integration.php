<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 integration
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Integration extends \Pronamic_WP_Pay_Gateways_AbstractIntegration {
	public function __construct() {
		$this->id            = 'rabobank-omnikassa-2';
		$this->name          = 'Rabobank - OmniKassa 2.0';
		$this->product_url   = 'https://www.rabobank.nl/bedrijven/betalen/geld-ontvangen/rabo-omnikassa/';
		$this->dashboard_url = 'https://bankieren.rabobank.nl/omnikassa-dashboard/';
		$this->provider      = 'rabobank';

		// Actions
		$function = array( 'Pronamic\WordPress\Pay\Gateways\OmniKassa2\Listener', 'listen' );

		if ( ! has_action( 'wp_loaded', $function ) ) {
			add_action( 'wp_loaded', $function );
		}
	}

	public function get_config_factory_class() {
		return __NAMESPACE__ . '\ConfigFactory';
	}

	public function get_settings_class() {
		return __NAMESPACE__ . '\Settings';
	}

	/**
	 * Get required settings for this integration.
	 *
	 * @see https://github.com/wp-premium/gravityforms/blob/1.9.16/includes/fields/class-gf-field-multiselect.php#L21-L42
	 * @since 1.1.6
	 * @return array
	 */
	public function get_settings() {
		$settings = parent::get_settings();

		$settings[] = 'omnikassa-2';

		return $settings;
	}
}
