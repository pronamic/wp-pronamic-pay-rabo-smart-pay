<?php
/**
 * Order items.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2018 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Order items.
 *
 * @author  Reüel van der Steege
 * @version 2.0.3
 * @since   2.0.3
 */
class OrderItems {
	/**
	 * Order items.
	 *
	 * @var array
	 */
	private $order_items;

	/**
	 * Construct order results message.
	 *
	 * @param array $items Order items.
	 */
	public function __construct( $items = null ) {
		if ( is_array( $items ) ) {
			foreach ( $items as $item ) {
				$this->add_item( $item );
			}
		}
	}

	/**
	 * Add order item.
	 *
	 * @param OrderItem|array $order_item Order item.
	 *
	 * @return void
	 */
	public function add_item( $order_item ) {
		if ( ! ( $order_item instanceof OrderItem ) ) {
			$order_item = new OrderItem( $order_item );
		}

		$this->order_items[] = $order_item;
	}

	/**
	 * Get order items.
	 *
	 * @return array
	 */
	public function get_order_items() {
		return $this->order_items;
	}

	/**
	 * Get JSON.
	 *
	 * @return object|null
	 */
	public function get_json() {
		$data = array();

		$items = $this->get_order_items();

		foreach ( $items as $item ) {
			$amount = $item->get_amount();
			$tax    = $item->get_tax();

			$item_data = array(
				'id'          => $item->get_id(),
				'name'        => $item->get_name(),
				'description' => $item->get_description(),
				'quantity'    => $item->get_quantity(),
				'amount'      => ( $amount instanceof Money ) ? $amount->get_json() : null,
				'tax'         => ( $tax instanceof Money ) ? $tax->get_json() : null,
				'category'    => $item->get_category(),
				'vatCategory' => $item->get_vat_category(),
			);

			$item_data = array_filter( $item_data );

			if ( ! empty( $item_data ) ) {
				$data[] = (object) $item_data;
			}
		}

		if ( empty( $data ) ) {
			return null;
		}

		return $data;
	}

	/**
	 * Get signature data.
	 *
	 * @return array
	 */
	public function get_signature_data() {
		$data = array();

		$order_items = $this->get_order_items();

		foreach ( $order_items as $item ) {
			// Optional ID.
			$item_id = $item->get_id();

			if ( ! empty( $item_id ) ) {
				$data[] = $item_id;
			}

			// Required fields.
			$data[] = $item->get_name();
			$data[] = $item->get_description();
			$data[] = $item->get_quantity();
			$data[] = $item->get_amount()->get_currency();
			$data[] = $item->get_amount()->get_amount();

			// Required tax field.
			$tax = $item->get_tax();

			if ( empty( $tax ) ) {
				$data[] = $tax;
			} else {
				$data[] = $tax->get_currency();
				$data[] = $tax->get_amount();
			}

			// Required category.
			$data[] = $item->get_category();

			// Optional VAT category.
			$vat_category = $item->get_vat_category();

			if ( ! empty( $vat_category ) ) {
				$data[] = $vat_category;
			}
		}

		return $data;
	}
}
