<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity\Album;
use AppBundle\Repository\AlbumInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Controller as service
 *
 * @Route(service="app.shopping_controller")
 */
class ShoppingController {

	use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

	/**
	 * @var AlbumInterface Album repository implementation
	 */
	protected $albums;

	public function __construct(AlbumInterface $albums) {
		$this->albums = $albums;
	}	

	/**
     * Display the album details
     *
     * @Route("/add/{album_id}", name="add_to_cart")
	 *
     * @param int $album_id
     *
	 * @return Symfony\Component\HttpFoundation\Response
	 *
     * @throws NotFoundHttpException When an invalid album id is provided
     */
	public function addProduct(Request $request, $album_id) {

		if (!$this->checkCsrf('ajax', $request->query->get('_token'))) {
			throw new AccessDeniedException('Invalid csrf token');
		}
		
		$album = $this->albums->getById($album_id);

		if (!$album) {
			throw $this->NotFoundHttpException('No album with id ' . $album_id);
		}

		if (!$this->container->has('ucu_cart.cart')) {
            throw new \LogicException('Cart bundle is not enabled in your application.');			
		}

		$this->container->get('ucu_cart.cart')->addToCart($album, 1);

		return new JsonResponse(['total' => $this->container->get('ucu_cart.cart')->getCount()]);

	}

	protected function checkCsrf($id, $token) {

		if (!$this->container->has('security.csrf.token_manager')) {
            throw new \LogicException('CSRF protection is not enabled in your application.');
        }

        return $this->container->get('security.csrf.token_manager')->isTokenValid(new CsrfToken($id, $token));
	}

	public function getCountAction() {
		return new Response($this->container->get('ucu_cart.cart')->getCount());
		return $this->container->get('templating')->render(
			'partials/count.html.twig', 
			['count' => $this->container->get('ucu_cart.cart')->getCount()]
		);
	}


}
