<?php

namespace Ucu\MailBundle\Services\Provider;

use Ucu\MailBundle\Service;


interface ProviderInterface {

	/**
	 * Fetch the mail as an instance of Mail
	 *
	 * @param string Template path e.g. AppBundle::
	 * @param array $parameters
	 *
	 * @return object stdClass instance with html and text properties that store the templates
	 */
	public function fetch($template, $parameters = []);
}