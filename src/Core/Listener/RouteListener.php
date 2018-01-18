<?php
namespace App\Core\Listener;

use App\Menu\Util\RouterManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RouteListener implements EventSubscriberInterface
{
	/**
	 * @var RouterManager
	 */
	private $routerManager;

	/**
	 *
	 * @param FinishRequestEvent $event
	 *
	 * @return void
	 */
	public function onKernelRequest(GetResponseEvent $event)
	{
		$this->routerManager->setCurrentRoute($event->getRequest());
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::REQUEST => ['onKernelRequest', -32],
		];
	}

	/**
	 * RouteListener constructor.
	 *
	 * @param RouterManager $routerManager
	 */
	public function __construct(RouterManager $routerManager)
	{
		$this->routerManager = $routerManager;
	}
}