<?php

namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Since the AppBundle controllers do not longer extend the base Controller class, inject the services
 * that we need using this class
 * (http://symfony.com/doc/current/cookbook/controller/service.html#alternatives-to-base-controller-methods)
 */
class CommonServices {

	/**
	 * @var Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
	 */
	protected $templating;

	/**
	 * @var Symfony\Bundle\FrameworkBundle\Routing\Router
	 */
	protected $router;

	/** 
	 * @var Symfony\Component\Form\FormFactory
	 */
	protected $form_factory;

	/**
	 * @var Symfony\Component\HttpFoundation\Session\Session
	 */

	public function __construct(EngineInterface $templating, Router $router, FormFactory $form_factory, Session $session) {

		$this->templating 	= $templating;
		$this->router 		= $router;
		$this->form_factory = $form_factory;
		$this->session 		= $session;

	}

	/**
	 * @return Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
	 */
	public function getTemplating() {

		return $this->templating;

	}

	/**
	 * @return Symfony\Bundle\FrameworkBundle\Routing\Router
	 */
	public function getRouter() {

		return $this->router;

	}

	/**
	 * @return Symfony\Component\Form\FormFactory
	 */
	public function getFormFactory() {

		return $this->form_factory;

	}

	public function addFlash($type, $message) {

		$this->session->getFlashBag()->add($type, $message);

	}
}