<?php

namespace AppBundle\Cart;

interface CartInterface {

	/**
	 * @return array Array of CartItem instances
	 */
	public function getCartItems();

	/**	 	 
	 * @param EntityInterface $entity
	 * @param int $qty (default = 1)
	 * @param boolean $add (default = true)
	 * @return void
	 */
	public function updateCart(EntityInterface $entity, $qty = 1, $add = true);

	/**
	 * @param EntityInterface $entity
	 * @param int $qty
	 * @return void
	 */
	public function addToCart(EntityInterface $entity, $qty);

	/**
	 * @param mixed $itemOrId
	 * @return void
	 */
	public function removeFromCart($itemOrId);

	/**
	 * Get the total number of products in the cart	 
	 *
	 * @return int
	 */
	public function getCount();

	/**
	 * @return void
	 */ 
	public function emptyCart();

	/**
	 * Get the total value
	 */
	public function getTotalValue();
}