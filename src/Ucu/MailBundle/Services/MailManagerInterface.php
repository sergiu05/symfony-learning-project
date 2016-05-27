<?php

namespace Ucu\MailBundle\Services;

interface MailManagerInterface {

	/**	 
	 * @param string $name
	 * @param array $parameters 	 	 
	 *
	 * @return boolean
	 */
	public function send($name, $parameters = []);
}

