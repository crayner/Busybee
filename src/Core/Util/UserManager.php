<?php
namespace App\Core\Util;

use App\Entity\Calendar;
use App\Entity\Person;
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
	 * @var Person
	 */
	private $person;

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
		$this->entityManager = $entityManager;
		$this->tokenStorage = $tokenStorage;
		$this->getUser();
	}

	/**
	 * @return string
	 */
	public function formatUserName(UserInterface $user = null): string
	{
		if ($user instanceof User)
			$id = $user->getId();

		if ($this->getUser())
			$id = $this->user->getId();

		if (empty($this->person))
			$this->person = $this->entityManager->getRepository(Person::class)->findOneByUser($id);

		if ($this->person instanceof Person)
			return $this->person->formatName();

		if ($user instanceof User)
			return $user->formatName();

		if ($this->getUser())
			return $this->getUser()->formatName();

		return '';
	}

    /**
     * @return null|object
     */
    public function getSystemCalendar()
    {
        if (! $this->getUser())
            return $this->entityManager->getRepository(Calendar::class)->findOneBy(['status' => 'current']);

        $calendar = $this->user->getUserSettings('calendar');
        $cal = is_null($calendar) ? null : $this->entityManager->getRepository(Calendar::class)->find($calendar) ;
        if (is_null($cal))
            $cal = $this->entityManager->getRepository(Calendar::class)->loadCurrentCalendar();

        return $cal;
    }

    /**
     * @return null|object
     */
    public function getCurrentCalendar()
    {
        return $this->getSystemCalendar();
    }

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function getUserSetting($name)
	{
		return $this->getUser() ? $this->getUser()->getUserSettings($name) : null;
	}

	private function getUser()
    {
        if (! $this->user)
        {
            if ($this->tokenStorage->getToken())
                $this->user = $this->tokenStorage->getToken()->getUser();
            else
                $this->user = null;
        }

        return $this->user;
    }
}