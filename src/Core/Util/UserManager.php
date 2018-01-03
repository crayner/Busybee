<?php
namespace App\Core\Util;

use App\Entity\Calendar;
use Doctrine\ORM\EntityManagerInterface;
use Hillrange\Security\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
	 * UserManager constructor.
	 *
	 * @param TokenStorageInterface $token
	 */
	public function __construct(TokenStorageInterface $token, EntityManagerInterface $entityManager)
	{
		if ($token->getToken())
			$this->user = $token->getToken()->getUser();
		else
			$this->user = null;
		$this->entityManager = $entityManager;
	}

	/**
	 * @return string
	 */
	public function formatUserName(): string
	{
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
			return null;

		$calendar = $this->user->getUserSettings('Calendar');
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
			return null;

		return $this->user->getUserSettings($name);
	}
}