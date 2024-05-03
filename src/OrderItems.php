<?php
/**
 * Order items.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use JsonSerializable;

/**
 * Order items.
 *
 * @author  Reüel van der Steege
 * @version 2.1.8
 * @since   2.0.3
 */
final class OrderItems implements JsonSerializable {
	/**
	 * Order items.
	 *
	 * @var array<OrderItem>
	 */
	private $order_items = [];

	/**
	 * Construct order results message.
	 *
	 * @param array<OrderItem> $items Order items.
	 */
	public function __construct( $items = null ) {
		if ( \is_array( $items ) ) {
			foreach ( $items as $item ) {
				$this->add_item( $item );
			}
		}
	}

	/**
	 * Create and add new order item.
	 *
	 * @param string $name     Name.
	 * @param int    $quantity Quantity.
	 * @param Money  $amount   Amount.
	 * @param string $category Category.
	 * @return OrderItem
	 * @throws \InvalidArgumentException Throws invalid argument exception when arguments are invalid.
	 */
	public function new_item( $name, $quantity, Money $amount, $category ) {
		$item = new OrderItem( $name, $quantity, $amount, $category );

		$this->add_item( $item );

		return $item;
	}

	/**
	 * Add order item.
	 *
	 * @param OrderItem $item Order item.
	 * @return void
	 */
	public function add_item( OrderItem $item ) {
		$this->order_items[] = $item;
	}

	/**
	 * Get order items.
	 *
	 * @return array<OrderItem>
	 */
	public function get_order_items() {
		return $this->order_items;
	}

	/**
	 * Get JSON.
	 *
	 * @return array<object>|null
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		$data = \array_map(
			static function ( OrderItem $item ) {
				return $item;
			},
			$this->get_order_items()
		);

		return $data;
	}

	/**
	 * Get signature fields.
	 *
	 * @param array<string> $fields Fields.
	 * @return array<string>
	 */
	public function get_signature_fields( $fields = [] ) {
		foreach ( $this->get_order_items() as $item ) {
			$fields = $item->get_signature_fields( $fields );
		}

		return $fields;
	}
}
