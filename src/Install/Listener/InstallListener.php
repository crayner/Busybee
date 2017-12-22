<?php
namespace App\Install\Listener;

use App\Core\Manager\TableManager;
use App\Entity\Setting;
use App\Install\Manager\InstallManager;
use App\Install\Manager\VersionManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class InstallListener implements EventSubscriberInterface
{
	/**
	 * @var RedirectResponse
	 */
	private $router;

	/**
	 * @var InstallManager
	 */
	private $installManager;

	/**
	 * @var TableManager
	 */
	private $tableManager;

	/**
	 * @var VersionManager
	 */
	private $versionManager;

	/**
	 * @param GetResponseEvent $event
	 *
	 * @return void
	 */
	public function onKernelRequest(GetResponseEvent $event)
	{
		if (! $event->isMasterRequest() || in_array($event->getRequest()->get('_route'),
				[
					'install_build',
					'install_check_mailer_parameters',
					'install_misc_check',
					'install_database',
					'install_system_settings',
				]
			)
		) return null;

		dump($this);die();

		// Test for db installation.
		$response = null;

		$this->installManager->getSQLParameters();
		// Are the database settings correct?
		if (! $this->installManager->testConnected())
			$response = new RedirectResponse($this->router->generate('install_build'));

		// Can I connect to the databse
		if (! $this->installManager->hasDatabase())
			$response = new RedirectResponse($this->router->generate('install_database'));

		// Are the database tables installed?
		if (false === $this->tableManager->isTableInstalled(Setting::class))
			$response = new RedirectResponse($this->router->generate('install_database'));

		if (! $this->versionManager->isUpToDate())
			$response = new RedirectResponse($this->router->generate('install_system_settings'));

		if (! empty($response))
			$event->setResponse($response);

		return ;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::REQUEST => ['onKernelRequest', 4],
		];
	}

	/**
	 * InstallListener constructor.
	 *
	 * @param RouterInterface $router
	 * @param InstallManager  $installManager
	 */
	public function __construct(RouterInterface $router, InstallManager $installManager, TableManager $tableManager, VersionManager $versionManager)
	{
		$this->router           = $router;
		$this->installManager   = $installManager;
		$this->tableManager     = $tableManager;
		$this->versionManager   = $versionManager;
	}
}