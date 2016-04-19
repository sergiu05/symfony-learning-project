<?php

namespace AppBundle\Repository;

/**
 * GenreRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GenreRepository extends \Doctrine\ORM\EntityRepository
{
	public function getGenresWithAlbums() {
		
		$qb = $this->getEntityManager()->createQueryBuilder();

		return $qb->select(array('g', 'a'))
					->from('AppBundle\Entity\Genre', 'g')
					->leftJoin('g.albums', 'a')
					->getQuery()
					->getResult();
	}

}
