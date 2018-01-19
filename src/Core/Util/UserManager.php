<?php
namespace App\Core\Util;

use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;
use Hillrange\Security\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	/**
	 * @var TokenStorageInterface
	 */
	private $tokenStorage;

	/**
	 * UserManager constructor.
	 *
	 * @param TokenStorageInterface  $tokenStorage
	 * @param EntityManagerInterface $entityManager
	 */
	public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
	{
		if ($tokenStorage->getToken())
			$this->user = $tokenStorage->getToken()->getUser();
		else
			$this->user = null;
		$this->entityManager = $entityManager;
		$this->tokenStorage = $tokenStorage;
	}

	/**
	 * @return string
	 */
	public function formatUserName(UserInterface $user = null): string
	{
		if ($user instanceof User)
			return $user->formatName();

		if ($this->user)
			return $this->user->formatName();

		return '';
	}

	/**
	 * @return null|object
	 */
	public function getSystemCalendar()
	{
		if (! $this->user)
			return $this->entityManager->getRepository(Calendar::class)->findOneBy(['status' => 'current']);

		$calendar = $this->user->getUserSettings('calendar');
		$cal = is_null($calendar) ? null : $this->entityManager->getRepository(Calendar::class)->find($calendar) ;
		if (is_null($cal))
			$cal = $this->entityManager->getRepository(Calendar::class)->loadCurrentCalendar();

		return $cal;
	}

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function getUserSetting($name)
	{
		if (! $this->user)
		{
			if ($this->tokenStorage->getToken())
				$this->user = $this->tokenStorage->getToken()->getUser();
			else
				$this->user = null;
		}
		return $this->user ? $this->user->getUserSettings($name) : null;
	}
}