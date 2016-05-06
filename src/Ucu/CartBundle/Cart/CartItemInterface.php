<?php

namespace Ucu\CartBundle\Cart;

interface CartItemInterface {

	public function getEntity();

	public function getQuantity();

	public function setQuantity($qty);

	public function getPrice();

}