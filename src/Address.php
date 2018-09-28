<?php
/**
 * Address
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use InvalidArgumentException;
use stdClass;
use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use JsonSchema\Validator;

/**
 * Address
 *
 * @author  Remco Tolsma
 * @version 2.0.4
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
	 * @var string
	 */
	private $house_number;

	/**
	 * House number additions.
	 *
	 * @var string
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
	 * Get JSON.
	 *
	 * @return object|null
	 */
	public function get_json() {
		$data = array(
			'firstName'           => $this->first_name,
			'middleName'          => $this->middle_name,
			'lastName'            => $this->last_name,
			'street'              => $this->street,
			'houseNumber'         => $this->house_number,
			'houseNumberAddition' => $this->house_number_addition,
			'postalCode'          => $this->postal_code,
			'city'                => $this->city,
			'countryCode'         => $this->country_code,
		);

		$data = array_filter( $data );

		if ( empty( $data ) ) {
			return null;
		}

		return (object) $data;
	}
}
