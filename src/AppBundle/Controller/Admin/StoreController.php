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


use AppBundle\Form\UserUpdateStatusType;

/**
 * @Route("/admin")
 * @Security("has_role('ROLE_ADMIN')")
 */
class StoreController extends Controller {

	/**
	 * @Route("/users", name="admin_user_index")
	 * @Route("/", name="admin_index")
	 */
	public function indexAction() {

		$repository = $this->getDoctrine()->getRepository('AppBundle:User');
		$users = $repository->findAll();
 
		return $this->render("admin/users/index.html.twig", ['users' => $users]);
	}

	/**
	 * @Route("/genres", name="admin_genre_index")
	 * @Method({"GET", "POST"})
	 */
	public function getGenresAction() {

		$repository = $this->getDoctrine()->getRepository('AppBundle:Genre');
		$genres = $repository->getGenresWithAlbums();		
		$forms = [];
		
		foreach($genres as $genre) {						
			$forms[$genre->getId()] = $genre->isDeletable() ? $this->createGenreDeleteForm($genre)->createView() : null;
		}

		return $this->render("admin/genres/index.html.twig", compact('genres', 'forms'));
	}

	private function createGenreDeleteForm(Genre $genre) {
		return $this->createFormBuilder()
					->setAction($this->generateUrl('admin_genre_delete', ['id' => $genre->getId()] ))
					->setMethod('DELETE')
					->getForm();
	}

	/**
	 * @Route("/genres/new", name="admin_genre_new")
	 */
	public function newGenreAction(Request $request) {
		$genre = new Genre;

		$form = $this->createForm('AppBundle\Form\GenreType', $genre);					

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			$genre->upload();

			$em->persist($genre);
			$em->flush();

			$this->addFlash('success', 'New genre with id '.$genre->getId().' created.');

			return $this->redirectToRoute('admin_genre_index');
		}

		return $this->render('admin/genres/new.html.twig', array(
			'genre' => $genre,
			'form' => $form->createView()
		));
	}


	/**
	 * @Route("/genres/{id}/edit", name="admin_genre_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editGenreAction(Request $request, Genre $genre) {
		$form = $this->createForm('AppBundle\Form\GenreType', $genre, ["method" => "POST"]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();

			if ($genre->getFile()) {
				$genre->upload();
			}

			$em->flush();

			$this->addFlash('success', 'Genre '.$genre->getName().' was updated successfully.');

			return $this->redirectToRoute('admin_genre_index');
		}

		return $this->render('admin/genres/edit.html.twig', [
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
			$em = $this->getDoctrine()->getManager();
			$em->remove($genre);
			$em->flush();

			$this->addFlash('success', "Genre deleted successfully.");
		}

		return $this->redirectToRoute('admin_genre_index');
	}


	/**
	 * @Route("users/{id}/edit", requirements={"id" = "\d+"}, name="admin_user_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editUserAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$repository = $this->getDoctrine()->getRepository('AppBundle:User');
		$user = $em->getRepository('AppBundle:User')->find($id);

		if (!$user) {
			throw $this->createNotFoundException('No user found for id '.$id);
		}

		$editForm = $this->createForm(UserUpdateStatusType::class, $user);
		
		$editForm->handleRequest($request);

		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$em->flush();

			$this->addFlash('success', "User #{$user->getId()} updated.");
			return $this->redirectToRoute('admin_user_index');
		}

		return $this->render('admin/users/edit.html.twig', array(
			'user'			=> $user,
			'edit_form'		=> $editForm->createView()
		));
	}

	/**
	 * @Route("/albums", name="admin_album_index")
	 */
	public function getAlbumsAction() {
		$repository = $this->getDoctrine()->getRepository('AppBundle:Album');
		$albums = $repository->getAlbumsWithArtistsAndOrders();

		$forms = [];
		foreach($albums as $album) {
			$forms[$album->getId()] = $album->isDeletable() ? $this->createDeleteAlbumForm($album)->createView() : null;
		}

		return $this->render('admin/albums/index.html.twig', compact('albums', 'forms'));
	}

	private function createDeleteAlbumForm(Album $album) {
		return $this->createFormBuilder()
					->setAction($this->generateUrl('admin_album_delete', ['id' => $album->getId()]))
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
		$album = new Album;
		
		$form = $this->createForm('AppBundle\Form\AlbumType', $album);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->persist($album);
			$em->flush();

			$this->addFlash('success', 'Album created successfully.');
			return $this->redirectToRoute('admin_album_index');
		}

		return $this->render('admin/albums/new.html.twig', ['form' => $form->createView()]);

	}

	/**
	 * @Route("/albums/{id}/edit", requirements={"id" = "\d+"}, name="admin_album_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editAlbumAction(Album $album, Request $request) {
		$editForm = $this->createForm('AppBundle\Form\AlbumType', $album);
		$editForm->handleRequest($request);
		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$em = $this->getDoctrine()->getManager();
			$em->flush();

			$this->addFlash("success", "Album #{$album->getId()} was updated.");
			return $this->redirectToRoute('admin_album_index');
		}
		return $this->render('admin/albums/edit.html.twig', [
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
			$em = $this->getDoctrine()->getManager();
			$em->remove($album);
			$em->flush();

			$this->addFlash('success', 'Album was deleted.');
		}
		return $this->redirectToRoute('admin_album_index');
	}

}