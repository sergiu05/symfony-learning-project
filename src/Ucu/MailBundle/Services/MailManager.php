<?php

namespace Ucu\MailBundle\Services;

use Ucu\MailBundle\Services\Mailer\MailerInterface;
use Ucu\MailBundle\Services\Provider\ProviderInterface;
use Psr\Log\LoggerInterface;


/**
 * Learning sources:
 * (1) http://intelligentbee.com/blog/2015/12/02/send-emails-in-symfony2-the-right-way/
 * (2) http://www.inanzzz.com/index.php/post/l8km/sending-emails-with-swiftmailer-and-mailtrap-in-symfony
 * (3) https://github.com/christoph-hautzinger/SystemMailBundle
 * Thanks!
 */

class MailManager implements MailManagerInterface {

	/**
	 * @var MailerInterface
	 */
	protected $mailer;

	/**
	 * @var ProviderInterface
	 */
	protected $templating;

	/**
	 * @var LoggerInterface
	 */
	protected $logger;

	/**
	 * @var array
	 */
	protected $defaults;

	/**	 
	 * @param MailerInterface $mailer	 
	 * @param EngineInterface $templating
	 * @param LoggerInterface $logger
	 */
	public function __construct(MailerInterface $mailer, ProviderInterface $templating, LoggerInterface $logger, array $defaults = []) {		
		$this->mailer 		= $mailer;
		$this->templating 	= $templating;
		$this->logger 		= $logger;
		$this->defaults 	= $defaults;
	}

	/**	 
	 * @param string $name
	 * @param array $parameters 	 	 
	 *
	 * @return boolean
	 */
	public function send($name, $parameters = []) {
		$templates = $this->templating->fetch($name, $parameters);		

		# merge default settings provided via config.yml with $parameters
		$settings = array_intersect_key($parameters + $this->defaults, $this->defaults);
		
		return $this->mailer->send($templates, $settings);
	}
	

}