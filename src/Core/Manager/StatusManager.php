<?php
namespace App\Core\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StatusManager
{
	/**
	 * StatusManager constructor.
	 *
	 * @param ObjectManager $objectManager
	 */
	public function __construct()
	{
	}

	/**
	 * @return bool
	 */
	public function isInstalled(SessionInterface $session = null)
	{
		if ($session instanceof SessionInterface && $session->has('is_installed') && $session->get('is_installed'))
				return true;
		return false;
	}
}