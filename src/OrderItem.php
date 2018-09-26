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
	 * @param array $item Order item data.
	 *
	 * @return self
	 */
	public function __construct( array $item ) {
		if ( isset( $item['id'] ) ) {
			$this->id = $item['id'];
		}

		if ( isset( $item['name'] ) ) {
			$this->name = $item['name'];
		}

		if ( isset( $item['description'] ) ) {
			$this->description = $item['description'];
		}

		if ( isset( $item['quantity'] ) ) {
			$this->quantity = intval( $item['quantity'] );
		}

		if ( isset( $item['amount'] ) ) {
			$this->amount = $item['amount'];
		}

		if ( isset( $item['tax'] ) ) {
			$this->tax = $item['tax'];
		}

		if ( isset( $item['category'] ) ) {
			$this->category = $item['category'];
		}

		if ( isset( $item['vat_category'] ) ) {
			$this->vat_category = intval( $item['vat_category'] );
		}
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
	 * @return int|string
	 */
	public function get_description() {
		return $this->description;
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
}
