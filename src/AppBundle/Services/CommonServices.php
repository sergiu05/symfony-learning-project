<?php

namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

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

	public function __construct(EngineInterface $templating, Router $router) {

		$this->templating = $templating;
		$this->router = $router;

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
}