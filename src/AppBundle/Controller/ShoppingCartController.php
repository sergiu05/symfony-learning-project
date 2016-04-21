<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ShoppingCartController extends Controller
{	
	/**
     * @Route("/", name="homepage")
     */
    public function indexAction() {

        return $this->render('website/homepage.html.twig');

    }

    /**
     * Display all the musical genders
     *
     * @Route("/store", name="store")
     */
    public function getGenresAction() {    	

    	$repository = $this->getDoctrine()->getRepository('AppBundle:Genre');

    	$genres = $repository->findAll();

    	return $this->render('website/store-browse.html.twig', array('genres' => $genres));

    }

    /**
     * Display the albums for a specific gender
     *
     * @Route("/browse", name="browse_genre")
     */
    public function getAlbumsAction(Request $request) {
    	
    	$repository = $this->getDoctrine()->getRepository('AppBundle:Album');
    	
    	if ($request->query->has('genre')) {    		
			$albums = $repository->getAlbumsFor($request->query->get('genre'));
    		return $this->render('website/store-index.html.twig', array('albums' => $albums, 'genre' => $request->query->get('genre')));
    	} 
		
    	return $this->redirectToRoute('store');    	
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

    	$albumRepository = $this->getDoctrine()->getRepository('AppBundle:Album');
    	$genreRepository = $this->getDoctrine()->getRepository('AppBundle:Genre');

    	if ($album = $albumRepository->find($album_id)) {    		
    		$genres = $genreRepository->findAll();
    		return $this->render('website/store-details.html.twig', array('album' => $album, 'genres' => $genres));
    	}

    	throw $this->createNotFoundException(
            'No product found for id ' . $album_id
        );
    }




}
