<?php

namespace Ucu\MailBundle\Services\Mailer;

use Ucu\MailBundle\Services\Mail;

class SwiftMailer implements MailerInterface {

	/**
	 * @var \Swift_Mailer
	 */
	protected $mailer;

	/**
	 * @param \Swift_Mailer $mailer
	 */
	public function __construct(\Swift_Mailer $mailer) {
		$this->mailer = $mailer;
	}

	/**
	 * @param stdClass $templates
	 * @param array $settings
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 */
	public function send(\stdClass $templates, array $settings) {
		
		try {
			$swift_message = new \Swift_Message();
			$swift_message
				->setFrom($settings['from']['email'], $settings['from']['name'])
				->setTo($settings['to']['email'], $settings['to']['name'])
				->setSubject($settings['subject'])
				->setReplyTo($settings['replyTo'])
				->setBody($templates->html, 'text/html')
				->addPart($templates->text, 'text/plain');			

			$response = $this->mailer->send($swift_message);	

		} catch (\Exception $e) {
			throw $e;
		}

		return (bool) $response;
	}

	
}