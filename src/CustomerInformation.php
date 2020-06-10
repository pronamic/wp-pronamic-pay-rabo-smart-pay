<?php
/**
 * Customer information.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use DateTime;

/**
 * Customer information.
 *
 * @author  Remco Tolsma
 * @version 2.1.10
 * @since   2.0.2
 */
class CustomerInformation {
	/**
	 * The e-mailadress of the consumer.
	 *
	 * @var string|null
	 */
	private $email_address;

	/**
	 * The date of birth of the consumer.
	 *
	 * @var DateTime|null
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
	 * @param DateTime|null $date_of_birth Date of birth.
	 * @return void
	 */
	public function set_date_of_birth( DateTime $date_of_birth = null ) {
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
		if ( ! \in_array( $gender, array( null, 'F', 'M' ), true ) ) {
			throw new \InvalidArgumentException(
				\sprintf(
					'Gender "%s" must be equal to `null`, "F" or "M".',
					\strval( $gender )
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
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = (object) array();

		if ( null !== $this->email_address ) {
			$object->emailAddress = $this->email_address;
		}

		if ( null !== $this->date_of_birth ) {
			$object->dateOfBirth = $this->date_of_birth->format( 'd-m-Y' );
		}

		if ( null !== $this->gender ) {
			$object->gender = $this->gender;
		}

		if ( null !== $this->initials ) {
			$object->initials = $this->initials;
		}

		if ( null !== $this->telephone_number ) {
			$object->telephoneNumber = $this->telephone_number;
		}

		return $object;
	}

	/**
	 * Get signature fields.
	 *
	 * @param array<string> $fields Fields.
	 * @return array<string>
	 */
	public function get_signature_fields( $fields = array() ) {
		$fields[] = \strval( $this->email_address );
		$fields[] = ( null === $this->date_of_birth ) ? '' : $this->date_of_birth->format( 'd-m-Y' );
		$fields[] = \strval( $this->gender );
		$fields[] = \strval( $this->initials );
		$fields[] = \strval( $this->telephone_number );

		return $fields;
	}
}
