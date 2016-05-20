<?php

namespace Ucu\CartBundle\Event;

use Ucu\CartBundle\Cart\EntityInterface;
use Symfony\Component\EventDispatcher\Event;

class CartItemEvent extends Event {

	protected $item;

	public function __construct(EntityInterface $item) {
		$this->item = $item;
	}

	public function getItem() {
		return $this->item;
	}
}