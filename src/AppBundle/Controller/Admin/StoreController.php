<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\User;


use AppBundle\Form\UserUpdateStatusType;

/**
 * @Route("/admin")
 * @Security("has_role('ROLE_ADMIN')")
 */
class StoreController extends Controller {

	/**
	 * @Route("/", name="admin_user_index")
	 */
	public function indexAction() {

		$repository = $this->getDoctrine()->getRepository('AppBundle:User');
		$users = $repository->findAll();
 
		return $this->render("admin/users/index.html.twig", ['users' => $users]);
	}

	/**
	 * @Route("/genres", name="admin_album_index")
	 */
	public function getAlbumsAction() {

	}

	/**
	 * @Route("/albums", name="admin_genre_index")
	 */
	public function getGenresAction() {

	}

	/**
	 * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="admin_user_edit")
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

}