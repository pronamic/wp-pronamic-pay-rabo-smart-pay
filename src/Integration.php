<?php

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
class Pronamic_WP_Pay_Gateways_OmniKassa2_Integration extends Pronamic_WP_Pay_Gateways_AbstractIntegration {
	public function __construct() {
		$this->id            = 'rabobank-omnikassa-2';
		$this->name          = 'Rabobank - OmniKassa 2.0';
		$this->product_url   = 'https://www.rabobank.nl/bedrijven/betalen/geld-ontvangen/rabo-omnikassa/';
		$this->dashboard_url = 'https://bankieren.rabobank.nl/omnikassa-dashboard/';
		$this->provider      = 'rabobank';
	}

	public function get_config_factory_class() {
		return 'Pronamic_WP_Pay_Gateways_OmniKassa2_ConfigFactory';
	}

	public function get_settings_class() {
		return 'Pronamic_WP_Pay_Gateways_OmniKassa2_Settings';
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
