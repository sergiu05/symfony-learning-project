<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use AppBundle\Repository\UserInterface as AppUserInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Add an additional authentication system based on URL API token (e.g. /admin?apikey=9tNcGUE7LELM73rcJ996gNHy6em9rkEV).
 * For each user, the api key is stored in the users table.
 * 
 * Learning source: http://symfony.com/doc/current/cookbook/security/guard-authentication.html
 */
class TokenAuthenticator extends AbstractGuardAuthenticator {

	/**
	 * @var AppBundle\Repository\UserInterface A User repository interface implementation
	 */
	private $users;

	/**
	 * @var RouterInterface
	 */
	private $router;

	/**
	 * Create new instance.
	 *
	 * @param AppUserInterface $users	 
	 */
	public function __construct(AppUserInterface $users) {
		$this->users = $users;
	}

	/**
	 * @param RouterInterface $router;
	 */
	public function setRouter(RouterInterface $router) {
		$this->router = $router;
	}

	/**
	 * Get the authentication credentials from the request. If null is returned, authentication will be skipped.
	 *
	 * @param Request $request
	 * @return array 
	 */
	public function getCredentials(Request $request) {

		if (!$token = $request->query->get('apikey')) {
			return null;
		}

		return array(
			'token' => $token
		);
	}

	/**
	 * Return a UserInterface object based on the credentials returned by getCredentials()
	 *
	 * @param array $crendentials
	 * @param UserProviderInterface $userProvider
	 * @return UserInterface
	 */
	public function getUser($credentials, UserProviderInterface $userProvider) {

		$apiKey = $credentials['token'];

		return $this->users->getByApiKey($apiKey);

	}

	/**
	 * Check if the credentials returned by getCredentials() are valid. For api token authentication
	 * no credential check is needed
	 * 
	 * @return boolean
	 */
	public function checkCredentials($credentials, UserInterface $user) {
		return true;
	}

	/**
     * {@inheritdoc}
     */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
		return null;
	}

	/**
     * {@inheritdoc}
     */
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {		
		return new RedirectResponse($this->router->generate('homepage'));		
	}

	/**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null) {
    	return new Response('provide api key', 403);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe() {
    	return false;
    }
}
