<?php

namespace Ucu\MailBundle\Services\Mailer;

use Ucu\MailBundle\Services\Mail;

interface MailerInterface {

	public function send(\stdClass $templates, array $settings);
	
}