<?php

namespace Ucu\MailBundle\Services\Provider;

use Ucu\MailBundle\Services\Mail;

class TwigProvider implements ProviderInterface {

	/**
	 * @var \Twig_Environment
	 */
	protected $twig;

	/**
	 * @param \Twig_Environment $twig	 
	 */
	public function __construct(\Twig_Environment $twig) {
		$this->twig = $twig;
	}

	/**
	 * @param string $name Template path e.g. AppBundle::Emails::order.html.twig
	 * @param array $parameters
	 *
	 * @return object stdClass instance with html and text properties that store the templates
	 */
	public function fetch($name, $parameters = []) {
		$template = $this->twig->loadTemplate($name);
		
		$ret = new \stdClass();
		$ret->html = $template->renderBlock('body_html', $parameters);
		$ret->text = $template->renderBlock('body_text', $parameters);
		
		return $ret;		
	}
}