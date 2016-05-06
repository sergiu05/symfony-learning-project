<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity\Album;
use AppBundle\Repository\AlbumInterface;

/**
 * Controller as service
 *
 * @Route(service="app.shopping_controller")
 */
class ShoppingController extends Controller {

	/**
	 * @var Album
	 */
	protected $albums;

	public function __construct(AlbumInterface $albums) {
		$this->albums = $albums;
	}

	/**
     * Display the album details
     *
     * @Route("/add/{album_id}", name="add_to_cart")
	 *
     * @param int $album_id
     *
	 * @return Symfony\Component\HttpFoundation\Response
	 *
     * @throws NotFoundHttpException When an invalid album id is provided
     */
	public function addProduct($album_id) {

		$album = $this->albums->getById($album_id);

		if (!$album) {
			throw $this->NotFoundHttpException('No album with id ' . $album_id);
		}		

	}


}
