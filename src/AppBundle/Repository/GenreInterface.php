<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Genre;

interface GenreInterface {
	
	/**
	 * @return Genre[]
	 */
	public function all();

	 /**
	  * @return Genre[]
	  */
	public function getGenresWithAlbums();

	/**
	 * @return void
	 */
	public function save(Genre $genre, $insert = true);

	/**
	 * @return void
	 */
	public function delete(Genre $genre); 

	/**
	 * @return Genre
	 */
	public function newEntity();

}