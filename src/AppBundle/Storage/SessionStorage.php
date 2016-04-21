<?php

namespace AppBundle\Storage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;


class SessionStorage implements StorageInterface {

	/**
	 * @var SessionInterface
	 */
	protected $session;

	public function __construct(SessionInterface $session) {
		$this->session = $session;
	}

	/**
	 * @return boolean
	 */
	public function has($name) {
		return $this->session->has($name);
	}

	/**
	 * @return void
	 */
	public function set($name, $value) {
		return $this->session->set($name, $value);
	}

	/**
	 * @return mixed
	 */
	public function get($name, $default = null) {
		return $this->session->get($name, $default);
	}

	/**
	 * @return array
	 */
	public function all() {
		return $this->session->all();
	}

	/**
	 * @return void
	 */
	public function remove($name) {
		return $this->session->remove($name);
	}

	public function destroy() {
		return $this->session->clear();
	}
}