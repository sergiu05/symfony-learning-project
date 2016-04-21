<?php

namespace AppBundle\Cart;

interface CartItemInterface {

	public function getEntity();

	public function getQuantity();

	public function setQuantity($qty);

	public function getPrice();

}