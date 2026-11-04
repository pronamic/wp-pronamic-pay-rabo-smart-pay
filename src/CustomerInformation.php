<?php
/**
 * Customer information.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2026 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use DateTimeInterface;
use JsonSerializable;

/**
 * Customer information.
 *
 * @version 2.2.4
 * @since   2.0.2
 */
final class CustomerInformation implements JsonSerializable {
	/**
	 * The e-mailadress of the consumer.
	 *
	 * @var string|null
	 */
	private $email_address;

	/**
	 * The date of birth of the consumer.
	 *
	 * @var DateTimeInterface|null
	 */
	private $date_of_birth;

	/**
	 * The gender of the consumer.
	 *
	 * @var string|null
	 */
	private $gender;

	/**
	 * The initials of the consumer.
	 *
	 * @var string|null
	 */
	private $initials;

	/**
	 * The consumer's telephone number.
	 *
	 * @var string|null
	 */
	private $telephone_number;

	/**
	 * The full name of the consumer.
	 *
	 * Preferred way to show the name of the customer in the Rabo Smart Pay dashboard.
	 *
	 * @link https://github.com/rabobank-nederland/omnikassa-sdk-doc#customer-name-dashboard
	 * @var string|null
	 */
	private $full_name;

	/**
	 * Set the e-mailadress of the consumer.
	 *
	 * @param string|null $email_address E-mailadress of the consumer.
	 * @return void
	 */
	public function set_email_address( $email_address ) {
		$this->email_address = $email_address;
	}

	/**
	 * Set date of birth.
	 *
	 * @param DateTimeInterface|null $date_of_birth Date of birth.
	 * @return void
	 */
	public function set_date_of_birth( ?DateTimeInterface $date_of_birth = null ) {
		$this->date_of_birth = $date_of_birth;
	}

	/**
	 * Set gender.
	 *
	 * @param string|null $gender Gender.
	 * @return void
	 * @throws \InvalidArgumentException Throws invalid argument exception when gender is not null, 'F' or 'M'.
	 */
	public function set_gender( $gender ) {
		if ( ! \in_array( $gender, [ null, 'F', 'M' ], true ) ) {
			throw new \InvalidArgumentException(
				\sprintf(
					'Gender "%s" must be equal to `null`, "F" or "M".',
					\esc_html( $gender )
				)
			);
		}

		$this->gender = $gender;
	}

	/**
	 * Set initials.
	 *
	 * @param string|null $initials Initials.
	 * @return void
	 */
	public function set_initials( $initials ) {
		$this->initials = $initials;
	}

	/**
	 * Set telephone number.
	 *
	 * @param string|null $telephone_number Telephone number.
	 * @return void
	 */
	public function set_telephone_number( $telephone_number ) {
		$this->telephone_number = $telephone_number;
	}

	/**
	 * Set full name.
	 *
	 * @param string|null $full_name Full name.
	 * @return void
	 */
	public function set_full_name( $full_name ) {
		$this->full_name = $full_name;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		$data = [];

		if ( null !== $this->email_address ) {
			$data['emailAddress'] = $this->email_address;
		}

		if ( null !== $this->date_of_birth ) {
			$data['dateOfBirth'] = $this->date_of_birth->format( 'd-m-Y' );
		}

		if ( null !== $this->gender ) {
			$data['gender'] = $this->gender;
		}

		if ( null !== $this->initials ) {
			$data['initials'] = $this->initials;
		}

		if ( null !== $this->telephone_number ) {
			$data['telephoneNumber'] = $this->telephone_number;
		}

		if ( null !== $this->full_name ) {
			$data['fullName'] = $this->full_name;
		}

		return (object) $data;
	}

	/**
	 * Get signature fields.
	 *
	 * @param array<string> $fields Fields.
	 * @return array<string>
	 */
	public function get_signature_fields( $fields = [] ) {
		$fields[] = (string) $this->email_address;
		$fields[] = ( null === $this->date_of_birth ) ? '' : $this->date_of_birth->format( 'd-m-Y' );
		$fields[] = (string) $this->gender;
		$fields[] = (string) $this->initials;
		$fields[] = (string) $this->telephone_number;

		return $fields;
	}
}
