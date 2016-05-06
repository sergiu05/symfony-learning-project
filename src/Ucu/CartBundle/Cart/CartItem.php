<?php

namespace Ucu\CartBundle\Cart;

class CartItem implements CartItemInterface {

	protected $entity;

	protected $qty;

	public function __construct(EntityInterface $entity, $qty) {
		$this->entity = $entity;
		$this->qty = intval($qty);
	}

	public function getEntity() {
		return $this->entity;
	}

	public function getQuantity() {
		return $this->qty;
	}

	public function setQuantity($qty) {
		$this->qty = intval($qty);
	}

	public function getPrice() {
		return $this->entity->getPrice();
	}

}