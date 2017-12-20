<?php
namespace App\Core\Manager;

use App\Entity\Setting;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StatusManager
{
	/**
	 * @var TableManager
	 */
	private $tableManager;

	/**
	 * @var bool|null
	 */
	private $installed;

	/**
	 * StatusManager constructor.
	 *
	 * @param TableManager $tableManager
	 */
	public function __construct(TableManager $tableManager)
	{
		$this->tableManager = $tableManager;
	}

	/**
	 * @return bool
	 */
	public function isInstalled(SessionInterface $session = null)
	{
		if (! is_null($this->installed))
			return $this->installed;
		if ($session instanceof SessionInterface && $session->has('is_installed') && $session->get('is_installed'))
				return true;

		if (! $session instanceof SessionInterface)
			$this->installed = $this->tableManager->isTableInstalled(Setting::class);

		return $this->installed;
	}

	/**
	 * @param bool $installed
	 *
	 * @return StatusManager
	 */
	public function setInstalled(bool $installed): StatusManager
	{
		$this->installed = $installed;

		return $this;
}
}