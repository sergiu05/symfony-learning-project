<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use AppBundle\Repository\GenreInterface;
use AppBundle\Repository\AlbumInterface;
use AppBundle\Services\CommonServices;

/**
 * @Route(service="app.store_controller")
 */
class StoreController 
{	
	/**
	 * The Genre repository instance
 	 *
	 * @var AppBundle\Repository\GenreInterface
	 */
	protected $genres;

	/**
	 * The Album repository instance
	 *
	 * @var AppBundle\Repository\AlbumInterface
	 */
	protected $albums;

	/**
	 * @var AppBundle\Services\CommonServices 	 
	 */
	protected $_services;
	
	/**
	 * Create a new controller instance. Since this controller no longer extends the Symfony base Controller class
	 * some of the former helper methods (e.g. render()) are now injected with an instance of CommonServices class
	 * (http://symfony.com/doc/current/cookbook/controller/service.html#alternatives-to-base-controller-methods)
 	 *
	 * @param GenreInterface $genres
	 * @param AlbumInterface $albums
	 * @param CommonServices $services	 
	 */
	public function __construct(GenreInterface $genres, AlbumInterface $albums, CommonServices $services) {

		$this->genres = $genres;
		$this->albums = $albums;
		$this->_services = $services;

	} 
	
	/**
     * @Route("/", name="homepage")
     */
    public function indexAction() {
    	
    	return $this->_services->getTemplating()->renderResponse('website/homepage.html.twig');        

    }

    /**
     * Display all the musical genders
     *
     * @Route("/store", name="store")
     */
    public function getGenresAction() {    	    	

    	$genres = $this->genres->all();

    	return $this->_services->getTemplating()->renderResponse('website/store-browse.html.twig', ['genres' => $genres]);    	

    }

    /**
     * Display the albums for a specific gender
     *
     * @Route("/browse", name="browse_genre")
     */
    public function getAlbumsAction(Request $request) {    	
    	
    	if ($request->query->has('genre')) {    		
			$albums = $this->albums->getAlbumsFor($request->query->get('genre'));
    		return $this->_services->getTemplating()->renderResponse(
    			'website/store-index.html.twig', 
    			array(
    				'albums' => $albums, 
    				'genre' => $request->query->get('genre')
    			)
    		);
    	} 
		
    	return new RedirectResponse($this->_services->getRouter()->generate('store'));
    }

    /**
     * Display the album details
     *
     * @Route("/details/{album_id}", name="album_details")
	 *
     * @param int $album_id
     *
	 * @return Symfony\Component\HttpFoundation\Response
	 *
     * @throws NotFoundHttpException When an invalid album id is provided
     */
    public function getAlbumDetails($album_id) {

    	if ($album = $this->albums->getById($album_id)) {    	
    		$genres = $this->genres->all();
    		return $this->_services->getTemplating()->renderResponse(
    			'website/store-details.html.twig', 
    			array(
    				'album' => $album, 
    				'genres' => $genres
    			)
    		);
    	}
    	
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('No product found for id ' . $album_id);
    }




}
