<?php
namespace App\Core\Util;

use App\Calendar\Util\CalendarManager;
use App\Entity\Calendar;
use App\Entity\Person;
use App\People\Util\PersonManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
{
	/**
	 * @var UserInterface
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
     * @param UserInterface|null $user
     * @return string
     * @throws \Doctrine\ORM\ORMException
     */
	public function formatUserName(UserInterface $user = null): string
	{
	    @trigger_error(__method__ . ' in class ' . __CLASS__ . ' is deprecated. Use PersonManager->getFullUserName' , E_USER_DEPRECATED);
	    die();
		if ($user instanceof UserInterface)
			$id = $user->getId();

		if ($this->getUser())
			$id = $this->user->getId();

		if ($user instanceof UserInterface)
			return $user->formatName();

		return $this->getUser() ? $this->getUser()->formatName() : '' ;
	}

    /**
     * @return null|Calendar
     */
    public function getSystemCalendar(): ?Calendar
    {
        return $this->getCurrentCalendar();
    }

    /**
     * @return null|Calendar
     */
    public function getCurrentCalendar(): ?Calendar
    {
        return CalendarManager::getCurrentCalendar();
    }

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	public function getUserSetting($name)
	{
		return $this->getUser() instanceof UserInterface ? $this->getUser()->getUserSettings($name) : null;
	}

    /**
     * @return UserInterface|string|null
     */
    public function getUser()
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

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * getPerson
     *
     * @return Person|null
     * @throws \Doctrine\ORM\ORMException
     */
    public function getPerson(): ?Person
    {
        if (empty($this->person))
            $this->person = $this->getEntityManager()->getRepository(Person::class)->findOneByUser($this->getUser());

        return $this->person;
    }

    /**
     * hasPerson
     *
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function hasPerson(): bool
    {
        $this->getPerson();
        if ($this->person instanceof Person)
            return true;
        return false;
    }

    /**
     * @param User $user
     * @return UserManager
     */
    public function setUser(UserInterface $user): UserManager
    {
        $this->user = $user;
        $this->person = null;
        return $this;
    }

    /**
     * @return PersonManager
     */
    public function getPersonManager(): PersonManager
    {
        return $this->personManager;
    }
}