<?php
/**
 * Integration
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Gateways\Common\AbstractIntegration;

/**
 * Integration
 *
 * @author  Remco Tolsma
 * @version 2.0.2
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

		/**
		 * Webhook listener function.
		 *
		 * @var callable $webhook_listener_function
		 */
		$webhook_listener_function = array( __NAMESPACE__ . '\WebhookListener', 'listen' );

		if ( ! has_action( 'wp_loaded', $webhook_listener_function ) ) {
			add_action( 'wp_loaded', $webhook_listener_function );
		}
	}

	/**
	 * Get config factory class.
	 *
	 * @return string
	 */
	public function get_config_factory_class() {
		return __NAMESPACE__ . '\ConfigFactory';
	}

	/**
	 * Get settings class.
	 *
	 * @return string
	 */
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
