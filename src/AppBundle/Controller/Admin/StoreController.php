<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\User;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Album;
use AppBundle\Repository\GenreInterface;
use AppBundle\Repository\AlbumInterface;
use AppBundle\Repository\UserInterface;
use AppBundle\Services\CommonServices;
use AppBundle\Form\UserUpdateStatusType;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;



/**
 * Controller as service
 *
 * @Route("/admin", service="app.admin.store_controller")
 * @Security("has_role('ROLE_ADMIN')")
 */
class StoreController {

	/**
	 * The Genre repository implementation
	 *
	 * @var AppBundle\Repository\GenreInterface
	 */
	protected $genres;

	/**
	 * The Album repository implementation
	 *
	 * @var AppBundle\Repository\AlbumInterface
	 */
	protected $albums;

	/**
	 * The User repository implementation
	 *
	 * @var AppBundle\Repository\UserInterface
	 */

	/**
	 * @var AppBundle\Services\CommonServices
	 */
	protected $_services;

	/**
	 * Create a new controller instance
	 *
	 * @param GenreInterface $genres
	 * @param AlbumInterface $albums
	 * @param UserInterface $users
	 * @param CommonServices $services
	 */
	public function __construct(GenreInterface $genres, AlbumInterface $albums, UserInterface $users, CommonServices $services) {

		$this->genres = $genres;
		$this->albums = $albums;
		$this->users = $users;
		$this->_services = $services;

	}
	/**
	 * @Route("/users", name="admin_user_index")
	 * @Route("/", name="admin_index")
	 */
	public function indexAction() {

		$users = $this->users->all();
 
		return $this->_services->getTemplating()->renderResponse(
			"admin/users/index.html.twig", 
			['users' => $users]
		);
	}

	/**
	 * @Route("/genres", name="admin_genre_index")
	 * @Method({"GET", "POST"})
	 */
	public function getGenresAction() {

		$genres = $this->genres->getGenresWithAlbums();		
		$forms = [];
		
		foreach($genres as $genre) {						
			$forms[$genre->getId()] = $genre->isDeletable() ? $this->createGenreDeleteForm($genre)->createView() : null;
		}

		return $this->_services->getTemplating()->renderResponse(
			"admin/genres/index.html.twig", 
			compact('genres', 'forms')
		);
	}

	private function createGenreDeleteForm(Genre $genre) {
		return $this->_services->getFormFactory()
					->createBuilder()
					->setAction($this->_services->getRouter()->generate('admin_genre_delete', ['id' => $genre->getId()] ))
					->setMethod('DELETE')
					->getForm();
	}

	/**
	 * @Route("/genres/new", name="admin_genre_new")
	 */
	public function newGenreAction(Request $request) {
		$genre = $this->genres->newEntity();
		
		$form = $this->_services->getFormFactory()->create('AppBundle\Form\GenreType', $genre);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			
			$genre->upload();

			$this->genres->save($genre);			

			$this->_services->addFlash('success', 'New genre with id '.$genre->getId().' created.');

			return new RedirectResponse($this->_services->getRouter()->generate('admin_genre_index'));
		}

