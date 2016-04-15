<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

# http://symfony.com/doc/current/cookbook/doctrine/registration_form.html
class RegistrationController extends Controller {

	/**
	 * @Route("/register", name="user_registration")
	 */
	public function registerAction(Request $request) {

		$user = new User();
		$form = $this->createForm(UserType::class, $user);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$password = $this->get('security.password_encoder')
							->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($password);

			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();

			$this->addFlash(
				'notice',
				'Your account was registered.'
			);

			return $this->redirectToRoute('homepage');
		}

		return $this->render('registration/register.html.twig', ['form' => $form->createView()]);
	}
}