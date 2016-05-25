<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use AppBundle\Entity\Album;
use AppBundle\Repository\AlbumInterface;
use AppBundle\Repository\OrderInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller as service
 *
 * @Route(service="app.shopping_controller")
 */
class ShoppingController implements ContainerAwareInterface {	

	/**
	 * @var AlbumInterface Album repository implementation
	 */
	protected $albums;

	/**
	 * @var OrderInterface Album repository implementation
	 */
	protected $orders;

	/**
     * @var ContainerInterface
     */
    protected $container;    

	public function __construct(AlbumInterface $albums, OrderInterface $orders) {
		$this->albums = $albums;
		$this->orders = $orders;
	}

	/**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @throws LoginException When cart bundle is not included in the service container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        if ( ! ($this->container && $this->container->has('ucu_cart.cart')) ) {
        	throw new \LogicException('Cart bundle is not enabled in your application.');			
        }
    }

	/**
	 * Display cart content
	 *
	 * @Route("/checkout", name="app.checkout")
	 * @Method("GET")
	 */
	public function indexAction() {
		
		return $this->container->get('templating')->renderResponse(
			'website/checkout.html.twig', [
				'cartItems' => $this->container->get('ucu_cart.cart')->getCartItems(),
				'cartTotal' => $this->container->get('ucu_cart.cart')->getTotalValue()
			]
		); 		
	}


	/**
     * Add a product to the cart
     *
     * @Route("/add/{album_id}", name="add_to_cart")
	 * @Method("POST")
	 *
	 * @param Request $request
     * @param int $album_id
     *
	 * @return Symfony\Component\HttpFoundation\Response
	 *
     * @throws NotFoundHttpException When an invalid album id is provided
     * @throws LoginException When cart bundle is not included in the service container
     * @throws AccessDeniedException
     */
	public function addProduct(Request $request, $album_id) {

		if (!$this->checkCsrf('ajax', $request->query->get('_token'))) {
			throw new AccessDeniedException('Invalid csrf token');
		}
		
		$album = $this->albums->getById($album_id);

		if (!$album) {
			throw $this->NotFoundHttpException('No album with id ' . $album_id);
		}
		
		$this->container->get('ucu_cart.cart')->addToCart($album, 1);

		return new JsonResponse(['total' => $this->container->get('ucu_cart.cart')->getCount()]);

	}

	/**
	 * @param string $id
	 * @param string $token
	 *
	 * @return boolean
	 *
	 * @throws LogicException When csrf token manager not included in the service container
	 */
	protected function checkCsrf($id, $token) {

		if (!$this->container->has('security.csrf.token_manager')) {
            throw new \LogicException('CSRF protection is not enabled in your application.');
        }

        return $this->container->get('security.csrf.token_manager')->isTokenValid(new CsrfToken($id, $token));
	}

	public function getCountAction() {
		
		return $this->container->get('templating')->renderResponse(
			'partials/count.html.twig', 
			['count' => $this->container->get('ucu_cart.cart')->getCount()]
		);
	}

	/**
	 * Remove product from cart
	 *
	 * @Route("/removefromcart/{album_id}", 
	 *		name = "app.remove_from_cart", 
	 *		requirements = {
	 * 			"album_id": "\d+"
	 * 		},
	 * 		options = { "expose" = true }
	 * )
	 * @Method("POST")
	 *
	 * @param Request $request
	 * @param int $album_id
	 *
	 * @return JsonRequest
	 */
	public function removeProductAction(Request $request, $album_id) {
		if (!$this->checkCsrf('ajax', $request->query->get('_token'))) {
			throw new AccessDeniedException('Invalid csrf token');
		}

		$album = $this->albums->getById($album_id);

		if (!$album) {
			throw $this->NotFoundHttpException('No album with id ' . $album_id);
		}

		$this->container->get('ucu_cart.cart')->removeFromCart($album_id);

		return new JsonResponse(['count' => $this->container->get('ucu_cart.cart')->getCount()]);


	}

	/**
	 * Update a product from the cart
	 *
	 * @Route("/updatecart/{album_id}/{qty}", 
	 * 		name = "app.update_cart", 
	 *		requirements = {
	 * 			"album_id": "\d+",
	 * 			"qty": "\d+"
	 * 		},
	 *		options = { "expose" = true }
	 * )
	 * @Method("POST")
	 *
	 * @param Request $request
	 * @param int $album_id
	 * @param int $qty
	 *
	 * @return JsonRequest
	 *
	 * @throws AccessDeniedException
	 */
	public function updateCartAction(Request $request, $album_id, $qty) {
		if (!$this->checkCsrf('ajax', $request->query->get('_token'))) {
			throw new AccessDeniedException('Invalid csrf token');
		}

		$album = $this->albums->getById($album_id);

		if (!$album) {
			throw $this->NotFoundHttpException('No album with id ' . $album_id);
		}

		$this->container->get('ucu_cart.cart')->updateCart($album, $qty, false);

		return new JsonResponse(['count' => $this->container->get('ucu_cart.cart')->getCount()]);
	}

	/**
	 * Place the order
	 *
	 * @Route("/process", name="app.process")
	 * @Security("has_role('ROLE_USER')")
	 * @Method("GET")
	 */
	public function processOrderAction(Request $request) {		

		$user = $this->container->get('security.token_storage')->getToken()->getUser();

		if ($this->orders->process(
				$this->container->get('ucu_cart.cart')->getCartItemsAsArray(),
				$this->container->get('ucu_cart.cart')->getTotalValue(),
				$user
			)) {
			$this->container->get('ucu_cart.cart')->emptyCart();
			return $this->container->get('templating')->renderResponse('website/process_ok.html.twig');
		}
		
		return $this->container->get('templating')->renderResponse('website/process_error.html.twig');

	}


}
