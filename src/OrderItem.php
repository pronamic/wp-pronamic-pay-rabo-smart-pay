<?php
/**
 * Order item.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Order item.
 *
 * @author  Reüel van der Steege
 * @version 2.0.3
 * @since   2.0.3
 */
class OrderItem {
	/**
	 * Item id.
	 *
	 * @var string
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
	 * @var string
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
	 * @var Money
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
	 * @var int
	 */
	private $vat_category;

	/**
	 * Construct order result.
	 *
	 * @param string $name     Name.
	 * @param int    $quantity Quantity.
	 * @param Money  $amount   Amount.
	 * @param string $category Category.
	 */
	public function __construct( $name, $quantity, Money $amount, $category ) {
		$this->name     = $name;
		$this->quantity = $quantity;
		$this->amount   = $amount;
		$this->category = $category;
	}

	/**
	 * Get item ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set item ID.
	 *
	 * @param string|null $id ID.
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
	 * Get item description.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Set item description.
	 *
	 * @param string $description Description.
	 */
	public function set_description( $description ) {
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
	 * @return Money
	 */
	public function get_tax() {
		return $this->tax;
	}

	/**
	 * Set tax.
	 *
	 * @param Money|null $tax Tax.
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
	 * Get VAT category.
	 *
	 * @return int
	 */
	public function get_vat_category() {
		return $this->vat_category;
	}

	/**
	 * Set VAT category.
	 *
	 * @param int $vat_category VAT category.
	 */
	public function set_vat_category( $vat_category ) {
		$this->vat_category = $vat_category;
	}

	/**
	 * Get JSON.
	 *
	 * @return object
	 */
	public function get_json() {
		$object = (object) array();

		if ( null !== $this->id ) {
			$object->id = $this->id;
		}

		$object->name = $this->name;

		if ( null !== $this->description ) {
			$object->description = $this->description;
		}

		$object->quantity = $this->quantity;
		$object->amount   = $this->amount->get_json();

		if ( null !== $this->tax ) {
			$object->tax = $this->tax->get_json();
		}

		$object->category = $this->category;

		if ( null !== $this->vat_category ) {
			$object->vatCategory = $this->vat_category;
		}

		return $object;
	}

	/**
	 * Get signature data.
	 *
	 * @param array $data Data.
	 * @return array
	 */
	public function get_signature_fields( $data = array() ) {
		if ( null !== $this->id ) {
			$data[] = $this->id;
		}

		$data[] = $this->name;
		$data[] = $this->description;
		$data[] = $this->quantity;
		$data[] = $this->amount->get_currency();
		$data[] = $this->amount->get_amount();

		if ( null === $this->tax ) {
			$data[] = null;
		} else {
			$data[] = $this->tax->get_currency();
			$data[] = $this->tax->get_amount();
		}

		$data[] = $this->category;

		if ( null !== $this->vat_category ) {
			$data[] = $this->vat_category;
		}

		return $data;
	}
}
