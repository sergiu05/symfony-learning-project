<?php

namespace AppBundle\Repository;

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

}
