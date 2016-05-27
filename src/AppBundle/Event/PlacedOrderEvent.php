<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

class PlacedOrderEvent extends Event {

	/**
	 * @var array
	 */
	protected $items;

	/**
	 * @var float
	 */
	protected $total;

	/**
	 * @var UserInterface
	 */
	protected $user;

	/**
	 * @param array $items
	 */
	public function __construct(array $items, $total, UserInterface $user) {
		$this->items = $items;
		$this->total = $total;
		$this->user = $user;
	}

	/**
	 * @return array
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 * @return float
	 */
	public function getTotal() {
		return $this->total;
	}

	/**
	 * @return UserInterface
	 */
	public function getUser() {
		return $this->user;
	}

}