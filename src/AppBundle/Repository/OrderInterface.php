<?php

namespace AppBundle\Repository;

use Symfony\Component\Security\Core\User\UserInterface as CoreUserInterface;

interface OrderInterface {

	/**
	 * @param array $items
	 * @param float $total
	 * @param CoreUserInterface $user
	 *
	 * @return boolean
	 */
	public function process(array $items, $total, CoreUserInterface $user);


}