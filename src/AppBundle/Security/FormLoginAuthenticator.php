<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;




/**
 * The tradition form authentication done via Guard component
 *
 * Learning source: https://knpuniversity.com/screencast/guard/login-form
 */
class FormLoginAuthenticator extends AbstractFormLoginAuthenticator {

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $encoder;

	/**
	 * @var UrlGeneratorInterface
	 */
	private $urlGenerator;

	/**
	 * @var CsrfTokenManagerInterface 
	 */
	private $csrfTokenManager;

	/**
	 *
	 * @param UserPasswordEncoderInterface $encoder
	 * @param UrlGeneratorInterface $urlGenerator
	 */
	public function __construct(UserPasswordEncoderInterface $encoder, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager) {
		$this->encoder = $encoder;
		$this->urlGenerator = $urlGenerator;
		$this->csrfTokenManager = $csrfTokenManager;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getLoginUrl() {		
		return $this->urlGenerator->generate('security_login_form');
	}

	/**
	 * {@inheritdoc}
	 */
	protected function getDefaultSuccessRedirectUrl() {
		return $this->urlGenerator->generate('homepage');
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCredentials(Request $request) {
		if ($request->getPathInfo() != '/login_check') {
			return;
		}

		$csrfToken = $request->request->get('_csrf_token');
		if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
			throw new InvalidCsrfTokenException('Invalid csrf token.');
		}
		$username = $request->request->get('_username');
		$request->getSession()->set(Security::LAST_USERNAME, $username);
		$password = $request->request->get('_password');

		return [
			'username' => $username,
			'password' => $password
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getUser($credentials, UserProviderInterface $userProvider) {
		$username = $credentials['username'];

		return $userProvider->loadUserByUsername($username);
	}

	/**
	 * {@inheritdoc}
	 */
	public function checkCredentials($credentials, UserInterface $user) {
		$plainPassword = $credentials['password'];

		if (!$this->encoder->isPasswordValid($user, $plainPassword)) {
			throw new BadCredentialsException();
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function start(Request $request, AuthenticationException $authException = null) {
		return new RedirectResponse($this->urlGenerator->generate('security_login_form'));
	}
}
