<?php
/**
 * Address
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use JsonSerializable;

/**
 * Address
 *
 * @author  Remco Tolsma
 * @version 2.1.10
 * @since   2.0.2
 */
final class Address implements JsonSerializable {
	/**
	 * First name.
	 *
	 * @var string|null
	 */
	private $first_name;

	/**
	 * Insert or second name.
	 *
	 * @var string|null
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
	 * @return void
	 */
	public function __construct( $last_name, $street, $postal_code, $city, $country_code ) {
		$this->set_last_name( $last_name );
		$this->set_street( $street );
		$this->set_postal_code( $postal_code );
		$this->set_city( $city );
		$this->set_country_code( $country_code );
	}

	/**
	 * Get first name.
	 *
	 * @return string|null
	 */
	public function get_first_name() {
		return $this->first_name;
	}

	/**
	 * Set first name.
	 *
	 * @param string|null $first_name First name.
	 * @return void
	 */
	public function set_first_name( $first_name ) {
		DataHelper::validate_null_or_an( $first_name, 50, 'Address.firstName' );

		$this->first_name = $first_name;
	}

	/**
	 * Get middle name.
	 *
	 * @return string|null
	 */
	public function get_middle_name() {
		return $this->middle_name;
	}

	/**
	 * Set middle name.
	 *
	 * @param string|null $middle_name Middle name.
	 * @return void
	 */
	public function set_middle_name( $middle_name ) {
		DataHelper::validate_null_or_an( $middle_name, 20, 'Address.middleName' );

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
	 * @return void
	 */
	public function set_last_name( $last_name ) {
		DataHelper::validate_an( $last_name, 50, 'Address.lastName' );

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
	 * @return void
	 */
	public function set_street( $street ) {
		DataHelper::validate_an( $street, 100, 'Address.street' );

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
	 * @return void
	 */
	public function set_house_number( $house_number ) {
		DataHelper::validate_null_or_an( $house_number, 100, 'Address.houseNumber' );

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
	 * @return void
	 */
	public function set_house_number_addition( $house_number_addition ) {
		DataHelper::validate_null_or_an( $house_number_addition, 6, 'Address.houseNumberAddition' );

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
	 * @return void
	 */
	public function set_postal_code( $postal_code ) {
		DataHelper::validate_an( $postal_code, 10, 'Address.postalCode' );

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
	 * @return void
	 */
	public function set_city( $city ) {
		DataHelper::validate_an( $city, 40, 'Address.city' );

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
	 * @return void
	 */
	public function set_country_code( $country_code ) {
		DataHelper::validate_an( $country_code, 2, 'Address.countryCode' );

		$this->country_code = $country_code;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		$data = [];

		$data['firstName']  = $this->first_name;
		$data['middleName'] = $this->middle_name;
		$data['lastName']   = $this->last_name;
		$data['street']     = $this->street;

		if ( null !== $this->house_number ) {
			$data['houseNumber'] = $this->house_number;
		}

		if ( null !== $this->house_number_addition ) {
			$data['houseNumberAddition'] = $this->house_number_addition;
		}

		$data['postalCode']  = $this->postal_code;
		$data['city']        = $this->city;
		$data['countryCode'] = $this->country_code;

		return (object) $data;
	}

	/**
	 * Get signature fields.
	 *
	 * @param array<string> $fields Fields.
	 * @return array<string>
	 */
	public function get_signature_fields( $fields = [] ) {
		$fields[] = (string) $this->first_name;
		$fields[] = (string) $this->middle_name;
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
