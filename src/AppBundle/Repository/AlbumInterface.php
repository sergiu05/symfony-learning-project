<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Album;

interface AlbumInterface {

	/**
	 * @return Album[]	 
	 */
	public function getAlbumsFor($genre);

	/**
	 * @return Album[]
	 */
	public function getAlbumsWithArtistsAndOrders();

	/**
	 * @return mixed Album or null
	 */
	public function getById($id);

	/**
	 * @return void
	 */
	public function save(Album $album, $insert = true);

	/**
	 * @return void
	 */
	public function delete(Album $album); 

	/**
	 * @return Album
	 */
	public function newEntity();

}
