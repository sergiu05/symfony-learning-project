<?php

namespace AppBundle\Cart;

use AppBundle\Storage\StorageInterface;

class Cart implements CartInterface {

	/**
	 * Cart session bag
	 */
	const CART = '__cart__';

	/**
	 * @var StorageInterface	 
	 */
	protected $storage;

	/**
	 * Create a new instance and set up its dependency
	 *
	 * @param StorageInterface $storage	 
	 */
	public function __construct(StorageInterface $storage) {
		$this->storage = $storage;
	}
	
	public function getCartItems() {	
		return $this->storage->get(self::CART, []);
	}

	public function updateCart(EntityInterface $entity, $qty = 1, $add = true) {
		
		$items = $this->getCartItems();
		$item = $items[$entity->getId()];
		if ($add) {			
			$item->setQuantity($item->getQuantity() + $qty); 
		} else {
			$item->setQuantity($qty);
		}
		$this->storage->set(self::CART, $items);
	}

	public function addToCart(EntityInterface $entity, $qty) {

		if ($qty < 1) return;

		if ($this->hasItem($entity)) {
			$this->updateCart($entity, $qty);
		} else {
			$newItem = new CartItem($entity, $qty);
			$items = $this->getCartItems();
			$items[$entity->getId()] = $newItem;
			$this->storage->set(self::CART, $items);			
		}
	}


	public function hasItem($itemOrId) {		
		$id = $this->_getId($itemOrId);
		return array_key_exists($id, $this->getCartItems());
	}


	public function removeFromCart($itemOrId) {

		$id = $this->_getId($itemOrId);

		if (!$this->hasItem($id)) {
			return;
		}

		$items = $this->getCartItems();
		unset($items[$id]);
		$this->storage->set(self::CART, $items);
	}

	
	public function getCount() {
		$count = 0;

		foreach($this->getCartItems() as $item) {

		}

		return $count;
	}


	public function emptyCart() {
		$this->storage->remove(self::CART);
	}


	public function getTotalValue() {
		$total = 0;

		foreach($this->getCartItems() as $item) {
			$total += $item->getPrice() * $item->getQuantity();
		}

		return $total;
	}

	/**
	 * Return the id of current entity or the argument provided
	 *
	 * @param mixed $itemOrId (EntityInterface or int)
	 * @return int
	 */
	private function _getId($itemOrId) {	
		return $itemOrId instanceof EntityInterface ? $itemOrId->getId() : intval($itemOrId);
	}
	
}
