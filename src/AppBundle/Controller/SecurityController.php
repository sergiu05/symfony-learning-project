<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

# http://symfony.com/doc/current/cookbook/security/form_login_setup.html
class SecurityController extends Controller {

	/**
	 * @Route("/login", name="security_login_form")
	 * @Method("GET")
	 */
	public function loginAction() {

		$authenticationUtils = $this->get('security.authentication_utils');	

		return $this->render(
			'security/login.html.twig',
			array(
				'last_username' => $authenticationUtils->getLastUsername(),
				'error' => $authenticationUtils->getLastAuthenticationError()
			)
		);
	}

	/**
	 * This is the route the login form submits to. But this will never be executed as
	 * Symfony will intercept this first and handle the login automatically.
	 *
	 * @Route("/login_check", name="security_login_check")
	 */
	public function loginCheckAction() {
		throw new \Exception("This should never be reached!");
	}

	/**
	 * This is the route the login form submits to. But this will never be executed as
	 * Symfony will intercept this first and handle the login automatically.
	 *
	 * @Route("/logout", name="security_logout")
	 */
	public function logoutAction() {
		throw new \Exception("This should never be reached!");
	}
}
