<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Builder implements ContainerAwareInterface {

	use ContainerAwareTrait;	

	public function mainMenu(FactoryInterface $factory, array $options) {
		
		$menu = $factory->createItem('root');

		$menu->addChild('Home', ['route' => 'homepage']);

		$menu->addChild('Register', ['route' => 'user_registration']);

		return $menu;

	}
}
