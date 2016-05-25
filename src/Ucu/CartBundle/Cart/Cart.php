<?php

namespace Ucu\CartBundle\Cart;

use Ucu\CartBundle\Storage\StorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Ucu\CartBundle\Event\Events;
use Ucu\CartBundle\Event\CartItemEvent;
use Ucu\CartBundle\Event\CartItemsEvent;

class Cart implements CartInterface {

	/**
	 * Cart session bag name
	 */
	const CART = '__cart__';

	/**
	 * @var StorageInterface	 
	 */
	protected $storage;

	/**
	 * @var EventDispatcherInterface
	 */
	protected $dispatcher;

	/**
	 * Create a new instance and set up its dependency
	 *
	 * @param StorageInterface $storage	
	 * @param EventDispatcherInterface $dispatcher 
	 */
	public function __construct(StorageInterface $storage, EventDispatcherInterface $dispatcher) {
		$this->storage = $storage;
		$this->dispatcher = $dispatcher;
	}
	
	/**
	 * @return array Array of CartItem instances
	 */
	public function getCartItems() {	
		return $this->storage->get(self::CART, []);
	}

	/**
	 * @return array Cart content as array of arrays
	 */
	public function getCartItemsAsArray() {
		$items = $this->getCartItems();
 		array_walk($items, function(&$item, $key) { 			
 			$item = array('qty' => $item->getQuantity(), 'entity' => $item->getEntity()->toArray()); 			
 		});
 		return $items;
	}

	/**
	 * @param EntityInterface #entity
	 * @param int $qty
	 * @param boolean $add optional 
	 */
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

	/**
	 * @param EntityInterface $entity
	 * @param int $qty
	 */
	public function addToCart(EntityInterface $entity, $qty) {

		if ($qty < 1) return;

		# dispatch event Events::ADD_BEFORE 
		$event = new CartItemEvent($entity);
		$this->dispatcher->dispatch(Events::ADD_BEFORE, $event);
		if ($event->isPropagationStopped()) {
			return;
		}

		if ($this->hasItem($entity)) {
			$this->updateCart($entity, $qty);
		} else {
			$newItem = new CartItem($entity, $qty);
			$items = $this->getCartItems();
			$items[$entity->getId()] = $newItem;
			$this->storage->set(self::CART, $items);			
		}

		# dispatch event Events::ADD_AFTER 
		$this->dispatcher->dispatch(Events::ADD_AFTER, $event);
	}

	/**
	 * @param mixed $itemOrId (EntityInterface or int)
	 * @return boolean
	 */
	public function hasItem($itemOrId) {		
		$id = $this->_getId($itemOrId);
		return array_key_exists($id, $this->getCartItems());
	}

	/**
	 * @param mixed $itemOrId (EntityInterface or int)
	 */
	public function removeFromCart($itemOrId) {

		$id = $this->_getId($itemOrId);

		if (!$this->hasItem($id)) {
			return;
		}

		$items = $this->getCartItems();

		# dispatch event Events::REMOVE_BEFORE 
		$event = new CartItemEvent($items[$id]->getEntity());
		$this->dispatcher->dispatch(Events::REMOVE_BEFORE, $event);
		if ($event->isPropagationStopped()) {
			return;
		}

		unset($items[$id]);
		$this->storage->set(self::CART, $items);

		# dispatch event Events::REMOVE_AFTER 
		$this->dispatcher->dispatch(Events::REMOVE_AFTER, $event);
	}

	/**
	 * Count items from the cart
	 *
	 * @return int
	 */
	public function getCount() {
		$count = 0;

		foreach($this->getCartItems() as $item) {
			$count += $item->getQuantity();
		}

		return $count;
	}

	/**
	 * Destroy the cart
	 */
	public function emptyCart() {

		# dispatch event Events::CLEAR_BEFORE 
		$event = new CartItemsEvent($this->getCartItems());
		$this->dispatcher->dispatch(Events::CLEAR_BEFORE, $event);
		if ($event->isPropagationStopped()) {
			return;
		}

		$this->storage->remove(self::CART);

		# dispatch event Events::CLEAR_AFTER
		$this->dispatcher->dispatch(Events::CLEAR_AFTER, $event);
	}


	/**
	 * @return float
	 */
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
