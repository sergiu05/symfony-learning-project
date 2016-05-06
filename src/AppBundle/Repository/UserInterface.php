<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;

interface UserInterface {

	/**
	 * @var User[]
	 */
	public function all();

	/**
	 * @var User
	 */
	public function getById($id);

	/**
	 * @return void
	 */
	public function save(User $user, $insert = true);

	/**
	 * @return void
	 */
	public function delete(User $user); 
}