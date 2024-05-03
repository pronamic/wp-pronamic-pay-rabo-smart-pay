<?php
/**
 * Address transformer
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Pronamic\WordPress\Pay\Address as PronamicAddress;

/**
 * Address transformer
 *
 * @author  Remco Tolsma
 * @version 2.1.9
 * @since   2.0.2
 */
final class AddressTransformer {
	/**
	 * Transform Pronamic address to OmniKassa 2.0 address.
	 *
	 * @param PronamicAddress|null $pronamic_address Pronamic address to convert.
	 * @return Address|null
	 */
	public static function transform( PronamicAddress $pronamic_address = null ) {
		if ( null === $pronamic_address ) {
			return null;
		}

		$name = $pronamic_address->get_name();

		$last_name    = null === $name ? null : $name->get_last_name();
		$street       = $pronamic_address->get_street_name();
		$postal_code  = $pronamic_address->get_postal_code();
		$city         = $pronamic_address->get_city();
		$country_code = $pronamic_address->get_country_code();

		// Use line 1 as street if address splitting failed,
		// for example when no house number is given.
		if ( null === $street ) {
			$street = $pronamic_address->get_line_1();
		}

		if ( ! isset( $last_name, $street, $postal_code, $city, $country_code ) ) {
			return null;
		}

		// New address.
		$address = new Address( $last_name, $street, $postal_code, $city, $country_code );

		if ( null !== $name ) {
			$first_name  = $name->get_first_name();
			$middle_name = $name->get_middle_name();

			if ( null !== $first_name ) {
				$address->set_first_name( $first_name );
			}

			if ( null !== $middle_name ) {
				$address->set_middle_name( $middle_name );
			}
		}

		$address->set_house_number( $pronamic_address->get_house_number_base() );
		$address->set_house_number_addition( $pronamic_address->get_house_number_addition() );

		return $address;
	}
}
