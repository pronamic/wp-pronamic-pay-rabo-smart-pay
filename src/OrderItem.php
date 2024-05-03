<?php
/**
 * Order item.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use JsonSerializable;

/**
 * Order item.
 *
 * @author  Reüel van der Steege
 * @version 2.1.10
 * @since   2.0.3
 */
final class OrderItem implements JsonSerializable {
	/**
	 * Item id.
	 *
	 * @var string|null
	 */
	private $id;

	/**
	 * Item name (required).
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Description.
	 *
	 * @var string|null
	 */
	private $description;

	/**
	 * Quantity (required).
	 *
	 * @var int
	 */
	private $quantity;

	/**
	 * Amount (required).
	 *
	 * @var Money
	 */
	private $amount;

	/**
	 * Tax.
	 *
	 * @var Money|null
	 */
	private $tax;

	/**
	 * Category; physical or digital (required).
	 *
	 * @var string
	 */
	private $category;

	/**
	 * VAT category.
	 *
	 * @var string|null
	 */
	private $vat_category;

	/**
	 * Construct order result.
	 *
	 * @param string $name     Name.
	 * @param int    $quantity Quantity.
	 * @param Money  $amount   Amount.
	 * @param string $category Category.
	 * @throws \InvalidArgumentException Throws invalid argument exception when arguments are invalid.
	 */
	public function __construct( $name, $quantity, Money $amount, $category ) {
		$this->set_name( $name );
		$this->quantity = $quantity;
		$this->amount   = $amount;
		$this->set_category( $category );
	}

	/**
	 * Get item ID.
	 *
	 * @return string|null
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set item ID.
	 *
	 * @param string|null $id ID.
	 * @return void
	 */
	public function set_id( $id = null ) {
		$this->id = $id;
	}

	/**
	 * Get item name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Set item name.
	 *
	 * @param string $name Name.
	 * @return void
	 * @throws \InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..max 50`.
	 */
	public function set_name( $name ) {
		DataHelper::validate_an( $name, 50, 'OrderItems.name' );

		$this->name = $name;
	}

	/**
	 * Get item description.
	 *
	 * @return string|null
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set item description.
	 *
	 * @param string|null $description Description.
	 * @return void
	 * @throws \InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..max 100`.
	 */
	public function set_description( $description ) {
		DataHelper::validate_null_or_an( $description, 100, 'OrderItems.description' );

		$this->description = $description;
	}

	/**
	 * Get quantity.
	 *
	 * @return int
	 */
	public function get_quantity() {
		return $this->quantity;
	}

	/**
	 * Get amount.
	 *
	 * @return Money
	 */
	public function get_amount() {
		return $this->amount;
	}

	/**
	 * Get tax.
	 *
	 * @return Money|null
	 */
	public function get_tax() {
		return $this->tax;
	}

	/**
	 * Set tax.
	 *
	 * @param Money|null $tax Tax.
	 * @return void
	 */
	public function set_tax( Money $tax = null ) {
		$this->tax = $tax;
	}

	/**
	 * Get category.
	 *
	 * @return string
	 */
	public function get_category() {
		return $this->category;
	}

	/**
	 * Set category.
	 *
	 * @param string $category Product category: PHYSICAL or DIGITAL.
	 * @return void
	 * @throws \InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..max 8`.
	 */
	public function set_category( $category ) {
		DataHelper::validate_an( $category, 8, 'OrderItems.category' );

		$this->category = $category;
	}

	/**
	 * Get VAT category.
	 *
	 * @return string|null
	 */
	public function get_vat_category() {
		return $this->vat_category;
	}

	/**
	 * Set VAT category.
	 *
	 * @param string|null $vat_category VAT category.
	 * @return void
	 */
	public function set_vat_category( $vat_category ) {
		$this->vat_category = $vat_category;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		$data = [];

		if ( null !== $this->id ) {
			$data['id'] = $this->id;
		}

		$data['name'] = $this->name;

		if ( null !== $this->description ) {
			$data['description'] = $this->description;
		}

		$data['quantity'] = $this->quantity;
		$data['amount']   = $this->amount;

		if ( null !== $this->tax ) {
			$data['tax'] = $this->tax;
		}

		$data['category'] = $this->category;

		if ( null !== $this->vat_category ) {
			$data['vatCategory'] = $this->vat_category;
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
		if ( null !== $this->id ) {
			$fields[] = $this->id;
		}

		$fields[] = $this->name;
		$fields[] = \strval( (string) $this->description );
		$fields[] = \strval( $this->quantity );

		$fields = $this->amount->get_signature_fields( $fields );

		if ( null === $this->tax ) {
			$fields[] = '';
		} else {
			$fields = $this->tax->get_signature_fields( $fields );
		}

		$fields[] = $this->category;

		if ( null !== $this->vat_category ) {
			$fields[] = $this->vat_category;
		}

		return $fields;
	}
}
