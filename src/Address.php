<?php
/**
 * Address
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2019 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Address
 *
 * @author  Remco Tolsma
 * @version 2.1.0
 * @since   2.0.2
 */
class Address {
	/**
	 * First name.
	 *
	 * @var string
	 */
	private $first_name;

	/**
	 * Insert or second name.
	 *
	 * @var string
	 */
	private $middle_name;

	/**
	 * Surname.
	 *
	 * @var string
	 */
	private $last_name;

	/**
	 * Street.
	 *
	 * Note: In case of payment via Visa, Mastercard, V PAY,
	 * Bancontact and Maestro the street name will be
	 * truncated to 50 characters.
	 *
	 * @var string
	 */
	private $street;

	/**
	 * House number.
	 *
	 * Note: In case of payment via Visa, Mastercard, V PAY,
	 * Bancontact and Maestro the houseNumber concatenated
	 * with houseNumberAddition (see below) will be
	 * truncated to 10 characters.
	 *
	 * @var string|null
	 */
	private $house_number;

	/**
	 * House number additions.
	 *
	 * @var string|null
	 */
	private $house_number_addition;

	/**
	 * Postal code.
	 *
	 * @var string
	 */
	private $postal_code;

	/**
	 * City.
	 *
	 * @var string
	 */
	private $city;

	/**
	 * Country code, ISO 3166-1 alpha-2.
	 *
	 * @var string
	 */
	private $country_code;

	/**
	 * Construct address.
	 *
	 * @param string $last_name    Last name.
	 * @param string $street       Street.
	 * @param string $postal_code  Postal code.
	 * @param string $city         City.
	 * @param string $country_code Country code.
	 */
	public function __construct( $last_name, $street, $postal_code, $city, $country_code ) {
		$this->last_name    = $last_name;
		$this->street       = $street;
		$this->postal_code  = $postal_code;
		$this->city         = $city;
		$this->country_code = $country_code;
	}

	/**
	 * Get first name.
	 *
	 * @return string
	 */
	public function get_first_name() {
		return $this->first_name;
	}

	/**
	 * Set first name.
	 *
	 * @param string $first_name First name.
	 */
	public function set_first_name( $first_name ) {
		$this->first_name = $first_name;
	}

	/**
	 * Get middle name.
	 *
	 * @return string
	 */
	public function get_middle_name() {
		return $this->middle_name;
	}

	/**
	 * Set middle name.
	 *
	 * @param string $middle_name Middle name.
	 */
	public function set_middle_name( $middle_name ) {
		$this->middle_name = $middle_name;
	}

	/**
	 * Get last name.
	 *
	 * @return string
	 */
	public function get_last_name() {
		return $this->last_name;
	}

	/**
	 * Set last name.
	 *
	 * @param string $last_name Last name.
	 */
	public function set_last_name( $last_name ) {
		$this->last_name = $last_name;
	}

	/**
	 * Get street.
	 *
	 * @return string
	 */
	public function get_street() {
		return $this->street;
	}

	/**
	 * Set street.
	 *
	 * @param string $street Street.
	 */
	public function set_street( $street ) {
		$this->street = $street;
	}

	/**
	 * Get house number.
	 *
	 * @return string|null
	 */
	public function get_house_number() {
		return $this->house_number;
	}

	/**
	 * Set house number.
	 *
	 * @param string|null $house_number House number.
	 */
	public function set_house_number( $house_number ) {
		$this->house_number = $house_number;
	}

	/**
	 * Get house number addition.
	 *
	 * @return string|null
	 */
	public function get_house_number_addition() {
		return $this->house_number_addition;
	}

	/**
	 * Set house number addition.
	 *
	 * @param string|null $house_number_addition House number addition.
	 */
	public function set_house_number_addition( $house_number_addition ) {
		$this->house_number_addition = $house_number_addition;
	}

	/**
	 * Get postal code.
	 *
	 * @return string
	 */
	public function get_postal_code() {
		return $this->postal_code;
	}

	/**
	 * Set postal code.
	 *
	 * @param string $postal_code Postal code.
	 */
	public function set_postal_code( $postal_code ) {
		$this->postal_code = $postal_code;
	}

	/**
	 * Get city.
	 *
	 * @return string
	 */
	public function get_city() {
		return $this->city;
	}

	/**
	 * Set city.
	 *
	 * @param string $city City.
	 */
	public function set_city( $city ) {
		$this->city = $city;
	}

	/**
	 * Get country code.
	 *
	 * @return string
	 */
	public function get_country_code() {
		return $this->country_code;
	}

	/**
	 * Set country code.
	 *
	 * @param string $country_code Country code.
	 */
	public function set_country_code( $country_code ) {
		$this->country_code = $country_code;
	}

	/**
	 * Get JSON.
	 *
	 * @return object|null
	 */
	public function get_json() {
		$object = (object) array();

		$object->firstName  = $this->first_name;
		$object->middleName = $this->middle_name;
		$object->lastName   = $this->last_name;
		$object->street     = $this->street;

		if ( null !== $this->house_number ) {
			$object->houseNumber = $this->house_number;
		}

		if ( null !== $this->house_number_addition ) {
			$object->houseNumberAddition = $this->house_number_addition;
		}

		$object->postalCode  = $this->postal_code;
		$object->city        = $this->city;
		$object->countryCode = $this->country_code;

		return $object;
	}

	/**
	 * Get signature fields.
	 *
	 * @param array $fields Fields.
	 * @return array
	 */
	public function get_signature_fields( $fields = array() ) {
		$fields[] = $this->first_name;
		$fields[] = $this->middle_name;
		$fields[] = $this->last_name;
		$fields[] = $this->street;

		if ( null !== $this->house_number ) {
			$fields[] = $this->house_number;
		}

		if ( null !== $this->house_number_addition ) {
			$fields[] = $this->house_number_addition;
		}

		$fields[] = $this->postal_code;
		$fields[] = $this->city;
		$fields[] = $this->country_code;

		return $fields;
	}
}
