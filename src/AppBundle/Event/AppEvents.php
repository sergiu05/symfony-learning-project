<?php

namespace AppBundle\Event;

final class AppEvents {

	/**
	 * This event is triggered when a user places an order
	 *
	 * Listeners receive an instance of ....
	 */
	const PROCESSED_CART = 'app.processed_cart';
}