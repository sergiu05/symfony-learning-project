<?php

namespace AppBundle\Event\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use AppBundle\Event\AppEvents;
use AppBundle\Event\PlacedOrderEvent;
use Ucu\MailBundle\Services\MailManagerInterface;


class PlaceOrderListener implements EventSubscriberInterface {

	/**
	 * @var MailManagerInterface
	 */ 
	protected $mailManager;

	public function __construct(MailManagerInterface $mailManager) {
		$this->mailManager = $mailManager;
	}

	public static function getSubscribedEvents() {

		return array(
			AppEvents::PROCESSED_CART => 'onProcessedCart'
		);
	}

	public function onProcessedCart(PlacedOrderEvent $event) {
		$user = $event->getUser();
		$total = $event->getTotal();

		$this->mailManager->send('AppBundle:email:order.html.twig', ['to' => ['email' => $user->getEmail(), 'name' => $user->getUsername()], 'total' => $total]);
	}
}