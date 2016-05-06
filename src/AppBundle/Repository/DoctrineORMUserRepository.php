<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DoctrineORMUserRepository extends \Doctrine\ORM\EntityRepository implements UserInterface {

	public function all() {
		return $this->findAll();
	}

	public function getById($id) {
		return $this->find($id);
	}

	public function save(User $user, $insert = true) {
		if ($insert) {
			$this->getEntityManager()->persist($user);	
		}		
		$this->getEntityManager()->flush();
	}

	public function delete(User $user) {
		$this->getEntityManager()->remove($user);
		$this->getEntityManager()->flush();
	}
}