		return $this->_services->getTemplating()->renderResponse(
			'admin/genres/new.html.twig', 
			array(
				'genre' => $genre,
				'form' => $form->createView()
			)
		);
	}


	/**
	 * @Route("/genres/{id}/edit", name="admin_genre_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editGenreAction(Request $request, Genre $genre) {
		$form = $this->_services->getFormFactory()->create('AppBundle\Form\GenreType', $genre, ["method" => "POST"]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {			

			if ($genre->getFile()) {
				$genre->upload();
			}

			$this->genres->save($genre, false);

			$this->_services->addFlash('success', 'Genre '.$genre->getName().' was updated successfully.');
			
			return new RedirectResponse($this->_services->getRouter()->generate('admin_genre_index'));
		}

		return $this->_services->getTemplating()->renderResponse(
			'admin/genres/edit.html.twig', [
				'genre' => $genre,
				'edit_form' => $form->createView()
			]);
	}

	/**
	 * @Route("/genres/{id}", name="admin_genre_delete")
	 * @Method("DELETE") 
	 * @Security("genre.isDeletable()")
	 */
	public function deleteGenreAction(Request $request, Genre $genre) {
		$form = $this->createGenreDeleteForm($genre);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->genres->delete($genre);
			$this->_services->addFlash('success', "Genre deleted successfully.");
		}

		return new RedirectResponse($this->_services->getRouter()->generate('admin_genre_index'));
	}


	/**
	 * @Route("users/{id}/edit", requirements={"id" = "\d+"}, name="admin_user_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editUserAction($id, Request $request) {
		
		$user = $this->users->find($id);

		if (!$user) {
			throw new NotFoundHttpException('No user found for id '.$id);
		}

		$editForm = $this->_services->getFormFactory()->create(UserUpdateStatusType::class, $user);
		
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			
			$this->users->save($user, false);

			$this->_services->addFlash('success', "User #{$user->getId()} updated.");
			return new RedirectResponse($this->_services->getRouter()->generate('admin_user_index'));
		}

		return $this->_services->getTemplating()->renderResponse(
			'admin/users/edit.html.twig', 
			array(
				'user'			=> $user,
				'edit_form'		=> $editForm->createView()
			)
		);
	}

	/**
	 * @Route("/albums", name="admin_album_index")
	 */
	public function getAlbumsAction() {
		
		$albums = $this->albums->getAlbumsWithArtistsAndOrders();

		$forms = [];
		foreach($albums as $album) {
			$forms[$album->getId()] = $album->isDeletable() ? $this->createDeleteAlbumForm($album)->createView() : null;
		}

		return $this->_services->getTemplating()->renderResponse('admin/albums/index.html.twig', compact('albums', 'forms'));
	}

	private function createDeleteAlbumForm(Album $album) {
		return $this->_services->getFormFactory()
					->createBuilder()
					->setAction($this->_services->getRouter()->generate('admin_album_delete', ['id' => $album->getId()]))
					->setMethod('DELETE')
					->getForm();
	}

	/**
	 * Creates a new Album entity.
	 *
	 * @Route("/albums/create", name="admin_album_new")
	 * @Method({"GET", "POST"})
	 */
	public function createAlbumAction(Request $request) {
		$album = $this->albums->newEntity();
		
		$form = $this->_services->getFormFactory()->create('AppBundle\Form\AlbumType', $album);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			
			$this->albums->save($album);			

			$this->_services->addFlash('success', 'Album created successfully.');
			return new RedirectResponse($this->_services->getRouter()->generate('admin_album_index'));
		}

		return $this->_services->getTemplating()->renderResponse('admin/albums/new.html.twig', ['form' => $form->createView()]);		

	}

	/**
	 * @Route("/albums/{id}/edit", requirements={"id" = "\d+"}, name="admin_album_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAlbumAction(Album $album, Request $request) {
		$editForm = $this->createForm('AppBundle\Form\AlbumType', $album);
		$editForm->handleRequest($request);
		if ($editForm->isSubmitted() && $editForm->isValid()) {
			
			$this->albums->save($album, false);

			$this->_services->addFlash("success", "Album #{$album->getId()} was updated.");
			return new RedirectResponse($this->_services->getRouter()->generate('admin_album_index'));
		}
		return $this->_services->getTemplating()->renderResponse('admin/albums/edit.html.twig', [
				'album' => $album,
				'edit_form' => $editForm->createView()
			]);
	}

	/**
	 * @Route("/albums/{id}", requirements={"id"="\d+"}, name="admin_album_delete")
	 * @Method({"DELETE"})
	 * @Security("album.isDeletable()")
	 */
	public function deleteAlbumAction(Album $album, Request $request) {
		$form = $this->createDeleteAlbumForm($album);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			
			$this->albums->delete($album);

			$this->_services->addFlash('success', 'Album was deleted.');
		}
		return new RedirectResponse($this->_services->getRouter()->generate('admin_album_index'));
	}

}